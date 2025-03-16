<?php
include '../../includes/header.php';  // Incluir la cabecera de la plantilla
include '../../includes/conexion.php';  // Conexión a la base de datos
session_start();

// Verificar si se pasa un ID por GET
if (isset($_GET['id'])) {
    $idcategoria = (int)$_GET['id'];

    // Preparar la consulta para eliminar la categoría
    $query = "DELETE FROM categoria WHERE idcategoria = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $idcategoria);

    if ($stmt->execute()) {
        // Mensaje de éxito con sesión
        $_SESSION['mensaje'] = "Categoría eliminada correctamente.";
        $_SESSION['tipo_mensaje'] = "success";
    } else {
        // Mensaje de error con sesión
        $_SESSION['mensaje'] = "Error al eliminar la categoría: " . $conn->error;
        $_SESSION['tipo_mensaje'] = "danger";
    }
} else {
    // Mensaje de error si no se proporciona un ID
    $_SESSION['mensaje'] = "ID de categoría no proporcionado.";
    $_SESSION['tipo_mensaje'] = "danger";
}

// Redirigir a la página de listado sin los parámetros en la URL
header('Location: listar_categoria.php');
$conn->close();
?>
