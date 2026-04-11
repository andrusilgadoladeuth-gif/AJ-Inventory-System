<?php 
// IMPORTANTE: Asegúrate de que este session_start() esté siempre antes de cualquier HTML
session_start(); 
require_once 'db.php'; 

// --- 1. LÓGICA PARA ELIMINAR ---
if (isset($_GET['delete'])) {
    $id_a_eliminar = $_GET['delete'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id_a_eliminar]);
        
        $result = $pdo->query("SELECT MAX(id) FROM categories")->fetchColumn();
        $next_id = $result ? $result + 1 : 1;
        $pdo->exec("ALTER TABLE categories AUTO_INCREMENT = $next_id");
        
        header("Location: categories.php");
        exit();
    } catch (PDOException $e) {
        // Guardamos el error en la sesión para que se muestre una sola vez
        $_SESSION['error_message'] = "No se puede eliminar, tiene productos asociados.";
        header("Location: categories.php");
        exit();
    }
}

// --- 2. LÓGICA PARA GUARDAR ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['category_name'])) {
    $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
    $stmt->execute([$_POST['category_name']]);
    header("Location: categories.php?success=1"); 
    exit(); 
}

// --- 3. CONSULTA DE DATOS ---
$stmt = $pdo->query("SELECT * FROM categories ORDER BY id ASC");
$categories = $stmt->fetchAll();

include 'header.php'; 
?>

<div class="row">
    <div class="col-md-4">
        <?php if (isset($_GET['success'])): ?>
            <div class='alert alert-success alert-dismissible fade show'>
                Categoría guardada con éxito.
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class='alert alert-danger alert-dismissible fade show'>
                <strong>Error:</strong> <?php echo $_SESSION['error_message']; ?>
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
            </div>
            <?php unset($_SESSION['error_message']); // Borramos el error tras mostrarlo ?>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="fw-bold">Nueva Categoría</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label text-muted small">Nombre de la categoría</label>
                        <input type="text" name="category_name" class="form-control" placeholder="Ej: Electrónica" required>
                    </div>
                    <button type="submit" class="btn btn-dark w-100">Guardar Categoría</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="fw-bold">Listado de Categorías</h5>
                <table class="table table-hover mt-3">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Fecha (Colombia)</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): 
                            $date = new DateTime($cat['created_at'], new DateTimeZone('UTC'));
                            $date->setTimezone(new DateTimeZone('America/Bogota'));
                        ?>
                        <tr>
                            <td><?php echo $cat['id']; ?></td>
                            <td><span class="badge bg-light text-dark border"><?php echo $cat['name']; ?></span></td>
                            <td class="small text-muted"><?php echo $date->format('Y-m-d H:i'); ?></td>
                            <td class="text-center">
                                <a href="categories.php?delete=<?php echo $cat['id']; ?>" 
                                   class="btn btn-sm btn-outline-danger" 
                                   onclick="return confirm('¿Seguro que deseas eliminar esta categoría?')">Eliminar</a>
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