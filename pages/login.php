<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf();

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conexion->prepare('SELECT * FROM usuarios WHERE email = ?');
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($password, $usuario['password'])) {
        session_regenerate_id(true);
        $_SESSION['usuario']    = $usuario['nombre'];
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['rol']        = $usuario['rol'];
        header('Location: dashboard.php');
        exit();
    } else {
        $error = 'Credenciales incorrectas.';
    }
}
?>
<?php include '../includes/header.php'; ?>

<div class="container mt-5" style="max-width:420px">
    <h2 class="mb-4">Iniciar sesión</h2>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <div class="mb-3">
            <label class="form-label">Correo electrónico</label>
            <input type="email" name="email" class="form-control" required autofocus>
        </div>
        <div class="mb-3">
            <label class="form-label">Contraseña</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Iniciar sesión</button>
    </form>
    <p class="mt-3 text-center">¿No tienes cuenta? <a href="register.php">Regístrate</a></p>
</div>

<?php include '../includes/footer.php'; ?>
