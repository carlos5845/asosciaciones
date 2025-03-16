<?php
session_start(); // Iniciar sesión para manejar mensajes
include '../../includes/header.php';
include '../../includes/conexion.php'; // Asegura que la conexión está disponible

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $grupo_id = filter_input(INPUT_POST, 'grupo', FILTER_VALIDATE_INT);
    $fecha_inicio = filter_input(INPUT_POST, 'fecha_inicio', FILTER_SANITIZE_STRING);
    $fecha_fin = filter_input(INPUT_POST, 'fecha_fin', FILTER_SANITIZE_STRING);

    $errores = [];
    $mensajes = [];
    $i = 1;

    // Validar fechas
    if (strtotime($fecha_fin) < strtotime($fecha_inicio)) {
        $errores[] = "⚠️ La fecha de fin no puede ser anterior a la fecha de inicio.";
    }

    // Iniciar transacción
    $conn->begin_transaction();

    try {
        while (isset($_POST["dni_$i"])) {
            $dni = trim($_POST["dni_$i"]);
            $celular = isset($_POST["celular_$i"]) ? trim($_POST["celular_$i"]) : '';
            $cargo_id = filter_input(INPUT_POST, "cargo_$i", FILTER_VALIDATE_INT);
            // Corregir la asignación del estado: se compara con "1" ya que en el select el valor para Activo es "1"
            $estado = (isset($_POST["estado_$i"]) && $_POST["estado_$i"] === "1") ? 1 : 0;

            if (empty($dni)) {
                $errores[] = "⚠️ Fila $i: El campo DNI no puede estar vacío.";
                $i++;
                continue;
            }

            if (empty($cargo_id)) {
                $errores[] = "⚠️ Fila $i: El campo Cargo no puede estar vacío.";
                $i++;
                continue;
            }

            // Verificar si el socio existe
            $sql_socio = "SELECT idsocio FROM socio WHERE dni = ?";
            $stmt_socio = $conn->prepare($sql_socio);
            $stmt_socio->bind_param("s", $dni);
            $stmt_socio->execute();
            $result_socio = $stmt_socio->get_result();

            if ($result_socio->num_rows > 0) {
                $row_socio = $result_socio->fetch_assoc();
                $socio_id = $row_socio['idsocio'];

                // Verificar si el socio pertenece al grupo usando la tabla correcta: socio_asociacion
                $sql_verificar_grupo = "SELECT COUNT(*) as pertenece FROM socio_asociacion WHERE socio_idsocio = ? AND grupo_idgrupo = ?";
                $stmt_verificar_grupo = $conn->prepare($sql_verificar_grupo);
                $stmt_verificar_grupo->bind_param("ii", $socio_id, $grupo_id);
                $stmt_verificar_grupo->execute();
                $result_verificar_grupo = $stmt_verificar_grupo->get_result();
                $row_verificar_grupo = $result_verificar_grupo->fetch_assoc();

                if ($row_verificar_grupo['pertenece'] > 0) {
                    // Verificar si ya está en la junta usando los campos correctos
                    $sql_verificar = "SELECT COUNT(*) as existe FROM junta_directiva 
                                      WHERE socio_asociacion_socio_idsocio = ? AND socio_asociacion_grupo_idgrupo = ?";
                    $stmt_verificar = $conn->prepare($sql_verificar);
                    $stmt_verificar->bind_param("ii", $socio_id, $grupo_id);
                    $stmt_verificar->execute();
                    $result_verificar = $stmt_verificar->get_result();
                    $row_verificar = $result_verificar->fetch_assoc();

                    if ($row_verificar['existe'] == 0) {
                        // Insertar en junta_directiva con el orden correcto de columnas:
                        // socio_asociacion_socio_idsocio, socio_asociacion_grupo_idgrupo, cargo_idcargo, fecha_inicio, fecha_fin, celular, estado
                        $sql_insert = "INSERT INTO junta_directiva (socio_asociacion_socio_idsocio, socio_asociacion_grupo_idgrupo, cargo_idcargo, fecha_inicio, fecha_fin, celular, estado) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?)";
                        $stmt_insert = $conn->prepare($sql_insert);
                        $stmt_insert->bind_param("iiisssi", $socio_id, $grupo_id, $cargo_id, $fecha_inicio, $fecha_fin, $celular, $estado);

                        if ($stmt_insert->execute()) {
                            $mensajes[] = "✅ DNI $dni registrado correctamente en la junta.";
                        } else {
                            $errores[] = "⚠️ Error al registrar el socio con DNI $dni.";
                        }
                    } else {
                        $errores[] = "⚠️ El socio con DNI $dni ya está en la junta de este grupo.";
                    }
                } else {
                    $errores[] = "⚠️ El socio con DNI $dni no pertenece al grupo seleccionado.";
                }
            } else {
                $errores[] = "⚠️ El socio con DNI $dni no existe en la base de datos.";
            }

            $i++;
        }

        // Confirmar transacción si no hay errores
        if (empty($errores)) {
            $conn->commit();
        } else {
            $conn->rollback();
        }
    } catch (Exception $e) {
        $conn->rollback();
        $errores[] = "⚠️ Error en la transacción: " . $e->getMessage();
    }

    $conn->close();

    // Almacenar mensajes en la sesión
    $_SESSION['mensajes'] = $mensajes;
    $_SESSION['errores'] = $errores;

    // Redirigir
    header("Location: registro_junta.php");
    exit();
}
?>
