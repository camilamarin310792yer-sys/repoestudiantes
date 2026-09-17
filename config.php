<?php
// Configuración de conexión MySQL - AlwaysData
$host = "mysql-yerissas.alwaysdata.net";
$user = "yerissas";
$password = "25455120";
$dbname = "yerissas_alumnos";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conexion = new mysqli($host, $user, $password, $dbname);
    $conexion->set_charset("utf8mb4");

    // Crea la tabla automáticamente si no existe.
    $conexion->query("
        CREATE TABLE IF NOT EXISTS alumnos (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(120) NOT NULL,
            identificacion VARCHAR(50) NOT NULL UNIQUE,
            telefono VARCHAR(30) NOT NULL,
            creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
} catch (Exception $e) {
    die("Error de conexión o creación de tabla: " . htmlspecialchars($e->getMessage()));
}
?>
