<?php

// Iniciar la sesión para acceder a $_SESSION
session_start();

// Mostrar errores de PHP para depuración
ini_set('display_errors', 1);
error_reporting(E_ALL);

include "../db.php";

// Definir la estructura del JSON de salida
$output = [
    "draw" => intval($_POST['draw']),
    "recordsTotal" => 0,
    "recordsFiltered" => 0,
    "data" => []
];

// Comprobar si la conexión con la base de datos fue exitosa
if (!$conexion) {
    $output["error"] = "Error de conexión a la base de datos.";
    echo json_encode($output);
    exit;
}

// Obtener el total de registros
$resultTotal = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM pacientes");
if (!$resultTotal) {
    $output["error"] = "Error al obtener el total de registros: " . mysqli_error($conexion);
    echo json_encode($output);
    exit;
}
$totalRecords = mysqli_fetch_assoc($resultTotal)['total'];
$output['recordsTotal'] = $totalRecords;
$output['recordsFiltered'] = $totalRecords;

// Función para formatear números con puntos
function formatCedula($cedula)
{
    return '<strong>' . number_format($cedula, 0, '', '.') . '</strong>';
}

// Definir columnas para el orden
$columns = [
    "CAST(cedula AS UNSIGNED)",
    "nombre",
    "apellido",
    "institucion",
    "edad",
    "años_servicio",
    "genero"
];

// Obtener índice y dirección de orden desde el request de DataTables o aplicar el orden predeterminado
$orderColumnIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
$orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';
$orderDir = ($orderDir === 'asc' || $orderDir === 'desc') ? $orderDir : 'asc';

$orderColumnName = isset($columns[$orderColumnIndex]) ? $columns[$orderColumnIndex] : 'CAST(cedula AS UNSIGNED)';
$orderClause = "$orderColumnName $orderDir";

// Obtener el término de búsqueda
$searchTerm = $_POST['search']['value'];

// Construir la consulta SQL con la búsqueda
$query = "SELECT * FROM pacientes WHERE 
    nombre LIKE '%$searchTerm%' OR 
    apellido LIKE '%$searchTerm%' OR 
    cedula LIKE '%$searchTerm%' OR 
    institucion LIKE '%$searchTerm%' 
    ORDER BY $orderClause 
    LIMIT {$_POST['start']}, {$_POST['length']}";

$result = mysqli_query($conexion, $query);
if (!$result) {
    $output["error"] = "Error en la consulta SQL: " . mysqli_error($conexion);
    echo json_encode($output);
    exit;
}

while ($row = mysqli_fetch_assoc($result)) {
    $row['nombre_completo'] = $row['nombre'] . ' ' . $row['apellido'];
    $row['cedula'] = formatCedula($row['cedula']);

    // Botón "Ver Paciente" siempre visible
    $acciones = '
      <button type="button" class="btn btn-infor btn-sm" data-toggle="modal" data-target="#verPacienteModal" data-id="' . $row['id'] . '">
        <i class="fa fa-eye"></i>
      </button>
    ';

    // Agregar botones de edición y eliminación solo si id_rol es 1
    if (isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == 1) {
        $acciones .= '
          <a class="btn btn-edit btn-sm" href="../includes/_paciente/editar_paciente.php?id=' . $row['id'] . '">
            <i class="fa fa-edit"></i>
          </a>
          <a href="../includes/_paciente/eliminar_paciente.php?id=' . $row['id'] . '" data-nombre="' . $row['nombre'] . '" data-apellido="' . $row['apellido'] . '" class="btn btn-del btn-sm btn-delete">
            <i class="fa fa-trash"></i>
          </a>
        ';
    }

    $row['acciones'] = $acciones;
    $output['data'][] = $row;
}

echo json_encode($output);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo 'Error en la codificación JSON: ' . json_last_error_msg();
}
