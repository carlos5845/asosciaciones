<?php
include '../../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!$conn) {
        die('Error de conexión a la base de datos: ' . mysqli_connect_error());
    }

    $fecha_fundacion = $_POST['fecha_fundacion'];
    $grupo_id = $_POST['grupo_id'];
    $archivo_acta = $_FILES['archivo_acta'];

    if (empty($fecha_fundacion)) {
        header('Location: registrar_constitucion.php?error=La fecha de fundación es obligatoria');
        exit;
    }

    if ($archivo_acta['error'] !== 0) {
        header('Location: registrar_constitucion.php?error=Error al subir el archivo (Código: ' . $archivo_acta['error'] . ')');
        exit;
    }

    if ($archivo_acta['type'] !== 'application/pdf') {
        header('Location: registrar_constitucion.php?error=El archivo debe ser un PDF');
        exit;
    }

    $maxSize = 2 * 1024 * 1024; // 2MB
    if ($archivo_acta['size'] > $maxSize) {
        header('Location: registrar_constitucion.php?error=El archivo no debe superar los 2MB');
        exit;
    }

    // 🔍 Verificar si el grupo ya tiene una acta registrada
    $checkGrupo = $conn->prepare("SELECT grupo_idgrupo FROM acta_constitucion WHERE grupo_idgrupo = ?");
    $checkGrupo->bind_param("i", $grupo_id);
    $checkGrupo->execute();
    $checkGrupo->store_result();

    if ($checkGrupo->num_rows > 0) {
        header('Location: registrar_constitucion.php?error=Este grupo ya tiene un acta registrada');
        exit;
    }
    $checkGrupo->close();

    // Procesar la subida del archivo
    $archivo_nombre = time() . "_" . basename($archivo_acta['name']);
    $ruta_destino = "../../uploads/constitucion/" . $archivo_nombre;

    if (!file_exists("../../uploads/constitucion/")) {
        mkdir("../../uploads/constitucion/", 0777, true);
    }

    if (move_uploaded_file($archivo_acta['tmp_name'], $ruta_destino)) {
        $query = "INSERT INTO acta_constitucion (fecha_fundacion, archivo_acta, grupo_idgrupo) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param("ssi", $fecha_fundacion, $archivo_nombre, $grupo_id);
            if ($stmt->execute()) {
                header('Location: registrar_constitucion.php?success=1');
            } else {
                header('Location: registrar_constitucion.php?error=Error al registrar el acta: ' . urlencode($stmt->error));
            }
            $stmt->close();
        } else {
            header('Location: registrar_constitucion.php?error=Error en la consulta SQL');
        }
    } else {
        header('Location: registrar_constitucion.php?error=Error al mover el archivo');
    }
}

$conn->close();
?>
