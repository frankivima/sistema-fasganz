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
$resultTotal = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM beneficiarios");
if (!$resultTotal) {
    $output["error"] = "Error al obtener el total de registros: " . mysqli_error($conexion);
    echo json_encode($output);
    exit;
}
$totalRecords = mysqli_fetch_assoc($resultTotal)['total'];
$output['recordsTotal'] = $totalRecords;
$output['recordsFiltered'] = $totalRecords;

// Función para formatear la cédula con puntos o mostrar texto alternativo
function formatCedula($cedula, $edad)
{
    if (is_numeric($cedula) && $cedula != '0') {
        // Formatea la cédula si es un número válido
        return '<strong>' . number_format($cedula, 0, '', '.') . '</strong>';
    } else {
        // Define el texto según la edad si el valor de cédula es inválido o vacío
        if ($edad < 12) {
            return '<strong class="font-rojo">MENOR</strong>';
        } else {
            return '<strong class="font-rojo">IDENTIDAD NO PRESENTADA</strong>';
        }
    }
}


// Función para formatear números con puntos
function formatFechaNacimiento($fecha_nac)
{
    return '<strong>' . date('d-m-Y', strtotime($fecha_nac['fecha_nac'])) . '</strong>';
}


// Definir columnas para el orden
$columns = [
    "CAST(cedula_empleado AS UNSIGNED)",
    "nombre",
    "apellido",
    "parentesco",
    "edad",
    "genero",
    "IF(cedula_beneficiario REGEXP '^[0-9]+$', CAST(cedula_beneficiario AS UNSIGNED), cedula_beneficiario)"
];

// Obtener índice y dirección de orden desde el request de DataTables o aplicar el orden predeterminado
$orderColumnIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
$orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';
$orderDir = ($orderDir === 'asc' || $orderDir === 'desc') ? $orderDir : 'asc';

// Mapear el índice de la columna al nombre de la columna en la base de datos o usar el orden por defecto
$orderColumnName = isset($columns[$orderColumnIndex]) ? $columns[$orderColumnIndex] : 'CAST(cedula_empleado AS UNSIGNED)';

// Construir la cláusula ORDER BY
$orderClause = "$orderColumnName $orderDir";

// Obtener el término de búsqueda
$searchTerm = $_POST['search']['value'];

// Construir la consulta SQL con la búsqueda y el orden
$query = "SELECT * FROM beneficiarios WHERE 
    cedula_empleado LIKE '%$searchTerm%' OR
    nombre LIKE '%$searchTerm%' OR 
    apellido LIKE '%$searchTerm%' OR 
    cedula_beneficiario LIKE '%$searchTerm%' OR
    parentesco LIKE '%$searchTerm%' 
     ORDER BY $orderClause 
    LIMIT {$_POST['start']}, {$_POST['length']}";

// Obtener el total de registros (sin filtrado)
$resultTotal = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM beneficiarios");
if (!$resultTotal) {
    $output["error"] = "Error al obtener el total de registros: " . mysqli_error($conexion);
    echo json_encode($output);
    exit;
}
$totalRecords = mysqli_fetch_assoc($resultTotal)['total'];
$output['recordsTotal'] = $totalRecords;

// Obtener el total de registros después del filtrado
$resultFiltered = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM beneficiarios WHERE 
    cedula_empleado LIKE '%$searchTerm%' OR
    nombre LIKE '%$searchTerm%' OR 
    apellido LIKE '%$searchTerm%' OR 
    cedula_beneficiario LIKE '%$searchTerm%' OR
    parentesco LIKE '%$searchTerm%'");
if (!$resultFiltered) {
    $output["error"] = "Error al obtener el total de registros filtrados: " . mysqli_error($conexion);
    echo json_encode($output);
    exit;
}
$filteredRecords = mysqli_fetch_assoc($resultFiltered)['total'];
$output['recordsFiltered'] = $filteredRecords;

// Ejecutar la consulta SQL para obtener los datos
$result = mysqli_query($conexion, $query);
if (!$result) {
    $output["error"] = "Error en la consulta SQL: " . mysqli_error($conexion);
    echo json_encode($output);
    exit;
}

// Procesar los datos obtenidos

while ($row = mysqli_fetch_assoc($result)) {
    $row['nombre_completo'] = $row['nombre'] . ' ' . $row['apellido']; // Combina el nombre y apellido

    // Formatear la cédula con puntos
    $row['cedula_empleado'] = formatCedula($row['cedula_empleado'], $row['edad']);

    // Formatear la cédula del beneficiario
    $row['cedula_beneficiario'] = formatCedula($row['cedula_beneficiario'], $row['edad']);

    // Botón "Ver Paciente" siempre visible
    $acciones = '
      <button type="button" class="btn btn-infor btn-sm" data-toggle="modal" data-target="#verBeneficiarioModal" data-id="' . $row['id'] . '">
        <i class="fa fa-eye"></i>
    </button>
    ';

    // Agregar botones de edición y eliminación solo si id_rol es 1
    if (isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == 1) {
        $acciones .= '
          <a class="btn btn-edit btn-sm" href="../includes/_beneficiario/editar_beneficiario.php?id=' . $row['id'] . '">
            <i class="fa fa-edit"></i>
        </a>

         <a href="../includes/_beneficiario/eliminar_beneficiario.php?id=' . $row['id'] . '" data-nombre="' . $row['nombre'] . '" data-apellido="' . $row['apellido'] . '" class="btn btn-del btn-sm btn-delete">
        <i class="fa fa-trash"></i>
        ';
    }

    $row['acciones'] = $acciones;
    $output['data'][] = $row;
}

// Codificar el resultado como JSON
echo json_encode($output);

// Comprobar si hay errores de JSON
if (json_last_error() !== JSON_ERROR_NONE) {
    echo 'Error en la codificación JSON: ' . json_last_error_msg();
}
