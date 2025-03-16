<?php
require_once(__DIR__ . "/../../includes/conexion.php");

if (!$conn) {
    die(json_encode(["error" => "Error de conexión a la base de datos."]));
}

header('Content-Type: application/json');

$dni = isset($_GET['dni']) ? trim($_GET['dni']) : '';

if (!$dni || !preg_match('/^\d{8}$/', $dni)) {
    echo json_encode(["error" => "DNI inválido"]);
    exit;
}

$query = "
SELECT s.*, COUNT(sa.grupo_idgrupo) AS total_asociaciones
FROM socio s
LEFT JOIN socio_asociacion sa ON s.idsocio = sa.socio_idsocio
WHERE s.dni = ?
GROUP BY s.idsocio";

$stmt = mysqli_prepare($conn, $query);
if (!$stmt) {
    echo json_encode(["error" => "Error en la consulta"]);
    exit;
}

mysqli_stmt_bind_param($stmt, 's', $dni);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || !($socio = mysqli_fetch_assoc($result))) {
    echo json_encode(["error" => "No se encontró un socio con el DNI proporcionado"]);
    exit;
}

// Obtener asociaciones del socio
$asociaciones = [];
$query_asociaciones = "
SELECT g.idgrupo, g.nombre_grupo, g.ubicacion, 
       cat.tipo AS categoria, agr.agrupamientocol AS agrupamiento, 
       sa.cod_puesto, sa.rubro, sa.observacion, 
       GROUP_CONCAT(DISTINCT c.tipo_cargo ORDER BY c.tipo_cargo SEPARATOR ', ') AS cargos_junta,
       COALESCE(
           CASE  
           WHEN COUNT(va.verificacion) = 0 THEN 'En espera'
           WHEN SUM(CASE WHEN va.verificacion IN ('Presente', 'Justificado') THEN 1 ELSE 0 END) > 0 THEN 'Activo'
           ELSE 'Inactivo'
           END, 'En espera'
       ) AS estado
FROM socio_asociacion sa
JOIN grupo g ON sa.grupo_idgrupo = g.idgrupo
LEFT JOIN junta_directiva jd ON sa.socio_idsocio = jd.socio_asociacion_socio_idsocio AND sa.grupo_idgrupo = jd.socio_asociacion_grupo_idgrupo
LEFT JOIN cargo c ON jd.cargo_idcargo = c.idcargo
JOIN categoria cat ON g.categoria_idcategoria = cat.idcategoria
JOIN agrupamiento agr ON g.agrupamiento_idagrupamiento = agr.idagrupamiento
LEFT JOIN verificacion_asociados va ON sa.socio_idsocio = va.socio_asociacion_socio_idsocio AND sa.grupo_idgrupo = va.socio_asociacion_grupo_idgrupo
WHERE sa.socio_idsocio = ?
GROUP BY g.idgrupo";

$stmt_asoc = mysqli_prepare($conn, $query_asociaciones);
if ($stmt_asoc) {
    mysqli_stmt_bind_param($stmt_asoc, 'i', $socio['idsocio']);
    mysqli_stmt_execute($stmt_asoc);
    $result_asoc = mysqli_stmt_get_result($stmt_asoc);
    
    while ($row = mysqli_fetch_assoc($result_asoc)) {
        $asociaciones[] = $row;
    }
}

echo json_encode([
    "socio" => $socio,
    "asociaciones" => $asociaciones
]);
