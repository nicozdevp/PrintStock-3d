<?php
require_once __DIR__ . '/../config/app.php';

function requireAuth(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['usuario'])) {
        header('Location: ' . BASE_URL . '/pages/login.php');
        exit();
    }
}

function requireRole(string ...$roles): void {
    requireAuth();
    if (!in_array($_SESSION['rol'] ?? '', $roles, true)) {
        http_response_code(403);
        include __DIR__ . '/header.php';
        echo '<div class="container mt-5"><div class="alert alert-danger"><strong>Acceso denegado.</strong> No tienes permiso para ver esta sección.</div></div>';
        include __DIR__ . '/footer.php';
        exit();
    }
}

function hasRole(string ...$roles): bool {
    return isset($_SESSION['rol']) && in_array($_SESSION['rol'], $roles, true);
}

function isAdmin(): bool {
    return hasRole('admin');
}

function csrfToken(): string {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrf(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        die('Token CSRF inválido. <a href="javascript:history.back()">Volver</a>');
    }
}

function estadoBadge(string $estado): string {
    $colores = [
        'pendiente'  => 'warning text-dark',
        'en_proceso' => 'info text-dark',
        'completado' => 'success',
        'cancelado'  => 'danger',
    ];
    $color = $colores[$estado] ?? 'secondary';
    return '<span class="badge bg-' . $color . '">' . htmlspecialchars($estado) . '</span>';
}

function rolBadge(string $rol): string {
    $colores = [
        'admin'    => 'danger',
        'vendedor' => 'primary',
        'operario' => 'warning text-dark',
        'cliente'  => 'success',
    ];
    $color = $colores[$rol] ?? 'secondary';
    return '<span class="badge bg-' . $color . '">' . htmlspecialchars($rol) . '</span>';
}
