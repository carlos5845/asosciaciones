<?php
include '../../includes/header.php';
include '../../includes/conexion.php';
session_start(); // Asegúrate de que esté al inicio para usar sesiones

// Número de registros por página
$registros_por_pagina = 5;

// Obtener el número de página
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$pagina_actual = $pagina_actual > 0 ? $pagina_actual : 1;

// Obtener la búsqueda si está definida
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Calcular el offset
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Si hay búsqueda, realizar filtro, si no, mostrar todo
$query = "SELECT ac.idacta_constitucion, ac.fecha_fundacion, ac.archivo_acta, g.etiqueta_grupo, g.nombre_grupo
          FROM acta_constitucion ac
          JOIN grupo g ON ac.grupo_idgrupo = g.idgrupo
          WHERE ac.archivo_acta LIKE '%$search%' 
          OR g.nombre_grupo LIKE '%$search%' 
          OR g.etiqueta_grupo LIKE '%$search%'
          LIMIT $offset, $registros_por_pagina";
$result = $conn->query($query);

// Contar total de registros para paginación
$query_total = "SELECT COUNT(*) as total 
                FROM acta_constitucion ac 
                JOIN grupo g ON ac.grupo_idgrupo = g.idgrupo 
                WHERE ac.archivo_acta LIKE '%$search%' 
                OR g.nombre_grupo LIKE '%$search%' 
                OR g.etiqueta_grupo LIKE '%$search%'";
$result_total = $conn->query($query_total);
$total_actas = $result_total->fetch_assoc()['total'];
$total_paginas = ceil($total_actas / $registros_por_pagina);
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Actas de Constitución</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>

<div class="container mt-5">
    <div class="card shadow w-100">
        <div class="card-header bg-primary text-white text-center">
            <h4><i class="fas fa-list"></i> Listado de Actas de Constitución</h4>
        </div>
        <div class="card-body">

            <!-- Incluir el sistema de alertas -->
            <?php if (isset($_SESSION['mensaje'])): ?>
                <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?> alert-dismissible fade show" role="alert">
                    <?= $_SESSION['mensaje'] ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?>
            <?php endif; ?>

            <!-- Formulario de búsqueda -->
            <form method="GET" action="">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Buscar por grupo o archivo..." name="search" value="<?= htmlspecialchars($search) ?>">
                    <div class="input-group-append">
                        <button class="btn btn-info" type="submit"><i class="fas fa-search"></i> Buscar</button>
                    </div>
                </div>
            </form>

            <!-- Botón para registrar nuevo acta -->
            <a href="registrar_constitucion.php" class="btn btn-success mb-3">
                <i class="fas fa-plus"></i> Registrar Nueva Acta
            </a>

            <!-- Tabla de actas de constitución -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Nº</th>
                            <th>Codigo</th>
                            <th>Grupo</th>
                            <th>Fecha de Fundación</th>
                            <th>Archivo</th>
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
                                    <td><?= htmlspecialchars($row['fecha_fundacion']) ?></td>
                                    <td>
                                        <a href="../../uploads/constitucion/<?= htmlspecialchars($row['archivo_acta']) ?>" target="_blank" class="btn btn-danger btn-sm">
                                            <i class="fas fa-file-pdf"></i> Ver Acta
                                        </a>
                                    </td>
                                    <td>
                                        <!-- Botón para eliminar acta -->
                                        <a href="eliminar_constitucion.php?idacta=<?= $row['idacta_constitucion'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar esta acta?');">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile;
                        else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No se encontraron registros de actas.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>


                <!-- Paginación -->
                <?php if ($total_paginas > 1): ?>
                    <nav>
                        <ul class="pagination justify-content-center">
                            <?php
                            $query_params = $_GET;
                            unset($query_params['pagina']);
                            $base_url = "listar_constitucion.php?" . http_build_query($query_params);
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

        </div>
    </div>
</div>

<!-- Agregar los scripts de Bootstrap -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
<script>
    // Cerrar la alerta después de 3 segundos
    setTimeout(() => {
        $(".alert").alert('close');
    }, 3000);
</script>

<?php
$conn->close();
include '../../includes/footer.php';
?>