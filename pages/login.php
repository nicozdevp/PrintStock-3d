<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($password, $usuario["password"])) {
        $_SESSION["usuario"] = $usuario["nombre"];
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Credenciales incorrectas";
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <h2>Login</h2>
    <form method="POST">
        <input type="email" name="email" placeholder="Correo" class="form-control mb-3" required>
        <input type="password" name="password" placeholder="Contraseña" class="form-control mb-3" required>
        <button type="submit" class="btn btn-primary">Iniciar sesión</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>