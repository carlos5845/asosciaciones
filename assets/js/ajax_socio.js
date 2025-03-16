$(document).ready(function () {
    $("#formRegistrarSocio").submit(function (event) {
        event.preventDefault(); // Evita la recarga de la página

        var formData = $(this).serialize(); // Captura los datos del formulario

        $.ajax({
            type: "POST",
            url: "modules/socio/procesar_socio.php",
            data: formData,
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    $("#mensaje").html('<div class="alert alert-success">' + response.message + '</div>');
                    $("#formRegistrarSocio")[0].reset(); // Limpiar formulario
                } else {
                    $("#mensaje").html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function () {
                $("#mensaje").html('<div class="alert alert-danger">Error en la conexión.</div>');
            }
        });
    });
});
