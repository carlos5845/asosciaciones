<?php
include '../../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dia_laborable = $_POST['dia_laborable_id']; // El valor del ENUM (ejemplo: "Lunes")
    $grupo_idgrupo = $_POST['grupo_idgrupo'];

    if (empty($dia_laborable) || empty($grupo_idgrupo)) {
        header("Location: registrar_laborable.php?error=Todos los campos son obligatorios");
        exit;
    }

    // Verificar si el día laborable ya existe en la tabla `dia_laborable`
    $query_check_dia = "SELECT iddia_laborable FROM dia_laborable WHERE dia = ?";
    $stmt_check_dia = $conn->prepare($query_check_dia);
    $stmt_check_dia->bind_param("s", $dia_laborable);
    $stmt_check_dia->execute();
    $stmt_check_dia->store_result();

    if ($stmt_check_dia->num_rows == 0) {
        // Si el día no existe, insertarlo en la tabla `dia_laborable`
        $query_insert_dia = "INSERT INTO dia_laborable (dia) VALUES (?)";
        $stmt_insert_dia = $conn->prepare($query_insert_dia);
        $stmt_insert_dia->bind_param("s", $dia_laborable);
        $stmt_insert_dia->execute();
        $dia_laborable_id = $stmt_insert_dia->insert_id; // Obtener el ID del día recién insertado
        $stmt_insert_dia->close();
    } else {
        // Si el día ya existe, obtener el `iddia_laborable`
        $stmt_check_dia->bind_result($dia_laborable_id);
        $stmt_check_dia->fetch();
    }

    $stmt_check_dia->close();

    // **Verificar si la relación ya existe**
    $query_check_relacion = "SELECT COUNT(*) FROM dia_laborable_has_grupo WHERE dia_laborable_iddia_laborable = ? AND grupo_idgrupo = ?";
    $stmt_check_relacion = $conn->prepare($query_check_relacion);
    $stmt_check_relacion->bind_param("ii", $dia_laborable_id, $grupo_idgrupo);
    $stmt_check_relacion->execute();
    $stmt_check_relacion->bind_result($existe);
    $stmt_check_relacion->fetch();
    $stmt_check_relacion->close();

    if ($existe > 0) {
        // ⚠️ Si la relación ya existe, redirigir con un mensaje de error en la URL
        header("Location: registrar_laborable.php?error=duplicado");
        exit();
    }

    // Insertar la relación en la tabla `dia_laborable_has_grupo`
    $query_insert_relacion = "INSERT INTO dia_laborable_has_grupo (dia_laborable_iddia_laborable, grupo_idgrupo) 
                              VALUES (?, ?)";
    $stmt_relacion = $conn->prepare($query_insert_relacion);
    $stmt_relacion->bind_param("ii", $dia_laborable_id, $grupo_idgrupo);

    if ($stmt_relacion->execute()) {
        // ✅ Registro exitoso
        header("Location: registrar_laborable.php?success=1");
    } else {
        // ❌ Error en la inserción
        header("Location: registrar_laborable.php?error=Hubo un problema al registrar la relación");
    }

    $stmt_relacion->close();
    $conn->close();
}
?>
