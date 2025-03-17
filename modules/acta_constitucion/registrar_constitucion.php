<?php
// Verificar si los archivos existen antes de requerirlos
require_once(__DIR__ . "/../../includes/conexion.php");
require_once(__DIR__ . "/../../includes/header.php");

// Verificar conexión
if (!$conn) {
    die("Error: No se pudo conectar a la base de datos.");
}

// Obtener mensaje de éxito o error desde la URL
$mensaje_exito = isset($_GET['success']) ? "¡Éxito! Registro guardado correctamente." : null;
$mensaje_error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : null;
?>

<div class="container mt-5">
    <div class="row justify-content-center"> <!-- Cambio: Se añadió 'justify-content-center' para centrar -->
        <div class="col-12"> <!-- Cambio: Se reemplazó 'col-md-8' por 'col-12' para ocupar todo el ancho -->
            <div class="card shadow w-100"> <!-- Cambio: Se agregó 'w-100' para que la tarjeta ocupe todo el ancho -->
                <div class="card-header bg-primary text-white text-center">
                    <h4><i class="fas fa-file-alt"></i> Registrar Acta de Constitución</h4>
                </div>
                <div class="card-body">
                    <!-- Formulario de registro -->
                    <form action="procesar_constitucion.php" method="POST" enctype="multipart/form-data">

                        <!-- Fecha de Fundación -->
                        <div class="form-group">
                            <label for="fecha_fundacion">Fecha de Fundación:</label>
                            <input type="date" name="fecha_fundacion" id="fecha_fundacion"
                                class="form-control w-100" required> <!-- Cambio: Se agregó 'w-100' -->
                        </div>

                        <!-- Archivo del Acta -->
                        <div class="form-group">
                            <label for="archivo_acta">Archivo del Acta (PDF):</label>
                            <input type="file" name="archivo_acta" class="form-control-file"
                                accept=".pdf" required>
                            <small class="text-muted">Solo archivos en formato PDF.</small>
                        </div>

                        <!-- Grupo -->
                        <div class="form-group">
                            <label for="grupo">Grupo:</label>
                            <div class="d-flex">
                                <select name="grupo_id" class="form-control w-100" required> <!-- Cambio: Se agregó 'w-100' -->
                                    <option value="">Selecciona un grupo</option>
                                    <?php
                                    $query = "SELECT idgrupo, nombre_grupo FROM grupo";
                                    $result = $conn->query($query);
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value='{$row['idgrupo']}'>{$row['nombre_grupo']}</option>";
                                    }
                                    ?>
                                </select>
                                <a href="http://localhost/asociaciones/modules/asociaciones/registrar_grupo.php"
                                    class="btn btn-primary ml-2"> <!-- Cambio: Se añadió 'ml-2' para separación -->
                                    <i class="fas fa-plus"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Botones de Guardar y Ver Lista -->
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Guardar
                            </button>
                            <a href="listar_constitucion.php" class="btn btn-primary">
                                <i class="fas fa-list"></i> Ver Lista
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>