<?php
require_once(__DIR__ . "/../../includes/conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['idsocio'];
    $nombre = trim($_POST['nombre']);
    $apellido_pat = trim($_POST['apellido_pat']);
    $apellido_mat = trim($_POST['apellido_mat']);
    $dni = trim($_POST['dni']);

    $stmt = $conn->prepare("UPDATE socio SET nombre = ?, apellido_pat = ?, apellido_mat = ?, dni = ? WHERE idsocio = ?");
    $stmt->bind_param("ssssi", $nombre, $apellido_pat, $apellido_mat, $dni, $id);

    if ($stmt->execute()) {
        echo "Socio actualizado correctamente.";
    } else {
        echo "Error al actualizar.";
    }

    $stmt->close();
    $conn->close();
}
?>
