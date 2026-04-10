<?php 
require_once 'db.php'; 

// --- LÓGICA PARA ELIMINAR (Mover aquí arriba para evitar el error) ---
if (isset($_GET['delete'])) {
    $id_a_eliminar = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id_a_eliminar]);
    
    // Ahora el header funcionará perfecto porque no se ha enviado HTML aún
    header("Location: categories.php"); 
    exit(); 
}

// --- LÓGICA PARA GUARDAR ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['category_name'])) {
    $name = $_POST['category_name'];
    $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
    $stmt->execute([$name]);
    // Nota: Aquí no usamos header, así que el mensaje de éxito se mostrará abajo
    $mensaje_exito = true;
}

// Obtener todas las categorías
$stmt = $pdo->query("SELECT * FROM categories ORDER BY id DESC");
$categories = $stmt->fetchAll();

include 'header.php'; // El HTML empieza AQUÍ
?>

<div class="row">
    <div class="col-md-4">
        <?php if (isset($mensaje_exito)): ?>
            <div class='alert alert-success alert-dismissible fade show'>
                Categoría guardada con éxito.
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
            </div>
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
                            <th>Fecha</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td><?php echo $cat['id']; ?></td>
                            <td><span class="badge bg-light text-dark border"><?php echo $cat['name']; ?></span></td>
                            <td class="small text-muted"><?php echo $cat['created_at']; ?></td>
                            <td class="text-center">
                                <a href="categories.php?delete=<?php echo $cat['id']; ?>" 
                                   class="btn btn-sm btn-outline-danger" 
                                   onclick="return confirm('¿Estás seguro de eliminar esta categoría?')">
                                   Eliminar
                                </a>
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