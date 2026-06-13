<?php
require_once '../includes/auth.php';
requireRole('admin');
require_once '../config/database.php';

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: productos.php');
    exit();
}

$stmt = $conexion->prepare('SELECT * FROM productos WHERE id = ?');
$stmt->execute([$id]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    header('Location: productos.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf();

    $nombre      = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio      = (float) ($_POST['precio'] ?? 0);
    $stock       = (int) ($_POST['stock'] ?? 0);
    $categoria   = trim($_POST['categoria'] ?? '');

    if ($nombre !== '') {
        $stmt = $conexion->prepare(
            'UPDATE productos SET nombre=?, descripcion=?, precio=?, stock=?, categoria=? WHERE id=?'
        );
        $stmt->execute([$nombre, $descripcion, $precio, $stock, $categoria, $id]);
        header('Location: productos.php');
        exit();
    } else {
        $error = 'El nombre del producto es obligatorio.';
    }
}
?>
<?php include '../includes/header.php'; ?>

<div class="container mt-5" style="max-width:600px">
    <h2 class="mb-4">Editar producto</h2>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <div class="mb-3">
            <label class="form-label">Nombre <span class="text-danger">*</span></label>
            <input type="text" name="nombre" class="form-control"
                   value="<?= htmlspecialchars($producto['nombre']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control" rows="3"><?= htmlspecialchars($producto['descripcion'] ?? '') ?></textarea>
        </div>
        <div class="row">
            <div class="col mb-3">
                <label class="form-label">Precio ($)</label>
                <input type="number" name="precio" class="form-control" min="0" step="0.01"
                       value="<?= $producto['precio'] ?>">
            </div>
            <div class="col mb-3">
                <label class="form-label">Stock</label>
                <input type="number" name="stock" class="form-control" min="0"
                       value="<?= (int)$producto['stock'] ?>">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Categoría</label>
            <input type="text" name="categoria" class="form-control"
                   value="<?= htmlspecialchars($producto['categoria'] ?? '') ?>">
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-warning">Actualizar</button>
            <a href="productos.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
