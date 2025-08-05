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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['archivoExcel'])) {
    $archivo = $_FILES['archivoExcel']['tmp_name'];

    if ($archivo) {
        $documento = IOFactory::load($archivo);
        $hojaActual = $documento->getSheet(0);
        $numeroFilas = $hojaActual->getHighestDataRow();

        $mysqli->begin_transaction();
        try {
            for ($indiceFila = 3; $indiceFila <= $numeroFilas; $indiceFila++) {
                $celdaCedula = $hojaActual->getCell('A' . $indiceFila)->getValue();
                $celdaNombre = $hojaActual->getCell('B' . $indiceFila)->getValue();
                $celdaApellido = $hojaActual->getCell('C' . $indiceFila)->getValue();
                $celdaCargo = $hojaActual->getCell('D' . $indiceFila)->getValue();
                $celdaInstitucion = $hojaActual->getCell('E' . $indiceFila)->getValue();
                $celdaFechaNac = Date::excelToDateTimeObject($hojaActual->getCell('F' . $indiceFila)->getValue())->format('Y-m-d');
                $celdaFechaIngreso = Date::excelToDateTimeObject($hojaActual->getCell('G' . $indiceFila)->getValue())->format('Y-m-d');
                $celdaGenero = $hojaActual->getCell('H' . $indiceFila)->getValue();
                $celdaFechaRegistro = Date::excelToDateTimeObject($hojaActual->getCell('I' . $indiceFila)->getValue())->format('Y-m-d');
                $celdaEncargado = $hojaActual->getCell('J' . $indiceFila)->getValue();

                // Verificación previa de la cédula para evitar duplicados
                $consulta = $mysqli->prepare("SELECT * FROM pacientes WHERE cedula = ?");
                $consulta->bind_param("s", $celdaCedula);
                $consulta->execute();
                $result = $consulta->get_result();
                if ($result->num_rows > 0) {
                    throw new Exception("La cédula '$celdaCedula' ya está registrada. Esto quiere decir que uno de los registros ya se encuentra en la base de datos y no se agregó ninguno registro del archivo, por favor verifique los datos e intentelo de nuevo.");
                }
                $consulta->close();

                $sql = "INSERT INTO pacientes (cedula, nombre, apellido, cargo, institucion, fecha_nac, fecha_ingreso, genero, fecha_registro, encargado_registro) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("ssssssssss", $celdaCedula, $celdaNombre, $celdaApellido, $celdaCargo, $celdaInstitucion, $celdaFechaNac, $celdaFechaIngreso, $celdaGenero, $celdaFechaRegistro, $celdaEncargado);
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
            location.assign('../../../views/pacientes.php');
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
                window.location = '../../../views/pacientes.php';
            });
        </script>";
        }
        $mysqli->close();
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al cargar el archivo Excel.',
                showConfirmButton: true,
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#034D81'
            }).then(function() {
                window.location = '../../../views/pacientes.php';
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
        window.location = '../../../views/pacientes.php';
    });
</script>";
}
?>