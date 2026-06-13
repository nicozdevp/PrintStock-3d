<?php
if (!defined('BASE_URL')) {
    // Cambiar a '/nombre-carpeta' si el proyecto está en un subdirectorio del servidor web
    define('BASE_URL', '');
}

define('DEBUG', true);

if (DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}
