<?php
include '../../includes/conexion.php';
session_start(); // Asegúrate de que esté al inicio para usar sesiones

// Verificar si el ID del agrupamiento está presente en el parámetro GET
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $idagrupamiento = $_GET['id'];

    // Eliminar el registro del agrupamiento de la base de datos
    $delete_query = "DELETE FROM agrupamiento WHERE idagrupamiento = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("i", $idagrupamiento);

    if ($delete_stmt->execute()) {
        // Redirigir con mensaje de éxito personalizado
        $_SESSION['mensaje'] = "El agrupamiento ha sido eliminado correctamente.";
        $_SESSION['tipo_mensaje'] = "success";
    } else {
        // Redirigir con mensaje de error
        $_SESSION['mensaje'] = "No se pudo eliminar el agrupamiento.";
        $_SESSION['tipo_mensaje'] = "danger";
    }
} else {
    // Redirigir con mensaje de error si no se pasa un ID válido
    $_SESSION['mensaje'] = "ID inválido.";
    $_SESSION['tipo_mensaje'] = "danger";
}

// Redirigir de vuelta al listado de agrupamientos
header('Location: listar_agrupamiento.php');
$conn->close();
?>
