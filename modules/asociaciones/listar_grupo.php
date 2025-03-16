<?php
include '../../includes/header.php';
include '../../includes/conexion.php';

$registros_por_pagina = 5;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$pagina_actual = max(1, $pagina_actual);

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$offset = ($pagina_actual - 1) * $registros_por_pagina;

$query = "SELECT g.idgrupo, g.nombre_grupo, g.etiqueta_grupo, g.ubicacion, a.agrupamientocol, g.estado
          FROM grupo g
          JOIN agrupamiento a ON g.agrupamiento_idagrupamiento = a.idagrupamiento
          WHERE g.nombre_grupo LIKE '%$search%' 
             OR a.agrupamientocol LIKE '%$search%' 
             OR g.etiqueta_grupo LIKE '%$search%' 
             OR g.estado LIKE '%$search%'
          LIMIT $offset, $registros_por_pagina";

$result = $conn->query($query);

$query_total = "SELECT COUNT(*) as total 
                FROM grupo g 
                JOIN agrupamiento a ON g.agrupamiento_idagrupamiento = a.idagrupamiento
                WHERE g.nombre_grupo LIKE '%$search%' 
                   OR a.agrupamientocol LIKE '%$search%' 
                   OR g.etiqueta_grupo LIKE '%$search%' 
                   OR g.estado LIKE '%$search%'";

$result_total = $conn->query($query_total);
$total_grupos = $result_total->fetch_assoc()['total'];
$total_paginas = ceil($total_grupos / $registros_por_pagina);
?>

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white text-center">
            <h4><i class="fas fa-users"></i> Listado de Grupos</h4>
        </div>
        <div class="card-body">

            <form method="GET" action="">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Buscar..." name="search" value="<?= htmlspecialchars($search) ?>">
                    <div class="input-group-append">
                        <button class="btn btn-info" type="submit"><i class="fas fa-search"></i> Buscar</button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Nº</th>
                            <th>Codigo</th>
                            <th>Nombre del Grupo</th>
                            <th>Ubicación</th>
                            <th>Agrupamiento</th>
                            <th>Estado</th>
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
                                    <td><?= htmlspecialchars($row['etiqueta_grupo']) ?></td>
                                    <td><?= htmlspecialchars($row['nombre_grupo']) ?></td>
                                    <td><?= htmlspecialchars($row['ubicacion']) ?></td>
                                    <td><?= htmlspecialchars($row['agrupamientocol']) ?></td>
                                    <td>
                                        <span class="badge badge-<?= $row['estado'] == 'Activo' ? 'success' : 'danger' ?>">
                                            <?= htmlspecialchars($row['estado']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="eliminar_grupo.php?id=<?= $row['idgrupo'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este grupo?')">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile;
                        else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">No se encontraron grupos</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_paginas > 1): ?>
                <nav>
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= ($pagina_actual == 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?pagina=1">Primera</a>
                        </li>
                        <li class="page-item <?= ($pagina_actual == 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?pagina=<?= $pagina_actual - 1 ?>">Anterior</a>
                        </li>
                        <li class="page-item active">
                            <span class="page-link"><?= $pagina_actual ?></span>
                        </li>
                        <li class="page-item <?= ($pagina_actual == $total_paginas) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?pagina=<?= $pagina_actual + 1 ?>">Siguiente</a>
                        </li>
                        <li class="page-item <?= ($pagina_actual == $total_paginas) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?pagina=<?= $total_paginas ?>">Última</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php $conn->close(); ?>
