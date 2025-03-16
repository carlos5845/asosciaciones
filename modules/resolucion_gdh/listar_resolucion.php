<?php
include '../../includes/header.php';
include '../../includes/conexion.php';

// Número de registros por página
$registros_por_pagina = 10;

// Obtener el número de página actual
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$pagina_actual = $pagina_actual > 0 ? $pagina_actual : 1;

// Obtener el criterio de búsqueda
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Calcular el offset para la paginación
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Construir la consulta con búsqueda en todas las columnas necesarias
$query = "SELECT rg.idresolucion_gdh, rg.num_resolucion, rg.fecha_emision, rg.archivo_gdh, 
                 g.nombre_grupo, g.etiqueta_grupo
          FROM resolucion_gdh rg
          JOIN grupo g ON rg.grupo_idgrupo = g.idgrupo
          WHERE rg.num_resolucion LIKE '%$search%' 
          OR g.nombre_grupo LIKE '%$search%' 
          OR g.etiqueta_grupo LIKE '%$search%'
          OR DATE_FORMAT(rg.fecha_emision, '%d/%m/%Y') LIKE '%$search%'
          OR rg.archivo_gdh LIKE '%$search%'
          LIMIT $offset, $registros_por_pagina";

$result = $conn->query($query);

// Consulta para contar el total de registros sin límite
$query_total = "SELECT COUNT(*) as total 
                FROM resolucion_gdh rg 
                JOIN grupo g ON rg.grupo_idgrupo = g.idgrupo 
                WHERE rg.num_resolucion LIKE '%$search%' 
                OR g.nombre_grupo LIKE '%$search%' 
                OR g.etiqueta_grupo LIKE '%$search%'
                OR DATE_FORMAT(rg.fecha_emision, '%d/%m/%Y') LIKE '%$search%'
                OR rg.archivo_gdh LIKE '%$search%'";
$result_total = $conn->query($query_total);
$total_resolucion = $result_total->fetch_assoc()['total'];
$total_paginas = ceil($total_resolucion / $registros_por_pagina);
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Resoluciones</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white text-center">
            <h4><i class="fas fa-list"></i> Listado de Resoluciones</h4>
        </div>
        <div class="card-body">
            <!-- Formulario de búsqueda -->
            <form method="GET" action="">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Buscar por resolución o grupo..." name="search" value="<?= htmlspecialchars($search) ?>">
                    <div class="input-group-append">
                        <button class="btn btn-info" type="submit"><i class="fas fa-search"></i> Buscar</button>
                    </div>
                </div>
            </form>

            <!-- Botón para registrar nueva resolución -->
            <a href="registrar_resolucion.php" class="btn btn-success mb-3">
                <i class="fas fa-plus"></i> Registrar Nueva Resolución
            </a>

            <!-- Tabla de resoluciones -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Nº</th>
                            <th>Código</th>
                            <th>Grupo</th>
                            <th>Resolución</th>
                            <th>Fecha de Emisión</th>
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
                                    <td><?= htmlspecialchars($row['num_resolucion']) ?></td>
                                    <td><?= htmlspecialchars(date("d/m/Y", strtotime($row['fecha_emision']))) ?></td>
                                    <td>
                                        <a href="../../uploads/resolucion/<?= htmlspecialchars($row['archivo_gdh']) ?>" target="_blank" class="btn btn-danger btn-sm">
                                            <i class="fas fa-file-pdf"></i> Ver Archivo
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile;
                        else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No se encontraron resoluciones.</td>
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

<!-- Bootstrap JS y FontAwesome -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>

<?php
$conn->close();
include '../../includes/footer.php';
?>
