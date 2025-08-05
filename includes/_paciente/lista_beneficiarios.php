<?php
// Incluir el archivo de conexión a la base de datos
require_once '../db.php';

// Verificar si se recibió la cédula del empleado
if (isset($_POST['cedula_empleado'])) {
    $cedulaEmpleado = $_POST['cedula_empleado'];

    // Eliminar los puntos de la cédula
    $cedulaEmpleado = str_replace('.', '', $cedulaEmpleado);

    // Preparar la consulta para obtener los beneficiarios
    $query = "SELECT * FROM beneficiarios WHERE cedula_empleado = ?";
    $stmt = mysqli_prepare($conexion, $query);

    if ($stmt) {
        // Vincular el parámetro
        mysqli_stmt_bind_param($stmt, "s", $cedulaEmpleado); // Cambia "s" por "i" si cedula_empleado es un entero
        mysqli_stmt_execute($stmt);

        // Obtener el resultado
        $result = mysqli_stmt_get_result($stmt);
        $beneficiarios = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // Verificar si se encontraron beneficiarios
        if ($beneficiarios) {
            $formateados = [];

            foreach ($beneficiarios as $beneficiario) {
                // Concatenar nombre y apellido
                $nombreCompleto = trim($beneficiario['nombre'] . ' ' . $beneficiario['apellido']);

                // Inicializar cedulaBeneficiario
                $cedulaBeneficiario = '';

                // Verificar si cedula_beneficiario está disponible
                if (!empty($beneficiario['cedula_beneficiario']) && $beneficiario['cedula_beneficiario'] != 0) {
                    // Formatear cédula en millones
                    $cedulaBeneficiario = number_format($beneficiario['cedula_beneficiario'], 0, '', '.');
                } else {
                    // Condición para presentar la cédula
                    if ($beneficiario['edad'] < 12) {
                        $cedulaBeneficiario = "MENOR";
                    } else {
                        $cedulaBeneficiario = "IDENTIDAD NO PRESENTADA";
                    }
                }

                // Agregar "años" a la edad
                $edadConAnios = $beneficiario['edad'] . ' años';

                // Formatear parentesco
                $parentesco = $beneficiario['parentesco'];

                // Ajustar el género a "Masculino" o "Femenino"
                $genero = ($beneficiario['genero'] === 'M') ? 'Masculino' : 'Femenino';

                // Agregar el beneficiario formateado al nuevo array
                $formateados[] = [
                    'nombre_completo' => $nombreCompleto,
                    'cedula' => $cedulaBeneficiario,
                    'edad' => $edadConAnios,
                    'parentesco' => $parentesco,
                    'genero' => $genero,
                ];
            }

            echo json_encode(['success' => true, 'beneficiarios' => $formateados]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontraron beneficiarios.']);
        }

        // Cerrar la declaración
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error en la preparación de la consulta: ' . mysqli_error($conexion)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Cédula no recibida.']);
}

// Cerrar la conexión
mysqli_close($conexion);
