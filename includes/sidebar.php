<!-- Sidebar -->
<div id="sidebar" class="sidebar">
    <div class="sidebar-header">
        <button class="sidebar-toggle btn btn-dark" onclick="toggleSidebar()" aria-label="Alternar menú">
            ☰
        </button>
        <span>Mi Sistema</span>
    </div>
    <ul class="nav flex-column">

        <!-- Dashboard -->
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#menuDashboard">
                <i class="bi bi-speedometer2"></i> Dashboard <i class="bi bi-chevron-down float-right"></i>
            </a>
            <div class="collapse" id="menuDashboard">
                <ul class="nav flex-column pl-3">
                    <li class="nav-item">
                        <a class="nav-link load-page" href="index.php?pagina=dashboard">
                            <i class="bi bi-house-fill"></i> Inicio
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <!-- Socios -->
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#menuSocios">
                <i class="bi bi-people"></i> Socios <i class="bi bi-chevron-down float-right"></i>
            </a>
            <div class="collapse" id="menuSocios">
                <ul class="nav flex-column pl-3">
                    <li class="nav-item">
                        <a class="nav-link load-page" href="index.php?pagina=buscar_socio/buscar_socio">
                            <i class="bi bi-search"></i> Buscar Socio
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link load-page" href="index.php?pagina=socio/registrar_socio">
                            <i class="bi bi-person-plus-fill"></i> Registrar Socio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link load-page" href="index.php?pagina=socio/asociar_socio">
                            <i class="bi bi-person-check"></i> Asociar Socio
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <!-- Grupos -->
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#menuGrupos">
                <i class="bi bi-people-fill"></i> Grupos <i class="bi bi-chevron-down float-right"></i>
            </a>
            <div class="collapse" id="menuGrupos">
                <ul class="nav flex-column pl-3">
                    <li class="nav-item">
                        <a class="nav-link load-page" href="index.php?pagina=asociaciones/registrar_grupo">
                            <i class="bi bi-folder-plus"></i> Registrar Grupo
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link load-page" href="index.php?pagina=grupo/dia_trabajo">
                            <i class="bi bi-calendar-event"></i> Día de Trabajo
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <!-- Documentos -->
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#menuDocumentos">
                <i class="bi bi-folder"></i> Documentos <i class="bi bi-chevron-down float-right"></i>
            </a>
            <div class="collapse" id="menuDocumentos">
                <ul class="nav flex-column pl-3">
                    <li class="nav-item">
                        <a class="nav-link load-page" href="index.php?pagina=acta_constitucion/registrar_constitucion">
                            <i class="bi bi-file-text-fill"></i> Registrar Acta
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link load-page" href="index.php?pagina=vigencia_poder/registrar_vigencia">
                            <i class="bi bi-calendar-check-fill"></i> Registrar Vigencia
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link load-page" href="index.php?pagina=padron_socios/registrar_padron">
                            <i class="bi bi-journal-text"></i> Registrar Padrón
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link load-page" href="index.php?pagina=resolucion_gdh/registrar_resolucion">
                            <i class="bi bi-file-earmark-check-fill"></i> Registrar Resolución
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <!-- Reportes -->
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#menuReportes">
                <i class="bi bi-bar-chart-line-fill"></i> Reportes <i class="bi bi-chevron-down float-right"></i>
            </a>
            <div class="collapse" id="menuReportes">
                <ul class="nav flex-column pl-3">
                    <li class="nav-item">
                        <a class="nav-link load-page" href="index.php?pagina=cruce_socios/cruce_socios">
                            <i class="bi bi-shuffle"></i> Cruce de Información
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link load-page" href="index.php?pagina=documentos/documentos">
                            <i class="bi bi-folder2-open"></i> Documentos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link load-page" href="index.php?pagina=reportes/grupos">
                            <i class="bi bi-people"></i> Grupos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link load-page" href="index.php?pagina=reportes/dia_laborable">
                            <i class="bi bi-calendar-event"></i> Día Laborable
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link load-page" href="index.php?pagina=reportes/cantidad_socios">
                            <i class="bi bi-person-lines-fill"></i> Cantidad de Socios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link load-page" href="index.php?pagina=verificacion/listar_verificacion">
                            <i class="bi bi-person-check-fill"></i> Verificacion de socios
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <!-- Contactos -->
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#menuContactos">
                <i class="bi bi-people-fill"></i> Contactos <i class="bi bi-chevron-down float-right"></i>
            </a>
            <div class="collapse" id="menuContactos">
                <ul class="nav flex-column pl-3">
                    <li class="nav-item">
                        <a class="nav-link load-page" href="index.php?pagina=cruce_socios/cruce_socios">
                            <i class="bi bi-journal-bookmark-fill"></i> Directorio
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <!-- Configuración -->
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#menuConfig">
                <i class="bi bi-gear-fill"></i> Configuración <i class="bi bi-chevron-down float-right"></i>
            </a>
            <div class="collapse" id="menuConfig">
                <ul class="nav flex-column pl-3">
                    <li class="nav-item">
                        <a class="nav-link load-page" href="index.php?pagina=configuracion/ajustes">
                            <i class="bi bi-tools"></i> Ajustes del Sistema
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link load-page" href="index.php?pagina=configuracion/preferencias">
                            <i class="bi bi-sliders"></i> Preferencias
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link load-page" href="index.php?pagina=configuracion/seguridad">
                            <i class="bi bi-shield-lock-fill"></i> Seguridad
                        </a>
                    </li>
                </ul>
            </div>
        </li>

    </ul>
</div>
<script src="../../menu.js"></script>