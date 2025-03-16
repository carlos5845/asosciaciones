<?php
session_start();  // Debe estar al principio del archivo

include '../../includes/header.php';
include '../../includes/conexion.php';

$socio = null;
$mensaje = $_GET['mensaje'] ?? '';
$mensaje_error = $_GET['error'] ?? '';
$dni = $_GET['dni'] ?? '';
$socio_en_asociacion = false;
$cantidad_asociaciones = 0;

// Buscar socio por DNI
if (!empty($dni)) {
    $query = "SELECT * FROM socio WHERE dni = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $dni);
    $stmt->execute();
    $result = $stmt->get_result();
    $socio = $result->fetch_assoc();
    $stmt->close();

    if ($socio) {
        // Contar la cantidad de asociaciones a las que pertenece el socio
        $query_asociaciones = "SELECT COUNT(*) AS cantidad FROM socio_asociacion WHERE socio_idsocio = ?";
        $stmt = $conn->prepare($query_asociaciones);
        $stmt->bind_param("i", $socio['idsocio']);
        $stmt->execute();
        $result_asociaciones = $stmt->get_result();
        $asociacion = $result_asociaciones->fetch_assoc();
        $stmt->close();

        $cantidad_asociaciones = $asociacion['cantidad'];

        if ($cantidad_asociaciones > 0) {
            $socio_en_asociacion = true;
            $mensaje_error = "El socio con DNI $dni ya pertenece a $cantidad_asociaciones asociación(es).";
        }
    } else {
        $mensaje_error = "El socio con DNI $dni no está registrado.";
    }
}

// Obtener asociaciones
$query_grupo = "SELECT idgrupo, nombre_grupo FROM grupo ORDER BY nombre_grupo";
$result_grupo = $conn->query($query_grupo);
?>

<!DOCTYPE html>
<html lang="es">

<body>
<div class="container mt-4">
    <h4 class="text-center mb-3">Asociar Socio a Asociación</h4>
<!-- Mostrar mensaje de éxito o error -->
    <div id="alert-container"></div>

<script>
    $(document).ready(function () {
        var mensaje = "<?= addslashes(htmlspecialchars($mensaje)) ?>";
        var mensaje_error = "<?= addslashes(htmlspecialchars($mensaje_error)) ?>";
        var mensaje_eliminacion = "<?= addslashes(htmlspecialchars($_GET['eliminacion'] ?? '')) ?>";

        function mostrarAlerta(tipo, texto) {
            let alerta = `
                <div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
                    <strong>${tipo === 'success' ? 'Éxito' : tipo === 'danger' ? 'Error' : 'Eliminado'}:</strong> ${texto}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `;
            $('#alert-container').append(alerta);
            setTimeout(function() {
                $(".alert").css("opacity", "0").slideUp(500, function () { $(this).remove(); });
            }, 5000);
        }

        if (mensaje) { mostrarAlerta('success', mensaje); }
        if (mensaje_error) { mostrarAlerta('danger', mensaje_error); }
        if (mensaje_eliminacion) { mostrarAlerta('warning', mensaje_eliminacion); }

        // Limpiar los parámetros de la URL sin recargar la página
        if (window.history.replaceState) {
            let url = new URL(window.location.href);
            url.searchParams.delete("mensaje");
            url.searchParams.delete("mensaje_error");
            url.searchParams.delete("eliminacion");
            window.history.replaceState(null, "", url.toString());
        }
    });
</script>
    <!-- Búsqueda del socio -->
    <div class="card">
        <div class="card-header bg-primary text-white">Buscar Socio</div>
        <div class="card-body">
            <form action="" method="get" class="form-inline">
                <label for="dni" class="mr-2">DNI:</label>
                <input type="text" name="dni" id="dni" class="form-control form-control-sm mr-2"
                    required maxlength="8" pattern="\d{8}" value="<?= htmlspecialchars($dni) ?>" style="width: 120px;">
                <button type="submit" class="btn btn-sm btn-primary">Buscar</button>
            </form>
        </div>
    </div>

    <?php if ($socio): ?>
    <!-- Datos del Socio -->
    <div class="card mt-3">
        <div class="card-header bg-info text-white">Datos del Socio</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6"><strong>Nombre:</strong> <?= htmlspecialchars($socio['nombre']) ?></div>
                <div class="col-md-6"><strong>Apellido Paterno:</strong> <?= htmlspecialchars($socio['apellido_pat']) ?></div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6"><strong>Apellido Materno:</strong> <?= htmlspecialchars($socio['apellido_mat']) ?></div>
                <div class="col-md-6"><strong>Género:</strong> <?= htmlspecialchars($socio['genero']) ?></div>
            </div>
            <div class="row mt-2">
                <div class="col-md-4"><strong>Departamento:</strong> <?= htmlspecialchars($socio['departamento']) ?></div>
                <div class="col-md-4"><strong>Provincia:</strong> <?= htmlspecialchars($socio['provincia']) ?></div>
                <div class="col-md-4"><strong>Distrito:</strong> <?= htmlspecialchars($socio['distrito']) ?></div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Formulario de asociación -->
    <div class="card mt-3">
        <div class="card-header bg-success text-white">Formulario</div>
        <div class="card-body">
            <form action="procesar_socio_asociacion.php" method="post">
                <fieldset id="form-fields" <?= ($socio && !$socio_en_asociacion) ? '' : 'disabled' ?>>
                    <input type="hidden" name="idsocio" value="<?= $socio['idsocio'] ?? '' ?>">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="idasociacion">Asociación:</label>
                                <select name="idasociacion" id="idasociacion" class="form-control form-control-sm" required>
                                    <option value="">-- Selecciona --</option>
                                    <?php while ($asociacion = $result_grupo->fetch_assoc()) { ?>
                                        <option value="<?= $asociacion['idgrupo'] ?>">
                                            <?= htmlspecialchars($asociacion['nombre_grupo']) ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="rubro">Rubro:</label>
                                <input type="text" name="rubro" id="rubro" class="form-control form-control-sm"
                                       required placeholder="Ej. Alimentos, Ropa...">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="cod_puesto">Código de Puesto:</label>
                        <div class="form-check">
                            <input type="checkbox" id="activar_cod_puesto" class="form-check-input">
                            <label for="activar_cod_puesto" class="form-check-label">Pertenece a un mercado</label>
                        </div>
                        <input type="text" name="cod_puesto" id="cod_puesto" class="form-control form-control-sm disabled"
                               placeholder="Código de Puesto" readonly>
                    </div>

                    <div class="row">
                        <div class="col-md-7">
                            <div class="form-group">
                                <label for="observacion">Observación:</label>
                                <input type="text" name="observacion" id="observacion"
                                       class="form-control form-control-sm" placeholder="Observaciones">
                            </div>
                        </div>
                    </div>

                    <!-- Botón Guardar (dentro del fieldset) -->
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </fieldset>
            </form>

            <!-- Botón "Ver Lista" fuera del fieldset -->
            <div class="mt-3">
                <a href="http://localhost/asociaciones/modules/socio_asociacion/listar_socio_asociacion.php" class="btn btn-primary btn-lista">
                    <i class="fas fa-list"></i> Ver Lista
                </a>
            </div>
        </div>
    </div>
</div>


<script>
$(document).ready(function () {
    setTimeout(function () { $(".alert").fadeOut("slow"); }, 4000);

    $("#activar_cod_puesto").change(function () {
        let codPuestoInput = $("#cod_puesto");
        codPuestoInput.prop("readonly", !this.checked);
        codPuestoInput.toggleClass("disabled", !this.checked);
        if (!this.checked) {
            codPuestoInput.val('');
        }
    });

    // Deshabilitar solo los elementos dentro del formulario, excepto el botón "Ver Lista"
    if ($("#dni").val().length !== 8 || <?= ($socio && !$socio_en_asociacion) ? 'false' : 'true' ?>) {
        $("#form-fields input, #form-fields select, #form-fields button").prop("disabled", true);
    }

    // Habilitar el botón "Ver Lista"
    $(".btn-lista").prop("disabled", false);
});
</script>
</body>
</html>

<?php $conn->close(); ?>
