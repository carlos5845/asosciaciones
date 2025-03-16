<?php
include '../../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoger los datos del formulario
    $num_resolucion = trim($_POST['num_resolucion']);
    $fecha_emision = $_POST['fecha_emision'];
    $grupo_id = $_POST['grupo_id'];
    $archivo_gdh = $_FILES['archivo_gdh'];

    // Validar que la fecha no sea futura
    if (!$fecha_emision || strtotime($fecha_emision) > time()) {
        header('Location: registrar_resolucion.php?error=Fecha de emisión inválida');
        exit();
    }

    // Verificar que se haya subido un archivo y que sea PDF
    if (isset($archivo_gdh) && $archivo_gdh['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $archivo_gdh['tmp_name'];
        $fileName = $archivo_gdh['name'];
        $fileSize = $archivo_gdh['size'];
        $fileType = $archivo_gdh['type'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExtensions = ['pdf'];
        if (!in_array($fileExt, $allowedExtensions)) {
            header('Location: registrar_resolucion.php?error=El archivo debe ser un PDF');
            exit();
        }

        // Evitar archivos duplicados renombrándolos con timestamp
        $nuevoNombre = time() . "_" . $fileName;
        $ruta_destino = "../../uploads/resolucion/" . $nuevoNombre;

        // Mover el archivo al directorio de destino
        if (move_uploaded_file($fileTmpPath, $ruta_destino)) {
            // Preparar la consulta con parámetros para evitar inyección SQL
            $query = "INSERT INTO resolucion_gdh (num_resolucion, fecha_emision, archivo_gdh, grupo_idgrupo) 
                      VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssi", $num_resolucion, $fecha_emision, $nuevoNombre, $grupo_id);

            if ($stmt->execute()) {
                $stmt->close();
                $conn->close();
                header('Location: registrar_resolucion.php?success=1');
                exit();
            } else {
                $stmt->close();
                $conn->close();
                header('Location: registrar_resolucion.php?error=' . urlencode($conn->error));
                exit();
            }
        } else {
            header('Location: registrar_resolucion.php?error=Error al subir el archivo');
            exit();
        }
    } else {
        header('Location: registrar_resolucion.php?error=Debe subir un archivo PDF');
        exit();
    }
}
?>
