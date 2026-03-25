<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit();
}
?>

<?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION["usuario"]); ?> 👋</h2>
    <p>Has iniciado sesión correctamente.</p>
    <a href="logout.php" class="btn btn-danger mt-3">Cerrar sesión</a>
</div>

<?php include '../includes/footer.php'; ?>