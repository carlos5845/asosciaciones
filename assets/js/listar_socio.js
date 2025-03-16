$(document).ready(function () {
    function cargarSocios(pagina = 1, search = '') {
        $.post('modules/socio/cargar_socio.php', { pag: pagina, search: search }, function (data) {
            $('#tabla-socios').html(data);
        });
    }

    // Cargar la tabla al inicio
    cargarSocios();

    // Buscar mientras el usuario escribe
    $('#search').on('input', function () {
        let search = $(this).val();
        cargarSocios(1, search);
    });

    // Manejo de paginación con eventos delegados
    $(document).on('click', '.pagination a', function (e) {
        e.preventDefault();
        let pagina = $(this).attr('data-page');
        let search = $('#search').val();
        cargarSocios(pagina, search);
    });

    // Evento para eliminar con SweetAlert2
    $(document).on("click", ".btn-eliminar", function () {
        let idSocio = $(this).data("id");

        Swal.fire({
            title: "¿Estás seguro?",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "modules/socio/eliminar_socio.php",
                    type: "GET",
                    data: { id: idSocio },
                    success: function (response) {
                        if (response.trim() === "eliminado") {
                            Swal.fire({
                                title: "Eliminado correctamente",
                                text: "El socio ha sido eliminado.",
                                timer: 2000,
                                showConfirmButton: false
                            });

                            // Recargar la tabla después de eliminar
                            setTimeout(() => {
                                cargarSocios();
                            }, 2000);
                        } else {
                            Swal.fire("Error", "No se pudo eliminar el socio.", "error");
                        }
                    },
                    error: function () {
                        Swal.fire("Error", "No se pudo conectar con el servidor.", "error");
                    }
                });
            }
        });
    });

    // Evento para editar con SweetAlert2
    $(document).on("click", ".btn-editar", function () {
        let idSocio = $(this).data("id");
        Swal.fire({
            title: "¿Editar socio?",
            showCancelButton: true,
            confirmButtonText: "Sí, editar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "?pagina=socio/editar_socio&id=" + idSocio;
            }
        });
    });
});
