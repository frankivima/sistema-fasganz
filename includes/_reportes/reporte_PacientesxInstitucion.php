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
            date_default_timezone_set('America/Caracas');
            $fecha = date("d/m/Y"); 
            $html = '<table width="100%">
                    <tr>
                        <td align="left"><img src="../../img/logo1.png" width="175" height="75"></td>
                        <td align="right"><span style="font-size: 10pt;">Fecha de Elaboración: ' . $fecha . '</span></td>     
                    </tr>
                </table>';
            $this->writeHTML($html, true, false, false, false, '');
            // Agregar una línea horizontal después del encabezado
            $this->Ln(0); // Espacio antes de la línea
            $this->Line($this->GetX(), $this->GetY(), $this->GetX() + 190, $this->GetY()); // Dibujar la línea
            $this->Ln(25); // Agregar un espacio después de la línea
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
$pdf = new TOC_TCPDF('P', 'mm', array(215.9, 279.4), true, 'UTF-8', false);


// Establece la información del documento
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('');
$pdf->SetTitle('Reporte de Pacientes por Institución');

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

$pdf->SetFont('helvetica', '', 8);
$pdf->Ln(5);

// Iniciar la tabla
$html .= '
<style>
    h1{
        font-family: Arial, Helvetica, sans-serif;
        text-align: center;
        text-transform: uppercase;
        font-weight: bold;
    }
    table {
        border-collapse: collapse;
        margin-top: 280px;
        width: 100%;
    }
    th, td {
        border: 1px solid black;
        text-align: center;
        padding: 8px;
        font-size: 12px;
    }
    th {
        font-weight: bold;
    }
</style>
<h5> </h5>
<h1>Reporte Pacientes por Institución</h1>
<h5> </h5>';

// Aquí agregamos el bloque de código para mostrar las fechas seleccionadas por el usuario o el mensaje de "Reporte general"
if (!empty($_POST['fecha_inicio']) && !empty($_POST['fecha_fin']) && !isset($_POST['ignore_dates'])) {
    $fecha_inicio = date("d-m-Y", strtotime($_POST['fecha_inicio']));
    $fecha_fin = date("d-m-Y", strtotime($_POST['fecha_fin']));
    $html .= '<h3 align="left">Se han seleccionado los registros desde el ' . $fecha_inicio . ' hasta el ' . $fecha_fin . '.</h3>';
} elseif (isset($_POST['ignore_dates'])) {
    $html .= '<h3 align="left">Se han generado registros sin filtro de fecha, mostrando un reporte general de todos los ingresos.</h3>';
}

$html .= '
<style>
    table {
        border-collapse: collapse;
        margin-top: 280px;
        width: 100%;
    }
    th, td {
        border: 1px solid black;
        text-align: center;
        padding: 8px;
        font-size: 12px;
    }
    th {
        font-weight: bold;
    }
</style>

<table>
        <tr style="background-color: #f2f2f2; font-weight: bold; font-size: 14px;">
            <th class="mayus">Especialidades</th>
            <th class="mayus text-center">Empleados</th>
            <th class="mayus text-center">Beneficiarios</th>
            <th class="mayus text-center">Total</th>
        </tr>
    <tbody>
';

// Obtener las fechas y la opción de ignorar fechas del formulario
$fecha_inicio = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : null;
$fecha_fin = isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : null;
$ignore_dates = isset($_POST['ignore_dates']) ? true : false;

// Incluir el archivo de conexión a la base de datos
include "../db.php";

// Consulta SQL para obtener el total de pacientes por institución y el total de beneficiarios
$sql = "
    SELECT p.institucion, 
           COUNT(DISTINCT p.cedula) AS total_empleados, 
           COUNT(DISTINCT b.id) AS total_beneficiarios
    FROM pacientes p
    LEFT JOIN beneficiarios b ON p.cedula = b.cedula_empleado";

// Si se proporcionan fechas y no se ignora, agregar el filtro
if ($fecha_inicio && $fecha_fin && !$ignore_dates) {
    $sql .= " WHERE p.fecha_registro BETWEEN '$fecha_inicio' AND '$fecha_fin'";
}

$sql .= " GROUP BY p.institucion";

// Ejecutar la consulta
$result = mysqli_query($conexion, $sql);

// Variables para almacenar los totales de cada columna
$totalEmpleados = 0;
$totalBeneficiarios = 0;
$totalGeneral = 0;

// Verificar si hay resultados
if (mysqli_num_rows($result) > 0) {
    // Hay resultados, generar la tabla
    while ($row = mysqli_fetch_assoc($result)) {
        $html .= '<tr>';
        $html .= '<td class="bold mayus font-primary">' . $row['institucion'] . '</td>';
        $html .= '<td class="text-center">' . $row['total_empleados'] . '</td>';
        $html .= '<td class="text-center">' . $row['total_beneficiarios'] . '</td>';
        $html .= '<td class="text-center">' . ($row['total_empleados'] + $row['total_beneficiarios']) . '</td>';

        // Calcular el total de la fila y sumarlo al total general
        $total = $row['total_empleados'] + $row['total_beneficiarios'];
        $totalGeneral += $total;
        
        // Sumar a los totales de cada columna
        $totalEmpleados += $row['total_empleados'];
        $totalBeneficiarios += $row['total_beneficiarios'];
        

        $html .= '</tr>';
    }
} else {
    // No hay resultados, mostrar un mensaje
    $html .= '<tr>';
    $html .= '<td colspan="4" class="text-center">No se registraron Paciente (Empleados y Beneficiarios) entre las fechas seleccionadas.</td>';
    $html .= '</tr>';
}

// Mostrar los totales en el footer
$html .= '
    </tbody>
    <tfoot>
        <tr>
            <td style="text-align: right; font-weight: bold;">Totales</td>
            <td class="text-center">' . $totalEmpleados . '</td>
            <td class="text-center">' . $totalBeneficiarios . '</td>
            <td class="text-center">' . $totalGeneral . '</td>
        </tr>
    </tfoot>
</table>
';

$pdf->writeHTML($html, true, false, false, false, 'C');

// Mueve el puntero a la última página
$pdf->lastPage();
ob_end_clean();

// Close and output PDF document
$pdf->Output('reporte_pacientes_institucion.pdf', 'I');