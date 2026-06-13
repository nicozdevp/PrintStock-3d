<?php
require_once '../includes/auth.php';
requireRole('admin');
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf();

    $nombre      = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio      = (float) ($_POST['precio'] ?? 0);
    $stock       = (int) ($_POST['stock'] ?? 0);
    $categoria   = trim($_POST['categoria'] ?? '');

    if ($nombre !== '') {
        $stmt = $conexion->prepare(
            'INSERT INTO productos (nombre, descripcion, precio, stock, categoria) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$nombre, $descripcion, $precio, $stock, $categoria]);
        header('Location: productos.php');
        exit();
    } else {
        $error = 'El nombre del producto es obligatorio.';
    }
}
?>
<?php include '../includes/header.php'; ?>

<div class="container mt-5" style="max-width:600px">
    <h2 class="mb-4">Nuevo producto</h2>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <div class="mb-3">
            <label class="form-label">Nombre <span class="text-danger">*</span></label>
            <input type="text" name="nombre" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control" rows="3"></textarea>
        </div>
        <div class="row">
            <div class="col mb-3">
                <label class="form-label">Precio ($)</label>
                <input type="number" name="precio" class="form-control" min="0" step="0.01" value="0">
            </div>
            <div class="col mb-3">
                <label class="form-label">Stock</label>
                <input type="number" name="stock" class="form-control" min="0" value="0">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Categoría</label>
            <input type="text" name="categoria" class="form-control" placeholder="Ej: Figuras, Repuestos, Decoración">
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success">Guardar</button>
            <a href="productos.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
