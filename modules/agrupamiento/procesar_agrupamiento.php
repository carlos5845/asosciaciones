<?php
include '../../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cod_etiqueta = trim($_POST['cod_etiqueta']);
    $agrupamientocol = trim($_POST['agrupamientocol']);

    // Validar si los campos están vacíos
    if (empty($cod_etiqueta) || empty($agrupamientocol)) {
        header('Location: registrar_agrupamiento.php?error=El campo no puede estar vacío');
        exit();
    }

    try {
        // Consulta para insertar los datos
        $query = "INSERT INTO agrupamiento (cod_etiqueta, agrupamientocol) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        
        if ($stmt === false) {
            throw new Exception("Error en la preparación de la consulta: " . $conn->error);
        }

        // Asociar los parámetros con la consulta
        $stmt->bind_param("ss", $cod_etiqueta, $agrupamientocol);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header('Location: registrar_agrupamiento.php?success=1');
            exit();
        }
    } catch (mysqli_sql_exception $e) {
        // Si el error es por clave duplicada (código 1062 en MySQL)
        if ($e->getCode() == 1062) {
            $errorMessage = "El código '$cod_etiqueta' ya está registrado.";
        } else {
            $errorMessage = "Error en la base de datos: " . $e->getMessage();
        }

        header('Location: registrar_agrupamiento.php?error=' . urlencode($errorMessage));
        exit();
    }

    // Cerrar la conexión en caso de error
    $stmt->close();
    $conn->close();
}
?>
