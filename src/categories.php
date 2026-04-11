<?php 
require_once 'db.php'; 

// --- 1. LÓGICA PARA ELIMINAR Y REINICIAR CONTADOR ---
if (isset($_GET['delete'])) {
    $id_a_eliminar = $_GET['delete'];
    
    // Eliminamos la categoría
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id_a_eliminar]);
    
    // Esta es la "magia" para que el ID no salte números:
    // 1. Buscamos el ID máximo actual
    $result = $pdo->query("SELECT MAX(id) FROM categories")->fetchColumn();
    $next_id = $result ? $result + 1 : 1;
    
    // 2. Reiniciamos el contador AUTO_INCREMENT al siguiente número lógico
    $pdo->exec("ALTER TABLE categories AUTO_INCREMENT = $next_id");
    
    header("Location: categories.php"); 
    exit(); 
}

// --- 2. LÓGICA PARA GUARDAR ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['category_name'])) {
    $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
    $stmt->execute([$_POST['category_name']]);
    
    header("Location: categories.php?success=1"); 
    exit(); 
}

// --- 3. CONSULTA DE DATOS (Orden ascendente) ---
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
                            // Conversión a hora local de Colombia
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
                                   onclick="return confirm('¿Seguro que deseas eliminar? Recuerda borrar los productos asociados primero.')">
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