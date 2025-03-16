<?php
// Verificar si los archivos existen antes de requerirlos
require_once(__DIR__ . "/../../includes/conexion.php");
require_once(__DIR__ . "/../../includes/header.php");

// Verificar conexión
if (!$conn) {
    die("Error: No se pudo conectar a la base de datos.");
}

// Variables para filtros (usando GET en lugar de POST)
$grupo_id = isset($_GET['grupo_id']) ? intval($_GET['grupo_id']) : '';
$busqueda = isset($_GET['busqueda']) ? $conn->real_escape_string($_GET['busqueda']) : '';

// Construcción dinámica de la condición SQL
$condiciones = [];

if (isset($_GET['filtrar_grupo']) && !empty($grupo_id)) {
    $condiciones[] = "s.idsocio IN (SELECT socio_idsocio FROM socio_asociacion WHERE grupo_idgrupo = $grupo_id)";
}

if (isset($_GET['filtrar_busqueda']) && !empty($busqueda)) {
    $condiciones[] = "(s.dni LIKE '%$busqueda%' OR s.nombre LIKE '%$busqueda%' OR s.apellido_pat LIKE '%$busqueda%' OR s.apellido_mat LIKE '%$busqueda%')";
}

// Unir condiciones con AND si hay más de una
$condicion_sql = !empty($condiciones) ? "WHERE " . implode(" AND ", $condiciones) : "";

// Número de registros por página
$registros_por_pagina = 5;

// Obtener el número de página actual
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$pagina_actual = max(1, $pagina_actual);

// Calcular el inicio de los registros para la consulta SQL
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Contar el total de registros (sin paginar)
$sql_total = "
SELECT COUNT(*) as total FROM (
    SELECT s.idsocio
    FROM socio s
    JOIN socio_asociacion sa ON s.idsocio = sa.socio_idsocio
    JOIN grupo g ON sa.grupo_idgrupo = g.idgrupo
    $condicion_sql
    GROUP BY s.idsocio
    HAVING COUNT(DISTINCT sa.grupo_idgrupo) > 1
) as subquery
";

$result_total = $conn->query($sql_total);
$total_registros = $result_total->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $registros_por_pagina);

// Consulta principal con paginación
$sql = "
SELECT 
s.idsocio,
s.dni,
CONCAT(s.nombre, ' ', s.apellido_pat, ' ', IFNULL(s.apellido_mat, '')) AS nombre_completo,
COUNT(DISTINCT sa.grupo_idgrupo) AS total_asociaciones,
GROUP_CONCAT(DISTINCT g.nombre_grupo ORDER BY g.nombre_grupo SEPARATOR '\n') AS asociaciones
FROM socio s
JOIN socio_asociacion sa ON s.idsocio = sa.socio_idsocio
JOIN grupo g ON sa.grupo_idgrupo = g.idgrupo
$condicion_sql
GROUP BY s.idsocio
HAVING COUNT(DISTINCT sa.grupo_idgrupo) > 1
LIMIT $registros_por_pagina OFFSET $offset;
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<body>
    <div class="container mt-4">
        <h2 class="text-center mb-4">Cruce de Socios por Grupo</h2>
        
        <form method="GET" class="mb-4">
            <input type="hidden" name="pagina" value="cruce_socios/cruce_socios"> <!-- Se mantiene en la página correcta -->

            <div class="row">
                <div class="col-md-4">
                    <label for="grupo_id" class="form-label">Seleccione un Grupo:</label>
                    <select name="grupo_id" id="grupo_id" class="form-control">
                        <option value="">-- Todos los Grupos --</option>
                        <?php
                        $grupos = $conn->query("SELECT idgrupo, nombre_grupo FROM grupo ORDER BY nombre_grupo");
                        while ($row = $grupos->fetch_assoc()) {
                            $selected = (isset($_GET['grupo_id']) && $_GET['grupo_id'] == $row['idgrupo']) ? 'selected' : '';
                            echo "<option value='" . $row['idgrupo'] . "' $selected>" . $row['nombre_grupo'] . "</option>";
                        }
                        ?>
                    </select>
                    <button type="submit" name="filtrar_grupo" class="btn btn-primary mt-2 w-100">Filtrar por Grupo</button>
                </div>

                <div class="col-md-4">
                    <label for="busqueda" class="form-label">Buscar por DNI o Nombre:</label>
                    <input type="text" name="busqueda" id="busqueda" class="form-control" 
                    value="<?= isset($_GET['busqueda']) ? htmlspecialchars($_GET['busqueda']) : '' ?>" 
                    placeholder="Ingrese DNI o Nombre">
                    <button type="submit" name="filtrar_busqueda" class="btn btn-success mt-2 w-100">Buscar</button>
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <a href="index.php?pagina=cruce_socios/cruce_socios" class="btn btn-danger w-100">Restablecer</a>
                </div>
            </div>
        </form>

        
        <!-- Tabla de resultados -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered text-center">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>DNI</th>
                        <th>Nombre Completo</th>
                        <th>Total Asociaciones</th>
                        <th>Asociaciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['idsocio']; ?></td>
                            <td><?php echo $row['dni']; ?></td>
                            <td><?php echo $row['nombre_completo']; ?></td>
                            <td><?php echo $row['total_asociaciones']; ?></td>
                            <td class="text-left"><?php echo nl2br($row['asociaciones']); ?></td>
                        </tr>
                    <?php } ?> 
                </tbody>
            </table>

            <!-- Paginación -->
            <?php if ($total_paginas > 1): ?>
                <nav>
                    <ul class="pagination justify-content-center">
                        <?php
                        $query_params = $_GET;
                        unset($query_params['pagina']);
                        $base_url = "cruce_socios.php?" . http_build_query($query_params);
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
</body>
</html>
