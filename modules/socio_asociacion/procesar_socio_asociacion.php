<?php
include '../../includes/conexion.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idsocio = $_POST['idsocio'] ?? '';
    $idasociacion = $_POST['idasociacion'] ?? '';
    $rubro = trim($_POST['rubro'] ?? '');
    $observacion = trim($_POST['observacion'] ?? '');
    $cod_puesto = trim($_POST['cod_puesto'] ?? '');

    // Validar que los campos requeridos no estén vacíos
    if (empty($idsocio) || empty($idasociacion) || empty($rubro)) {
        header("Location: registrar_socio_asociacion.php?mensaje_error=Todos los campos son requeridos.");
        exit();
    }

    // Verificar si el socio ya está registrado en la misma asociación
    $query_verificar = "SELECT COUNT(*) AS total FROM socio_asociacion WHERE socio_idsocio = ? AND grupo_idgrupo = ?";
    $stmt_verificar = $conn->prepare($query_verificar);
    $stmt_verificar->bind_param("ii", $idsocio, $idasociacion);
    $stmt_verificar->execute();
    $result_verificar = $stmt_verificar->get_result();
    $fila_verificar = $result_verificar->fetch_assoc();
    $stmt_verificar->close();

    if ($fila_verificar['total'] > 0) {
        header("Location: registrar_socio_asociacion.php?mensaje_error=El socio ya pertenece a esta asociación.");
        exit();
    }

    // Insertar en la base de datos si el socio no está registrado en la asociación
    $query = "INSERT INTO socio_asociacion (socio_idsocio, grupo_idgrupo, rubro, observacion, cod_puesto) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iisss", $idsocio, $idasociacion, $rubro, $observacion, $cod_puesto);

    if ($stmt->execute()) {
        // Redirigir sin el DNI para evitar errores
        header("Location: registrar_socio_asociacion.php?mensaje=El socio ha sido asociado con éxito.");
        exit();
    } else {
        header("Location: registrar_socio_asociacion.php?mensaje_error=Error al registrar socio.");
        exit();
    }

    $stmt->close();
}

$conn->close();
exit();
?>
