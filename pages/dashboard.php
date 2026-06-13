<?php
require_once '../includes/auth.php';
requireAuth();

$rutas = [
    'admin'    => BASE_URL . '/pages/admin/index.php',
    'vendedor' => BASE_URL . '/pages/vendedor/index.php',
    'operario' => BASE_URL . '/pages/operario/index.php',
    'cliente'  => BASE_URL . '/pages/cliente/index.php',
];

$rol = $_SESSION['rol'] ?? 'cliente';
header('Location: ' . ($rutas[$rol] ?? $rutas['cliente']));
exit();
