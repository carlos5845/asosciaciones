<?php
session_start();
include '../../includes/header.php';
include '../../includes/conexion.php';

// Número de registros por página
$registros_por_pagina = 5;

// Obtener número de página
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$pagina_actual = max(1, $pagina_actual); // Evitar valores negativos

// Obtener la búsqueda si está definida
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Calcular el offset
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Si hay búsqueda, realizar filtro, si no, mostrar todo
$query = "SELECT idcategoria, tipo FROM categoria WHERE tipo LIKE '%$search%' LIMIT $offset, $registros_por_pagina";
$result = $conn->query($query);

// Contar total de registros para paginación
$query_total = "SELECT COUNT(*) as total FROM categoria WHERE tipo LIKE '%$search%'";
$result_total = $conn->query($query_total);
$total_categorias = $result_total->fetch_assoc()['total'];
$total_paginas = ceil($total_categorias / $registros_por_pagina);
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Categorías</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link href="../../css/estilos.css" rel="stylesheet"> <!-- Referencia al archivo CSS -->
</head>

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white text-center">
            <h4><i class="fas fa-list"></i> Listado de Categorías</h4>
        </div>

        <div class="card-body">     
         <!-- Incluir el sistema de alertas -->
         <?php if (isset($_SESSION['mensaje'])): ?>
              <div class="alert alert-<?= htmlspecialchars($_SESSION['tipo_mensaje']) ?> alert-dismissible fade show" role="alert">
           <strong><?= htmlspecialchars($_SESSION['mensaje']) ?></strong>
           <button type="button" class="close" data-dismiss="alert" aria-label="Close">
             <span aria-hidden="true">&times;</span>
           </button>
        </div>
          <?php 
         // Limpiar los mensajes después de mostrarlos
          unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);
            ?>
         <?php endif; ?>
           
            <!-- Formulario de búsqueda -->
            <form method="GET" action="">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Buscar por tipo de categoría..." name="search" value="<?= htmlspecialchars($search) ?>">
                    <div class="input-group-append">
                        <button class="btn btn-info" type="submit"><i class="fas fa-search"></i> Buscar</button>
                    </div>
                </div>
            </form>

            <!-- Botón para registrar nueva categoría -->
            <a href="registrar_categoria.php" class="btn btn-success mb-3">
                <i class="fas fa-plus"></i> Registrar Nueva Categoría
            </a>

            <!-- Tabla de categorías -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
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
                                    <td><?= htmlspecialchars($row['tipo']) ?></td>
                                    <td>
                                        <!-- Enlace para eliminar -->
                                        <a href="eliminar_categoria.php?id=<?= $row['idcategoria'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar esta categoría?')">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile;
                        else: ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted">No se encontraron categorías</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Paginación -->
<?php if ($total_paginas > 1): ?>
    <nav>
        <ul class="pagination justify-content-center">
            <?php
            // Eliminar el parámetro 'pagina' de los parámetros GET para no duplicarlo
            $query_params = $_GET;
            unset($query_params['pagina']);
            // Crear la URL base para la paginación, asegurando que se mantengan los parámetros de búsqueda
            $base_url = "listar_categoria.php?" . http_build_query($query_params);
            ?>

            <!-- Primera página -->
            <li class="page-item <?= ($pagina_actual == 1) ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= $base_url ?>&pagina=1">Primera</a>
            </li>

            <!-- Página anterior -->
            <li class="page-item <?= ($pagina_actual == 1) ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= $base_url ?>&pagina=<?= $pagina_actual - 1 ?>">Anterior</a>
            </li>

            <!-- Página actual -->
            <li class="page-item active">
                <span class="page-link"><?= $pagina_actual ?></span>
            </li>

            <!-- Página siguiente -->
            <li class="page-item <?= ($pagina_actual == $total_paginas) ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= $base_url ?>&pagina=<?= $pagina_actual + 1 ?>">Siguiente</a>
            </li>

            <!-- Última página -->
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

<!-- Bootstrap JS y FontAwesome -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>

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
