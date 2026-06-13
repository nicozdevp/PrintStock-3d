<?php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('operario', 'admin');
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'estado') {
    validateCsrf();
    $id          = (int) ($_POST['id'] ?? 0);
    $nuevoEstado = $_POST['estado'] ?? '';
    $validos     = ['pendiente', 'en_proceso', 'completado', 'cancelado'];
    if ($id > 0 && in_array($nuevoEstado, $validos, true)) {
        $conexion->prepare('UPDATE pedidos SET estado = ? WHERE id = ?')->execute([$nuevoEstado, $id]);
    }
    header('Location: produccion.php');
    exit();
}

$pedidos = $conexion->query("
    SELECT p.*, c.nombre AS cliente_nombre
    FROM pedidos p
    JOIN usuarios c ON p.cliente_id = c.id
    WHERE p.estado != 'cancelado'
    ORDER BY FIELD(p.estado, 'en_proceso', 'pendiente', 'completado'), p.creado_en ASC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Cola de Producción</h2>
        <a href="index.php" class="btn btn-outline-secondary btn-sm">← Panel</a>
    </div>

    <?php if (empty($pedidos)): ?>
        <div class="alert alert-success">No hay pedidos activos en producción.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr><th>#</th><th>Cliente</th><th>Total</th><th>Estado actual</th><th>Fecha</th><th>Cambiar estado</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidos as $p): ?>
                    <tr class="<?= $p['estado'] === 'en_proceso' ? 'table-info' : ($p['estado'] === 'pendiente' ? 'table-warning' : '') ?>">
                        <td><?= $p['id'] ?></td>
                        <td><?= htmlspecialchars($p['cliente_nombre']) ?></td>
                        <td>$<?= number_format((float)$p['total'], 2) ?></td>
                        <td><?= estadoBadge($p['estado']) ?></td>
                        <td class="text-muted small"><?= substr($p['creado_en'], 0, 16) ?></td>
                        <td>
                            <?php if ($p['estado'] !== 'completado'): ?>
                            <form method="POST" class="d-flex gap-1">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                                <input type="hidden" name="action" value="estado">
                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                <?php if ($p['estado'] === 'pendiente'): ?>
                                    <button name="estado" value="en_proceso" class="btn btn-sm btn-info">▶ Iniciar</button>
                                <?php elseif ($p['estado'] === 'en_proceso'): ?>
                                    <button name="estado" value="completado" class="btn btn-sm btn-success">✓ Completar</button>
                                    <button name="estado" value="pendiente"  class="btn btn-sm btn-outline-warning">↩ Pausar</button>
                                <?php endif; ?>
                            </form>
                            <?php else: ?>
                                <span class="text-muted small">Finalizado</span>
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
