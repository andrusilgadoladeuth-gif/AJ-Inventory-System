<?php 
session_start();
require_once 'db.php'; 

// Lógica para ELIMINAR Cliente
if (isset($_GET['delete'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM customers WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
        $_SESSION['success_message'] = "Cliente eliminado correctamente.";
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "No se puede eliminar: el cliente ya tiene ventas registradas.";
    }
    header("Location: customers.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $pdo->prepare("INSERT INTO customers (name, identification, phone, address) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_POST['name'], $_POST['identification'], $_POST['phone'], $_POST['address']]);
    header("Location: customers.php");
    exit();
}

$customers = $pdo->query("SELECT * FROM customers ORDER BY name ASC")->fetchAll();
include 'header.php'; 
?>

<div class="row">
    <div class="col-md-4">
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
        <?php endif; ?>
        
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="fw-bold">Nuevo Cliente</h5>
                <form method="POST">
                    <input type="text" name="name" class="form-control mb-2" placeholder="Nombre Completo" required>
                    <input type="text" name="identification" class="form-control mb-2" placeholder="Cédula/NIT" required>
                    <input type="text" name="phone" class="form-control mb-2" placeholder="Teléfono">
                    <textarea name="address" class="form-control mb-2" placeholder="Dirección"></textarea>
                    <button type="submit" class="btn btn-primary w-100">Guardar Cliente</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card shadow-sm border-0 p-3">
            <h5 class="fw-bold">Mis Clientes</h5>
            <table class="table table-hover">
                <thead><tr><th>ID</th><th>Nombre</th><th>Identificación</th><th>Acciones</th></tr></thead>
                <tbody>
                    <?php foreach ($customers as $c): ?>
                    <tr>
                        <td><?php echo $c['id']; ?></td>
                        <td><?php echo $c['name']; ?></td>
                        <td><?php echo $c['identification']; ?></td>
                        <td>
                            <a href="customers.php?delete=<?php echo $c['id']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('¿Seguro que deseas eliminar este cliente?')">Eliminar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>