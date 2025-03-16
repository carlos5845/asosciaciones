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
        <h1>perfil</h1>
    </nav>

    <!-- Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Logo -->
        <a href="index.php" class="brand-link">
            <span class="brand-text font-weight-light">Mi Sistema</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="info">
                    <a href="#" class="d-block">Usuario</a>
                </div>
            </div>

            <!-- Menú lateral -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <a href="index.php?pagina=dashboard" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>
                                Socios
                                <i class="right fas fa-angle-left"></i>
                            </p>
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
                        </ul>
                    </li>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>
                                Configuración
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="index.php?pagina=configuracion/ajustes" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Ajustes del Sistema</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="index.php?pagina=configuracion/seguridad" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Seguridad</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>
</div>