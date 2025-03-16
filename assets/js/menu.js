$(document).ready(function () {
    // Obtener la URL actual
    var urlParams = new URLSearchParams(window.location.search);
    var pagina = urlParams.get('pagina');

    if (pagina) {
        // Buscar el enlace activo en el sidebar
        var activeLink = $('.nav-link.load-page[href="index.php?pagina=' + pagina + '"]');
        activeLink.addClass('active');

        // Encontrar el menú desplegable padre y abrirlo
        var menuId = activeLink.attr('data-menu');
        if (menuId) {
            $('#' + menuId).addClass('show');
        }
    }

    // Alternar los menús al hacer clic
    $('.nav-link[data-toggle="collapse"]').click(function () {
        var target = $(this).attr('href'); // Obtiene el ID del menú
        if ($(target).hasClass('show')) {
            $(target).collapse('hide'); // Si está abierto, lo cierra
        } else {
            $(target).collapse('show'); // Si está cerrado, lo abre
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    // Selecciona el botón de la hamburguesa y el sidebar
    const toggleButton = document.querySelector(".sidebar-toggle");
    const sidebar = document.getElementById("sidebar");
    const mainContent = document.querySelector(".main-content");

    // Agrega un evento de clic para alternar la visibilidad del sidebar
    toggleButton.addEventListener("click", function () {
        sidebar.classList.toggle("active");
        mainContent.classList.toggle("active");
    });
});
