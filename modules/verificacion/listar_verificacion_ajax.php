<?php
require_once(__DIR__ . "/../../includes/conexion.php");

$registros_por_pagina = 5;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$pagina_actual = max($pagina_actual, 1);
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Obtener el número total de registros
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

// Consulta de datos
$query = "
SELECT 
    s.idsocio,
    s.dni,
    IFNULL(s.nombre, 'N/A') AS nombre,
    IFNULL(s.apellido_pat, 'N/A') AS apellido_pat,
    IFNULL(s.apellido_mat, 'N/A') AS apellido_mat,
    IFNULL(g.nombre_grupo, 'N/A') AS grupo,
    MAX(CASE WHEN va.orden = 1 THEN va.verificacion END) AS primera_verificacion,
    MAX(CASE WHEN va.orden = 1 THEN va.fecha_verificacion END) AS primera_fecha,
    MAX(CASE WHEN va.orden = 2 THEN va.verificacion END) AS segunda_verificacion,
    MAX(CASE WHEN va.orden = 2 THEN va.fecha_verificacion END) AS segunda_fecha,
    MAX(CASE WHEN va.orden = 3 THEN va.verificacion END) AS tercera_verificacion,
    MAX(CASE WHEN va.orden = 3 THEN va.fecha_verificacion END) AS tercera_fecha,
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
    die("Error en la consulta: " . mysqli_error($conn));
}

// Tabla de resultados
echo '<div class="table-responsive" id="contenedor-tabla">
        <table class="table table-sm table-striped table-bordered text-center">
        <thead class="thead-dark">
            <tr>
                <th>Nº</th>
                <th>DNI</th>
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
        <tbody>';

$contador = $offset + 1;
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>
            <td>{$contador}</td>
            <td>{$row['dni']}</td>
            <td>{$row['nombre']}</td>
            <td>{$row['apellido_pat']}</td>
            <td>{$row['apellido_mat']}</td>
            <td>{$row['grupo']}</td>
            <td>{$row['primera_verificacion']}</td>
            <td>{$row['primera_fecha']}</td>
            <td>{$row['segunda_verificacion']}</td>
            <td>{$row['segunda_fecha']}</td>
            <td>{$row['tercera_verificacion']}</td>
            <td>{$row['tercera_fecha']}</td>
            <td>{$row['estado']}</td>
          </tr>";
    $contador++;
}
echo '</tbody></table></div>';

// Paginación
$rango = 1; 

echo '<nav aria-label="Page navigation">
        <ul class="pagination justify-content-end">';

if ($pagina_actual > 1 + $rango) {
    echo "<li class='page-item'><a class='page-link' href='#' data-pagina='1'>Primera</a></li>";
}
if ($pagina_actual > 1) {
    echo "<li class='page-item'><a class='page-link' href='#' data-pagina='" . ($pagina_actual - 1) . "'>Anterior</a></li>";
}

for ($i = max(1, $pagina_actual - $rango); $i <= min($total_paginas, $pagina_actual + $rango); $i++) {
    echo "<li class='page-item " . ($pagina_actual == $i ? "active" : "") . "'>
            <a class='page-link' href='#' data-pagina='$i'>$i</a>
          </li>";
}

if ($pagina_actual < $total_paginas) {
    echo "<li class='page-item'><a class='page-link' href='#' data-pagina='" . ($pagina_actual + 1) . "'>Siguiente</a></li>";
}
if ($pagina_actual < $total_paginas - $rango) {
    echo "<li class='page-item'><a class='page-link' href='#' data-pagina='$total_paginas'>Última</a></li>";
}

echo '</ul></nav>';

mysqli_close($conn);
?>
