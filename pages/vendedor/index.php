<?php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('vendedor', 'admin');
require_once __DIR__ . '/../../config/database.php';

$vid = (int) $_SESSION['usuario_id'];

$totalPedidos    = (int) $conexion->prepare("SELECT COUNT(*) FROM pedidos WHERE vendedor_id = ?")->execute([$vid]) ? $conexion->query("SELECT COUNT(*) FROM pedidos WHERE vendedor_id = $vid")->fetchColumn() : 0;
$pendientes      = (int) $conexion->query("SELECT COUNT(*) FROM pedidos WHERE vendedor_id = $vid AND estado = 'pendiente'")->fetchColumn();
$completados     = (int) $conexion->query("SELECT COUNT(*) FROM pedidos WHERE vendedor_id = $vid AND estado = 'completado'")->fetchColumn();
$totalVentas     = (float) $conexion->query("SELECT COALESCE(SUM(total),0) FROM pedidos WHERE vendedor_id = $vid AND estado != 'cancelado'")->fetchColumn();

$ultimosPedidos = $conexion->query("
    SELECT p.*, c.nombre AS cliente_nombre
    FROM pedidos p
    JOIN usuarios c ON p.cliente_id = c.id
    WHERE p.vendedor_id = $vid
    ORDER BY p.creado_en DESC LIMIT 6
")->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="container mt-5">
    <h2 class="mb-4">Panel de Vendedor — <?= htmlspecialchars($_SESSION['usuario']) ?></h2>

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
            <div class="card text-center p-3 border-success">
                <div class="fs-1 fw-bold text-success"><?= $completados ?></div>
                <div class="text-muted">Completados</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3 border-info">
                <div class="fs-2 fw-bold text-info">$<?= number_format($totalVentas, 0) ?></div>
                <div class="text-muted">Facturado</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center fw-bold">
                    Mis últimos pedidos
                    <a href="pedidos.php" class="btn btn-outline-primary btn-sm">Ver todos</a>
                </div>
                <?php if (empty($ultimosPedidos)): ?>
                    <div class="card-body text-muted">Aún no has creado pedidos.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light"><tr><th>#</th><th>Cliente</th><th>Total</th><th>Estado</th></tr></thead>
                            <tbody>
                                <?php foreach ($ultimosPedidos as $p): ?>
                                <tr>
                                    <td><?= $p['id'] ?></td>
                                    <td><?= htmlspecialchars($p['cliente_nombre']) ?></td>
                                    <td>$<?= number_format((float)$p['total'], 2) ?></td>
                                    <td><?= estadoBadge($p['estado']) ?></td>
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
                <div class="card-header fw-bold">Acciones</div>
                <div class="card-body d-grid gap-2">
                    <a href="pedido_nuevo.php"                           class="btn btn-success">+ Nuevo pedido</a>
                    <a href="pedidos.php"                                class="btn btn-outline-primary">Ver todos los pedidos</a>
                    <a href="<?= BASE_URL ?>/pages/cliente/catalogo.php" class="btn btn-outline-secondary">Ver catálogo</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
