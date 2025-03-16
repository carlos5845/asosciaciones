<?php
include '../../includes/conexion.php';

$partida_registral = $_POST['partida_registral'];
$grupo_id = $_POST['grupo_id'];
$archivo_vigencia = $_FILES['archivo_vigencia'];

$archivo_nombre = $archivo_vigencia['name'];
$archivo_tmp = $archivo_vigencia['tmp_name'];
$ruta_destino = "../../uploads/vigencia/" . $archivo_nombre;

if (move_uploaded_file($archivo_tmp, $ruta_destino)) {
    $query = "INSERT INTO `vigencia_poder` (partida_registral, archivo_vigencia, grupo_idgrupo)
              VALUES ('$partida_registral', '$archivo_nombre', $grupo_id)";
    if ($conn->query($query)) {
        echo "Registro guardado con éxito.";
    } else {
        echo "Error al guardar: " . $conn->error;
    }
} else {
    echo "Error al subir el archivo.";
}

$conn->close();
?>
