<div class="container">
    <h2 class="mt-4">Lista de Socios</h2>

    
    <!-- Campo de búsqueda -->
    <input type="text" id="search" class="form-control mb-3" placeholder="Buscar por DNI o Nombre...">

    <!-- Botón para agregar un nuevo socio -->
    <a href="?pagina=socio/registrar_socio" class="btn btn-primary mb-3">
        Registrar Nuevo Socio
    </a>


    <!-- Contenedor donde se cargará la tabla con AJAX -->
    <div id="tabla-socios"></div>
</div>

<script src="assets/js/listar_socio.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
