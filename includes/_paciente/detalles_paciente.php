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

    $query = "SELECT * FROM pacientes WHERE id = $id";
    $result = mysqli_query($conexion, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $paciente = mysqli_fetch_assoc($result);

        // Combina el nombre y apellido
        $paciente['nombre_completo'] = $paciente['nombre'] . ' ' . $paciente['apellido'];

        // Formatear la fecha de nacimiento y calcular la edad
        $fechaNacimiento = new DateTime($paciente['fecha_nac']);
        $fechaNacFormateada = $fechaNacimiento->format('d/m/Y');
        $hoy = new DateTime();
        $edad = $hoy->diff($fechaNacimiento)->y . " años";

        // Formatear la fecha de ingreso y calcular años, meses y días de servicio
        $fechaIngreso = new DateTime($paciente['fecha_ingreso']);
        $fechaIngresoFormateada = $fechaIngreso->format('d/m/Y');
        $diferenciaServicio = $hoy->diff($fechaIngreso);
        $aniosServicio = $diferenciaServicio->y . " años, " . $diferenciaServicio->m . " meses, " . $diferenciaServicio->d . " días";

        // Formatear cédula
        $cedulaFormateada = number_format($paciente['cedula'], 0, ',', '.');

        // Ajustar el género a "Masculino" o "Femenino"
        $genero = ($paciente['genero'] === 'M') ? 'Masculino' : 'Femenino';

        // Consulta para obtener el total de beneficiarios del paciente
        $cedulaPaciente = $paciente['cedula'];
        $queryBeneficiarios = "SELECT COUNT(*) as total_beneficiarios FROM beneficiarios WHERE cedula_empleado = ?";
        $stmtBeneficiarios = $conexion->prepare($queryBeneficiarios);
        $stmtBeneficiarios->bind_param("s", $cedulaPaciente);
        $stmtBeneficiarios->execute();
        $resultBeneficiarios = $stmtBeneficiarios->get_result();
        $totalBeneficiarios = $resultBeneficiarios->fetch_assoc()['total_beneficiarios'];

        // Ajustar los campos del paciente
        $response["success"] = true;
        $response["paciente"] = [
            "nombre_completo" => $paciente['nombre_completo'],
            "cedula" => $cedulaFormateada,
            "fecha_nac" => $fechaNacFormateada,
            "edad" => $edad,
            "fecha_ingreso" => $fechaIngresoFormateada,
            "años_servicio" => $aniosServicio,
            "institucion" => $paciente['institucion'],
            "cargo" => $paciente['cargo'],
            "genero" => $genero,
            "total_beneficiarios" => $totalBeneficiarios // Total de beneficiarios
        ];
    } else {
        $response["error"] = "Paciente no encontrado.";
    }
} else {
    $response["error"] = "ID de paciente no proporcionado.";
}

echo json_encode($response);
?>
