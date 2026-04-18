<?php 
session_start();
require_once 'db.php'; 

// 1. Forzamos la zona horaria en PHP
date_default_timezone_set('America/Bogota');
$fecha_actual = date("Y-m-d H:i:s"); 

// 2. Forzar la zona horaria en la conexión
$pdo->exec("SET time_zone = '-05:00'");

// 1. Lógica para Registrar Venta (Multi-producto)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_ids'])) {
    $product_ids = $_POST['product_ids']; // Array de IDs
    $quantities = $_POST['quantities'];   // Array de cantidades
    $customer_id = $_POST['customer_id'];
    
    $total_venta = 0;
    $items_a_vender = [];

    // Validar stock de todos los productos primero
    foreach ($product_ids as $index => $p_id) {
        $qty = (int)$quantities[$index];
        if ($qty <= 0) continue;

        $stmt = $pdo->prepare("SELECT name, price, stock FROM products WHERE id = ?");
        $stmt->execute([$p_id]);
        $product = $stmt->fetch();

        if ($product && $product['stock'] >= $qty) {
            $subtotal = $product['price'] * $qty;
            $total_venta += $subtotal;
            $items_a_vender[] = [
                'id' => $p_id,
                'qty' => $qty,
                'price' => $product['price']
            ];
        } else {
            $_SESSION['error_message'] = "Stock insuficiente para: " . ($product['name'] ?? "Producto ID $p_id");
            header("Location: sales.php");
            exit();
        }
    }

    if (!empty($items_a_vender)) {
        // Insertar cabecera de la venta
        $pdo->prepare("INSERT INTO sales (total_amount, customer_id, created_at) VALUES (?, ?, ?)")
             ->execute([$total_venta, $customer_id, $fecha_actual]);
             
        $sale_id = $pdo->lastInsertId();
        
        // Insertar detalles y actualizar stock
        foreach ($items_a_vender as $item) {
            $pdo->prepare("INSERT INTO sale_details (sale_id, product_id, quantity, price_at_sale) VALUES (?, ?, ?, ?)")
                ->execute([$sale_id, $item['id'], $item['qty'], $item['price']]);
                
            $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?")
                ->execute([$item['qty'], $item['id']]);
        }
        
        $_SESSION['success_message'] = "Venta multi-producto registrada con éxito. Total: $" . number_format($total_venta, 2);
        header("Location: invoice.php?id=" . $sale_id);
        exit();
    }
}

// Consultas para la vista
$products = $pdo->query("SELECT * FROM products WHERE stock > 0 ORDER BY name ASC")->fetchAll();
$customers = $pdo->query("SELECT * FROM customers ORDER BY name ASC")->fetchAll();

$recent_sales = $pdo->query("SELECT s.id, s.total_amount, s.created_at, GROUP_CONCAT(p.name SEPARATOR ', ') as names 
                             FROM sales s 
                             JOIN sale_details sd ON s.id = sd.sale_id 
                             JOIN products p ON sd.product_id = p.id 
                             GROUP BY s.id
                             ORDER BY s.id DESC LIMIT 5")->fetchAll();

include 'header.php'; 
?>

<div class="row">
    <div class="col-md-5">
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
                <h5 class="fw-bold mb-3">Registrar Nueva Venta</h5>
                <form method="POST" id="sales-form">
                    <div class="mb-3">
                        <label class="form-label">Cliente</label>
                        <select name="customer_id" class="form-select" required>
                            <option value="">Seleccione cliente...</option>
                            <?php foreach ($customers as $c): ?>
                                <option value="<?php echo $c['id']; ?>">
                                    <?php echo $c['name']; ?> (<?php echo $c['identification']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div id="product-list">
                        <div class="product-item border p-2 mb-2">
                            <label class="form-label small">Producto</label>
                            <select name="product_ids[]" class="form-select mb-2" required>
                                <option value="">Seleccione producto...</option>
                                <?php foreach ($products as $prod): ?>
                                    <option value="<?php echo $prod['id']; ?>">
                                        <?php echo $prod['name']; ?> (Stock: <?php echo $prod['stock']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label class="form-label small">Cantidad</label>
                            <input type="number" name="quantities[]" class="form-control" min="1" required>
                        </div>
                    </div>

                    <button type="button" class="btn btn-outline-secondary btn-sm w-100 mb-3" onclick="addProduct()">
                        + Agregar otro producto
                    </button>

                    <button type="submit" class="btn btn-primary w-100">Procesar Venta</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="fw-bold">Últimas 5 ventas</h5>
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr><th>Productos</th><th>Total</th><th>Fecha</th><th>Acción</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_sales as $sale): ?>
                        <tr>
                            <td class="small"><?php echo $sale['names']; ?></td>
                            <td>$<?php echo number_format($sale['total_amount'], 2); ?></td>
                            <td class="small"><?php echo date('d/m/Y h:i A', strtotime($sale['created_at'])); ?></td>
                            <td>
                                <a href="invoice.php?id=<?php echo $sale['id']; ?>" class="btn btn-sm btn-outline-dark">Factura</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function addProduct() {
    const div = document.createElement('div');
    div.className = 'product-item border p-2 mb-2 position-relative';
    div.innerHTML = `
        <button type="button" class="btn-close position-absolute top-0 end-0 m-1" onclick="this.parentElement.remove()"></button>
        <label class="form-label small">Producto</label>
        <select name="product_ids[]" class="form-select mb-2" required>
            <option value="">Seleccione producto...</option>
            <?php foreach ($products as $prod): ?>
                <option value="<?php echo $prod['id']; ?>"><?php echo $prod['name']; ?> (Stock: <?php echo $prod['stock']; ?>)</option>
            <?php endforeach; ?>
        </select>
        <label class="form-label small">Cantidad</label>
        <input type="number" name="quantities[]" class="form-control" min="1" required>
    `;
    document.getElementById('product-list').appendChild(div);
}
</script>

<?php include 'footer.php'; ?>