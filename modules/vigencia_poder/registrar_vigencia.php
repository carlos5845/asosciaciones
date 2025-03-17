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
    <div class="row justify-content-center"> <!-- Cambio: Se añadió 'justify-content-center' para centrar -->
        <div class="col-12"> <!-- Cambio: Se reemplazó 'col-md-8' por 'col-12' para ocupar todo el ancho -->
            <div class="card shadow w-100"> <!-- Cambio: Se agregó 'w-100' para que la tarjeta ocupe todo el ancho -->
                <div class="card-header bg-primary text-white text-center">
                    <h4><i class="fas fa-file-upload"></i> Registrar Vigencia de Poder</h4>
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

                    <form action="procesar_vigencia.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="partida_registral">Partida Registral:</label>
                            <input type="text" name="partida_registral" maxlength="8" pattern="\d{8}"
                                title="Debe ser un número de 8 dígitos"
                                class="form-control w-100" required> <!-- Cambio: Se agregó 'w-100' -->
                        </div>

                        <div class="form-group">
                            <label for="archivo_vigencia">Archivo de Vigencia (PDF):</label>
                            <input type="file" name="archivo_vigencia" class="form-control-file"
                                accept=".pdf" required>
                            <small class="text-muted">Solo archivos en formato PDF.</small>
                        </div>

                        <div class="form-group">
                            <label for="grupo">Grupo:</label>
                            <div class="d-flex">
                                <select name="grupo_id" class="form-control w-100" required> <!-- Cambio: Se agregó 'w-100' -->
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
                                <a href="http://localhost/asociaciones/modules/asociaciones/registrar_grupo.php"
                                    class="btn btn-primary ml-2"> <!-- Cambio: Se añadió 'ml-2' para separación -->
                                    <i class="fas fa-plus"></i>
                                </a>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>