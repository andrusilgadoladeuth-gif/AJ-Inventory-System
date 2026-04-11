<?php 
session_start();
require_once 'db.php'; 

// 1. Lógica para ELIMINAR (con control de errores)
if (isset($_GET['delete'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
        $_SESSION['success_message'] = "Producto eliminado.";
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "No se puede eliminar: tiene ventas asociadas.";
    }
    header("Location: products.php");
    exit();
}

// 2. Lógica para AGREGAR STOCK
if (isset($_POST['update_stock'])) {
    $stmt = $pdo->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
    $stmt->execute([$_POST['add_stock'], $_POST['product_id']]);
    header("Location: products.php");
    exit();
}

// 3. Lógica para GUARDAR nuevo producto
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'])) {
    $stmt = $pdo->prepare("INSERT INTO products (category_id, name, price, stock) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_POST['category_id'], $_POST['name'], $_POST['price'], $_POST['stock']]);
    header("Location: products.php");
    exit();
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
$products = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id ASC")->fetchAll();

include 'header.php'; 
?>

<div class="row">
    <div class="col-md-4">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class='alert alert-success'><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class='alert alert-danger'><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="fw-bold">Nuevo Producto</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Categoría</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Precio</label>
                        <input type="number" step="0.01" name="price" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stock</label>
                        <input type="number" name="stock" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-dark w-100">Guardar Producto</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="fw-bold">Listado de Inventario</h5>
                <table class="table table-hover mt-3">
                    <thead><tr><th>Nombre</th><th>Categoría</th><th>Precio</th><th>Stock</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php foreach ($products as $prod): ?>
                        <tr>
                            <td><?php echo $prod['name']; ?></td>
                            <td><?php echo $prod['category_name']; ?></td>
                            <td>$<?php echo number_format($prod['price'], 2); ?></td>
                            <td>
                                <?php echo $prod['stock']; ?>
                                <form method="POST" class="d-flex mt-1">
                                    <input type="hidden" name="product_id" value="<?php echo $prod['id']; ?>">
                                    <input type="number" name="add_stock" class="form-control form-control-sm w-50" placeholder="+" required>
                                    <button type="submit" name="update_stock" class="btn btn-sm btn-success ms-1">+</button>
                                </form>
                            </td>
                            <td>
                                <a href="products.php?delete=<?php echo $prod['id']; ?>" 
                                   class="btn btn-sm btn-outline-danger" 
                                   onclick="return confirm('¿Seguro?')">Eliminar</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>