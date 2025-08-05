<?php
include('../db.php');
header('Content-Type: application/json');

try {
    if (isset($_POST['cedula_empleado'])) {
        // Eliminar puntos de la cédula
        $cedulaEmpleado = str_replace('.', '', $_POST['cedula_empleado']);

        if (!isset($conexion)) {
            echo json_encode(['success' => false, 'message' => 'Conexión a la base de datos no establecida.']);
            exit;
        }

        // Realiza la consulta usando mysqli
        $query = "SELECT CONCAT(nombre, ' ', apellido) AS nombre_completo, cedula, institucion, cargo FROM pacientes WHERE cedula = ?";
        $stmt = $conexion->prepare($query);

        // Comprueba si la preparación de la consulta tuvo éxito
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Error en la consulta a la base de datos.']);
            exit;
        }

        // Vincula el parámetro y ejecuta la consulta
        $stmt->bind_param("s", $cedulaEmpleado);
        $stmt->execute();
        $result = $stmt->get_result();

        // Verifica si se obtuvo un resultado
        if ($empleado = $result->fetch_assoc()) {
            // Formatea la cédula con puntos
            $cedulaFormateada = number_format($empleado['cedula'], 0, '', '.');

            echo json_encode([
                'success' => true,
                'empleado' => [
                    'nombre_completo' => $empleado['nombre_completo'],
                    'cedula' => $cedulaFormateada, // Cédula con formato
                    'institucion' => $empleado['institucion'],
                    'cargo' => $empleado['cargo']
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontró el empleado titular.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Cédula del empleado no proporcionada.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error en el servidor: ' . $e->getMessage()]);
}
