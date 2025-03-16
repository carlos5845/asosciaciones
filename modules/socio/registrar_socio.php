<?php

// Verificar si los archivos existen antes de requerirlos
require_once(__DIR__ . "/../../includes/conexion.php");
require_once(__DIR__ . "/../../includes/header.php");

// Verificar conexión
if (!$conn) {
    die("Error: No se pudo conectar a la base de datos.");
}
?>
<div class="container-fluid mt-4">
    <div class="row">
        <!-- Asegurar que el contenido se mantenga dentro del layout -->
        <div class="col-md-10 offset-md-1">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4><i class="fas fa-user-plus"></i> Registrar Nuevo Socio</h4>
                </div>
                <div class="card-body">
                    
                    <!-- Contenedor de alertas -->
                    <div id="mensaje"></div>

                    <form id="formRegistrarSocio" action="modules/socio/procesar_socio.php" method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dni">DNI:</label>
                                    <input type="text" name="dni" maxlength="8" pattern="\d{8}" title="Debe ser un número de 8 dígitos" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nombre">Nombres:</label>
                                    <input type="text" name="nombre" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="apellido_pat">Apellido Paterno:</label>
                                    <input type="text" name="apellido_pat" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="apellido_mat">Apellido Materno:</label>
                                    <input type="text" name="apellido_mat" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="genero">Género:</label>
                                    <select name="genero" class="form-control" required>
                                        <option value="">- Seleccionar Género -</option>
                                        <option value="F">Femenino</option>
                                        <option value="M">Masculino</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="departamento">Departamento:</label>
                                    <input type="text" name="departamento" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="provincia">Provincia:</label>
                                    <input type="text" name="provincia" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="distrito">Distrito:</label>
                                    <input type="text" name="distrito" class="form-control">
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Guardar y Ver Lista -->
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Guardar
                            </button>
                        <a href="?pagina=socio/listar_socio" class="btn btn-primary">Ver Lista</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Incluir el script de alertas -->
<script src="js/alertasocio.js"></script>
<script src="js/ajax_socio.js"></script>


