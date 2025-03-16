<?php
include '../../includes/conexion.php';

// Verificar si se recibió un ID válido
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_socio_asociacion = intval($_GET['id']);

    // Preparar la consulta para eliminar el registro
    $query = "DELETE FROM socio_asociacion WHERE idsocio_asociacion = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_socio_asociacion);

    if ($stmt->execute()) {
        // Redirigir con mensaje de éxito
        header("Location: listar_socio_asociacion.php?mensaje=Registro eliminado correctamente");
    } else {
        // Redirigir con mensaje de error
        header("Location: listar_socio_asociacion.php?error=No se pudo eliminar el registro");
    }

    // Cerrar la consulta
    $stmt->close();
} else {
    // Si no se recibe un ID válido, redirigir con error
    header("Location: listar_socio_asociacion.php?error=ID inválido");
}

// Cerrar conexión
$conn->close();
exit;
?>
