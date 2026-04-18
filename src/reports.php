<?php 
session_start();
require_once 'db.php'; 
date_default_timezone_set('America/Bogota');

// 1. Lógica para ELIMINAR una venta del reporte
if (isset($_GET['delete_sale'])) {
    $sale_id = $_GET['delete_sale'];
    // Borramos primero los detalles por la clave foránea y luego la venta
    $pdo->prepare("DELETE FROM sale_details WHERE sale_id = ?")->execute([$sale_id]);
    $pdo->prepare("DELETE FROM sales WHERE id = ?")->execute([$sale_id]);
    
    $_SESSION['success_message'] = "Registro de venta eliminado.";
    header("Location: reports.php");
    exit();
}

// 2. Lógica de FILTROS (Mes y Año)
$selected_month = isset($_GET['month']) ? $_GET['month'] : date('m');
$selected_year = isset($_GET['year']) ? $_GET['year'] : date('Y');

$query = "SELECT s.id as sale_id, s.total_amount, s.created_at, 
          p.name as product_name, sd.quantity, sd.price_at_sale
          FROM sales s
          JOIN sale_details sd ON s.id = sd.sale_id
          JOIN products p ON sd.product_id = p.id
          WHERE MONTH(s.created_at) = ? AND YEAR(s.created_at) = ?
          ORDER BY s.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute([$selected_month, $selected_year]);
$sales = $stmt->fetchAll();

include 'header.php'; 
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <h2 class="fw-bold">Reporte de Ventas</h2>
        <button onclick="window.print()" class="btn btn-dark">Imprimir Reporte</button>
    </div>

    <div class="card shadow-sm border-0 mb-4 no-print">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-5">
                    <label class="form-label fw-bold">Mes</label>
                    <select name="month" class="form-select">
                        <?php
                        $meses = [
                            "01" => "Enero", "02" => "Febrero", "03" => "Marzo", "04" => "Abril",
                            "05" => "Mayo", "06" => "Junio", "07" => "Julio", "08" => "Agosto",
                            "09" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre"
                        ];
                        foreach ($meses as $num => $nombre): ?>
                            <option value="<?php echo $num; ?>" <?php echo ($selected_month == $num) ? 'selected' : ''; ?>>
                                <?php echo $nombre; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Año</label>
                    <select name="year" class="form-select">
                        <?php for($i = 2024; $i <= 2030; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php echo ($selected_year == $i) ? 'selected' : ''; ?>>
                                <?php echo $i; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="mb-3">Mostrando: <?php echo $meses[$selected_month] . " " . $selected_year; ?></h5>
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Producto</th>
                        <th>Cant.</th>
                        <th>Total</th>
                        <th class="no-print">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $gran_total = 0;
                    foreach ($sales as $sale): 
                        $gran_total += $sale['total_amount'];
                    ?>
                    <tr>
                        <td>#<?php echo $sale['sale_id']; ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($sale['created_at'])); ?></td>
                        <td><?php echo $sale['product_name']; ?></td>
                        <td><?php echo $sale['quantity']; ?></td>
                        <td class="fw-bold">$<?php echo number_format($sale['total_amount'], 2); ?></td>
                        <td class="no-print">
                            <a href="reports.php?delete_sale=<?php echo $sale['sale_id']; ?>" 
                               class="text-danger" 
                               onclick="return confirm('¿Eliminar este registro de venta?')">
                               Borrar
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-dark">
                    <tr>
                        <td colspan="4" class="text-end">TOTAL DEL MES:</td>
                        <td colspan="2">$<?php echo number_format($gran_total, 2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print, .navbar, .btn, .alert { display: none !important; }
    body { background-color: white !important; }
    .card { border: none !important; }
}
</style>

<?php include 'footer.php'; ?>