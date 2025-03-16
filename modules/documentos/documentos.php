<?php
// Verificar si los archivos existen antes de requerirlos
require_once(__DIR__ . "/../../includes/conexion.php");
require_once(__DIR__ . "/../../includes/header.php");

// Verificar conexión
if (!$conn) {
    die("Error: No se pudo conectar a la base de datos.");
}

// Definir cuántos registros por página
$registros_por_pagina = 10;

// Obtener el número de página actual (si se ha establecido)
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$pagina_actual = $pagina_actual > 0 ? $pagina_actual : 1; // Asegurar que la página actual sea positiva

// Obtener el término de búsqueda, si se ha proporcionado
$buscar = isset($_GET['buscar']) ? $_GET['buscar'] : '';

// Calcular el límite y el desplazamiento para la consulta SQL
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Ajustar la consulta para incluir el filtro de búsqueda
$query = "SELECT g.idgrupo, 
g.nombre_grupo,
g.etiqueta_grupo, 
v.partida_registral, 
v.archivo_vigencia, 
a.fecha_fundacion, a.archivo_acta
FROM grupo g
LEFT JOIN vigencia_poder v ON g.idgrupo = v.grupo_idgrupo
LEFT JOIN acta_constitucion a ON g.idgrupo = a.grupo_idgrupo
WHERE g.nombre_grupo LIKE '%$buscar%' OR v.partida_registral LIKE '%$buscar%'
LIMIT $offset, $registros_por_pagina";

$result = $conn->query($query);

// Verificar si la consulta se ejecutó correctamente
if (!$result) {
    die("Error en la consulta: " . $conn->error);
}

// Contar el total de registros filtrados para la paginación
$query_total = "SELECT COUNT(*) as total 
FROM grupo g 
LEFT JOIN vigencia_poder v ON g.idgrupo = v.grupo_idgrupo
LEFT JOIN acta_constitucion a ON g.idgrupo = a.grupo_idgrupo
WHERE g.nombre_grupo LIKE '%$buscar%' OR v.partida_registral LIKE '%$buscar%'";
$result_total = $conn->query($query_total);
$row_total = $result_total->fetch_assoc();
$total_asociaciones = $row_total['total'];

// Calcular el número total de páginas
$total_paginas = ceil($total_asociaciones / $registros_por_pagina);
?>

<div class="container mt-5">
    <h2>Listado de Asociaciones con sus Archivos</h2>

    <!-- Formulario de búsqueda -->
    <form method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-4">
                <input type="text" name="buscar" class="form-control" placeholder="Buscar por nombre de grupo o partida" value="<?= isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : '' ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Buscar</button>
            </div>
        </div>
    </form>

    <!-- Botones para registrar nueva vigencia de poder o acta de constitución -->
    <a href="" class="btn btn-success mb-3">Registrar Vigencia</a>
    <a href="" class="btn btn-success mb-3">Registrar Acta</a>
    <a href="" class="btn btn-success mb-3">Registrar Padron</a>
    <a href="" class="btn btn-success mb-3">Registrar Resolucion</a>

    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Codigo</th>
                <th>Grupo</th>
                <th>Partida Registral</th>
                <th>Fecha fundación</th>
                <th>Archivo Acta</th>
                <th>Archivo Vigencia</th>
                <th>Archivo Padron</th>
                <th>Archivo Resolucion</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $counter = $offset + 1; // Inicializar contador para la numeración automática
            if ($result->num_rows > 0): 
                while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $counter++ ?></td>
                        <td><?= htmlspecialchars($row['etiqueta_grupo']) ?></td>
                        <td><?= htmlspecialchars($row['nombre_grupo']) ?></td>
                        <td><?= htmlspecialchars($row['partida_registral']) ?></td>
                        <td><?= htmlspecialchars($row['fecha_fundacion']) ?></td>
                        <td>
                            <?php if ($row['archivo_vigencia']): ?>
                                <a href="../../uploads/vigencia/<?= htmlspecialchars($row['archivo_vigencia']) ?>" target="_blank" class="btn btn-info btn-sm">Ver</a>
                            <?php else: ?>
                                No disponible
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($row['archivo_acta']): ?>
                                <a href="../../uploads/constitucion/<?= htmlspecialchars($row['archivo_acta']) ?>" target="_blank" class="btn btn-info btn-sm">Ver</a>
                            <?php else: ?>
                                No disponible
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($row['archivo_vigencia']): ?>
                                <a href="../../uploads/vigencia/<?= htmlspecialchars($row['archivo_vigencia']) ?>" target="_blank" class="btn btn-info btn-sm">Ver</a>
                            <?php else: ?>
                                No disponible
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($row['archivo_acta']): ?>
                                <a href="../../uploads/constitucion/<?= htmlspecialchars($row['archivo_acta']) ?>" target="_blank" class="btn btn-info btn-sm">Ver</a>
                            <?php else: ?>
                                No disponible
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">No se encontraron asociaciones.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Paginación -->
    <nav aria-label="Paginación">
        <ul class="pagination">
            <li class="page-item <?= $pagina_actual == 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="?pagina=1&buscar=<?= urlencode($buscar) ?>">Primera</a>
            </li>
            <li class="page-item <?= $pagina_actual == 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="?pagina=<?= $pagina_actual - 1 ?>&buscar=<?= urlencode($buscar) ?>">Anterior</a>
            </li>

            <!-- Mostrar el número de página actual -->
            <li class="page-item active">
                <span class="page-link"><?= $pagina_actual ?></span>
            </li>

            <li class="page-item <?= $pagina_actual == $total_paginas ? 'disabled' : '' ?>">
                <a class="page-link" href="?pagina=<?= $pagina_actual + 1 ?>&buscar=<?= urlencode($buscar) ?>">Siguiente</a>
            </li>
            <li class="page-item <?= $pagina_actual == $total_paginas ? 'disabled' : '' ?>">
                <a class="page-link" href="?pagina=<?= $total_paginas ?>&buscar=<?= urlencode($buscar) ?>">Última</a>
            </li>
        </ul>
    </nav>

</div>

<?php
$conn->close();
?>
