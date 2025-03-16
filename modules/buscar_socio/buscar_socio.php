<form id="formBuscarSocio" class="form-inline">
    <label for="dni" class="mr-2">DNI:</label>
    <input type="text" name="dni" id="dni" class="form-control form-control-sm mr-2" required maxlength="8" pattern="\d{8}">
    <button type="submit" class="btn btn-sm btn-primary">Buscar</button>
    <a href="?pagina=socio/registrar_socio" class="btn btn-sm btn-success ml-2">Agregar Socio</a>
</form>

<!-- Aquí agregamos la clase mt-4 para darle espacio -->
<div id="resultado" class="mt-4"></div>

<script src="assets/js/buscar_socio.js"></script>
