<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf();

    $nombre   = trim($_POST['nombre'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);

    $check = $conexion->prepare('SELECT id FROM usuarios WHERE email = ?');
    $check->execute([$email]);
    if ($check->fetch()) {
        $error = 'Ya existe una cuenta con ese correo.';
    } else {
        $stmt = $conexion->prepare('INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)');
        if ($stmt->execute([$nombre, $email, $password])) {
            header('Location: login.php');
            exit();
        } else {
            $error = 'Error al registrar usuario.';
        }
    }
}
?>
<?php include '../includes/header.php'; ?>

<div class="container mt-5" style="max-width:420px">
    <h2 class="mb-4">Crear cuenta</h2>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Correo electrónico</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Contraseña</label>
            <input type="password" name="password" class="form-control" minlength="8" required>
        </div>
        <button type="submit" class="btn btn-success w-100">Registrarse</button>
    </form>
    <p class="mt-3 text-center">¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
</div>

<?php include '../includes/footer.php'; ?>
