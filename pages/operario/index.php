<?php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('operario', 'admin');
require_once __DIR__ . '/../../config/database.php';

$pendientes  = (int) $conexion->query("SELECT COUNT(*) FROM pedidos WHERE estado = 'pendiente'")->fetchColumn();
$enProceso   = (int) $conexion->query("SELECT COUNT(*) FROM pedidos WHERE estado = 'en_proceso'")->fetchColumn();
$bajoStock   = $conexion->query("SELECT * FROM productos WHERE stock <= 5 ORDER BY stock ASC")->fetchAll(PDO::FETCH_ASSOC);
$completadosHoy = (int) $conexion->query("SELECT COUNT(*) FROM pedidos WHERE estado = 'completado' AND DATE(creado_en) = CURDATE()")->fetchColumn();
?>
<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="container mt-5">
    <h2 class="mb-4">Panel de Operario — <?= htmlspecialchars($_SESSION['usuario']) ?></h2>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card text-center p-3 border-warning">
                <div class="fs-1 fw-bold text-warning"><?= $pendientes ?></div>
                <div class="text-muted">Pendientes</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3 border-info">
                <div class="fs-1 fw-bold text-info"><?= $enProceso ?></div>
                <div class="text-muted">En proceso</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3 border-danger">
                <div class="fs-1 fw-bold text-danger"><?= count($bajoStock) ?></div>
                <div class="text-muted">Stock bajo (≤5)</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3 border-success">
                <div class="fs-1 fw-bold text-success"><?= $completadosHoy ?></div>
                <div class="text-muted">Completados hoy</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mb-4">
            <?php if (!empty($bajoStock)): ?>
            <div class="card border-danger mb-4">
                <div class="card-header text-white bg-danger fw-bold">⚠️ Productos con stock bajo o agotado</div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr><th>Producto</th><th>Categoría</th><th>Stock</th><th></th></tr></thead>
                        <tbody>
                            <?php foreach ($bajoStock as $p): ?>
                            <tr>
                                <td><?= htmlspecialchars($p['nombre']) ?></td>
                                <td class="text-muted small"><?= htmlspecialchars($p['categoria'] ?? '—') ?></td>
                                <td>
                                    <span class="badge bg-<?= (int)$p['stock'] === 0 ? 'danger' : 'warning text-dark' ?>">
                                        <?= (int)$p['stock'] ?>
                                    </span>
                                </td>
                                <td><a href="stock.php" class="btn btn-sm btn-outline-warning">Actualizar</a></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header fw-bold">Acciones</div>
                <div class="card-body d-grid gap-2">
                    <a href="produccion.php" class="btn btn-warning">Ver cola de producción</a>
                    <a href="stock.php"      class="btn btn-outline-info">Actualizar stock</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
