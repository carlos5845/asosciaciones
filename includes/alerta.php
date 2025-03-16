<?php
// Verifica si hay un mensaje de éxito, error o eliminación en la URL
$mensaje_exito = isset($_GET['success']) ? "¡Éxito! Registro guardado correctamente." : null;
$mensaje_error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : null;
$mensaje_eliminacion = isset($_GET['eliminacion']) ? htmlspecialchars($_GET['eliminacion']) : null;
?>

<!-- Mostrar mensaje de éxito -->
<?php if ($mensaje_exito): ?>
    <div id="alertaExito" class="alert alert-success alert-dismissible fade show" role="alert">
        <strong><?= $mensaje_exito ?></strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- Mostrar mensaje de error -->
<?php if ($mensaje_error): ?>
    <div id="alertaError" class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error:</strong> <?= $mensaje_error ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- Mostrar mensaje de eliminación -->
<?php if ($mensaje_eliminacion): ?>
    <div id="alertaEliminacion" class="alert alert-success alert-dismissible fade show" role="alert">
        <strong><?= $mensaje_eliminacion ?></strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- Script para ocultar las alertas y limpiar la URL -->
<script>
    setTimeout(function() {
        let alertaExito = document.getElementById("alertaExito");
        let alertaError = document.getElementById("alertaError");
        let alertaEliminacion = document.getElementById("alertaEliminacion");

        if (alertaExito) {
            alertaExito.classList.add("fade");
            setTimeout(() => alertaExito.style.display = "none", 500);
        }

        if (alertaError) {
            alertaError.classList.add("fade");
            setTimeout(() => alertaError.style.display = "none", 500);
        }

        if (alertaEliminacion) {
            alertaEliminacion.classList.add("fade");
            setTimeout(() => alertaEliminacion.style.display = "none", 500);
        }

        // Limpiar la URL sin recargar la página
        if (window.history.replaceState) {
            let newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
            window.history.replaceState({path: newUrl}, "", newUrl);
        }
    }, 5000);
</script>
