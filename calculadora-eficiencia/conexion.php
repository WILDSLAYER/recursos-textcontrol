<?php
// Configuración de la base de datos
$host = 'localhost'; // Cambia esto si tu servidor no es localhost
$dbname = 'gest-eficiencia'; // Nombre de la base de datos
$username = 'root'; // Usuario de la base de datos
$password = ''; // Contraseña del usuario (por defecto en XAMPP es vacío)

try {
    // Crear una nueva conexión PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Configurar el modo de error de PDO para excepciones
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Manejar errores de conexión
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
?>