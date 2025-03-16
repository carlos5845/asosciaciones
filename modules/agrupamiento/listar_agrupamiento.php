<?php
include '../../includes/header.php';
include '../../includes/conexion.php';
session_start(); // Asegúrate de que esté al inicio para usar sesiones

// Número de registros por página
$registros_por_pagina = 10;

// Obtener el número de página
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$pagina_actual = max($pagina_actual, 1); // Asegura que no sea menor a 1

// Obtener la búsqueda si está definida
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Escapar la búsqueda para evitar inyección de SQL
$search = $conn->real_escape_string($search);

// Calcular el offset
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Consulta para filtrar por `cod_etiqueta` y `agrupamientocol`
$query = "SELECT idagrupamiento, cod_etiqueta, agrupamientocol 
          FROM agrupamiento 
          WHERE cod_etiqueta LIKE '%$search%' 
             OR agrupamientocol LIKE '%$search%' 
          LIMIT $offset, $registros_por_pagina";
$result = $conn->query($query);

// Contar total de registros para paginación
$query_total = "SELECT COUNT(*) as total 
                FROM agrupamiento 
                WHERE cod_etiqueta LIKE '%$search%' 
                   OR agrupamientocol LIKE '%$search%'";
$result_total = $conn->query($query_total);
$total_agrupamientos = $result_total->fetch_assoc()['total'];
$total_paginas = ceil($total_agrupamientos / $registros_por_pagina);

// Mostrar mensaje de confirmación si se pasó a través de la URL
if (isset($_GET['success']) && $_GET['success'] == 1) {
    echo '<div class="alert alert-success" role="alert">Agrupamiento eliminado con éxito.</div>';
} elseif (isset($_GET['error'])) {
    echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($_GET['error']) . '</div>';
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Agrupamientos</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        .card { max-width: 800px; margin: 0 auto; }
        #agrupamientocol { max-width: 100%; }
    </style>
</head>

       <div class="container mt-5">
          <div class="card shadow">
        <div class="card-header bg-primary text-white text-center">
            <h4><i class="fas fa-list"></i> Listado de Agrupamientos</h4>
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
                    <input type="text" class="form-control" placeholder="Buscar..." name="search" value="<?= htmlspecialchars($search) ?>">
                    <div class="input-group-append">
                        <button class="btn btn-info" type="submit"><i class="fas fa-search"></i> Buscar</button>
                    </div>
                </div>
            </form>

            <!-- Botón para registrar nuevo agrupamiento -->
            <a href="registrar_agrupamiento.php" class="btn btn-success mb-3">
                <i class="fas fa-plus"></i> Registrar Nuevo Agrupamiento
            </a>

            <!-- Tabla de agrupamientos -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Nº</th>
                            <th>Cod_etiqueta</th>
                            <th>Tipo</th>
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
                                    <td><?= htmlspecialchars($row['cod_etiqueta']) ?></td>
                                    <td><?= htmlspecialchars($row['agrupamientocol']) ?></td>
                                    <td>
                                        <a href="eliminar_agrupamiento.php?id=<?= $row['idagrupamiento'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este agrupamiento?')">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile;
                        else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">No se encontraron agrupamientos</td>
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
            $base_url = "listar_agrupamiento?" . http_build_query($query_params);
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
<!-- Cerrar la alerta después de 3 segundos -->
<script>
    setTimeout(() => {
        $(".alert").alert('close');
    }, 3000);
</script>

<?php
$conn->close();
include '../../includes/footer.php';
?>
