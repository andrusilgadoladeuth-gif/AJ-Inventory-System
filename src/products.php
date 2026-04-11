<?php 
require_once 'db.php'; 
date_default_timezone_set('America/Bogota');

// 1. Lógica para ELIMINAR
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: products.php");
    exit();
}

// 2. Lógica para GUARDAR
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'])) {
    $stmt = $pdo->prepare("INSERT INTO products (category_id, name, price, stock) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_POST['category_id'], $_POST['name'], $_POST['price'], $_POST['stock']]);
    header("Location: products.php?success=1");
    exit();
}

// 3. Obtener categorías y productos
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
$products = $pdo->query("SELECT p.*, c.name as category_name 
                         FROM products p 
                         LEFT JOIN categories c ON p.category_id = c.id 
                         ORDER BY p.id ASC")->fetchAll();

include 'header.php'; 
?>

<div class="row">
    <div class="col-md-4">
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
                        <label class="form-label">Nombre del Producto</label>
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
                    <thead class="table-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $prod): ?>
                        <tr>
                            <td><?php echo $prod['name']; ?></td>
                            <td><span class="badge bg-secondary"><?php echo $prod['category_name']; ?></span></td>
                            <td>$<?php echo number_format($prod['price'], 2); ?></td>
                            <td><?php echo $prod['stock']; ?></td>
                            <td class="text-center">
                                <a href="products.php?delete=<?php echo $prod['id']; ?>" 
                                   class="btn btn-sm btn-outline-danger" 
                                   onclick="return confirm('¿Seguro que deseas eliminar este producto?')">Eliminar</a>
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