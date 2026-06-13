<?php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('cliente');
require_once __DIR__ . '/../../config/database.php';

$cid = (int) $_SESSION['usuario_id'];

$pedidos = $conexion->query("
    SELECT p.*,
           GROUP_CONCAT(pr.nombre ORDER BY pr.nombre SEPARATOR ', ') AS productos_nombres
    FROM pedidos p
    LEFT JOIN pedido_items pi ON pi.pedido_id = p.id
    LEFT JOIN productos pr ON pr.id = pi.producto_id
    WHERE p.cliente_id = $cid
    GROUP BY p.id
    ORDER BY p.creado_en DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Mis Pedidos</h2>
        <a href="pedido_nuevo.php" class="btn btn-success">🛒 Nuevo pedido</a>
    </div>

    <?php if (empty($pedidos)): ?>
        <div class="alert alert-info">
            Aún no tienes pedidos.
            <a href="pedido_nuevo.php" class="alert-link">¡Haz tu primer pedido!</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead class="table-dark">
                    <tr><th>#</th><th>Productos</th><th>Total</th><th>Estado</th><th>Fecha</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidos as $p): ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td class="small text-muted" style="max-width:250px">
                            <?= htmlspecialchars($p['productos_nombres'] ?? '—') ?>
                        </td>
                        <td class="fw-bold">$<?= number_format((float)$p['total'], 2) ?></td>
                        <td><?= estadoBadge($p['estado']) ?></td>
                        <td class="text-muted small"><?= substr($p['creado_en'], 0, 16) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
