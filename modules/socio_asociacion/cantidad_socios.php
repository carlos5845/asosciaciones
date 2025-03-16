<?php

// Verificar si los archivos existen antes de requerirlos
require_once(__DIR__ . "/../../includes/conexion.php");
require_once(__DIR__ . "/../../includes/header.php");

// Verificar conexión
if (!$conn) {
    die("Error: No se pudo conectar a la base de datos.");
}

// Capturar el término de búsqueda
$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';

// Configuración de paginación
$registros_por_pagina = 5;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$pagina_actual = max(1, $pagina_actual);
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Consulta para contar total de registros con filtro de búsqueda
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

// Consulta principal con búsqueda
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

?>

<body>
<div class="container mt-3">
    <h4 class="text-center mb-3">Lista de Grupos y Cantidad de Socios</h4>

    <!-- Buscador -->
    <form method="GET" action="" class="mb-3 d-flex">
        <div class="input-group search-container">
            <input type="text" name="busqueda" class="form-control" placeholder="Buscar grupo..." 
                   value="<?= $busqueda ?>">
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit">Buscar</button>
            </div>
        </div>
    </form>

    <a href="exportar_grupo_excel.php" class="btn btn-success mb-2">Exportar a Excel</a>
    
    <table class="table table-bordered table-sm mt-3">
        <thead class="thead-dark">
            <tr>
                <th>Nº</th>
                <th>Cod Etiqueta</th>
                <th>Grupo</th>
                <th>Agrupamiento</th>
                <th>Cantidad de Socios</th>
            </tr>
        </thead>
        <tbody>
    <?php 
    $counter = $offset + 1;
    while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $counter++ ?></td>
                <td><?= $row['etiqueta_grupo'] ?></td>
                <td><?= $row['nombre_grupo'] ?></td>
                <td><?= $row['agrupamientocol'] ?></td>
                <td><?= $row['cantidad_socios'] ?></td>
            </tr>
    <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Paginación -->
    <?php if ($total_paginas > 1): ?>
    <nav>
        <ul class="pagination justify-content-center">
            <?php
            $query_params = $_GET;
            unset($query_params['pagina']);
            $base_url = "listar_grupos_socios.php?" . http_build_query($query_params);
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
<?php $conn->close();
