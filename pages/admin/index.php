<?php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../../config/database.php';

$totalUsuarios  = (int) $conexion->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
$totalProductos = (int) $conexion->query("SELECT COUNT(*) FROM productos")->fetchColumn();
$totalPedidos   = (int) $conexion->query("SELECT COUNT(*) FROM pedidos")->fetchColumn();

$roleRows  = $conexion->query("SELECT rol, COUNT(*) AS total FROM usuarios GROUP BY rol")->fetchAll(PDO::FETCH_ASSOC);
$estadoRows = $conexion->query("SELECT estado, COUNT(*) AS total FROM pedidos GROUP BY estado")->fetchAll(PDO::FETCH_ASSOC);
$roleStats  = array_column($roleRows, 'total', 'rol');
$estadoStats = array_column($estadoRows, 'total', 'estado');

$ultimosPedidos = $conexion->query("
    SELECT p.*, c.nombre AS cliente_nombre
    FROM pedidos p
    JOIN usuarios c ON p.cliente_id = c.id
    ORDER BY p.creado_en DESC LIMIT 8
")->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="container mt-5">
    <h2 class="mb-4">Panel de Administrador</h2>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card text-white bg-primary text-center p-3">
                <div class="fs-1 fw-bold"><?= $totalUsuarios ?></div>
                <div>Usuarios</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-white bg-success text-center p-3">
                <div class="fs-1 fw-bold"><?= $totalProductos ?></div>
                <div>Productos</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-white bg-warning text-dark text-center p-3">
                <div class="fs-1 fw-bold"><?= $estadoStats['pendiente'] ?? 0 ?></div>
                <div>Pendientes</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-white bg-info text-dark text-center p-3">
                <div class="fs-1 fw-bold"><?= $totalPedidos ?></div>
                <div>Pedidos totales</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header fw-bold">Usuarios por rol</div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php foreach (['admin', 'vendedor', 'operario', 'cliente'] as $r): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?= rolBadge($r) ?>
                            <span class="fw-bold"><?= $roleStats[$r] ?? 0 ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="card-footer">
                    <a href="<?= BASE_URL ?>/pages/admin/usuarios.php" class="btn btn-primary btn-sm w-100">Gestionar usuarios</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header fw-bold">Pedidos por estado</div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php foreach (['pendiente', 'en_proceso', 'completado', 'cancelado'] as $e): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?= estadoBadge($e) ?>
                            <span class="fw-bold"><?= $estadoStats[$e] ?? 0 ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="card-footer">
                    <a href="<?= BASE_URL ?>/pages/vendedor/pedidos.php" class="btn btn-outline-primary btn-sm w-100">Ver todos los pedidos</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header fw-bold">Accesos rápidos</div>
                <div class="card-body d-grid gap-2">
                    <a href="<?= BASE_URL ?>/pages/producto_nuevo.php"     class="btn btn-success">+ Nuevo producto</a>
                    <a href="<?= BASE_URL ?>/pages/productos.php"          class="btn btn-outline-success">Gestionar productos</a>
                    <a href="<?= BASE_URL ?>/pages/admin/usuarios.php"     class="btn btn-outline-primary">Gestionar usuarios</a>
                    <a href="<?= BASE_URL ?>/pages/vendedor/pedidos.php"   class="btn btn-outline-secondary">Ver pedidos</a>
                    <a href="<?= BASE_URL ?>/pages/operario/produccion.php" class="btn btn-outline-warning">Producción</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header fw-bold">Últimos pedidos</div>
        <?php if (empty($ultimosPedidos)): ?>
            <div class="card-body text-muted">No hay pedidos registrados aún.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark"><tr><th>#</th><th>Cliente</th><th>Total</th><th>Estado</th><th>Fecha</th></tr></thead>
                    <tbody>
                        <?php foreach ($ultimosPedidos as $p): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td><?= htmlspecialchars($p['cliente_nombre']) ?></td>
                            <td>$<?= number_format((float)$p['total'], 2) ?></td>
                            <td><?= estadoBadge($p['estado']) ?></td>
                            <td class="text-muted small"><?= $p['creado_en'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
