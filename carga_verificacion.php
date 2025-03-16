<?php
require 'includes/conexion.php'; // Conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["archivo"])) {
    $archivo = $_FILES["archivo"]["tmp_name"];

    if (!file_exists($archivo) || $_FILES["archivo"]["size"] == 0) {
        die("❌ Error: Archivo vacío o no válido.");
    }

    if (($gestor = fopen($archivo, "r")) !== FALSE) {
        fgetcsv($gestor, 1000, ";"); // Omitir la primera línea (encabezado)

        $conn->begin_transaction(); // Iniciar transacción
        $filasProcesadas = 0;
        $filasIgnoradas = 0;

        try {
            $stmt_insert = $conn->prepare("INSERT INTO verificacion_asociados 
                (salida_campo_idsalida, socio_asociacion_socio_idsocio, socio_asociacion_grupo_idgrupo, verificacion, fecha_verificacion, observacion) 
                VALUES (?, ?, ?, ?, ?, ?)");

            $filaActual = 1; // Contador de filas

            while (($datos = fgetcsv($gestor, 1000, ";")) !== FALSE) {
                $filaActual++;

                if (count($datos) < 6) {
                    echo "⚠️ Fila $filaActual ignorada: Incompleta <br>";
                    $filasIgnoradas++;
                    continue;
                }

                list($salida_campo_idsalida, $dni_socio, $socio_asociacion_grupo_idgrupo, $verificacion, $fecha_verificacion, $observacion) = array_map('trim', $datos);

                // Validar el campo verificación
                $valoresPermitidos = ["Presente", "Justificado", "Ausente"];
                if (!in_array($verificacion, $valoresPermitidos)) {
                    echo "⚠️ Fila $filaActual ignorada: Verificación inválida ($verificacion) <br>";
                    $filasIgnoradas++;
                    continue;
                }

                // Convertir fecha de DD/MM/YYYY a YYYY-MM-DD
                $fechaObj = DateTime::createFromFormat('d/m/Y', $fecha_verificacion);
                if (!$fechaObj) {
                    echo "⚠️ Fila $filaActual ignorada: Fecha inválida ($fecha_verificacion) <br>";
                    $filasIgnoradas++;
                    continue;
                }
                $fecha_verificacion = $fechaObj->format('Y-m-d');

                // Obtener idsocio desde el DNI
                $stmt_socio = $conn->prepare("SELECT idsocio FROM socio WHERE dni = ?");
                $stmt_socio->bind_param("s", $dni_socio);
                $stmt_socio->execute();
                $stmt_socio->bind_result($idsocio);
                $stmt_socio->fetch();
                $stmt_socio->close();

                if (!$idsocio) {
                    echo "⚠️ Fila $filaActual ignorada: Socio no encontrado (DNI: $dni_socio) <br>";
                    $filasIgnoradas++;
                    continue;
                }

                // Verificar si el socio ya tiene un registro para esa fecha
                $stmt_verificar = $conn->prepare("SELECT COUNT(*) FROM verificacion_asociados 
                    WHERE socio_asociacion_socio_idsocio = ? AND socio_asociacion_grupo_idgrupo = ? 
                    AND salida_campo_idsalida = ? AND fecha_verificacion = ?");
                $stmt_verificar->bind_param("iiis", $idsocio, $socio_asociacion_grupo_idgrupo, $salida_campo_idsalida, $fecha_verificacion);
                $stmt_verificar->execute();
                $stmt_verificar->bind_result($existe);
                $stmt_verificar->fetch();
                $stmt_verificar->close();

                if ($existe > 0) {
                    echo "⚠️ Fila $filaActual ignorada: Registro duplicado (Fecha: $fecha_verificacion) <br>";
                    $filasIgnoradas++;
                    continue;
                }

                // Insertar la verificación del socio
                $stmt_insert->bind_param("iiisss", $salida_campo_idsalida, $idsocio, $socio_asociacion_grupo_idgrupo, $verificacion, $fecha_verificacion, $observacion);
                if ($stmt_insert->execute()) {
                    echo "✅ Fila $filaActual insertada: Socio ID: $idsocio | Grupo: $socio_asociacion_grupo_idgrupo | Verificación: $verificacion | Fecha: $fecha_verificacion <br>";
                    $filasProcesadas++;
                } else {
                    echo "❌ Error en fila $filaActual: " . $stmt_insert->error . "<br>";
                }
            }

            fclose($gestor);
            $conn->commit(); // Confirmar transacción
            echo "<br>📌 Carga finalizada.<br>";
            echo "✅ Filas insertadas: $filasProcesadas<br>";
            echo "⚠️ Filas ignoradas: $filasIgnoradas (ver detalles arriba)<br>";
        } catch (Exception $e) {
            $conn->rollback();
            echo "❌ Error: " . $e->getMessage();
        } finally {
            $stmt_insert->close();
        }
    } else {
        echo "❌ Error al abrir el archivo.";
    }
} else {
    echo "❌ No se recibió un archivo válido.";
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <title>Subir CSV</title>
</head>
<body>
    <h2>Subir archivo CSV</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <label>Selecciona el archivo CSV:</label>
        <input type="file" name="archivo" accept=".csv" required>
        <button type="submit">Subir</button>
    </form>
</body>
</html>
