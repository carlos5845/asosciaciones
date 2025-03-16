<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "data_asociaciones";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Asegurar UTF-8
$conn->set_charset("utf8");

// Si se envió el formulario
if (isset($_POST["submit"])) {
    $filename = $_FILES["file"]["tmp_name"];

    if ($_FILES["file"]["size"] > 0) {
        $file = fopen($filename, "r");
        fgetcsv($file); // Omitir encabezado

        // Preparar consulta para verificar idgrupo existente
        $stmt_select_idgrupo = $conn->prepare("SELECT idgrupo FROM grupo WHERE idgrupo = ?");

        // **PRIMERA PASADA: Revisar si hay idgrupo duplicados en la base de datos**
        $duplicadosEncontrados = [];
        $duplicadosDetalles = [];
        $lineNumber = 1; // Contador de líneas
        $totalFilas = 0;

        while (($column = fgetcsv($file, 10000, ";")) !== FALSE) {
            $lineNumber++; // Aumentar el número de línea
            $totalFilas++; // Contar total de filas leídas
            if (count($column) < 7) {
                continue;
            }

            $idgrupo = trim($column[0]);

            if (!empty($idgrupo)) {
                $stmt_select_idgrupo->bind_param("i", $idgrupo);
                $stmt_select_idgrupo->execute();
                $result_idgrupo = $stmt_select_idgrupo->get_result();

                if ($result_idgrupo->num_rows > 0) {
                    $duplicadosEncontrados[] = $idgrupo;
                    $duplicadosDetalles[] = "Fila $lineNumber: ID: $idgrupo, Etiqueta: {$column[1]}, Nombre: {$column[2]}, Estado: {$column[6]}";
                }
            }
        }
        fclose($file);

        // Si hay duplicados, detener toda la carga
        if (!empty($duplicadosEncontrados)) {
            echo "❌ Error: Se encontraron `idgrupo` duplicados en la base de datos. No se procesó ningún registro.<br>";
            echo "<b>Total de filas no procesadas: " . count($duplicadosEncontrados) . " de $totalFilas</b><br><br>";
            echo "<b>Detalles de los registros duplicados:</b><br>";
            foreach ($duplicadosDetalles as $detalle) {
                echo "🔴 $detalle <br>";
            }
            exit(); // Detiene la ejecución del script y no permite que se inserten datos
        }

        // **SEGUNDA PASADA: Insertar datos si no hubo duplicados**
        $file = fopen($filename, "r");
        fgetcsv($file); // Omitir encabezado
        $stmt_insert_grupo = $conn->prepare("INSERT INTO grupo (idgrupo, etiqueta_grupo, nombre_grupo, ubicacion, agrupamiento_idagrupamiento, categoria_idcategoria, estado) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $filasProcesadas = 0;

        while (($column = fgetcsv($file, 10000, ";")) !== FALSE) {
            if (count($column) < 7) {
                continue;
            }

            $idgrupo = trim($column[0]);
            $etiqueta_grupo = utf8_encode(trim($column[1]));
            $nombre_grupo = utf8_encode(trim($column[2]));
            $ubicacion = utf8_encode(trim($column[3]));
            $agrupamiento_idagrupamiento = trim($column[4]);
            $categoria_idcategoria = trim($column[5]);
            $estado = (trim($column[6]) === 'Activo') ? 'Activo' : 'Inactivo';

            if (!empty($idgrupo) && !empty($etiqueta_grupo) && !empty($nombre_grupo) && !empty($estado)) {
                $stmt_insert_grupo->bind_param("issssss", $idgrupo, $etiqueta_grupo, $nombre_grupo, $ubicacion, $agrupamiento_idagrupamiento, $categoria_idcategoria, $estado);
                if ($stmt_insert_grupo->execute()) {
                    $filasProcesadas++;
                }
            }
        }

        fclose($file);
        echo "✅ Carga completada. Filas procesadas: " . $filasProcesadas;
    } else {
        echo "❌ Error: Archivo vacío o no válido.";
    }
}

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