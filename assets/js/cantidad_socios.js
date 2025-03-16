document.addEventListener("DOMContentLoaded", function () {
    function cargarDatos(pagina = 1, busqueda = "") {
        let formData = new FormData();
        formData.append("pagina", pagina);
        formData.append("busqueda", busqueda);

        fetch("modules/reportes/cantidad_socios_ajax.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            let tablaHTML = `
                <table class="table table-bordered table-sm mt-3">
                    <thead class="thead-dark">
                        <tr>
                            <th>Nº</th>
                            <th>Cod Etiqueta</th>
                            <th>Grupo</th>
                            <th>Agrupamiento</th>
                            <th>Cantidad de Socios</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            if (data.datos.length > 0) {
                let contador = (pagina - 1) * 5 + 1;
                data.datos.forEach(row => {
                    tablaHTML += `
                        <tr>
                            <td>${contador++}</td>
                            <td>${row.etiqueta_grupo}</td>
                            <td>${row.nombre_grupo}</td>
                            <td>${row.agrupamientocol}</td>
                            <td>${row.cantidad_socios}</td>
                        </tr>
                    `;
                });
            } else {
                tablaHTML += `
                    <tr><td colspan="5" class="text-center">No hay resultados</td></tr>
                `;
            }

            tablaHTML += `</tbody></table>`;

            // Paginación
            if (data.total_paginas > 1) {
                tablaHTML += `<nav><ul class="pagination justify-content-center">`;

                if (pagina > 1) {
                    tablaHTML += `<li class="page-item"><a class="page-link paginar" data-pagina="1" href="#">Primera</a></li>`;
                    tablaHTML += `<li class="page-item"><a class="page-link paginar" data-pagina="${pagina - 1}" href="#">Anterior</a></li>`;
                }

                tablaHTML += `<li class="page-item active"><span class="page-link">${pagina}</span></li>`;

                if (pagina < data.total_paginas) {
                    tablaHTML += `<li class="page-item"><a class="page-link paginar" data-pagina="${pagina + 1}" href="#">Siguiente</a></li>`;
                    tablaHTML += `<li class="page-item"><a class="page-link paginar" data-pagina="${data.total_paginas}" href="#">Última</a></li>`;
                }

                tablaHTML += `</ul></nav>`;
            }

            document.getElementById("tabla-datos").innerHTML = tablaHTML;

            // Agregar eventos de paginación
            document.querySelectorAll(".paginar").forEach(el => {
                el.addEventListener("click", function (e) {
                    e.preventDefault();
                    let nuevaPagina = this.getAttribute("data-pagina");
                    cargarDatos(nuevaPagina, document.getElementById("busqueda").value);
                });
            });
        })
        .catch(error => console.error("Error:", error));
    }

    document.getElementById("btnBuscar").addEventListener("click", function () {
        cargarDatos(1, document.getElementById("busqueda").value);
    });

    cargarDatos();
});
