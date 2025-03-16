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
                    <h4><i class="fas fa-users"></i> Registrar Grupo</h4>
                </div>
                <div class="card-body">
                    <form action="procesar_grupo.php" method="POST">
                        <div class="form-group">
                            <label for="etiqueta_grupo">Código de la Asociación:</label>
                            <input type="text" id="etiqueta_grupo" name="etiqueta_grupo" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="nombre_grupo">Nombre de la Asociación:</label>
                            <input type="text" id="nombre_grupo" name="nombre_grupo" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="ubicacion">Ubicación:</label>
                            <input type="text" id="ubicacion" name="ubicacion" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="agrupamiento_id">Agrupamiento:</label>
                            <div class="d-flex">
                                <select id="agrupamiento_id" name="agrupamiento_id" class="form-control" required>
                                    <option value="">Selecciona un agrupamiento</option>
                                    <?php
                                    $query = "SELECT idagrupamiento, agrupamientocol FROM agrupamiento";
                                    if ($stmt = $conn->prepare($query)) {
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<option value='{$row['idagrupamiento']}'>{$row['agrupamientocol']}</option>";
                                        }
                                        $stmt->close();
                                    }
                                    ?>
                                </select>
                                <a href="http://localhost/asociaciones/modules/agrupamiento/registrar_agrupamiento.php" class="btn btn-primary ml-2" target="_blank">
                                    <i class="fas fa-plus"></i>
                                </a>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="categoria_id">Categoría:</label>
                            <div class="d-flex">
                                <select id="categoria_id" name="categoria_id" class="form-control" required>
                                    <option value="">Selecciona una categoría</option>
                                    <?php
                                    $query = "SELECT idcategoria, tipo FROM categoria";
                                    if ($stmt = $conn->prepare($query)) {
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<option value='{$row['idcategoria']}'>{$row['tipo']}</option>";
                                        }
                                        $stmt->close();
                                    }
                                    ?>
                                </select>
                                <a href="http://localhost/asociaciones/modules/categoria/registrar_categoria.php" class="btn btn-primary ml-2" target="_blank">
                                    <i class="fas fa-plus"></i>
                                </a>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="estado">Estado:</label>
                            <select id="estado" name="estado" class="form-control form-control-sm" required>
                                <option value="">- Seleccionar estado -</option>
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                            </select>
                        </div>

                        <!-- Botones de Guardar y Ver Lista -->
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Guardar
                            </button>
                            <a href="listar_grupo.php" class="btn btn-primary">
                                <i class="fas fa-list"></i> Ver Lista
                            </a>
                        </div>
                    </form>
                </div>
        </div>
    </div>
</div>
