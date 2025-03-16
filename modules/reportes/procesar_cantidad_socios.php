<?php
// listar_grupos_socios.php
require_once('conexion.php'); // Conexión a la base de datos

// Parámetros de búsqueda y página
$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$pagina = max(1, $pagina);

// Configuración de paginación
$registros_por_pagina = 5;
$offset = ($pagina - 1) * $registros_por_pagina;

// Consulta para contar el total de registros
$query_count = "SELECT COUNT(*) as total FROM grupo g
                LEFT JOIN agrupamiento a ON g.agrupamiento_idagrupamiento = a.idagrupamiento";
if (!empty($busqueda)) {
    $query_count .= " WHERE g.nombre_grupo LIKE ? OR g.etiqueta_grupo LIKE ? OR a.agrupamientocol LIKE ?";
}

$stmt_count = $conn->prepare($query_count);
if (!empty($busqueda)) {
    $param_busqueda = "%$busqueda%";
    $stmt_count->bind_param("sss", $param_busqueda, $param_busqueda, $param_busqueda);
}

$stmt_count->execute();
$result_count = $stmt_count->get_result();
$total_registros = $result_count->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $registros_por_pagina);

// Consulta para obtener los datos
$query = "
    SELECT g.idgrupo, g.nombre_grupo, g.etiqueta_grupo, a.agrupamientocol, COUNT(sa.socio_idsocio) AS cantidad_socios
    FROM grupo g
    LEFT JOIN socio_asociacion sa ON g.idgrupo = sa.grupo_idgrupo
    LEFT JOIN agrupamiento a ON g.agrupamiento_idagrupamiento = a.idagrupamiento";

if (!empty($busqueda)) {
    $query .= " WHERE g.nombre_grupo LIKE ? OR g.etiqueta_grupo LIKE ? OR a.agrupamientocol LIKE ?";
}

$query .= " GROUP BY g.idgrupo, g.nombre_grupo, g.etiqueta_grupo, a.agrupamientocol
            ORDER BY g.idgrupo
            LIMIT ? OFFSET ?";

$stmt = $conn->prepare($query);
if (!empty($busqueda)) {
    $stmt->bind_param("sssii", $param_busqueda, $param_busqueda, $param_busqueda, $registros_por_pagina, $offset);
} else {
    $stmt->bind_param("ii", $registros_por_pagina, $offset);
}

$stmt->execute();
$result = $stmt->get_result();

// Crear el HTML de la tabla
$table_html = '';
$counter = $offset + 1;
while ($row = $result->fetch_assoc()) {
    $table_html .= '<tr>';
    $table_html .= '<td>' . $counter++ . '</td>';
    $table_html .= '<td>' . htmlspecialchars($row['etiqueta_grupo']) . '</td>';
    $table_html .= '<td>' . htmlspecialchars($row['nombre_grupo']) . '</td>';
    $table_html .= '<td>' . htmlspecialchars($row['agrupamientocol']) . '</td>';
    $table_html .= '<td>' . htmlspecialchars($row['cantidad_socios']) . '</td>';
    $table_html .= '</tr>';
}

// Generar la paginación
$pagination_html = '';
if ($total_paginas > 1) {
    $pagination_html .= '<nav><ul class="pagination justify-content-center">';
    $pagination_html .= '<li class="page-item ' . ($pagina == 1 ? 'disabled' : '') . '">
                        <a class="page-link" href="#" data-pagina="1">Primera</a>
                    </li>';
    $pagination_html .= '<li class="page-item ' . ($pagina == 1 ? 'disabled' : '') . '">
                        <a class="page-link" href="#" data-pagina="' . ($pagina - 1) . '">Anterior</a>
                    </li>';

    for ($i = 1; $i <= $total_paginas; $i++) {
        $pagination_html .= '<li class="page-item ' . ($pagina == $i ? 'active' : '') . '">
                            <a class="page-link" href="#" data-pagina="' . $i . '">' . $i . '</a>
                        </li>';
    }

    $pagination_html .= '<li class="page-item ' . ($pagina == $total_paginas ? 'disabled' : '') . '">
                        <a class="page-link" href="#" data-pagina="' . ($pagina + 1) . '">Siguiente</a>
                    </li>';
    $pagination_html .= '<li class="page-item ' . ($pagina == $total_paginas ? 'disabled' : '') . '">
                        <a class="page-link" href="#" data-pagina="' . $total_paginas . '">Última</a>
                    </li>';
    $pagination_html .= '</ul></nav>';
}

// Responder con el HTML generado
echo json_encode([
    'table' => $table_html,
    'pagination' => $pagination_html
]);
?>
