<?php
include '../../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoger los datos del formulario
    $etiqueta_grupo = trim($_POST['etiqueta_grupo']);
    $nombre_grupo = trim($_POST['nombre_grupo']);
    $ubicacion = trim($_POST['ubicacion']);
    $agrupamiento_id = $_POST['agrupamiento_id'];
    $categoria_id = $_POST['categoria_id'];
    $estado = $_POST['estado'];

    // Validación básica
    if (empty($etiqueta_grupo) || empty($nombre_grupo) || empty($ubicacion) || empty($agrupamiento_id) || empty($categoria_id) || empty($estado)) {
        header('Location: registrar_grupo.php?error=' . urlencode('Todos los campos son obligatorios.'));
        exit();
    }

    try {
        // Preparar la consulta para evitar inyección SQL
        $query = "INSERT INTO grupo (etiqueta_grupo, nombre_grupo, ubicacion, agrupamiento_idagrupamiento, categoria_idcategoria, estado) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssiss", $etiqueta_grupo, $nombre_grupo, $ubicacion, $agrupamiento_id, $categoria_id, $estado);
        $stmt->execute();
        
        $stmt->close();
        $conn->close();

        // Redirigir con mensaje de éxito
        header('Location: registrar_grupo.php?success=1');
        exit();
    } catch (mysqli_sql_exception $e) {
        $errorMessage = "Ocurrió un error.";

        // Si el error es por clave duplicada (código 1062 en MySQL)
        if ($e->getCode() == 1062) {
            $errorMessage = "El código '$etiqueta_grupo' ya está registrado.";
        } else {
            $errorMessage = "Error en la base de datos: " . $e->getMessage();
        }

        header('Location: registrar_grupo.php?error=' . urlencode($errorMessage));
        exit();
    }
}
?>