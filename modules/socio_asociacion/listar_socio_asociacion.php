<?php
include '../../includes/header.php';
include '../../includes/conexion.php';

// Número de registros por página
$registros_por_pagina = 5;

// Obtener número de página
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$pagina_actual = max(1, $pagina_actual);

// Mensajes de éxito o error
$mensaje = $_GET['mensaje'] ?? '';
$mensaje_error = $_GET['error'] ?? '';

// Variables de búsqueda y filtros
$search = $_GET['search'] ?? '';
$asociacion_filtro = $_GET['asociacion'] ?? '';

// Calcular OFFSET
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Construcción de la cláusula WHERE
$where_clause = " WHERE 1=1"; // Siempre verdadero
$params = [];
$param_types = "";

if (!empty($search)) {
    $where_clause .= " AND (
        s.dni LIKE ? OR
        s.nombre LIKE ? OR
        s.apellido_pat LIKE ? OR
        s.apellido_mat LIKE ? OR
        g.nombre_grupo LIKE ? OR
        sa.cod_puesto LIKE ? OR
        sa.rubro LIKE ? OR
        sa.observacion LIKE ?
    )";

    $search_param = "%$search%";
    array_push($params, $search_param, $search_param, $search_param, $search_param, 
                         $search_param, $search_param, $search_param, $search_param);
    $param_types .= str_repeat("s", 8);
}

if (!empty($asociacion_filtro)) {
    $where_clause .= " AND g.idgrupo = ?";
    array_push($params, $asociacion_filtro);
    $param_types .= "i";
}

// Contar total de registros
$count_query = "
    SELECT COUNT(*) as total
    FROM socio_asociacion sa
    JOIN socio s ON sa.socio_idsocio = s.idsocio
    JOIN grupo g ON sa.grupo_idgrupo = g.idgrupo
    $where_clause
";
$stmt_count = $conn->prepare($count_query);

if (!empty($params)) {
    $stmt_count->bind_param($param_types, ...$params);
}
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$total_registros = $result_count->fetch_assoc()['total'];
$total_paginas = max(1, ceil($total_registros / $registros_por_pagina));

// Consulta con paginación
$query = "
    SELECT s.dni, s.nombre, s.apellido_pat, s.apellido_mat, 
           g.idgrupo, g.nombre_grupo, sa.cod_puesto, sa.rubro, sa.observacion,
           sa.socio_idsocio, sa.grupo_idgrupo
    FROM socio_asociacion sa
    JOIN socio s ON sa.socio_idsocio = s.idsocio
    JOIN grupo g ON sa.grupo_idgrupo = g.idgrupo
    $where_clause
    ORDER BY s.apellido_pat, s.apellido_mat, s.nombre 
    LIMIT ? OFFSET ?";

array_push($params, $registros_por_pagina, $offset);
$param_types .= "ii";

$stmt = $conn->prepare($query);
$stmt->bind_param($param_types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Obtener lista de asociaciones para el filtro
$asociaciones_query = "SELECT idgrupo, nombre_grupo FROM grupo ORDER BY nombre_grupo";
$result_asociaciones = $conn->query($asociaciones_query);
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Socios Asociados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-3">
    <h4 class="text-center mb-3">Lista de Socios Asociados</h4>

    <form method="GET" action="">
        <div class="row mb-3">
            <div class="col-md-6">
                <input type="text" class="form-control" placeholder="Buscar..." name="search" 
                    value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-md-3">
                <select name="asociacion" class="form-control">
                    <option value="">-- Filtrar por Asociación --</option>
                    <?php while ($row_asociacion = $result_asociaciones->fetch_assoc()): ?>
                        <option value="<?= $row_asociacion['idgrupo'] ?>" 
                            <?= ($asociacion_filtro == $row_asociacion['idgrupo']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($row_asociacion['nombre_grupo']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-info btn-block" type="submit">Filtrar</button>
            </div>
        </div>
    </form>

    <table class="table table-bordered table-sm mt-3">
        <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>DNI</th>
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>Asociación</th>
                <th>Código Puesto</th>
                <th>Rubro</th>
                <th>Observación</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
    <?php 
    $counter = $offset + 1;
    if ($result->num_rows > 0): 
        while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $counter++ ?></td>
                <td><?= htmlspecialchars($row['dni']) ?></td>
                <td><?= htmlspecialchars($row['nombre']) ?></td>
                <td><?= htmlspecialchars($row['apellido_pat'] . ' ' . $row['apellido_mat']) ?></td>
                <td><?= htmlspecialchars($row['nombre_grupo']) ?></td>
                <td><?= htmlspecialchars($row['cod_puesto'] ?? '-') ?></td>
                <td><?= htmlspecialchars($row['rubro']) ?></td>
                <td><?= htmlspecialchars($row['observacion'] ?? '-') ?></td>
                <td>
                    <a href="editar_socio_asociacion.php?socio=<?= $row['socio_idsocio'] ?>&grupo=<?= $row['grupo_idgrupo'] ?>" 
                       class="btn btn-sm btn-warning">Editar</a>
                    <a href="eliminar_socio_asociacion.php?socio=<?= $row['socio_idsocio'] ?>&grupo=<?= $row['grupo_idgrupo'] ?>" 
                       class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de eliminar este registro?');">Eliminar</a>
                </td>
            </tr>
        <?php endwhile; 
    else: ?>
        <tr>
            <td colspan="9" class="text-center text-muted">No se encontraron registros.</td>
        </tr>
    <?php endif; ?>
</tbody>
    </table>
</div>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<?php $conn->close(); include '../../includes/footer.php'; ?>
