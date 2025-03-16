<?php
include '../../includes/header.php';
include '../../includes/conexion.php';
// Obtener mensaje de éxito o error desde la URL
$mensaje_exito = isset($_GET['success']) ? "¡Éxito! Registro guardado correctamente." : null;
$mensaje_error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : null;
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Agrupamiento</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4><i class="fas fa-layer-group"></i> Registrar Nuevo Agrupamiento</h4>
                </div>
                <div class="card-body">
                    
                    <!-- Incluir el sistema de alertas -->
                    <?php include '../../includes/alerta.php'; ?>

                    <form action="procesar_agrupamiento.php" method="POST">

                         <div class="form-group">
                            <label for="cod_etiqueta">Codigo agrupamiento:</label>
                            <input type="text" name="cod_etiqueta" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="agrupamientocol">Nombre del Agrupamiento:</label>
                            <input type="text" name="agrupamientocol" class="form-control" required>
                        </div>

                        <!-- Botones de Guardar y Ver Lista -->
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Guardar
                            </button>
                            <a href="listar_agrupamiento.php" class="btn btn-primary">
                                <i class="fas fa-list"></i> Ver Lista
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS y FontAwesome -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>

<?php include '../../includes/footer.php'; ?>
