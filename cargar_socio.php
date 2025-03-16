<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "data_asociaciones";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("❌ Conexión fallida: " . $conn->connect_error);
}

// Si se envió el formulario
if (isset($_POST["submit"])) {
    $filename = $_FILES["file"]["tmp_name"];

    if ($_FILES["file"]["size"] > 0) {
        $file = fopen($filename, "r");
        fgetcsv($file); // Omitir encabezado

        // Preparar consulta de inserción evitando duplicados en la BD
        $stmt_insert_socio = $conn->prepare("
            INSERT IGNORE INTO socio (dni, nombre, apellido_pat, apellido_mat, genero, departamento, provincia, distrito)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        // Array para evitar DNIs duplicados dentro del mismo archivo
        $dnisProcesados = [];

        // Variables de conteo
        $filasProcesadas = 0;
        $filasIgnoradas = 0;

        while (($column = fgetcsv($file, 10000, ";")) !== FALSE) {
            // Verifica que la fila tenga el número correcto de columnas (8 columnas)
            if (count($column) < 8) {
                echo "⚠️ Fila ignorada (menos de 8 columnas).<br>";
                $filasIgnoradas++;
                continue;
            }

            // Asignación de variables
            $dni = trim($column[0]);
            $nombre = trim($column[1]);
            $apellido_pat = trim($column[2]);
            $apellido_mat = trim($column[3]);
            $genero = trim($column[4]);
            $departamento = trim($column[5]);
            $provincia = trim($column[6]);
            $distrito = trim($column[7]);

            // Validaciones básicas
            if (empty($dni) || empty($nombre) || empty($apellido_pat) || !is_numeric($dni)) {
                echo "⚠️ Fila ignorada (datos incompletos o inválidos): $dni - $nombre $apellido_pat<br>";
                $filasIgnoradas++;
                continue;
            }

            // Verificar si el DNI ya fue procesado en este archivo
            if (isset($dnisProcesados[$dni])) {
                echo "⚠️ DNI duplicado en el archivo: $dni. Ignorado.<br>";
                $filasIgnoradas++;
                continue;
            }

            // Agregar a la lista de DNIs procesados
            $dnisProcesados[$dni] = true;

            // Insertar en la base de datos
            $stmt_insert_socio->bind_param("ssssssss", $dni, $nombre, $apellido_pat, $apellido_mat, $genero, $departamento, $provincia, $distrito);
            if ($stmt_insert_socio->execute()) {
                $filasProcesadas++;
                echo "✅ Socio insertado: $dni<br>";
            } else {
                echo "❌ Error al insertar socio con DNI: $dni<br>";
            }
        }

        fclose($file);
        echo "<br>📌 Carga finalizada.<br>";
        echo "✅ Filas insertadas: $filasProcesadas<br>";
        echo "⚠️ Filas ignoradas: $filasIgnoradas<br>";
    } else {
        echo "❌ Error: Archivo vacío o no válido.";
    }
}

// Cerrar conexión
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Subir CSV</title>
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
