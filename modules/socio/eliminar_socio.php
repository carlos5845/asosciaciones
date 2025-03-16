<?php
require_once(__DIR__ . "/../../includes/conexion.php");

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    $stmt = $conn->prepare("DELETE FROM socio WHERE idsocio = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "eliminado";
    } else {
        echo "error";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "error";
}
?>
