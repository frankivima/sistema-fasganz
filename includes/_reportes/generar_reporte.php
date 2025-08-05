<?php
// Seguridad de sesiones
session_start();
error_reporting(0);
$varsesion = $_SESSION['fasganz'];

if ($varsesion == null || $varsesion == '' || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 3)) {
    echo '
    <script>
        alert("No tienes permiso para acceder a esta página.");
        // Redirecciona al usuario a la página de inicio de sesión
        window.location.href = "../includes/_sesion/login.php";
    </script>';
    die(); // Detiene la ejecución del código si no tiene permiso
}

// Incluye la clase TCPDF
require_once('../TCPDF-main/examples/tcpdf_include.php');

/**
 * Clase extendida de TCPDF con encabezado y pie de página personalizados para la página de TOC (Tabla de contenido).
 */
class TOC_TCPDF extends TCPDF
{

    /**
     * Método para sobrescribir el encabezado.
     * @public
     */
    public function Header()
    {
        if ($this->tocpage) {
            // Aquí puedes agregar el encabezado personalizado para la página de TOC
            // Puedes personalizar el contenido del encabezado aquí
            // Puedes usar HTML para el encabezado si es necesario
            $html = '<table width="100%">
                    <tr>
                        <td align="left"><img src="../../img/logo1.png" width="110" height="50"></td>
                        <td align="right"><img src="../../img/logo2.png" width="100" height="50"></td>
                    </tr>
                </table>';
            $this->writeHTML($html, true, false, false, false, '');
            // Agregar una línea horizontal después del encabezado
            $this->Ln(0); // Espacio antes de la línea
            $this->Line($this->GetX(), $this->GetY(), $this->GetX() + 180, $this->GetY()); // Dibujar la línea
        } else {
            // Usa el encabezado normal para otras páginas
            parent::Header();
        }
    }

    /**
     * Método para sobrescribir el pie de página.
     * @public
     */
    public function Footer()
    {
        // No necesitas modificar el pie de página en este caso, pero puedes hacerlo si es necesario
        // Si deseas personalizar el pie de página para otras páginas, puedes hacerlo aquí
        parent::Footer();
    }
}

// Crea un nuevo documento PDF
$pdf = new TOC_TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Establece la información del documento
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('');
$pdf->SetTitle('Reporte de Historias Médicas');

// Establece los datos del encabezado predeterminado
// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 001', PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));
$pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));

// Establece las fuentes del encabezado y pie de página
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Establece la fuente monoespaciada predeterminada
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Establece los márgenes
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Establece los saltos de página automáticos
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Establece el factor de escala de la imagen
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


// Agrega una página para la tabla de contenido (TOC)
$pdf->addTOCPage();

$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 5, 'Fecha del Reporte: ' . date('d-m-Y'), 0, 1, 'L');  // Agregar la fecha actual
$pdf->Ln(5);
// Iniciar la tabla
$html .= '
<style>
    h1, h3 {
        font-family: Arial, Helvetica, sans-serif;
        text-align: center;
    }
</style>
<h1>Reporte de Historia Médicas Por Pacientes</h1>
<h3>Detalles de Historia</h3>

';

$html .= '
<style>
    table {
        border-collapse: collapse;
        margin-top: 20px;
        width: 100%;
    }
    th, td {
        border: 1px solid black;
        text-align: center;
        padding: 8px;
    }
    th {
        font-weight: bold;
    }
</style>

<table>
<tr style="background-color: #f2f2f2; font-weight: bold; font-size: 14px;">
    <th>Nº de Historia</th>
    <th>Cédula Paciente</th>
    <th>Info. Paciente</th>
</tr>';


// Incluir el archivo de conexión a la base de datos
include "../db.php";

// Ejecutar la consulta SQL para obtener las historias médicas
$result = mysqli_query($conexion, "SELECT h.id, 
                                          CONCAT(p.nombres, ' ', p.apellidos) AS nombre_paciente,
                                          p.cedula_paciente
                                   FROM historias_medicas h
                                   JOIN historia_pacientes p ON h.id_paciente = p.id_paciente");

// Recorrer los resultados y agregar filas a la tabla
while ($fila = mysqli_fetch_assoc($result)) {
    $html .= '<tr>';
    $html .= '<td>' . 'HM' . str_pad($fila['id'], 6, '0', STR_PAD_LEFT) . '</td>';
    $html .= '<td>' . $fila['cedula_paciente'] . '</td>';
    $html .= '<td>' . $fila['nombre_paciente'] . '</td>';
    $html .= '</tr>';
}

// Obtener el número total de filas en el resultado de la consulta
$totalFilas = mysqli_num_rows($result);

$html .= '
    </table>
    <tfoot>
        <tr>
            <td colspan="3" style="text-align: left; font-weight: bold;" background-color: #f2f2f2;>Total de Historias Registradas: ' . $totalFilas . '</td>
        </tr>
    </tfoot>
';

$html .= '</table>';


$pdf->writeHTML($html, true, false, false, false, 'C');

// Mueve el puntero a la última página
$pdf->lastPage();
ob_end_clean();

// Close and output PDF document
$pdf->Output('reporte_historial_medico.pdf', 'I');
