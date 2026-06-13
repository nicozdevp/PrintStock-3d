<?php
require_once '../includes/auth.php';
requireRole('admin');
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'eliminar') {
    validateCsrf();
    $id = (int) ($_POST['id'] ?? 0);
    if ($id > 0) {
        $conexion->prepare('DELETE FROM productos WHERE id = ?')->execute([$id]);
    }
    header('Location: productos.php');
    exit();
}

$productos = $conexion->query('SELECT * FROM productos ORDER BY creado_en DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Productos / Piezas 3D</h2>
        <a href="producto_nuevo.php" class="btn btn-success">+ Nuevo producto</a>
    </div>

    <?php if (empty($productos)): ?>
        <div class="alert alert-info">No hay productos registrados aún.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $p): ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td><?= htmlspecialchars($p['nombre']) ?></td>
                        <td><?= htmlspecialchars($p['categoria'] ?? '—') ?></td>
                        <td>$<?= number_format((float)$p['precio'], 2) ?></td>
                        <td>
                            <span class="badge <?= (int)$p['stock'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                                <?= (int)$p['stock'] ?>
                            </span>
                        </td>
                        <td>
                            <a href="producto_editar.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                            <form method="POST" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar «<?= htmlspecialchars($p['nombre'], ENT_QUOTES) ?>»?')">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                                <input type="hidden" name="action" value="eliminar">
                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
