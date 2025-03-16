<?php
// Activar la visualización de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once(__DIR__ . "/../../includes/conexion.php");

$registros_por_pagina = 5;
$pagina_actual = isset($_POST['pag']) ? (int)$_POST['pag'] : 1;
$pagina_actual = max($pagina_actual, 1);
$search = isset($_POST['search']) ? trim($_POST['search']) : '';
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Construcción de la consulta SQL
$query = "SELECT idsocio, dni, nombre, apellido_pat, apellido_mat, genero FROM socio WHERE 1=1";
$params = [];
$types = "";

if (!empty($search)) {
    $search_param = "%$search%";
    $query .= " AND (dni LIKE ? OR nombre LIKE ? OR apellido_pat LIKE ? OR apellido_mat LIKE ?)";
    array_push($params, $search_param, $search_param, $search_param, $search_param);
    $types .= "ssss";
}

$query .= " ORDER BY idsocio ASC LIMIT ?, ?";
array_push($params, $offset, $registros_por_pagina);
$types .= "ii";

// Preparar la consulta
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conn->error);
}

// Vincular parámetros
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
if (!$result) {
    die("Error al obtener resultados: " . $stmt->error);
}

// Obtener el total de registros para la paginación
$total_query = "SELECT COUNT(*) as total FROM socio WHERE 1=1";
$total_params = [];
$total_types = "";

if (!empty($search)) {
    $total_query .= " AND (dni LIKE ? OR nombre LIKE ? OR apellido_pat LIKE ? OR apellido_mat LIKE ?)";
    array_push($total_params, $search_param, $search_param, $search_param, $search_param);
    $total_types .= "ssss";
}

$stmt_total = $conn->prepare($total_query);
if (!$stmt_total) {
    die("Error en la preparación de la consulta de total: " . $conn->error);
}

if (!empty($total_types)) {
    $stmt_total->bind_param($total_types, ...$total_params);
}

$stmt_total->execute();
$result_total = $stmt_total->get_result();
$total_socios = $result_total->fetch_assoc()['total'] ?? 0;
$total_paginas = ($total_socios > 0) ? ceil($total_socios / $registros_por_pagina) : 1;
?>

<div class="table-responsive">
    <table class="table table-striped">
        <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>DNI</th>
                <th>Nombre</th>
                <th>Apellido Paterno</th>
                <th>Apellido Materno</th>
                <th>Género</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $counter = $offset + 1;
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $counter++ ?></td>
                        <td><?= htmlspecialchars($row['dni']) ?></td>
                        <td><?= htmlspecialchars($row['nombre']) ?></td>
                        <td><?= htmlspecialchars($row['apellido_pat']) ?></td>
                        <td><?= htmlspecialchars($row['apellido_mat']) ?></td>
                        <td><?= htmlspecialchars($row['genero']) ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm btn-editar" data-id="<?= $row['idsocio'] ?>">
                                Editar
                            </button>
                            <button class="btn btn-danger btn-sm btn-eliminar" data-id="<?= $row['idsocio'] ?>">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                <?php endwhile; 
            } else { ?>
                <tr>
                    <td colspan="7" class="text-center text-muted">No se encontraron socios.</td>
                </tr>
            <?php } ?>
        </tbody>

    </table>
</div>

<!-- Paginación -->
<nav>
    <ul class="pagination justify-content-center">
        <li class="page-item <?= ($pagina_actual == 1) ? 'disabled' : '' ?>">
            <a class="page-link" href="#" data-page="1">Primera</a>
        </li>
        <li class="page-item <?= ($pagina_actual == 1) ? 'disabled' : '' ?>">
            <a class="page-link" href="#" data-page="<?= max(1, $pagina_actual - 1) ?>">Anterior</a>
        </li>
        <li class="page-item active">
            <span class="page-link"><?= $pagina_actual ?></span>
        </li>
        <li class="page-item <?= ($pagina_actual >= $total_paginas) ? 'disabled' : '' ?>">
            <a class="page-link" href="#" data-page="<?= min($total_paginas, $pagina_actual + 1) ?>">Siguiente</a>
        </li>
        <li class="page-item <?= ($pagina_actual >= $total_paginas) ? 'disabled' : '' ?>">
            <a class="page-link" href="#" data-page="<?= $total_paginas ?>">Última</a>
        </li>
    </ul>
</nav>
