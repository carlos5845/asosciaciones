<?php
require_once(__DIR__ . "/../../includes/conexion.php");

if (!$conn) {
    die(json_encode(["error" => "Error de conexión a la base de datos."]));
}

$busqueda = isset($_POST['busqueda']) ? $_POST['busqueda'] : '';
$pagina_actual = isset($_POST['pagina']) ? (int)$_POST['pagina'] : 1;
$registros_por_pagina = 5;
$offset = ($pagina_actual - 1) * $registros_por_pagina;

$count_query = "SELECT COUNT(*) as total FROM grupo g 
    LEFT JOIN agrupamiento a ON g.agrupamiento_idagrupamiento = a.idagrupamiento";

if (!empty($busqueda)) {
    $count_query .= " WHERE g.nombre_grupo LIKE ? OR g.etiqueta_grupo LIKE ? OR a.agrupamientocol LIKE ?";
}

$stmt_count = $conn->prepare($count_query);
if (!empty($busqueda)) {
    $param_busqueda = "%$busqueda%";
    $stmt_count->bind_param("sss", $param_busqueda, $param_busqueda, $param_busqueda);
}

$stmt_count->execute();
$result_count = $stmt_count->get_result();
$total_registros = $result_count->fetch_assoc()['total'];
$total_paginas = max(1, ceil($total_registros / $registros_por_pagina));

$query = "
    SELECT 
        g.idgrupo, 
        g.nombre_grupo, 
        g.etiqueta_grupo, 
        a.agrupamientocol, 
        COUNT(sa.socio_idsocio) AS cantidad_socios
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

$datos = [];
while ($row = $result->fetch_assoc()) {
    $datos[] = $row;
}

echo json_encode([
    "datos" => $datos,
    "total_paginas" => $total_paginas,
    "pagina_actual" => $pagina_actual
]);

$conn->close();
