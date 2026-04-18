<?php 
require_once 'db.php';
// 1. Forzamos zona horaria
date_default_timezone_set('America/Bogota');

if (!isset($_GET['id'])) {
    header("Location: sales.php");
    exit();
}

$id = $_GET['id'];

// CAMBIO CLAVE: Traemos todos los detalles asociados a esta venta
$query = "SELECT s.*, c.name as customer_name, c.identification, c.phone, c.address, 
                 p.name as product_name, sd.quantity, sd.price_at_sale 
          FROM sales s 
          JOIN customers c ON s.customer_id = c.id 
          JOIN sale_details sd ON s.id = sd.sale_id 
          JOIN products p ON sd.product_id = p.id 
          WHERE s.id = ?";

$stmt = $pdo->prepare($query);
$stmt->execute([$id]); 
$items = $stmt->fetchAll(); // Usamos fetchAll para obtener todos los productos

if (!$items) {
    echo "Factura no encontrada.";
    exit();
}

// Tomamos los datos generales de la primera fila (ya que se repiten en todas)
$data = $items[0];

include 'header.php';
?>

<div class="container bg-white p-5 shadow mt-3" id="factura" style="max-width: 800px;">
    <div class="row">
        <div class="col-6">
            <h2 class="fw-bold text-primary">AJ INVENTORY</h2>
            <p class="mb-0">NIT: 12345678-9</p>
            <p>San Antero, Córdoba, Colombia</p>
        </div>
        <div class="col-6 text-end">
            <h3 class="text-muted">FACTURA DE VENTA</h3>
            <h4 class="fw-bold">#<?php echo $data['id']; ?></h4>
            <p class="mb-0 fw-bold">Fecha y Hora:</p>
            <p>
                <?php 
                $timestamp = strtotime($data['created_at']);
                $hora_colombia = $timestamp - (5 * 3600);
                echo date('d/m/Y h:i A', $hora_colombia); 
                ?>
            </p>
        </div>
    </div>
    
    <hr>
    
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="fw-bold border-bottom pb-2">DATOS DEL CLIENTE</h5>
            <div class="row">
                <div class="col-6">
                    <p class="mb-1"><strong>Nombre:</strong> <?php echo $data['customer_name']; ?></p>
                    <p class="mb-1"><strong>Identificación:</strong> <?php echo $data['identification']; ?></p>
                </div>
                <div class="col-6">
                    <p class="mb-1"><strong>Teléfono:</strong> <?php echo $data['phone'] ? $data['phone'] : 'N/A'; ?></p>
                    <p class="mb-1"><strong>Dirección:</strong> <?php echo $data['address'] ? $data['address'] : 'N/A'; ?></p>
                </div>
            </div>
        </div>
    </div>

    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Descripción del Producto</th>
                <th class="text-center">Cantidad</th>
                <th class="text-end">Precio Unit.</th>
                <th class="text-end">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $gran_total = 0;
            foreach ($items as $item): 
                $subtotal = $item['quantity'] * $item['price_at_sale'];
                $gran_total += $subtotal;
            ?>
            <tr>
                <td><?php echo $item['product_name']; ?></td>
                <td class="text-center"><?php echo $item['quantity']; ?></td>
                <td class="text-end">$<?php echo number_format($item['price_at_sale'], 2); ?></td>
                <td class="text-end">$<?php echo number_format($subtotal, 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-end h5">TOTAL A PAGAR:</th>
                <th class="h5 text-success text-end">$<?php echo number_format($data['total_amount'], 2); ?></th>
            </tr>
        </tfoot>
    </table>

    <div class="mt-5 text-center small text-muted">
        <p>Gracias por su compra en AJ INVENTORY</p>
    </div>

    <div class="no-print text-center mt-4">
        <button onclick="window.print()" class="btn btn-dark shadow-sm">
            <i class="bi bi-printer"></i> Imprimir Factura
        </button>
        <a href="sales.php" class="btn btn-outline-secondary">
            <i class="bi bi-plus-circle"></i> Nueva Venta
        </a>
    </div>
</div>

<style>
@media print { 
    .no-print, .navbar, footer { display: none !important; } 
    .container { box-shadow: none !important; margin: 0; width: 100%; max-width: 100%; padding: 0 !important; }
    body { background-color: white !important; }
}
</style>

<?php include 'footer.php'; ?>