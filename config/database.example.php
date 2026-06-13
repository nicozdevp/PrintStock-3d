<?php
// Copia este archivo como database.php y ajusta las credenciales
require_once __DIR__ . '/app.php';

$host   = 'localhost';
$dbname = 'mi_web';       // nombre de tu base de datos
$user   = 'root';         // usuario de MySQL
$pass   = '';             // contraseña de MySQL

try {
    $conexion = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Error de conexión: ' . $e->getMessage());
}
