$(document).ready(function() {
    $("#formRegistrarSocio").submit(function(event) {
        event.preventDefault(); // Evita la recarga de la página

        $.ajax({
            type: "POST",
            url: "modules/socio/procesar_socio.php",
            data: $(this).serialize(),
            dataType: "json",
            success: function(response) {
                var mensajeHTML = '<div class="alert alert-' + (response.status === "success" ? "success" : "danger") + ' alert-dismissible fade show" role="alert">' +
                                  response.message +
                                  '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                                  '<span aria-hidden="true">&times;</span>' +
                                  '</button>' +
                                  '</div>';

                $("#mensaje").html(mensajeHTML);

                // Desvanecer la alerta después de 5 segundos
                setTimeout(function() {
                    $(".alert").fadeOut("slow", function() {
                        $(this).remove();
                    });
                }, 5000);

                if (response.status === "success") {
                    $("#formRegistrarSocio")[0].reset(); // Limpia el formulario si se registró correctamente
                }
            },
            error: function() {
                var errorHTML = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                'Error al procesar la solicitud' +
                                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                                '<span aria-hidden="true">&times;</span>' +
                                '</button>' +
                                '</div>';

                $("#mensaje").html(errorHTML);

                // Desvanecer la alerta después de 5 segundos
                setTimeout(function() {
                    $(".alert").fadeOut("slow", function() {
                        $(this).remove();
                    });
                }, 5000);
            }
        });
    });
});
