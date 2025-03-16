<?php
include '../../includes/header.php';
include '../../includes/conexion.php';
?>

<head>
    <script>
        $(document).ready(function() {
            // Desvanecer las alertas después de 5 segundos
            setTimeout(function() {
                $(".alert").fadeOut("slow", function() {
                    $(this).remove();
                });
            }, 5000);

            // Quitar los parámetros de la URL después de mostrar la alerta
            if (window.history.replaceState) {
                let url = new URL(window.location.href);
                url.searchParams.delete("success");
                url.searchParams.delete("error");
                window.history.replaceState({}, document.title, url.href);
            }
        });
    </script>
</head>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4><i class="fas fa-calendar"></i> Registrar Día Laborable - Grupo</h4>
                </div>
                <div class="card-body">
                    
                    <!-- Mensajes de alerta -->
<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>¡Éxito!</strong> Relación Día Laborable - Grupo registrada correctamente.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php elseif (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error:</strong> <?= htmlspecialchars($_GET['error']) ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php elseif (isset($_GET['warning'])): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>Advertencia:</strong> <?= htmlspecialchars($_GET['warning']) ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

                    <form action="procesar_laborable.php" method="POST">
                        <div class="form-group">
                            <label for="dia_laborable_id">Día Laborable:</label>
                            <select name="dia_laborable_id" class="form-control" required>
                                <option value="">Selecciona un día laborable</option>
                                <?php
                                $dias = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"];
                                foreach ($dias as $index => $dia) {
                                    echo "<option value='$dia'>$dia</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="grupo_idgrupo">Grupo:</label>
                            <select name="grupo_idgrupo" class="form-control" required>
                                <option value="">Selecciona un grupo</option>
                                <?php
                                $query = "SELECT idgrupo, nombre_grupo FROM grupo";
                                $result = $conn->query($query);
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value='{$row['idgrupo']}'>{$row['nombre_grupo']}</option>";
                                    }
                                } else {
                                    echo "<option disabled>No hay grupos disponibles</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Botones de Guardar y Ver Lista -->
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Guardar
                            </button>
                            <a href="listar_laborable.php" class="btn btn-primary">
                                <i class="fas fa-list"></i> Ver Lista de Registrados
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
