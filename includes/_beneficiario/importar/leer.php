<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Gestión de Citas - FASGANZ</title>

    <script src="../../../vendor/SweetAlert2/js/sweetalert2.all.min.js"></script>
    <script src="../../../vendor/JQuery/jquery-3.7.1.min.js"></script>

    <link rel="icon" href="../../../img/logo1.png" type="image/x-icon" />

</head>

<body>

</body>

</html>

<?php

require 'vendor/autoload.php';
require 'conexion.php'; // Asegúrate de que este archivo es el que contiene la conexión.

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

session_start(); // Asegúrate de que las sesiones están habilitadas para obtener el ID del usuario

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['archivoExcel'])) {
    $archivo = $_FILES['archivoExcel']['tmp_name'];
    $tipoArchivo = $_FILES['archivoExcel']['type'];

    // Verificar si el archivo es un archivo Excel válido (puedes ajustar los tipos permitidos según tus necesidades)
    $tiposPermitidos = [
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-excel'
    ];

    if ($archivo && in_array($tipoArchivo, $tiposPermitidos)) {
        try {
            $documento = IOFactory::load($archivo);
        } catch (Exception $e) {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El archivo no es válido o está corrupto.',
                    showConfirmButton: true,
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#034D81'
                }).then(function() {
                    window.location = '../../../views/pacientes.php';
                });
            </script>";
            exit();
        }

        $hojaActual = $documento->getSheet(1);
        $numeroFilas = $hojaActual->getHighestDataRow();

        $mysqli->begin_transaction();
        try {
            for ($indiceFila = 3; $indiceFila <= $numeroFilas; $indiceFila++) {
                $celdaCedulaEmpleado = $hojaActual->getCell('A' . $indiceFila)->getValue();
                $celdaNombre = $hojaActual->getCell('B' . $indiceFila)->getValue();
                $celdaApellido = $hojaActual->getCell('C' . $indiceFila)->getValue();
                $celdaCedulaBeneficiario = $hojaActual->getCell('D' . $indiceFila)->getValue();
                $celdaParentesco = $hojaActual->getCell('E' . $indiceFila)->getValue();
                $celdaFechaNac = $hojaActual->getCell('F' . $indiceFila)->getValue();
                if (!is_numeric($celdaFechaNac)) {
                    throw new Exception("El valor en la celda 'F$indiceFila' no es una fecha válida.");
                }
                $celdaFechaNac = Date::excelToDateTimeObject($celdaFechaNac)->format('Y-m-d');

                $celdaGenero = $hojaActual->getCell('G' . $indiceFila)->getValue();

                // Obtener la fecha y hora actual en Caracas
                date_default_timezone_set('America/Caracas');
                $celdaFechaRegistro = date('Y-m-d H:i:s');

                // Obtener el ID del usuario de la sesión
                $celdaEncargado = $_SESSION['user_id']; // Asegúrate de que 'usuario_id' es el nombre de la variable de sesión que almacena el ID del usuario

                // Verificación de que el empleado existe
                $consultaEmpleado = $mysqli->prepare("SELECT * FROM pacientes WHERE cedula = ?");
                $consultaEmpleado->bind_param("s", $celdaCedulaEmpleado);
                $consultaEmpleado->execute();
                $resultadoEmpleado = $consultaEmpleado->get_result();
                if ($resultadoEmpleado->num_rows == 0) {
                    throw new Exception("La cédula del empleado '$celdaCedulaEmpleado' no está registrada, verifique los datos.");
                }
                $consultaEmpleado->close();

                // Validación de cedula_beneficiario
                if (empty($celdaCedulaBeneficiario) || $celdaCedulaBeneficiario == '0' || !ctype_digit($celdaCedulaBeneficiario)) {
                    $celdaCedulaBeneficiario = "0"; // Asigna "N/T" si la cédula es inválida
                }

                // Verificación previa de la cédula del beneficiario para evitar duplicados con la misma cédula de empleado
                if ($celdaCedulaBeneficiario !== "0") {
                    $consultaBeneficiario = $mysqli->prepare("SELECT * FROM beneficiarios WHERE cedula_beneficiario = ? AND cedula_empleado = ?");
                    $consultaBeneficiario->bind_param("ss", $celdaCedulaBeneficiario, $celdaCedulaEmpleado);
                    $consultaBeneficiario->execute();
                    $resultadoBeneficiario = $consultaBeneficiario->get_result();
                    if ($resultadoBeneficiario->num_rows > 0) {
                        throw new Exception("La cédula del beneficiario '$celdaCedulaBeneficiario' ya está registrada con el empleado '$celdaCedulaEmpleado'. No se pueden duplicar estos registros.");
                    }
                    $consultaBeneficiario->close();
                }


                $consultaParentesco = $mysqli->prepare("SELECT * FROM beneficiarios WHERE cedula_empleado = ? AND parentesco = ? AND (parentesco = 'MADRE' OR parentesco = 'PADRE' OR parentesco = 'CONYUGUE' OR parentesco = 'ESPOSO(A)' OR parentesco = 'CONCUBINO(A)')");
                $consultaParentesco->bind_param("ss", $celdaCedulaEmpleado, $celdaParentesco);
                $consultaParentesco->execute();
                $resultadoParentesco = $consultaParentesco->get_result();
                if ($resultadoParentesco->num_rows > 0) {
                    throw new Exception("El empleado '$celdaCedulaEmpleado' ya tiene un beneficiario registrado con el parentesco '$celdaParentesco'. No se permiten duplicados en este parentesco.");
                }
                $consultaParentesco->close();

                $sql = "INSERT INTO beneficiarios (cedula_empleado, nombre, apellido, cedula_beneficiario, parentesco, fecha_nac, genero, fecha_registro, encargado_registro) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("sssssssss", $celdaCedulaEmpleado, $celdaNombre, $celdaApellido, $celdaCedulaBeneficiario, $celdaParentesco, $celdaFechaNac, $celdaGenero, $celdaFechaRegistro, $celdaEncargado);
                $stmt->execute();
            }
            $mysqli->commit();

            echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: 'Todos los datos han sido importados exitosamente.',
                showConfirmButton: true,
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#034D81',
                timer: 3000,
            }).then(function() {
                location.assign('../../../views/beneficiarios.php');
        });
        </script>";
        } catch (Exception $e) {
            $mysqli->rollback();
            echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '" . addslashes($e->getMessage()) . "',
                showConfirmButton: true,
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#034D81'
            }).then(function() {
                window.location = '../../../views/beneficiarios.php';
            });
        </script>";
        }
        $mysqli->close();
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'El archivo subido no es un archivo Excel válido.',
                showConfirmButton: true,
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#034D81'
            }).then(function() {
                window.location = '../../../views/beneficiarios.php';
            });
        </script>";
    }
} else {
    echo "<script>
    Swal.fire({
        icon: 'warning',
        title: 'Advertencia',
        text: 'No se ha subido ningún archivo.',
        showConfirmButton: true,
        confirmButtonText: 'Aceptar',
        confirmButtonColor: '#034D81'
    }).then(function() {
        window.location = '../../../views/beneficiarios.php';
    });
</script>";
}
?>