<?php
include '../../includes/header.php';
include '../../includes/conexion.php';

// Número de registros por página
$registros_por_pagina = 10;

// Obtener el número de página
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$pagina_actual = $pagina_actual > 0 ? $pagina_actual : 1;

// Obtener la búsqueda si está definida
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Calcular el offset
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Si hay búsqueda, realizar filtro, si no, mostrar todo
$query = "SELECT v.idvigencia_poder, v.partida_registral, v.archivo_vigencia, g.nombre_grupo, g.etiqueta_grupo
          FROM vigencia_poder v
          JOIN grupo g ON v.grupo_idgrupo = g.idgrupo
          WHERE v.partida_registral LIKE '%$search%' OR g.nombre_grupo LIKE '%$search%'
          LIMIT $offset, $registros_por_pagina";
$result = $conn->query($query);

// Contar total de registros para paginación
$query_total = "SELECT COUNT(*) as total FROM vigencia_poder v JOIN grupo g ON v.grupo_idgrupo = g.idgrupo WHERE v.partida_registral LIKE '%$search%' OR g.nombre_grupo LIKE '%$search%'";
$result_total = $conn->query($query_total);
$total_vigencias = $result_total->fetch_assoc()['total'];
$total_paginas = ceil($total_vigencias / $registros_por_pagina);
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Vigencias de Poder</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white text-center">
            <h4><i class="fas fa-list"></i> Listado de Vigencias de Poder</h4>
        </div>
        <div class="card-body">
            <!-- Formulario de búsqueda -->
            <form method="GET" action="">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Buscar por grupo o partida registral..." name="search" value="<?= htmlspecialchars($search) ?>">
                    <div class="input-group-append">
                        <button class="btn btn-info" type="submit"><i class="fas fa-search"></i> Buscar</button>
                    </div>
                </div>
            </form>

            <!-- Botón para registrar nueva vigencia de poder -->
            <a href="http://localhost/asociaciones/modules/vigencia_poder/form.php" class="btn btn-success mb-3">
                <i class="fas fa-plus"></i> Registrar Nueva Vigencia
            </a>

            <!-- Tabla de vigencias -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Nº</th>
                            <th>Codigo</th>
                            <th>Grupo</th>
                            <th>Partida Registral</th>
                            <th>Archivo</th>
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
                                    <td><?= htmlspecialchars($row['partida_registral']) ?></td>
                                    <td>
                                        <a href="../../uploads/vigencia/<?= htmlspecialchars($row['archivo_vigencia']) ?>" target="_blank" class="btn btn-danger btn-sm">
                                            <i class="fas fa-file-pdf"></i> Ver Archivo
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile;
                        else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">No se encontraron vigencias de poder.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <?php if ($total_paginas > 1): ?>
                <nav>
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= ($pagina_actual == 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?pagina=1&search=<?= htmlspecialchars($search) ?>">Primera</a>
                        </li>
                        <li class="page-item <?= ($pagina_actual == 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?pagina=<?= $pagina_actual - 1 ?>&search=<?= htmlspecialchars($search) ?>">Anterior</a>
                        </li>

                        <li class="page-item active">
                            <span class="page-link"><?= $pagina_actual ?></span>
                        </li>

                        <li class="page-item <?= ($pagina_actual == $total_paginas) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?pagina=<?= $pagina_actual + 1 ?>&search=<?= htmlspecialchars($search) ?>">Siguiente</a>
                        </li>
                        <li class="page-item <?= ($pagina_actual == $total_paginas) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?pagina=<?= $total_paginas ?>&search=<?= htmlspecialchars($search) ?>">Última</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php
$conn->close();
include '../../includes/footer.php';
?>
