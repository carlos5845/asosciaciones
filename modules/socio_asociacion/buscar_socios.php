<?php
include '../../includes/conexion.php';

$search = isset($_GET['q']) ? $_GET['q'] : '';  // El término de búsqueda (DNI)
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Página actual para paginación

// Definir el límite de resultados por página
$limit = 10;
$offset = ($page - 1) * $limit;

// Consulta para buscar socios solo por DNI
$query = "SELECT idsocio, CONCAT(nombre, ' ', apellido_pat, ' ', apellido_mat) AS text
          FROM socio
          WHERE dni LIKE ?
          LIMIT $limit";

$stmt = $conn->prepare($query);
$search_term = "%" . $search . "%";  // Se utiliza el LIKE para búsqueda parcial
$stmt->bind_param("s", $search_term); // Solo se pasa el parámetro DNI
$stmt->execute();

$result = $stmt->get_result();

// Crear un array para almacenar los resultados
$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = [
        'id' => $row['idsocio'],
        'text' => $row['text']
    ];
}

// Devolver los resultados como un JSON
echo json_encode([
    'items' => $items,
    'more' => false // Aquí podrías agregar lógica para manejar la paginación si es necesario
]);

$stmt->close();
$conn->close();
?>
