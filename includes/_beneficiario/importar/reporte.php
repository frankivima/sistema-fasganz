<?php

require 'vendor/autoload.php';
require 'conexion.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

$parentesco = $mysqli->real_escape_string($_POST['parentesco']);
$genero = $mysqli->real_escape_string($_POST['genero']);

$sql = "SELECT * FROM beneficiarios WHERE parentesco LIKE '$parentesco' AND genero LIKE '$genero'";
$resultado = $mysqli->query($sql);

$excel = new Spreadsheet();
$hojaActiva = $excel->getActiveSheet();
$hojaActiva->setTitle("Beneficiarios");

$hojaActiva->setCellValue('A1', 'CEDULA EMPLEADO');
$hojaActiva->setCellValue('B1', 'NOMBRE');
$hojaActiva->setCellValue('C1', 'APELLIDO');
$hojaActiva->setCellValue('D1', 'CEDULA BENEFICIARIO');
$hojaActiva->setCellValue('E1', 'PARENTESCO');
$hojaActiva->setCellValue('F1', 'GENERO');
$hojaActiva->setCellValue('G1', 'FECHA NACIMIENTO');
$hojaActiva->setCellValue('H1', 'EDAD');

$fila = 2;

while ($rows = $resultado->fetch_assoc()) {

    $hojaActiva->setCellValue('A' . $fila, $rows['cedula_empleado']);
    $hojaActiva->setCellValue('B' . $fila, $rows['nombre']);
    $hojaActiva->setCellValue('C' . $fila, $rows['apellido']);
    $hojaActiva->setCellValue('D' . $fila, $rows['cedula_beneficiario']);
    $hojaActiva->setCellValue('E' . $fila, $rows['parentesco']);
    $hojaActiva->setCellValue('F' . $fila, $rows['genero']);
    $hojaActiva->setCellValue('G' . $fila, $rows['fecha_nac']);
    $hojaActiva->setCellValue('H' . $fila, $rows['edad']);
    $fila++;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Reporte Pacientes Tipo Beneficiarios.xlsx"');
header('Cache-Control: max-age=0');

$writer = IOFactory::createWriter($excel, 'Xlsx');
$writer->save('php://output');

exit;
