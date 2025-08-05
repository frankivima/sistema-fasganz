<?php
// Seguridad de sesiones
session_start();
error_reporting(0);
$varsesion = $_SESSION['fasganz'];

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
$pdf->SetTitle('Reporte del Personal Médicos');

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
$pdf->Ln(5);
// Iniciar la tabla
$html .= '
<style>
    h1, h3 {
        font-family: Arial, Helvetica, sans-serif;
        text-align: center;
        text-transform: uppercase;
    }

    table {
        border-collapse: collapse;
        margin-top: 20px;
        width: 100%;
    }
    th {
        border: 1px solid black;
        text-align: center;
        font-size: 12px;
        font-weight: bold;
        background-color: #f2f2f2;
    }
    td {
        border: 1px solid black;
        text-align: center;
        padding: 8px;
        font-size: 10px;
    }

    .row-height {
        height: 25px;
    }
</style>

<h1></h1>
<h1>Personal Médico</h1>
<h1></h1>

';


// Función para obtener el día de la semana
function numeroADia($numero) {
    $dias = [
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado',
        7 => 'Domingo'
    ];
    return isset($dias[$numero]) ? $dias[$numero] : 'N/T';
}


// Incluir el archivo de conexión a la base de datos
include "../db.php";

// Capturar los datos del formulario
$especialidad = $_POST['especialidad'];
$horario = $_POST['horario'];

// Construir la consulta SQL base
$sql = "SELECT * FROM medicos";


// Obtener el día de la semana del número
$diaHorario = ($horario != 'TODAS') ? numeroADia($horario) : 'TODAS';

// Construir el mensaje según las selecciones del usuario
// Construir el mensaje según las selecciones del usuario
$mensaje = '';
if ($especialidad == 'TODAS' && $horario == 'TODAS') {
    $mensaje = 'Reporte General del Personal Médico.';
} elseif ($especialidad != 'TODAS' && $horario != 'TODAS') {
    $mensaje = 'Médicos de la Especialidad de ' . $especialidad . ' que Atienden los Días ' . $diaHorario . '.';
} elseif ($especialidad != 'TODAS' && $horario == 'TODAS') {
    $mensaje = 'Personal Médico de la Especialidad ' . $especialidad . '.';
} elseif ($especialidad == 'TODAS' && $horario != 'TODAS') {
    $mensaje = 'Personal Médico que Atienden los Días ' . $diaHorario . '.';
}

// Agregar condiciones de filtrado si se seleccionó un departamento o un tipo de equipo
if (!empty($especialidad) && $especialidad != 'TODAS') {
    $sql .= " WHERE especialidad = '$especialidad'";
    if (!empty($horario) && $horario != 'TODAS') {
        $sql .= " AND horario = '$horario'";
    }
} elseif (!empty($horario) && $horario != 'TODAS') {
    $sql .= " WHERE horario = '$horario'";
}

// Ordenar los resultados
$sql .= " ORDER BY especialidad ASC, horario ASC";

// Ejecutar la consulta SQL
$result = mysqli_query($conexion, $sql);

// Incluir el mensaje en el reporte
$html .= '<h4 style="text-align: left; font-style: italic;">' . $mensaje . '</h4>';

$html .= '

<table>

<tr>
    <th>Cédula</th>
    <th>Nombre Completo</th>
    <th>Edad</th>
    <th>Especialidad</th>
    <th>Horario</th>
    <th>Teléfono</th>
    <th>Email</th>
</tr>';

// Verificar si hay resultados
if (mysqli_num_rows($result) > 0) {
    // Recorrer los resultados y agregar filas a la tabla
    while ($fila = mysqli_fetch_assoc($result)) {
        $html .= '<tr>';
        $html .= '<td class="row-height">' . ($fila['cedula'] ? $fila['cedula'] : 'N/T') . '</td>';
        $html .= '<td>' . ($fila['nombre'] ? $fila['nombre'] . ' ' . $fila['apellido'] : 'N/T') . '</td>';
        $html .= '<td>' . ($fila['edad'] ? $fila['edad'] : 'N/T') . '</td>';
        $html .= '<td>' . ($fila['especialidad'] ? $fila['especialidad'] : 'N/T') . '</td>';
        $html .= '<td>' . numeroADia($fila['horario']) . '</td>';  // Convertir número a día aquí
        $html .= '<td>' . ($fila['telefono'] ? $fila['telefono'] : 'N/T') . '</td>';
        $html .= '<td>' . ($fila['email'] ? $fila['email'] : 'N/T') . '</td>';

        $html .= '</tr>';
    }
} else {
    // No hay resultados, mostrar un mensaje
    $html .= '<tr>';
    $html .= '<td colspan="8" class="text-center">No se encontraron registros.</td>';
    $html .= '</tr>';
}

// Obtener el número total de filas en el resultado de la consulta
$totalFilas = mysqli_num_rows($result);

$html .= '
    </table>
    <tfoot>
        <tr>
            <td colspan="8" style="text-align: left; font-weight: bold; background-color: #f2f2f2;">Total de Médicos: ' . $totalFilas . '</td>
        </tr>
    </tfoot>
';


$html .= '</table>';


$pdf->writeHTML($html, true, false, false, false, 'C');

// Mueve el puntero a la última página
$pdf->lastPage();
ob_end_clean();

// Close and output PDF document
$pdf->Output($mensaje . ' - ' . date('d-m-Y') . '.pdf', 'I');
