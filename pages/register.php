<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)";
    $stmt = $conexion->prepare($sql);

    if ($stmt->execute([$nombre, $email, $password])) {
        header("Location: login.php");
        exit();
    } else {
        echo "Error al registrar usuario";
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <h2>Registro</h2>
    <form method="POST">
        <input type="text" name="nombre" placeholder="Nombre" class="form-control mb-3" required>
        <input type="email" name="email" placeholder="Correo" class="form-control mb-3" required>
        <input type="password" name="password" placeholder="Contraseña" class="form-control mb-3" required>
        <button type="submit" class="btn btn-success">Registrarse</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>