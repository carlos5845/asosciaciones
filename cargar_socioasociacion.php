<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "data_asociaciones";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}

// Si se envió el formulario
if (isset($_POST["submit"])) {
    if ($_FILES["file"]["size"] > 0) {
        $filename = $_FILES["file"]["tmp_name"];

        // Verificar si el archivo realmente es CSV
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $filename);
        finfo_close($finfo);

        if ($mime !== "text/plain" && $mime !== "text/csv") {
            die("❌ Error: El archivo no es un CSV válido.");
        }

        $file = fopen($filename, "r");
        fgetcsv($file); // Omitir encabezado

        // Preparar consultas
        $stmt_select_socio = $conn->prepare("SELECT idsocio FROM socio WHERE dni = ?");
        $stmt_check_asociacion = $conn->prepare("SELECT 1 FROM socio_asociacion WHERE socio_idsocio = ? AND grupo_idgrupo = ?");
        $stmt_insert_socio_asociacion = $conn->prepare("INSERT INTO socio_asociacion (socio_idsocio, grupo_idgrupo, cod_puesto, rubro, observacion) VALUES (?, ?, ?, ?, ?)");

        $registros_procesados = 0;
        $registros_exitosos = 0;
        $registros_fallidos = 0;

        while (($column = fgetcsv($file, 10000, ";")) !== FALSE) {
            $registros_procesados++;

            $dni = trim($column[0]);
            $grupo_idgrupo = intval(trim($column[1]));
            $cod_puesto = trim($column[2]);
            $rubro = trim($column[3]);
            $observacion = trim($column[4]);

            // Completar el DNI con ceros a la izquierda si tiene menos de 8 dígitos
            $dni = str_pad($dni, 8, "0", STR_PAD_LEFT);

            // Validación de datos
            if (empty($dni) || empty($grupo_idgrupo) || empty($rubro)) {
                echo "⚠️ Registro inválido en fila $registros_procesados. Datos faltantes: ";
                echo "DNI: $dni, Grupo ID: $grupo_idgrupo, Rubro: $rubro. Saltando...<br>";
                $registros_fallidos++;
                continue;
            }

            // Buscar el idsocio por DNI
            $stmt_select_socio->bind_param("s", $dni);
            $stmt_select_socio->execute();
            $result_socio = $stmt_select_socio->get_result();

            if ($result_socio->num_rows > 0) {
                $row_socio = $result_socio->fetch_assoc();
                $idsocio = $row_socio["idsocio"];
            } else {
                echo "⚠️ Socio con DNI $dni no encontrado. Fila fallida: ";
                echo "DNI: $dni, Grupo ID: $grupo_idgrupo, Rubro: $rubro. Saltando...<br>";
                $registros_fallidos++;
                continue;
            }

            // Verificar si el socio ya está en esa asociación
            $stmt_check_asociacion->bind_param("ii", $idsocio, $grupo_idgrupo);
            $stmt_check_asociacion->execute();
            $result_check = $stmt_check_asociacion->get_result();

            if ($result_check->num_rows > 0) {
                echo "⚠️ Socio ID $idsocio ya está en el grupo $grupo_idgrupo. Fila fallida: ";
                echo "DNI: $dni, Grupo ID: $grupo_idgrupo, Rubro: $rubro. Saltando...<br>";
                $registros_fallidos++;
                continue;
            }

            // Insertar en socio_asociacion
            $stmt_insert_socio_asociacion->bind_param("iisss", $idsocio, $grupo_idgrupo, $cod_puesto, $rubro, $observacion);
            if ($stmt_insert_socio_asociacion->execute()) {
                $registros_exitosos++;
            } else {
                echo "❌ Error al insertar socio ID: $idsocio en grupo ID: $grupo_idgrupo. Fila fallida: ";
                echo "DNI: $dni, Grupo ID: $grupo_idgrupo, Rubro: $rubro. " . $stmt_insert_socio_asociacion->error . "<br>";
                $registros_fallidos++;
            }
        }

        fclose($file);
        echo "<br>✅ Carga finalizada.<br>";
        echo "📌 Total procesados: $registros_procesados<br>";
        echo "✅ Éxitos: $registros_exitosos<br>";
        echo "⚠️ Fallidos: $registros_fallidos<br>";
    } else {
        echo "❌ Error: Archivo vacío o no válido.";
    }
}

// Cerrar conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir CSV</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Subir archivo CSV</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <label>Selecciona el archivo CSV:</label>
        <input type="file" name="file" accept=".csv" required>
        <button type="submit" name="submit">Subir</button>
    </form>
</body>
</html>
