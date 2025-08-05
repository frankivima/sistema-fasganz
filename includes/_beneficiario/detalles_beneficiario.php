<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include "../db.php";

$response = [
    "success" => false,
    "paciente" => []
];

if (!$conexion) {
    echo json_encode($response);
    exit;
}

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Realiza la consulta para obtener los datos del beneficiario
    $query = "SELECT * FROM beneficiarios WHERE id = $id";
    $result = mysqli_query($conexion, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $beneficiario = mysqli_fetch_assoc($result);
        
        // Combina el nombre y apellido
        $beneficiario['nombre_completo'] = $beneficiario['nombre'] . ' ' . $beneficiario['apellido'];

        // Formatear la fecha de nacimiento
        $fechaNacimiento = new DateTime($beneficiario['fecha_nac']);
        $fechaNacFormateada = $fechaNacimiento->format('d/m/Y');

        // Calcular la edad
        $hoy = new DateTime();
        $diferencia = $hoy->diff($fechaNacimiento);
        $edad = $diferencia->y; // Guarda solo el número de años para la comparación

        // Formatear cédulas
        $cedulaBeneficiario = $beneficiario['cedula_beneficiario']; // Mantener el valor original para verificar
        $cedulaEmpleado = number_format($beneficiario['cedula_empleado'], 0, ',', '.');

        // Ajustar el género a "Masculino" o "Femenino"
        $genero = ($beneficiario['genero'] === 'M') ? 'Masculino' : 'Femenino';

        // Verificar el cedula_beneficiario
        if (empty($cedulaBeneficiario)) {
            if ($edad < 12) {
                $cedulaBeneficiario = "MENOR";
            } else {
                $cedulaBeneficiario = "IDENTIDAD NO PRESENTADA";
            }
        } else {
            // Formatear la cédula si existe
            $cedulaBeneficiario = number_format($cedulaBeneficiario, 0, ',', '.');
        }

        // Ajusta los campos según los detalles que quieres mostrar
        $response["success"] = true;
        $response["beneficiario"] = [
            "nombre_completo" => $beneficiario['nombre_completo'],
            "cedula_beneficiario" => $cedulaBeneficiario, // Cédula beneficiario formateada o mensaje
            "cedula_empleado" => $cedulaEmpleado,          // Cédula empleado formateada
            "fecha_nac" => $fechaNacFormateada,            // Fecha de nacimiento formateada
            "edad" => $edad . " años",                      // Edad calculada
            "genero" => $genero,                            // Género ajustado
            "parentesco" => $beneficiario['parentesco']
        ];
    } else {
        $response["error"] = "Beneficiario no encontrado.";
    }
} else {
    $response["error"] = "ID de beneficiario no proporcionado.";
}

echo json_encode($response);
?>
