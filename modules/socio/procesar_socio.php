<?php
// Habilitar la visualización de errores para depuración
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Incluir la conexión a la base de datos
include __DIR__ . "/../../includes/conexion.php";

// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    header('Content-Type: application/json'); // Enviar respuesta JSON

    // Verificar conexión a la base de datos
    if (!$conn) {
        echo json_encode(["success" => false, "message" => "Error de conexión a la base de datos"]);
        exit;
    }

    // Capturar datos del formulario
    $dni = trim($_POST['dni']);
    $nombre = trim($_POST['nombre']);
    $apellido_pat = trim($_POST['apellido_pat']);
    $apellido_mat = trim($_POST['apellido_mat']);
    $genero = isset($_POST['genero']) ? trim($_POST['genero']) : null;
    $departamento = trim($_POST['departamento']);
    $provincia = trim($_POST['provincia']);
    $distrito = trim($_POST['distrito']);

    // Validaciones
    if (empty($dni) || empty($nombre) || empty($apellido_pat) || empty($departamento) || empty($provincia) || empty($genero)) {
        echo json_encode(["success" => false, "message" => "Todos los campos obligatorios deben llenarse"]);
        exit;
    }

    if (!preg_match('/^\d{8}$/', $dni)) {
        echo json_encode(["success" => false, "message" => "DNI debe ser un número de 8 dígitos"]);
        exit;
    }

    // Verificar si el DNI ya existe
    $query_check_dni = "SELECT 1 FROM socio WHERE dni = ?";
    $stmt_check_dni = $conn->prepare($query_check_dni);
    $stmt_check_dni->bind_param("s", $dni);
    $stmt_check_dni->execute();
    $stmt_check_dni->store_result();

    if ($stmt_check_dni->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "DNI ya está registrado"]);
        $stmt_check_dni->close();
        exit;
    }
    $stmt_check_dni->close();

    // Insertar nuevo socio
    $query = "INSERT INTO socio (dni, nombre, apellido_pat, apellido_mat, genero, departamento, provincia, distrito) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("ssssssss", $dni, $nombre, $apellido_pat, $apellido_mat, $genero, $departamento, $provincia, $distrito);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Socio registrado correctamente"]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al registrar el socio: " . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Error en la consulta SQL"]);
    }

    $conn->close();
}
?>
