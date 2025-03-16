<?php
include '../../includes/conexion.php';

// Verificar si se recibió el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $grupo_id = $_POST['grupo_id'];
    $archivo_padron = $_FILES['archivo_vigencia'];

    // Verificar si el archivo fue cargado correctamente
    if ($archivo_padron['error'] == 0) {
        $archivo_nombre = $archivo_padron['name'];
        $archivo_tmp = $archivo_padron['tmp_name'];
        $ruta_destino = "../../uploads/padron/" . $archivo_nombre;

        // Mover el archivo a la carpeta de destino
        if (move_uploaded_file($archivo_tmp, $ruta_destino)) {
            // Preparar la consulta SQL
            $query = "INSERT INTO `padron_socios` (archivo_padron, grupo_idgrupo) 
                      VALUES ('$archivo_nombre', '$grupo_id')";

            // Ejecutar la consulta
            if ($conn->query($query)) {
                header("Location: registrar_padron.php?success=1");
                exit;
            } else {
                // Error al guardar en la base de datos
                header("Location: registrar_padron.php?error=" . $conn->error);
                exit;
            }
        } else {
            // Error al mover el archivo
            header("Location: registrar_padron.php?error=Error al subir el archivo.");
            exit;
        }
    } else {
        // Error con el archivo
        header("Location: registrar_padron.php?error=Archivo no válido.");
        exit;
    }
}

$conn->close();
?>
