<?php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('operario', 'admin');
require_once __DIR__ . '/../../config/database.php';

$mensaje = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf();
    $productoId  = (int) ($_POST['producto_id'] ?? 0);
    $nuevoStock  = (int) ($_POST['nuevo_stock'] ?? 0);
    if ($productoId > 0 && $nuevoStock >= 0) {
        $conexion->prepare('UPDATE productos SET stock = ? WHERE id = ?')->execute([$nuevoStock, $productoId]);
        $mensaje = 'Stock actualizado correctamente.';
    }
}

$productos = $conexion->query('SELECT * FROM productos ORDER BY stock ASC, nombre ASC')->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gestión de Stock</h2>
        <a href="index.php" class="btn btn-outline-secondary btn-sm">← Panel</a>
    </div>

    <?php if ($mensaje): ?>
        <div class="alert alert-success"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <?php if (empty($productos)): ?>
        <div class="alert alert-info">No hay productos registrados.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr><th>Producto</th><th>Categoría</th><th>Stock actual</th><th>Nuevo stock</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $p): ?>
                    <tr class="<?= (int)$p['stock'] === 0 ? 'table-danger' : ((int)$p['stock'] <= 5 ? 'table-warning' : '') ?>">
                        <td><?= htmlspecialchars($p['nombre']) ?></td>
                        <td class="text-muted small"><?= htmlspecialchars($p['categoria'] ?? '—') ?></td>
                        <td>
                            <span class="badge bg-<?= (int)$p['stock'] === 0 ? 'danger' : ((int)$p['stock'] <= 5 ? 'warning text-dark' : 'success') ?> fs-6">
                                <?= (int)$p['stock'] ?>
                            </span>
                        </td>
                        <td>
                            <form method="POST" class="d-flex gap-2">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                                <input type="hidden" name="producto_id" value="<?= $p['id'] ?>">
                                <input type="number" name="nuevo_stock" class="form-control form-control-sm"
                                       style="width:90px" min="0" value="<?= (int)$p['stock'] ?>">
                                <button type="submit" class="btn btn-sm btn-primary">Guardar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="text-muted small mt-2">
            <span class="badge bg-danger">0</span> Agotado &nbsp;
            <span class="badge bg-warning text-dark">1–5</span> Stock bajo &nbsp;
            <span class="badge bg-success">6+</span> Normal
        </p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
