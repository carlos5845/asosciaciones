<?php
include '../../includes/header.php';
include '../../includes/conexion.php';

// Obtener los cargos desde la base de datos
$cargos = [];
$query = "SELECT idcargo, tipo_cargo FROM cargo";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $cargos[] = $row;
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4><i class="fas fa-users"></i> Registrar Junta Directiva</h4>
                </div>
                <div class="card-body">
                    <!-- Contenedor para alertas dinámicas dentro de la card -->
                    <div id="alert-container"></div>

                    <!-- Mensajes de alerta del servidor -->
                    <?php if (!empty($_GET['success']) || !empty($_GET['error'])): ?>
                        <?php if (!empty($_GET['success'])): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php echo str_replace('|', '<br>', $_GET['success']); ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($_GET['error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo str_replace('|', '<br>', $_GET['error']); ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <form action="procesar_junta.php" method="POST">
                        <div class="form-group">
                            <label for="grupo">Grupo:</label>
                            <select name="grupo" id="grupo" class="form-control" required>
                                <option value="">Selecciona un grupo</option>
                                <?php
                                $query = "SELECT idgrupo, nombre_grupo FROM grupo";
                                $result = $conn->query($query);
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['idgrupo']}'>{$row['nombre_grupo']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="fecha_inicio">Fecha de Inicio:</label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="fecha_fin">Fecha de Fin:</label>
                            <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" required>
                        </div>

                        <table class="table table-bordered" id="tabla-miembros">
                            <thead>
                                <tr>
                                    <th>DNI</th>
                                    <th>Nombre</th>
                                    <th>Apellidos</th>
                                    <th>Celular</th>
                                    <th>Estado</th>
                                    <th>Cargo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr id="fila_1">
                                    <td>
                                        <input type="text" name="dni_1" id="dni_1" class="form-control" onblur="autoComplete(1)" required>
                                    </td>
                                    <td>
                                        <input type="text" name="nombre_1" id="nombre_1" class="form-control" readonly>
                                    </td>
                                    <td>
                                        <input type="text" name="apellido_1" id="apellido_1" class="form-control" readonly>
                                    </td>
                                    <td>
                                        <!-- Se remueve readonly y se agrega pattern para 9 dígitos -->
                                        <input type="text" name="celular_1" id="celular_1" class="form-control" pattern="\d{9}" title="Ingrese 9 dígitos" required>
                                    </td>
                                    <td>
                                        <select name="estado_1" id="estado_1" class="form-control" required>
                                            <option value="">Seleccionar estado</option>
                                            <option value="1">Activo</option>
                                            <option value="0">Inactivo</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="cargo_1" id="cargo_1" class="form-control" required>
                                            <option value="">Selecciona un cargo</option>
                                            <?php
                                            foreach ($cargos as $cargo) {
                                                echo "<option value='{$cargo['idcargo']}'>{$cargo['tipo_cargo']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger" onclick="eliminarFila(1)">Eliminar</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="text-left mb-3">
                            <button type="button" class="btn btn-primary" id="agregar-miembro">
                                <i class="fas fa-user-plus"></i> Agregar Miembro
                            </button>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Guardar Junta Directiva
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

<script>
    // Función para mostrar alertas Bootstrap dinámicamente dentro de la card
    function showAlert(message, type) {
        var alertHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
                        message +
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                        '<span aria-hidden="true">&times;</span></button></div>';
        $('#alert-container').html(alertHtml);
    }

    $(document).ready(function() {
        var contador = 1;

        $("#agregar-miembro").click(function() {
            contador++;
            var nuevaFila = `
                <tr id="fila_${contador}">
                    <td><input type="text" name="dni_${contador}" id="dni_${contador}" class="form-control" onblur="autoComplete(${contador})" required></td>
                    <td><input type="text" name="nombre_${contador}" id="nombre_${contador}" class="form-control" readonly></td>
                    <td><input type="text" name="apellido_${contador}" id="apellido_${contador}" class="form-control" readonly></td>
                    <td>
                        <input type="text" name="celular_${contador}" id="celular_${contador}" class="form-control" pattern="\\d{9}" title="Ingrese 9 dígitos" required>
                    </td>
                    <td>
                        <select name="estado_${contador}" id="estado_${contador}" class="form-control" required>
                            <option value="">Seleccionar estado</option>
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </td>
                    <td>
                        <select name="cargo_${contador}" id="cargo_${contador}" class="form-control" required>
                            <option value="">Selecciona un cargo</option>
                            <?php
                            foreach ($cargos as $cargo) {
                                echo "<option value='{$cargo['idcargo']}'>{$cargo['tipo_cargo']}</option>";
                            }
                            ?>
                        </select>
                    </td>
                    <td><button type="button" class="btn btn-danger" onclick="eliminarFila(${contador})">Eliminar</button></td>
                </tr>
            `;
            $("#tabla-miembros tbody").append(nuevaFila);
        });

        window.eliminarFila = function(id) {
            $("#fila_" + id).remove();
        };
    });

    function autoComplete(index) {
        var dni = $('#dni_' + index).val();
        var idGrupo = $('#grupo').val();

        if (!idGrupo) {
            showAlert('Debe seleccionar un grupo antes de buscar un socio.', 'warning');
            $('#dni_' + index).val('');
            return;
        }

        if (dni.length !== 8 || isNaN(dni)) {
            showAlert('El DNI debe tener 8 dígitos numéricos.', 'warning');
            $('#dni_' + index).val('');
            return;
        }

        $.ajax({
            url: 'buscar_socio.php',
            type: 'GET',
            data: { dni: dni, grupo: idGrupo },
            beforeSend: function() {
                $('#nombre_' + index).val('Buscando...');
                $('#apellido_' + index).val('Buscando...');
            },
            success: function(response) {
                if (response.success) {
                    $('#nombre_' + index).val(response.nombre);
                    $('#apellido_' + index).val(response.apellido);
                    if(response.celular) {
                        $('#celular_' + index).val(response.celular);
                    } else {
                        $('#celular_' + index).val('');
                    }
                } else {
                    showAlert(response.error, 'danger');
                    $('#dni_' + index).val('');
                    $('#nombre_' + index).val('');
                    $('#apellido_' + index).val('');
                    $('#celular_' + index).val('');
                }
            },
            error: function() {
                showAlert('Error al conectar con el servidor.', 'danger');
                $('#nombre_' + index).val('');
                $('#apellido_' + index).val('');
                $('#celular_' + index).val('');
            }
        });
    }
</script>
