<?php
include '../../includes/header.php';
include '../../includes/conexion.php';

// Número de registros por página
$registros_por_pagina = 5;

// Página actual
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$pagina_actual = max($pagina_actual, 1);

// Filtro de búsqueda
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Cálculo del offset
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Contar el total de registros para paginación
$count_query = "
SELECT COUNT(DISTINCT s.idsocio, g.idgrupo) AS total
FROM verificacion_asociados va
LEFT JOIN socio s ON va.socio_asociacion_socio_idsocio = s.idsocio
LEFT JOIN grupo g ON va.socio_asociacion_grupo_idgrupo = g.idgrupo
WHERE (s.nombre LIKE '%$search%' OR s.apellido_pat LIKE '%$search%' OR s.apellido_mat LIKE '%$search%')
";

$count_result = mysqli_query($conn, $count_query);
$total_registros = mysqli_fetch_assoc($count_result)['total'];
$total_paginas = ceil($total_registros / $registros_por_pagina);

$query = "
SELECT 
    s.idsocio,
    s.dni,
    IFNULL(s.nombre, 'N/A') AS nombre,
    IFNULL(s.apellido_pat, 'N/A') AS apellido_pat,
    IFNULL(s.apellido_mat, 'N/A') AS apellido_mat,
    IFNULL(g.nombre_grupo, 'N/A') AS grupo,

    -- Primera verificación y fecha
    MAX(CASE WHEN va.orden = 1 THEN va.verificacion END) AS primera_verificacion,
    MAX(CASE WHEN va.orden = 1 THEN va.fecha_verificacion END) AS primera_fecha,

    -- Segunda verificación y fecha
    MAX(CASE WHEN va.orden = 2 THEN va.verificacion END) AS segunda_verificacion,
    MAX(CASE WHEN va.orden = 2 THEN va.fecha_verificacion END) AS segunda_fecha,

    -- Tercera verificación y fecha
    MAX(CASE WHEN va.orden = 3 THEN va.verificacion END) AS tercera_verificacion,
    MAX(CASE WHEN va.orden = 3 THEN va.fecha_verificacion END) AS tercera_fecha,

    -- Estado del socio
    CASE  
        WHEN 
            SUM(CASE WHEN va.verificacion = 'Presente' THEN 1 ELSE 0 END) > 0 OR 
            SUM(CASE WHEN va.verificacion = 'Justificado' THEN 1 ELSE 0 END) > 0 
        THEN 'Activo'  
        WHEN 
            SUM(CASE WHEN va.verificacion IS NOT NULL THEN 1 ELSE 0 END) = 0 
        THEN 'En espera'
        ELSE 'Inactivo'  
    END AS estado  

FROM (
    SELECT 
        va.*, 
        ROW_NUMBER() OVER (
            PARTITION BY va.socio_asociacion_socio_idsocio, va.socio_asociacion_grupo_idgrupo ORDER BY va.fecha_verificacion
        ) AS orden
    FROM verificacion_asociados va
) va
LEFT JOIN socio s ON va.socio_asociacion_socio_idsocio = s.idsocio
LEFT JOIN grupo g ON va.socio_asociacion_grupo_idgrupo = g.idgrupo
WHERE (s.nombre LIKE '%$search%' OR s.apellido_pat LIKE '%$search%' OR s.apellido_mat LIKE '%$search%')
GROUP BY s.idsocio, g.idgrupo, s.nombre, s.apellido_pat, s.apellido_mat, g.nombre_grupo
ORDER BY s.idsocio, g.nombre_grupo
LIMIT $registros_por_pagina OFFSET $offset
";

$result = mysqli_query($conn, $query);
if (!$result) {
    die("Error en la consulta de socios: " . mysqli_error($conn));
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Vigencias de Poder</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    

    <style>
    .table {
        font-size: 12px; /* Tamaño de letra más pequeño */
    }

    .table th, .table td {
        text-align: center; /* Centra el contenido horizontalmente */
        vertical-align: middle; /* Centra el contenido verticalmente */
        padding: 5px; /* Ajusta el espaciado */
        height: 30px; /* Asegura que las celdas tengan suficiente altura */
    }

    .thead-dark th {
        background-color: #343a40 !important; /* Color oscuro para el encabezado */
        color: white;
    }
    </style>

</head>

<div class="container mt-3">
    <h3 class="mb-3">Lista de Socios con Verificaciones</h3>

    <form method="GET" action="listar_socio.php">
        <input class="form-control mb-2 w-50" type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Buscar socio...">
        <button type="submit" class="btn btn-primary">Buscar</button>
    </form>

    <div class="table-responsive">
        <table class="table table-striped table-bordered text-center">
            <thead class="thead-dark">
                <tr>
                    <th>Nº</th>
                    <th>Dni</th>
                    <th>Nombre</th>
                    <th>Apellido Paterno</th>
                    <th>Apellido Materno</th>
                    <th>Grupo</th>
                    <th>Primera Verificación</th>
                    <th>Primera Fecha</th>
                    <th>Segunda Verificación</th>
                    <th>Segunda Fecha</th>
                    <th>Tercera Verificación</th>
                    <th>Tercera Fecha</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $contador = $offset + 1;
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td>" . $contador++ . "</td>
                            <td>" . $row['dni'] . "</td>
                            <td>" . $row['nombre'] . "</td>
                            <td>" . $row['apellido_pat'] . "</td>
                            <td>" . $row['apellido_mat'] . "</td>
                            <td>" . $row['grupo'] . "</td>
                            <td>" . $row['primera_verificacion'] . "</td>
                            <td>" . $row['primera_fecha'] . "</td>
                            <td>" . $row['segunda_verificacion'] . "</td>
                            <td>" . $row['segunda_fecha'] . "</td>
                            <td>" . $row['tercera_verificacion'] . "</td>
                            <td>" . $row['tercera_fecha'] . "</td>
                            <td>" . $row['estado'] . "</td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Paginación -->
        <?php if ($total_paginas > 1): ?>
        <nav>
            <ul class="pagination justify-content-center">
                <?php
                $query_params = $_GET;
                unset($query_params['pagina']);
                $base_url = "listar_socio.php?" . http_build_query($query_params);
                ?>

                <li class="page-item <?= ($pagina_actual == 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= $base_url ?>&pagina=1">Primera</a>
                </li>

                <li class="page-item <?= ($pagina_actual == 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= $base_url ?>&pagina=<?= $pagina_actual - 1 ?>">Anterior</a>
                </li>

                <li class="page-item active">
                    <span class="page-link"><?= $pagina_actual ?></span>
                </li>

                <li class="page-item <?= ($pagina_actual == $total_paginas) ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= $base_url ?>&pagina=<?= $pagina_actual + 1 ?>">Siguiente</a>
                </li>

                <li class="page-item <?= ($pagina_actual == $total_paginas) ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= $base_url ?>&pagina=<?= $total_paginas ?>">Última</a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<?php
mysqli_close($conn);
?>
