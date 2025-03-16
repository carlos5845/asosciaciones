<?php
include '../../includes/conexion.php';

// Verificar si se ha enviado el ID
if (isset($_GET['id'])) {
    $idgrupo = (int)$_GET['id'];

    if ($idgrupo > 0) {
        // Verificar si el grupo existe
        $query_verificar = "SELECT idgrupo FROM grupo WHERE idgrupo = $idgrupo";
        $resultado_verificar = mysqli_query($conn, $query_verificar);

        if (mysqli_num_rows($resultado_verificar) > 0) {
            // Intentar eliminar el grupo directamente con mysqli_query
            $query = "DELETE FROM grupo WHERE idgrupo = $idgrupo";
            if (mysqli_query($conn, $query)) {
                mysqli_close($conn);
                header('Location: listar_grupo.php?success=1');
                exit();
            } else {
                error_log("Error en la eliminación: " . mysqli_error($conn));
                mysqli_close($conn);
                header('Location: listar_grupo.php?error=No se pudo eliminar el grupo');
                exit();
            }
        } else {
            mysqli_close($conn);
            header('Location: listar_grupo.php?error=El grupo no existe');
            exit();
        }
    } else {
        header('Location: listar_grupo.php?error=ID no válido');
        exit();
    }
} else {
    header('Location: listar_grupo.php?error=No se proporcionó el ID');
    exit();
}
?>
