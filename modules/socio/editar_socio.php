<?php
require_once(__DIR__ . "/../../includes/conexion.php");

// Verifica si se recibió un ID
if (!isset($_GET['id'])) {
    echo "<script>alert('No se especificó un socio para editar.'); window.location.href='?pagina=socio/listar_socio';</script>";
    exit();
}

$id = (int)$_GET['id'];

// Obtener datos del socio
$stmt = $conn->prepare("SELECT * FROM socio WHERE idsocio = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$socio = $result->fetch_assoc();
$stmt->close();

if (!$socio) {
    echo "<script>alert('Socio no encontrado.'); window.location.href='?pagina=socio/listar_socio';</script>";
    exit();
}
?>

<div class="container">
    <h2 class="mt-4">Editar Socio</h2>
    <form id="formEditarSocio">
        <input type="hidden" name="idsocio" value="<?= $socio['idsocio'] ?>">

        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" class="form-control" name="nombre" value="<?= $socio['nombre'] ?>" required>
        </div>
        <div class="form-group">
            <label for="apellido_pat">Apellido Paterno:</label>
            <input type="text" class="form-control" name="apellido_pat" value="<?= $socio['apellido_pat'] ?>" required>
        </div>
        <div class="form-group">
            <label for="apellido_mat">Apellido Materno:</label>
            <input type="text" class="form-control" name="apellido_mat" value="<?= $socio['apellido_mat'] ?>" required>
        </div>
        <div class="form-group">
    <label for="dni">DNI:</label>
    <input type="text" class="form-control" name="dni" id="dni" value="<?= htmlspecialchars($socio['dni']) ?>" 
           required maxlength="8" minlength="8" pattern="^\d{8}$" oninput="validarDNI(this)">
</div>

        <button type="submit" class="btn btn-primary">Guardar cambios</button>
        <a href="?pagina=socio/listar_socio" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
$(document).ready(function() {
    $("#formEditarSocio").submit(function(e) {
        e.preventDefault();

        $.ajax({
            url: "modules/socio/procesar_editar_socio.php",
            type: "POST",
            data: $(this).serialize(),
            success: function(response) {
                Swal.fire({
                    title: "Éxito",
                    text: response,
                    icon: "success"
                }).then(() => {
                    window.location.href = "?pagina=socio/listar_socio";
                });
            },
            error: function() {
                Swal.fire("Error", "Hubo un problema al actualizar", "error");
            }
        });
    });
});
</script>