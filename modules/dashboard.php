    <?php
    // Verificar si los archivos existen antes de requerirlos
    if (!file_exists(__DIR__ . "/../includes/conexion.php")) {
        die("Error: No se encontró conexion.php en " . __DIR__ . "/../includes/");
    }

    if (!file_exists(__DIR__ . "/../includes/header.php")) {
        die("Error: No se encontró header.php en " . __DIR__ . "/../includes/");
    }

    // Incluir archivos de conexión y header
    require_once(__DIR__ . "/../includes/conexion.php");
    require_once(__DIR__ . "/../includes/header.php");

    // Verificar si la conexión a la base de datos está establecida
    if (!$conn) {
        die("Error: No se pudo conectar a la base de datos.");
    }

    // Consulta para obtener el total de socios
    $sql_socios = "SELECT COUNT(*) AS total FROM socio";
    $result_socios = $conn->query($sql_socios);
    $total_socios = $result_socios ? $result_socios->fetch_assoc()['total'] : 0;

    // Consulta para obtener el total de asociaciones (grupos)
    $sql_asociaciones = "SELECT COUNT(*) AS total FROM grupo";
    $result_asociaciones = $conn->query($sql_asociaciones);
    $total_asociaciones = $result_asociaciones ? $result_asociaciones->fetch_assoc()['total'] : 0;

    // Consulta para contar socios que pertenecen a más de una asociación
    $sql_socios_multiples = "SELECT COUNT(*) AS total FROM (
        SELECT socio_idsocio FROM socio_asociacion 
        GROUP BY socio_idsocio HAVING COUNT(grupo_idgrupo) > 1
    ) AS subquery";
    $result_socios_multiples = $conn->query($sql_socios_multiples);
    $socios_multiples = $result_socios_multiples ? (int) $result_socios_multiples->fetch_assoc()['total'] : 0;

    // Consulta para contar el total de asociados (incluye repeticiones si un socio está en varias asociaciones)
    $sql_total_asociados = "SELECT COUNT(*) AS total FROM socio_asociacion";
    $result_total_asociados = $conn->query($sql_total_asociados);
    $total_asociados = $result_total_asociados ? (int) $result_total_asociados->fetch_assoc()['total'] : 0;

    // Consulta para contar todos los registros en verificacion_asociados
    $sql_total_verificados = "SELECT COUNT(*) AS total_registrados FROM verificacion_asociados";
    $result_total_verificados = $conn->query($sql_total_verificados);
    $total_verificados = $result_total_verificados ? (int) $result_total_verificados->fetch_assoc()['total_registrados'] : 0;

    // Calcular el total de socios no verificados
    $total_no_verificados = $total_asociados - $total_verificados;

    ?>

    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard - Asociaciones</title>
        <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    </head>
    <body>
        <div class="container mt-5">
            <h2 class="mb-4"></h2>
            <div class="row">

                <!-- Caja Total de Socios -->
                <div class="col-md-3">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-header"><i class="bi bi-person"></i> Total de Socios</div>
                        <div class="card-body">
                            <h1 class="card-title"><?= $total_socios; ?></h1>
                        </div>
                    </div>
                </div>

                <!-- Caja Total de Asociaciones -->
                <div class="col-md-3">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-header"><i class="bi bi-people"></i> Total de Asociaciones</div>
                        <div class="card-body">
                            <h1 class="card-title"><?= $total_asociaciones; ?></h1>
                        </div>
                    </div>
                </div>

                <!-- Caja Socios Múltiples -->
                <div class="col-md-3">
                    <div class="card text-white bg-warning mb-3">
                        <div class="card-header"><i class="bi bi-people-fill"></i> Duplicidad de socios</div>
                        <div class="card-body">
                            <h1 class="card-title"><?= $socios_multiples; ?></h1>
                        </div>
                    </div>
                </div>

                <!-- Total de Asociados -->
                <div class="col-md-3">
                    <div class="card text-white bg-info mb-3">
                        <div class="card-header"><i class="bi bi-bar-chart"></i> Total de Asociados</div>
                        <div class="card-body">
                            <h1 class="card-title"><?= $total_asociados; ?></h1>
                        </div>
                    </div>
                </div>

                <!-- Caja de Total de Socios Verificados -->
                <div class="col-md-3">
                    <div class="card text-white bg-secondary mb-3">
                        <div class="card-header"><i class="bi bi-check-circle"></i> Socios Verificados</div>
                        <div class="card-body">
                            <h1 class="card-title"><?= $total_verificados; ?></h1>
                        </div>
                    </div>
                </div>

                <!-- Caja de Total de Socios No Verificados -->
                <div class="col-md-3">
                    <div class="card text-white bg-danger mb-3">
                        <div class="card-header"><i class="bi bi-x-circle"></i> Socios No Verificados</div>
                        <div class="card-body">
                            <h1 class="card-title"><?= $total_no_verificados; ?></h1>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </body>
    </html>

    <?php $conn->close(); ?>
