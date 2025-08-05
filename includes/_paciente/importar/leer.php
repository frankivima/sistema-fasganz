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
        
        $hojaActual = $documento->getSheet(0);
        $numeroFilas = $hojaActual->getHighestDataRow();

        $mysqli->begin_transaction();
        try {
            for ($indiceFila = 3; $indiceFila <= $numeroFilas; $indiceFila++) {
                // Obtener y limpiar los valores de las celdas
                $celdaCedula = trim($hojaActual->getCell('A' . $indiceFila)->getValue());
                
                // Ignorar filas completamente vacías
                if (empty($celdaCedula)) {
                    continue;
                }

                $celdaNombre = trim($hojaActual->getCell('B' . $indiceFila)->getValue());
                $celdaApellido = trim($hojaActual->getCell('C' . $indiceFila)->getValue());
                $celdaCargo = trim($hojaActual->getCell('D' . $indiceFila)->getValue());
                $celdaInstitucion = trim($hojaActual->getCell('E' . $indiceFila)->getValue());
                
                // Manejo de fecha de nacimiento (F)
                $celdaFechaNac = trim($hojaActual->getCell('F' . $indiceFila)->getValue());
                if (empty($celdaFechaNac)) {
                    throw new Exception("La celda 'F$indiceFila' está vacía. Este campo es obligatorio.");
                }
                if (is_numeric($celdaFechaNac)) {
                    // Convertir usando el serial de fecha de Excel
                    $fechaNac = Date::excelToDateTimeObject($celdaFechaNac);
                } else {
                    // Intentar parsear como cadena de fecha
                    $fechaNac = DateTime::createFromFormat('Y-m-d', $celdaFechaNac);
                    if (!$fechaNac) {
                        // Intentar con otros formatos comunes
                        $fechaNac = date_create($celdaFechaNac);
                    }
                }
                if (!$fechaNac) {
                    throw new Exception("El valor en la celda 'F$indiceFila' no es una fecha válida.");
                }
                $celdaFechaNac = $fechaNac->format('Y-m-d');

                // Manejo de fecha de ingreso (H)
                $celdaFechaIngreso = trim($hojaActual->getCell('H' . $indiceFila)->getValue());
                if (empty($celdaFechaIngreso)) {
                    throw new Exception("La celda 'H$indiceFila' está vacía. Este campo es obligatorio.");
                }
                if (is_numeric($celdaFechaIngreso)) {
                    // Convertir usando el serial de fecha de Excel
                    $fechaIngreso = Date::excelToDateTimeObject($celdaFechaIngreso);
                } else {
                    // Intentar parsear como cadena de fecha
                    $fechaIngreso = DateTime::createFromFormat('Y-m-d', $celdaFechaIngreso);
                    if (!$fechaIngreso) {
                        // Intentar con otros formatos comunes
                        $fechaIngreso = date_create($celdaFechaIngreso);
                    }
                }
                if (!$fechaIngreso) {
                    throw new Exception("El valor en la celda 'H$indiceFila' no es una fecha válida.");
                }
                $celdaFechaIngreso = $fechaIngreso->format('Y-m-d');
            
                $celdaGenero = trim($hojaActual->getCell('G' . $indiceFila)->getValue());
                
                // Obtener la fecha y hora actual en Caracas
                date_default_timezone_set('America/Caracas');
                $celdaFechaRegistro = date('Y-m-d H:i:s');
                
                // Obtener el ID del usuario de la sesión
                $celdaEncargado = $_SESSION['user_id']; // Asegúrate de que 'user_id' es el nombre de la variable de sesión que almacena el ID del usuario

                // Verificación previa de la cédula para evitar duplicados
                $consulta = $mysqli->prepare("SELECT * FROM pacientes WHERE cedula = ?");
                $consulta->bind_param("s", $celdaCedula);
                $consulta->execute();
                $result = $consulta->get_result();
                if ($result->num_rows > 0) {
                    throw new Exception("La cédula '$celdaCedula' ya está registrada. Esto significa que uno de los registros en el archivo ya se encuentra en la base de datos. Por lo tanto, toda la importación ha sido cancelada. Por favor, corrija los registros en el archivo y vuelva a intentarlo.");
                }
                $consulta->close();

                // Insertar los datos en la base de datos
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
                text: 'El archivo subido no es un archivo Excel válido.',
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
