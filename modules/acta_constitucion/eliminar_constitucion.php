<?php
include '../../includes/conexion.php';
session_start(); // Asegúrate de que esté al inicio para usar sesiones

// Verificar si el ID del acta está presente en el parámetro GET
if (isset($_GET['idacta']) && is_numeric($_GET['idacta'])) {
    $idacta = $_GET['idacta'];

    // Obtener los detalles del acta, para también eliminar el archivo de la carpeta
    $query = "SELECT archivo_acta FROM acta_constitucion WHERE idacta_constitucion = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $idacta);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $archivo_acta = $row['archivo_acta'];

        // Eliminar el archivo de la carpeta de uploads
        $archivo_ruta = "../../uploads/constitucion/" . $archivo_acta;
        if (file_exists($archivo_ruta)) {
            unlink($archivo_ruta); // Eliminar el archivo
        }

        // Eliminar el registro de la base de datos
        $delete_query = "DELETE FROM acta_constitucion WHERE idacta_constitucion = ?";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bind_param("i", $idacta);

        if ($delete_stmt->execute()) {
            // Redirigir con mensaje de éxito personalizado
            $_SESSION['mensaje'] = "El acta de constitución ha sido eliminada correctamente.";
            $_SESSION['tipo_mensaje'] = "success";
        } else {
            // Redirigir con mensaje de error
            $_SESSION['mensaje'] = "No se pudo eliminar el registro del acta.";
            $_SESSION['tipo_mensaje'] = "danger";
        }
    } else {
        // Redirigir con mensaje si no se encuentra el acta
        $_SESSION['mensaje'] = "Acta no encontrada.";
        $_SESSION['tipo_mensaje'] = "danger";
    }
} else {
    // Redirigir con mensaje de error si no se pasa un ID válido
    $_SESSION['mensaje'] = "ID inválido.";
    $_SESSION['tipo_mensaje'] = "danger";
}

// Redirigir de vuelta al listado de actas
header('Location: listar_constitucion.php');
exit();  // Asegurarse de que no se ejecute más código
$conn->close();
