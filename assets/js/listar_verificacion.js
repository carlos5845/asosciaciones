$(document).ready(function() {
    function cargarSocios(pagina = 1, search = "") {
        $.ajax({
            url: "modules/verificacion/listar_verificacion_ajax.php",
            type: "GET",
            data: { pagina: pagina, search: search },
            success: function(response) {
                $("#tablaSocios").html(response);
            },
            error: function() {
                alert("Error al cargar los socios.");
            }
        });
    }

    // Cargar la lista al cargar la página
    cargarSocios();

    // Buscar al hacer clic
    $("#btnBuscar").click(function() {
        let search = $("#search").val().trim();
        cargarSocios(1, search);
    });

    // Detectar cambios en el input de búsqueda y actualizar en tiempo real
    $("#search").on("input", function() {
        let search = $(this).val().trim();
        cargarSocios(1, search);
    });

    // Manejo de la paginación con eventos delegados
    $(document).on("click", ".page-link", function(e) {
        e.preventDefault();
        let pagina = $(this).data("pagina");
        let search = $("#search").val().trim();
        if (pagina) {
            cargarSocios(pagina, search);
        }
    });
});
