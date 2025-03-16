document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("formBuscarSocio").addEventListener("submit", function(event) {
        event.preventDefault(); // Evitar recarga de la página
        
        let dni = document.getElementById("dni").value.trim();
        if (!/^\d{8}$/.test(dni)) {
            alert("Ingrese un DNI válido de 8 dígitos.");
            return;
        }

        let resultadoDiv = document.getElementById("resultado");
        resultadoDiv.innerHTML = "<p class='text-info'>Buscando...</p>";

        fetch("modules/buscar_socio/buscar_socio_ajax.php?dni=" + dni)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                resultadoDiv.innerHTML = `<div class='alert alert-danger'>${data.error}</div>`;
            } else {
                let socio = data.socio;
                let asociaciones = data.asociaciones;

                let html = `
                        <div class="card mt-3 shadow-sm">
                            <div class="card-header bg-info text-white h5">Datos del Socio</div>
                            <div class="card-body">
                                <table class="table table-sm">
                                    <tr><th>DNI</th><td>${socio.dni}</td></tr>
                                    <tr><th>Nombre</th><td>${socio.nombre} ${socio.apellido_pat} ${socio.apellido_mat}</td></tr>
                                    <tr><th>Género</th><td>${socio.genero === 'F' ? 'Femenino' : (socio.genero === 'M' ? 'Masculino' : 'No especificado')}</td></tr>
                                    <tr><th>Departamento</th><td>${socio.departamento}</td></tr>
                                    <tr><th>Provincia</th><td>${socio.provincia}</td></tr>
                                    <tr><th>Distrito</th><td>${socio.distrito}</td></tr>
                                </table>
                            </div>
                    </div>`;

                    if (asociaciones.length > 0) {
                        html += `
                        <div class="card mt-3 shadow-sm">
                            <div class="card-header bg-info text-white h5">Asociaciones del Socio</div>
                            <div class="table-responsive">
                               <table class="table table-sm-custom table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Nº</th>
                                            <th>Grupo</th>
                                            <th>Ubicación</th>
                                            <th>Código Puesto</th>
                                            <th>Rubro</th>
                                            <th>Categoría</th>
                                            <th>Agrupamiento</th>
                                            <th>Observación</th>
                                            <th>Cargo</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                            <tbody>`;
                            asociaciones.forEach((asoc, index) => {
                                html += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${asoc.nombre_grupo}</td>
                                <td>${asoc.ubicacion}</td>
                                <td>${asoc.cod_puesto ? asoc.cod_puesto : ''}</td>
                                <td>${asoc.rubro}</td>
                                <td>${asoc.categoria}</td>
                                <td>${asoc.agrupamiento}</td>
                                <td>${asoc.observacion}</td>
                                <td>${asoc.cargos_junta || 'SOCIO'}</td>
                                <td>${asoc.estado}</td>
                                    </tr>`;
                                });
                            html += `</tbody></table></div></div>`;
                        }
                        resultadoDiv.innerHTML = html;
                    }
                })
        .catch(error => {
            resultadoDiv.innerHTML = `<div class='alert alert-danger'>Error al buscar socio.</div>`;
        });
    });
});
