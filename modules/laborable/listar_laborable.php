<?php
session_start(); // Asegúrate de que esté al inicio para usar sesiones
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

// Consulta para obtener los grupos y los días laborables relacionados
$query = "
    SELECT 
        g.idgrupo,
        g.nombre_grupo,
        g.etiqueta_grupo, 
        MAX(CASE WHEN dl.dia = 'Lunes' THEN 'Sí' ELSE '' END) AS Lunes,
        MAX(CASE WHEN dl.dia = 'Martes' THEN 'Sí' ELSE '' END) AS Martes,
        MAX(CASE WHEN dl.dia = 'Miércoles' THEN 'Sí' ELSE '' END) AS Miércoles,
        MAX(CASE WHEN dl.dia = 'Jueves' THEN 'Sí' ELSE '' END) AS Jueves,
        MAX(CASE WHEN dl.dia = 'Viernes' THEN 'Sí' ELSE '' END) AS Viernes,
        MAX(CASE WHEN dl.dia = 'Sábado' THEN 'Sí' ELSE '' END) AS Sábado,
        MAX(CASE WHEN dl.dia = 'Domingo' THEN 'Sí' ELSE '' END) AS Domingo
    FROM 
        dia_laborable_has_grupo dlhg
    JOIN 
        grupo g ON dlhg.grupo_idgrupo = g.idgrupo
    JOIN 
        dia_laborable dl ON dl.iddia_laborable = dlhg.dia_laborable_iddia_laborable
    WHERE 
        (dl.dia LIKE '%$search%' OR g.nombre_grupo LIKE '%$search%')
    GROUP BY 
        g.idgrupo
    LIMIT $offset, $registros_por_pagina
";

$result = $conn->query($query);

// Contar total de registros para la paginación
$query_total = "
    SELECT COUNT(DISTINCT g.idgrupo) as total 
    FROM dia_laborable_has_grupo dlhg
    JOIN grupo g ON dlhg.grupo_idgrupo = g.idgrupo
    JOIN dia_laborable dl ON dl.iddia_laborable = dlhg.dia_laborable_iddia_laborable
    WHERE (dl.dia LIKE '%$search%' OR g.nombre_grupo LIKE '%$search%')
";
$result_total = $conn->query($query_total);
$total_laborables = $result_total->fetch_assoc()['total'];
$total_paginas = ceil($total_laborables / $registros_por_pagina);
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Días Laborables y Grupos</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Desvanecer las alertas después de 5 segundos
            setTimeout(function() {
                $(".alert").fadeOut("slow", function() {
                    $(this).remove();
                });
            }, 5000);

            // Quitar los parámetros de éxito/error de la URL después de mostrar la alerta
            if (window.history.replaceState) {
                let url = new URL(window.location.href);
                url.searchParams.delete("success");
                url.searchParams.delete("error");
                window.history.replaceState({}, document.title, url.href);
            }
        });
    </script>
</head>

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white text-center">
            <h4><i class="fas fa-calendar-check"></i> Listado de Días Laborables y Grupos</h4>
        </div>
        <div class="card-body">

            <!-- Mostrar mensajes de confirmación o error -->
<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>¡Éxito!</strong> 
        <?php 
            if ($_GET['success'] == 1) {
                echo "Relación registrada correctamente.";
            } elseif ($_GET['success'] == 'deleted') {
                echo "Relación eliminada correctamente.";
            }
        ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php elseif (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error:</strong> <?= htmlspecialchars($_GET['error']) ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>


            <!-- Formulario de búsqueda -->
            <form method="GET" action="">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Buscar por día o grupo..." name="search" value="<?= htmlspecialchars($search) ?>">
                    <div class="input-group-append">
                        <button class="btn btn-info" type="submit"><i class="fas fa-search"></i> Buscar</button>
                    </div>
                </div>
            </form>

            <!-- Botón para registrar nuevo padrón -->
            <a href="http://localhost/asociaciones/modules/laborable/registrar_laborable.php" class="btn btn-success mb-3">
                <i class="fas fa-plus"></i> Registrar Día Laborable
            </a>

            <!-- Tabla de días laborables y grupos -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Nº</th>
                            <th>Código</th>
                            <th>Asociación</th>
                            <th>Lunes</th>
                            <th>Martes</th>
                            <th>Miércoles</th>
                            <th>Jueves</th>
                            <th>Viernes</th>
                            <th>Sábado</th>
                            <th>Domingo</th>
                            <th>Acciones</th> <!-- Columna para el botón eliminar -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $numerador = $offset + 1;
                        if ($result->num_rows > 0):
                            while ($row = $result->fetch_assoc()):
                                echo "<tr>";
                                echo "<td>" . $numerador++ . "</td>";
                                echo "<td>" . htmlspecialchars($row['etiqueta_grupo']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['nombre_grupo']) . "</td>";
                                echo "<td>" . ($row['Lunes'] ? 'Sí' : '') . "</td>";
                                echo "<td>" . ($row['Martes'] ? 'Sí' : '') . "</td>";
                                echo "<td>" . ($row['Miércoles'] ? 'Sí' : '') . "</td>";
                                echo "<td>" . ($row['Jueves'] ? 'Sí' : '') . "</td>";
                                echo "<td>" . ($row['Viernes'] ? 'Sí' : '') . "</td>";
                                echo "<td>" . ($row['Sábado'] ? 'Sí' : '') . "</td>";
                                echo "<td>" . ($row['Domingo'] ? 'Sí' : '') . "</td>";
                                echo "<td>
                                        <a href='eliminar_laborable.php?id=" . $row['idgrupo'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"¿Estás seguro de eliminar este grupo?\")'>
                                            <i class='fas fa-trash-alt'></i> Eliminar
                                        </a>
                                      </td>";
                                echo "</tr>";
                            endwhile;
                        else: ?>
                            <tr>
                                <td colspan="10" class="text-center text-muted">No se encontraron registros</td>
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

<?php include '../../includes/footer.php'; ?>
