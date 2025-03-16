<?php
// Verificar si los archivos existen antes de requerirlos
require_once(__DIR__ . "/../../includes/conexion.php");
require_once(__DIR__ . "/../../includes/header.php");

// Verificar conexión
if (!$conn) {
    die("Error: No se pudo conectar a la base de datos.");
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4><i class="fas fa-file-upload"></i> Registrar Resolución GDH</h4>
                </div>
                <div class="card-body">
                    
                    <!-- Mensajes de alerta -->
                    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>¡Éxito!</strong> Registro guardado correctamente.
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

                    <form action="procesar_resolucion.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="num_resolucion">Número de Resolución:</label>
                            <input type="text" name="num_resolucion" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="fecha_emision">Fecha de Emisión:</label>
                            <input type="date" name="fecha_emision" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="archivo_gdh">Archivo de Resolución GDH (PDF):</label>
                            <input type="file" name="archivo_gdh" class="form-control-file" accept=".pdf" required>
                            <small class="text-muted">Solo archivos en formato PDF.</small>
                        </div>

                        <div class="form-group">
                            <label for="grupo">Grupo:</label>
                            <div class="d-flex">
                            <select name="grupo_id" class="form-control" required>
                                <option value="">Selecciona un grupo</option>
                                <?php
                                $query = "SELECT idgrupo, nombre_grupo FROM grupo";
                                $result = $conn->query($query);
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value='{$row['idgrupo']}'>{$row['nombre_grupo']}</option>";
                                    }
                                } else {
                                    echo "<option disabled>No hay grupos disponibles</option>";
                                }
                                ?>
                            </select>
                             <a href="http://localhost/asociaciones/modules/asociaciones/registrar_grupo.php" class="btn btn-primary">
                                    <i class="fas fa-plus"></i>
                                </a>
                             </div>
                        </div>

                        <!-- Botones de Guardar y Ver Lista -->
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Guardar Resolucion
                            </button>
                            <a href="http://localhost/asociaciones/modules/resolucion_gdh/listar_resolucion.php" class="btn btn-primary">
                                <i class="fas fa-list"></i> Ver Lista
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
