<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/auth.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$loggedIn = isset($_SESSION['usuario']);
$rol      = $_SESSION['rol'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PrintStock 3D</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>/index.php">🖨️ PrintStock 3D</a>
        <div class="ms-auto d-flex gap-2 align-items-center">
            <?php if ($loggedIn): ?>

                <?php if ($rol === 'admin'): ?>
                    <a href="<?= BASE_URL ?>/pages/admin/index.php"      class="btn btn-outline-light btn-sm">Panel Admin</a>
                    <a href="<?= BASE_URL ?>/pages/admin/usuarios.php"   class="btn btn-outline-light btn-sm">Usuarios</a>
                    <a href="<?= BASE_URL ?>/pages/productos.php"        class="btn btn-outline-light btn-sm">Productos</a>
                    <a href="<?= BASE_URL ?>/pages/vendedor/pedidos.php" class="btn btn-outline-light btn-sm">Pedidos</a>

                <?php elseif ($rol === 'vendedor'): ?>
                    <a href="<?= BASE_URL ?>/pages/vendedor/index.php"       class="btn btn-outline-light btn-sm">Panel</a>
                    <a href="<?= BASE_URL ?>/pages/cliente/catalogo.php"     class="btn btn-outline-light btn-sm">Catálogo</a>
                    <a href="<?= BASE_URL ?>/pages/vendedor/pedidos.php"     class="btn btn-outline-light btn-sm">Pedidos</a>
                    <a href="<?= BASE_URL ?>/pages/vendedor/pedido_nuevo.php" class="btn btn-success btn-sm">+ Nuevo pedido</a>

                <?php elseif ($rol === 'operario'): ?>
                    <a href="<?= BASE_URL ?>/pages/operario/index.php"      class="btn btn-outline-light btn-sm">Panel</a>
                    <a href="<?= BASE_URL ?>/pages/operario/produccion.php" class="btn btn-outline-light btn-sm">Producción</a>
                    <a href="<?= BASE_URL ?>/pages/operario/stock.php"      class="btn btn-outline-light btn-sm">Stock</a>

                <?php elseif ($rol === 'cliente'): ?>
                    <a href="<?= BASE_URL ?>/pages/cliente/index.php"       class="btn btn-outline-light btn-sm">Panel</a>
                    <a href="<?= BASE_URL ?>/pages/cliente/catalogo.php"    class="btn btn-outline-light btn-sm">Catálogo</a>
                    <a href="<?= BASE_URL ?>/pages/cliente/mis_pedidos.php" class="btn btn-outline-light btn-sm">Mis pedidos</a>
                    <a href="<?= BASE_URL ?>/pages/cliente/pedido_nuevo.php" class="btn btn-success btn-sm">+ Pedir</a>
                <?php endif; ?>

                <span class="text-muted small d-none d-lg-inline">|</span>
                <span class="text-light small"><?= htmlspecialchars($_SESSION['usuario']) ?></span>
                <a href="<?= BASE_URL ?>/pages/logout.php" class="btn btn-danger btn-sm">Salir</a>

            <?php else: ?>
                <a href="<?= BASE_URL ?>/pages/login.php"    class="btn btn-outline-light btn-sm">Login</a>
                <a href="<?= BASE_URL ?>/pages/register.php" class="btn btn-warning btn-sm">Registro</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
