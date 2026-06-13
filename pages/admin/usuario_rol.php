<?php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../../config/database.php';

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: usuarios.php');
    exit();
}

$stmt = $conexion->prepare('SELECT * FROM usuarios WHERE id = ?');
$stmt->execute([$id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    header('Location: usuarios.php');
    exit();
}

if ((int)$id === (int)$_SESSION['usuario_id']) {
    header('Location: usuarios.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf();
    $nuevoRol = $_POST['rol'] ?? '';
    $rolesValidos = ['admin', 'vendedor', 'operario', 'cliente'];
    if (in_array($nuevoRol, $rolesValidos, true)) {
        $conexion->prepare('UPDATE usuarios SET rol = ? WHERE id = ?')->execute([$nuevoRol, $id]);
        header('Location: usuarios.php');
        exit();
    } else {
        $error = 'Rol no válido.';
    }
}
?>
<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="container mt-5" style="max-width:480px">
    <h2 class="mb-4">Cambiar rol de usuario</h2>

    <div class="card mb-4">
        <div class="card-body">
            <p class="mb-1"><strong>Nombre:</strong> <?= htmlspecialchars($usuario['nombre']) ?></p>
            <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($usuario['email']) ?></p>
            <p class="mb-0"><strong>Rol actual:</strong> <?= rolBadge($usuario['rol']) ?></p>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <div class="mb-4">
            <label class="form-label fw-bold">Nuevo rol</label>
            <div class="d-grid gap-2">
                <?php foreach (['admin', 'vendedor', 'operario', 'cliente'] as $r): ?>
                <div class="form-check border rounded p-3 <?= $usuario['rol'] === $r ? 'border-primary bg-light' : '' ?>">
                    <input class="form-check-input" type="radio" name="rol" id="rol_<?= $r ?>"
                           value="<?= $r ?>" <?= $usuario['rol'] === $r ? 'checked' : '' ?>>
                    <label class="form-check-label w-100" for="rol_<?= $r ?>">
                        <?= rolBadge($r) ?>
                        <?php
                        $desc = [
                            'admin'    => '— Acceso total: usuarios, productos, pedidos, producción.',
                            'vendedor' => '— Crea y gestiona pedidos, ve el catálogo y clientes.',
                            'operario' => '— Gestiona producción y actualiza stock de productos.',
                            'cliente'  => '— Navega el catálogo y realiza sus propios pedidos.',
                        ];
                        echo '<span class="text-muted small ms-1">' . $desc[$r] . '</span>';
                        ?>
                    </label>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-warning">Guardar cambio</button>
            <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
