<?php
ob_start(); // Inicia el buffer de salida
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>

    <!-- AdminLTE required CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- AdminLTE required JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">


</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
            </ul>
            <button class="btn btn-danger ml-auto">
                Cerrar Sesión
            </button>
        </nav>

        <!-- Sidebar -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="index.php" class="brand-link">
                <span class="brand-text font-weight-light">Mi Sistema</span>
            </a>

            <div class="sidebar">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item">
                            <a href="index.php?pagina=dashboard" class="nav-link">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        <!-- Socios -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Socios<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="index.php?pagina=buscar_socio/buscar_socio" class="nav-link"><i class="far fa-circle nav-icon"></i>
                                        <p>Buscar Socio</p>
                                    </a></li>
                                <li class="nav-item"><a href="index.php?pagina=socio/registrar_socio" class="nav-link"><i class="far fa-circle nav-icon"></i>
                                        <p>Registrar Socio</p>
                                    </a></li>
                                <li class="nav-item"><a href="index.php?pagina=socio/asociar_socio" class="nav-link"><i class="far fa-circle nav-icon"></i>
                                        <p>Asociar Socio</p>
                                    </a></li>
                            </ul>
                        </li>

                        <!-- Grupos -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-users-cog"></i>
                                <p>Grupos<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="index.php?pagina=asociaciones/registrar_grupo" class="nav-link"><i class="far fa-circle nav-icon"></i>
                                        <p>Registrar Grupo</p>
                                    </a></li>
                                <li class="nav-item"><a href="index.php?pagina=grupo/dia_trabajo" class="nav-link"><i class="far fa-circle nav-icon"></i>
                                        <p>Día de Trabajo</p>
                                    </a></li>
                            </ul>
                        </li>

                        <!-- Documentos -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-folder"></i>
                                <p>Documentos<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="index.php?pagina=acta_constitucion/registrar_constitucion" class="nav-link"><i class="far fa-circle nav-icon"></i>
                                        <p>Registrar Acta</p>
                                    </a></li>
                                <li class="nav-item"><a href="index.php?pagina=vigencia_poder/registrar_vigencia" class="nav-link"><i class="far fa-circle nav-icon"></i>
                                        <p>Registrar Vigencia</p>
                                    </a></li>
                                <li class="nav-item"><a href="index.php?pagina=padron_socios/registrar_padron" class="nav-link"><i class="far fa-circle nav-icon"></i>
                                        <p>Registrar Padrón</p>
                                    </a></li>
                                <li class="nav-item"><a href="index.php?pagina=resolucion_gdh/registrar_resolucion" class="nav-link"><i class="far fa-circle nav-icon"></i>
                                        <p>Registrar Resolución</p>
                                    </a></li>
                            </ul>
                        </li>

                        <!-- Reportes -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-chart-bar"></i>
                                <p>Reportes<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="index.php?pagina=cruce_socios/cruce_socios" class="nav-link"><i class="far fa-circle nav-icon"></i>
                                        <p>Cruce de Información</p>
                                    </a></li>
                                <li class="nav-item"><a href="index.php?pagina=documentos/documentos" class="nav-link"><i class="far fa-circle nav-icon"></i>
                                        <p>Documentos</p>
                                    </a></li>
                                <li class="nav-item"><a href="index.php?pagina=reportes/grupos" class="nav-link"><i class="far fa-circle nav-icon"></i>
                                        <p>Grupos</p>
                                    </a></li>
                                <li class="nav-item"><a href="index.php?pagina=reportes/dia_laborable" class="nav-link"><i class="far fa-circle nav-icon"></i>
                                        <p>Día Laborable</p>
                                    </a></li>
                                <li class="nav-item"><a href="index.php?pagina=reportes/cantidad_socios" class="nav-link"><i class="far fa-circle nav-icon"></i>
                                        <p>Cantidad de Socios</p>
                                    </a></li>
                                <li class="nav-item"><a href="index.php?pagina=verificacion/listar_verificacion" class="nav-link"><i class="far fa-circle nav-icon"></i>
                                        <p>Verificación de Socios</p>
                                    </a></li>
                            </ul>
                        </li>

                        <!-- Configuración -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-cogs"></i>
                                <p>Configuración<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="index.php?pagina=configuracion/ajustes" class="nav-link"><i class="far fa-circle nav-icon"></i>
                                        <p>Ajustes del Sistema</p>
                                    </a></li>
                                <li class="nav-item"><a href="index.php?pagina=configuracion/preferencias" class="nav-link"><i class="far fa-circle nav-icon"></i>
                                        <p>Preferencias</p>
                                    </a></li>
                                <li class="nav-item"><a href="index.php?pagina=configuracion/seguridad" class="nav-link"><i class="far fa-circle nav-icon"></i>
                                        <p>Seguridad</p>
                                    </a></li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>
    </div>
</body>


</html>