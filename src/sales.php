<?php 
session_start();
require_once 'db.php'; 
date_default_timezone_set('America/Bogota');

// 1. Lógica para Registrar Venta
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    
    // Consultar precio y stock actual
    $stmt = $pdo->prepare("SELECT price, stock FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    
    if ($product && $product['stock'] >= $quantity) {
        $total = $product['price'] * $quantity;
        
        // Registrar venta
        $pdo->prepare("INSERT INTO sales (total_amount) VALUES (?)")->execute([$total]);
        $sale_id = $pdo->lastInsertId();
        
        // Registrar detalle
        $pdo->prepare("INSERT INTO sale_details (sale_id, product_id, quantity, price_at_sale) VALUES (?, ?, ?, ?)")
            ->execute([$sale_id, $product_id, $quantity, $product['price']]);
            
        // Restar stock
        $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?")->execute([$quantity, $product_id]);
        
        $_SESSION['success_message'] = "Venta registrada con éxito. Total: $" . number_format($total, 2);
    } else {
        $_SESSION['error_message'] = "Stock insuficiente o producto no encontrado.";
    }
    // Redirigimos sin parámetros extra para mantener la URL limpia
    header("Location: sales.php");
    exit();
}

// Consultas para la vista
$products = $pdo->query("SELECT * FROM products WHERE stock > 0 ORDER BY name ASC")->fetchAll();
$recent_sales = $pdo->query("SELECT s.id, s.total_amount, s.created_at, p.name 
                             FROM sales s 
                             JOIN sale_details sd ON s.id = sd.sale_id 
                             JOIN products p ON sd.product_id = p.id 
                             ORDER BY s.created_at DESC LIMIT 5")->fetchAll();

include 'header.php'; 
?>

<div class="row">
    <div class="col-md-4">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class='alert alert-success alert-dismissible fade show'>
                <?php echo $_SESSION['success_message']; ?>
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class='alert alert-danger alert-dismissible fade show'>
                <?php echo $_SESSION['error_message']; ?>
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="fw-bold">Registrar Nueva Venta</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Producto</label>
                        <select name="product_id" class="form-select" required>
                            <option value="">Seleccione producto...</option>
                            <?php foreach ($products as $prod): ?>
                                <option value="<?php echo $prod['id']; ?>">
                                    <?php echo $prod['name']; ?> (Stock: <?php echo $prod['stock']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cantidad</label>
                        <input type="number" name="quantity" class="form-control" min="1" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Procesar Venta</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="fw-bold">Últimas 5 ventas</h5>
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr><th>Producto</th><th>Total</th><th>Fecha</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_sales as $sale): ?>
                        <tr>
                            <td><?php echo $sale['name']; ?></td>
                            <td>$<?php echo number_format($sale['total_amount'], 2); ?></td>
                            <td><?php echo $sale['created_at']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>