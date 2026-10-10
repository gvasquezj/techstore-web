<?php
// Parámetros de conexión al servidor local de XAMPP
$servidor   = "localhost";
$usuario    = "root";
$clave      = "";
$basedatos  = "TIENDA";

// Creación de la conexión con MySQL mediante MySQLi orientado a objetos
$conexion = new mysqli($servidor, $usuario, $clave, $basedatos);

// Verificación y manejo de errores de conexión
if ($conexion->connect_error) {
    die("Fallo de conexión: " . $conexion->connect_error);
}

// Configurar codificación en español para tildes y letra ñ
$conexion->set_charset("utf8mb4");
?>
