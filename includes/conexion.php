<?php
// Datos de la base de datos
$host = 'localhost';        // Cambiar si es diferente
$user = 'root';             // Cambiar según tus credenciales
$password = '';             // Cambiar según tus credenciales
$database = 'data_asociaciones';  // Nombre de la base de datos

// Crear la conexión utilizando el estilo orientado a objetos
$conn = new mysqli($host, $user, $password, $database);

// Comprobar si la conexión tiene errores
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Establecer el conjunto de caracteres para evitar problemas con caracteres especiales
$conn->set_charset('utf8');
?>
