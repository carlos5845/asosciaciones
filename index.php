<?php include 'includes/header.php'; ?>
<div class="wrapper">
    <!-- Sidebar -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- Contenido principal dinámico -->
    <div class="main-content">
        <?php
        // Obtener la página desde la URL
        $pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 'dashboard';

        // Evitar que el usuario intente acceder a directorios prohibidos
        $pagina = str_replace(['..', '\\'], '', $pagina); 

        // Ruta del archivo dentro de "modules/"
        $archivo = "modules/$pagina.php";  

        // Si el archivo no existe, intentamos buscarlo dentro de una subcarpeta
        if (!file_exists($archivo)) {
            $archivo = "modules/$pagina";
        }

        // Verificar si el archivo existe antes de incluirlo
        if (file_exists($archivo)) {
            include $archivo;
        } else {
            echo "<h2>Página no encontrada</h2>";
        }
        ?>
    </div>
</div>

<!-- Incluir el footer -->
<?php include 'includes/footer.php'; ?>
