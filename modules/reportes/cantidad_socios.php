<body>
<div class="container mt-3">
    <h4 class="text-center mb-3">Lista de Grupos y Cantidad de Socios</h4>

    <!-- Buscador -->
    <div class="mb-3 d-flex">
        <div class="input-group search-container">
            <input type="text" id="busqueda" class="form-control" placeholder="Buscar grupo...">
            <div class="input-group-append">
                <button class="btn btn-primary" id="btnBuscar">Buscar</button>
            </div>
        </div>
    </div>

    <a href="exportar_grupo_excel.php" class="btn btn-success mb-2">Exportar a Excel</a>

    <!-- Aquí se cargará la tabla con AJAX -->
    <div id="tabla-datos"></div>
</div>

<script src="assets/js/cantidad_socios.js"></script>
