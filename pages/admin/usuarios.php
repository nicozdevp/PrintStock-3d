<?php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../../config/database.php';

$usuarios = $conexion->query("SELECT * FROM usuarios ORDER BY rol, nombre")->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gestión de Usuarios</h2>
        <a href="<?= BASE_URL ?>/pages/admin/index.php" class="btn btn-outline-secondary btn-sm">← Volver al panel</a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Registrado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><?= htmlspecialchars($u['nombre']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= rolBadge($u['rol']) ?></td>
                    <td class="text-muted small"><?= $u['creado_en'] ?></td>
                    <td>
                        <?php if ((int)$u['id'] !== (int)$_SESSION['usuario_id']): ?>
                            <a href="usuario_rol.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-warning">Cambiar rol</a>
                        <?php else: ?>
                            <span class="text-muted small">(tú)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
