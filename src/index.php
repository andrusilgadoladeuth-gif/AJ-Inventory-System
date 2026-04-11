<?php 
require_once 'db.php';

// Consultas optimizadas con manejo de valores nulos
$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();

// Usamos IFNULL para asegurar que si no hay ventas, el resultado sea 0
$total_sales = $pdo->query("SELECT IFNULL(SUM(total_amount), 0) FROM sales")->fetchColumn();

$low_stock = $pdo->query("SELECT COUNT(*) FROM products WHERE stock < 5")->fetchColumn();

include 'header.php'; 
?>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card p-4 shadow-sm border-0 bg-primary text-white">
            <h5>Total Productos</h5>
            <h2 class="fw-bold"><?php echo (int)$total_products; ?></h2>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-4 shadow-sm border-0 bg-success text-white">
            <h5>Total Ventas ($)</h5>
            <h2 class="fw-bold">$<?php echo number_format((float)$total_sales, 2); ?></h2>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-4 shadow-sm border-0 bg-warning text-dark">
            <h5>Stock Bajo (< 5)</h5>
            <h2 class="fw-bold"><?php echo (int)$low_stock; ?></h2>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>