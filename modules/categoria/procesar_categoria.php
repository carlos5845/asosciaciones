<?php
include '../../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoger los datos del formulario
    $tipo = trim($_POST['tipo']);

    // Verificar que el campo 'tipo' no esté vacío
    if (empty($tipo)) {
        header('Location: registrar_categoria.php?error=El tipo de categoría es obligatorio');
        exit();
    }

    // Preparar la consulta para evitar inyección SQL
    $query = "INSERT INTO categoria (tipo) VALUES (?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $tipo);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header('Location: registrar_categoria.php?success=1');
        exit();
    } else {
        $stmt->close();
        $conn->close();
        header('Location: registrar_categoria.php?error=' . urlencode($conn->error));
        exit();
    }
}
?>
