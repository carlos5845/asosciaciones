<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Sistema</title>

    <!-- Estilos -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"> <!-- Font Awesome -->
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <!-- Botón menú hamburguesa alineado a la izquierda -->
                    <li class="nav-item">
                        <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                            <i class="fas fa-bars"></i>
                        </a>
                    </li>
                    <!-- Título "Mi Sistema" alineado a la izquierda -->
                    <li class="nav-item d-none d-sm-inline-block">
                        <a href="#" class="nav-link font-weight-bold">Mi Sistema</a>
                    </li>
                </ul>
            </div>
        </nav>


        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->


            <!-- Sidebar -->
            <div class="sidebar">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <!-- Dashboard -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="index.php?pagina=dashboard" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Inicio</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Socios -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Socios<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="index.php?pagina=buscar_socio/buscar_socio" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Buscar Socio</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="index.php?pagina=socio/registrar_socio" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Registrar Socio</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="index.php?pagina=socio/asociar_socio" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Asociar Socio</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jQuery (Debe ir antes de AdminLTE) -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>

</html>