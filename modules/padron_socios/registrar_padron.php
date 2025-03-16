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
                    <h4><i class="fas fa-file-upload"></i> Registrar Padrón de Socios</h4>
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

                    <form action="procesar_padron.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="archivo_vigencia">Archivo Padrón de Socios (PDF):</label>
                            <input type="file" name="archivo_vigencia" class="form-control-file" accept=".pdf" required>
                            <small class="text-muted">Solo archivos en formato PDF.</small>
                        </div>

                        <div class="form-group">
                            <label for="grupo">Grupo:</label>
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
                        </div>

                        <!-- Botones de Guardar y Ver Lista -->
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Guardar Socio
                            </button>
                            <a href="listar_constitucion.php" class="btn btn-primary">
                                <i class="fas fa-list"></i> Ver Lista de Registrados
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


