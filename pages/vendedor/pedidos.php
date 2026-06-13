<?php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('vendedor', 'admin');
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'cancelar') {
    validateCsrf();
    $id = (int) ($_POST['id'] ?? 0);
    if ($id > 0) {
        $conexion->prepare(
            "UPDATE pedidos SET estado = 'cancelado' WHERE id = ? AND estado = 'pendiente'"
        )->execute([$id]);
    }
    header('Location: pedidos.php');
    exit();
}

$pedidos = $conexion->query("
    SELECT p.*, c.nombre AS cliente_nombre, v.nombre AS vendedor_nombre
    FROM pedidos p
    JOIN usuarios c ON p.cliente_id = c.id
    LEFT JOIN usuarios v ON p.vendedor_id = v.id
    ORDER BY p.creado_en DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Pedidos</h2>
        <a href="pedido_nuevo.php" class="btn btn-success">+ Nuevo pedido</a>
    </div>

    <?php if (empty($pedidos)): ?>
        <div class="alert alert-info">No hay pedidos registrados aún.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr><th>#</th><th>Cliente</th><th>Vendedor</th><th>Total</th><th>Estado</th><th>Fecha</th><th>Acción</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidos as $p): ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td><?= htmlspecialchars($p['cliente_nombre']) ?></td>
                        <td><?= htmlspecialchars($p['vendedor_nombre'] ?? '—') ?></td>
                        <td>$<?= number_format((float)$p['total'], 2) ?></td>
                        <td><?= estadoBadge($p['estado']) ?></td>
                        <td class="text-muted small"><?= substr($p['creado_en'], 0, 16) ?></td>
                        <td>
                            <?php if ($p['estado'] === 'pendiente'): ?>
                            <form method="POST" class="d-inline"
                                  onsubmit="return confirm('¿Cancelar pedido #<?= $p['id'] ?>?')">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                                <input type="hidden" name="action" value="cancelar">
                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                <button class="btn btn-sm btn-danger">Cancelar</button>
                            </form>
                            <?php else: ?>
                                <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
