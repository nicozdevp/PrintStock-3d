<?php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('cliente');
require_once __DIR__ . '/../../config/database.php';

$cid = (int) $_SESSION['usuario_id'];

$totalPedidos = (int) $conexion->query("SELECT COUNT(*) FROM pedidos WHERE cliente_id = $cid")->fetchColumn();
$pendientes   = (int) $conexion->query("SELECT COUNT(*) FROM pedidos WHERE cliente_id = $cid AND estado = 'pendiente'")->fetchColumn();
$enProceso    = (int) $conexion->query("SELECT COUNT(*) FROM pedidos WHERE cliente_id = $cid AND estado = 'en_proceso'")->fetchColumn();
$totalGastado = (float) $conexion->query("SELECT COALESCE(SUM(total),0) FROM pedidos WHERE cliente_id = $cid AND estado != 'cancelado'")->fetchColumn();

$ultimosPedidos = $conexion->query("
    SELECT * FROM pedidos
    WHERE cliente_id = $cid
    ORDER BY creado_en DESC LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="container mt-5">
    <h2 class="mb-1">Hola, <?= htmlspecialchars($_SESSION['usuario']) ?> 👋</h2>
    <p class="text-muted mb-4">Bienvenido a tu panel de cliente.</p>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card text-center p-3 border-primary">
                <div class="fs-1 fw-bold text-primary"><?= $totalPedidos ?></div>
                <div class="text-muted">Mis pedidos</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3 border-warning">
                <div class="fs-1 fw-bold text-warning"><?= $pendientes ?></div>
                <div class="text-muted">Pendientes</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3 border-info">
                <div class="fs-1 fw-bold text-info"><?= $enProceso ?></div>
                <div class="text-muted">En producción</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3 border-success">
                <div class="fs-2 fw-bold text-success">$<?= number_format($totalGastado, 0) ?></div>
                <div class="text-muted">Total comprado</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header fw-bold d-flex justify-content-between">
                    Mis últimos pedidos
                    <a href="mis_pedidos.php" class="btn btn-outline-primary btn-sm">Ver todos</a>
                </div>
                <?php if (empty($ultimosPedidos)): ?>
                    <div class="card-body text-muted">Aún no has realizado pedidos.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light"><tr><th>#</th><th>Total</th><th>Estado</th><th>Fecha</th></tr></thead>
                            <tbody>
                                <?php foreach ($ultimosPedidos as $p): ?>
                                <tr>
                                    <td><?= $p['id'] ?></td>
                                    <td>$<?= number_format((float)$p['total'], 2) ?></td>
                                    <td><?= estadoBadge($p['estado']) ?></td>
                                    <td class="text-muted small"><?= substr($p['creado_en'], 0, 16) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header fw-bold">¿Qué deseas hacer?</div>
                <div class="card-body d-grid gap-2">
                    <a href="pedido_nuevo.php" class="btn btn-success">🛒 Hacer un pedido</a>
                    <a href="catalogo.php"     class="btn btn-outline-primary">Ver catálogo</a>
                    <a href="mis_pedidos.php"  class="btn btn-outline-secondary">Mis pedidos</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
