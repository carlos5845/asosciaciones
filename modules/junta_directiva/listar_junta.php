<?php
include '../../includes/header.php';
include '../../includes/conexion.php';

// Variables para la paginación
$registros_por_pagina = 10;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Búsqueda
$busqueda = isset($_GET['busqueda']) ? $conn->real_escape_string($_GET['busqueda']) : '';
$condicion_busqueda = $busqueda ? "AND (s.dni LIKE '%$busqueda%' OR s.nombre LIKE '%$busqueda%' OR s.apellido_pat LIKE '%$busqueda%' OR s.apellido_mat LIKE '%$busqueda%' OR c.tipo_cargo LIKE '%$busqueda%' OR g.nombre_grupo LIKE '%$busqueda%')" : '';

// Consulta con búsqueda y paginación
$query = "SELECT 
            j.idjunta_directiva, 
            j.fecha_inicio, 
            j.fecha_fin, 
            g.nombre_grupo, 
            s.nombre AS nombre_socio,
            s.dni AS dni_socio, 
            s.apellido_pat AS apellido_paterno, 
            s.apellido_mat AS apellido_materno, 
            c.tipo_cargo AS nombre_cargo, 
            j.celular 
          FROM junta_directiva j
          INNER JOIN socio_asociacion ga 
              ON j.socio_asociacion_socio_idsocio = ga.socio_idsocio 
              AND j.socio_asociacion_grupo_idgrupo = ga.grupo_idgrupo
          INNER JOIN grupo g ON ga.grupo_idgrupo = g.idgrupo
          INNER JOIN socio s ON ga.socio_idsocio = s.idsocio
          INNER JOIN cargo c ON j.cargo_idcargo = c.idcargo
          WHERE 1=1 $condicion_busqueda
          ORDER BY s.nombre, g.nombre_grupo, c.tipo_cargo
          LIMIT $registros_por_pagina OFFSET $offset";

$result = $conn->query($query);

// Consulta para contar total de registros
$query_total = "SELECT COUNT(*) AS total FROM junta_directiva j
                INNER JOIN socio_asociacion ga 
                  ON j.socio_asociacion_socio_idsocio = ga.socio_idsocio 
                  AND j.socio_asociacion_grupo_idgrupo = ga.grupo_idgrupo
                INNER JOIN grupo g ON ga.grupo_idgrupo = g.idgrupo
                INNER JOIN socio s ON ga.socio_idsocio = s.idsocio
                INNER JOIN cargo c ON j.cargo_idcargo = c.idcargo
                WHERE 1=1 $condicion_busqueda";
$result_total = $conn->query($query_total);
$total_registros = $result_total->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $registros_por_pagina);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Junta Directiva</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/estilos.css">
</head>
<body>
<div class="container mt-4">
    <h2 class="text-center">Lista de Junta Directiva</h2>
    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="busqueda" class="form-control" placeholder="Buscar..." value="<?= htmlspecialchars($busqueda) ?>">
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit">Buscar</button>
            </div>
        </div>
    </form>
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>N°</th>
                    <th>Grupo</th>
                    <th>DNI</th>
                    <th>Nombres</th>
                    <th>Apellido Paterno</th>
                    <th>Apellido Materno</th>
                    <th>Cargo</th>
                    <th>Celular</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $contador = $offset + 1;
                if ($result && $result->num_rows > 0): 
                    while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $contador++ ?></td>
                            <td><?= htmlspecialchars($row["nombre_grupo"]) ?></td>
                            <td><?= htmlspecialchars($row["dni_socio"]) ?></td>
                            <td><?= htmlspecialchars($row["nombre_socio"]) ?></td>
                            <td><?= htmlspecialchars($row["apellido_paterno"]) ?></td>
                            <td><?= htmlspecialchars($row["apellido_materno"]) ?></td>
                            <td><?= htmlspecialchars($row["nombre_cargo"]) ?></td>
                            <td><?= htmlspecialchars($row["celular"]) ?></td>
                            <td><?= htmlspecialchars($row["fecha_inicio"]) ?></td>
                            <td><?= htmlspecialchars($row["fecha_fin"]) ?></td>
                        </tr>
                    <?php endwhile; 
                else: ?>
                    <tr>
                        <td colspan="10" class="text-center">No hay datos en la junta directiva.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <nav>
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <li class="page-item <?= $i == $pagina_actual ? 'active' : '' ?>">
                    <a class="page-link" href="?pagina=<?= $i ?>&busqueda=<?= htmlspecialchars($busqueda) ?>"> <?= $i ?> </a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<?php include '../../includes/footer.php'; ?>
</body>
</html>
<?php
$conn->close();
?>
