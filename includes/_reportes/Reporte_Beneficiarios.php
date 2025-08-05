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
$pdf->SetTitle('Reporte de Pacientes Beneficiarios');

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

// Incluir el archivo de conexión a la base de datos
include "../db.php";

// Capturar los datos del formulario
$parentesco = $_POST['parentesco'];
$genero = $_POST['genero'];
$rangoEdad = $_POST['rangoEdad'];  // Capturar el rango de edad del formulario

// Construir la consulta SQL base
$sql = "SELECT * FROM beneficiarios";

// Traducir el rango de edad a texto legible
$edadTexto = '';
switch ($rangoEdad) {
    case 'MENORES':
        $edadTexto = "Menores de 18 años";
        break;
    case 'MAYORES':
        $edadTexto = "Mayores de 18 años";
        break;
    case 'MAYORES_25':
        $edadTexto = "Mayores de 25 años";
        break;
    case 'MAYORES_60':
        $edadTexto = "Mayores de 60 años";
        break;
    default:
        $edadTexto = '';
}

// Construir el mensaje según las selecciones del usuario
$mensaje = '';
$generoTexto = ($genero == 'F') ? 'Femenino' : (($genero == 'M') ? 'Masculino' : 'Indefinido');

if ($parentesco == 'TODAS' && $genero == 'TODAS' && $rangoEdad == '') {
    $mensaje = 'Reporte General de Pacientes Beneficiarios.';
} elseif ($parentesco != 'TODAS' || $genero != 'TODAS' || $rangoEdad != '') {
    $mensaje = 'Beneficiarios';
    if ($parentesco != 'TODAS') {
        $mensaje .= ' de Parentesco ' . $parentesco;
    }
    if ($genero != 'TODAS') {
        $mensaje .= ($parentesco != 'TODAS' ? ' de' : '') . ' Género ' . $generoTexto;
    }
    if ($rangoEdad != '') {
        $mensaje .= ($parentesco != 'TODAS' || $genero != 'TODAS' ? '' : '') . '  ' . $edadTexto;
    }
}

// Crear las condiciones de filtrado
$conditions = [];
if ($parentesco != 'TODAS') {
    $conditions[] = "parentesco = '$parentesco'";
}
if ($genero != 'TODAS') {
    $conditions[] = "genero = '$genero'";
}
if ($rangoEdad != '') {
    switch ($rangoEdad) {
        case 'MENORES':
            $conditions[] = "edad < 18";
            break;
        case 'MAYORES':
            $conditions[] = "edad >= 18";
            break;
        case 'MAYORES_25':
            $conditions[] = "edad > 25";
            break;
        case 'MAYORES_60':
            $conditions[] = "edad > 60";
            break;
    }
}
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(' AND ', $conditions);
}

$sql .= " ORDER BY CAST(cedula_empleado AS UNSIGNED) ASC, parentesco ASC";

// Ejecutar la consulta SQL
$result = mysqli_query($conexion, $sql);

// Establece el límite de registros por página
define('REGISTROS_POR_PAGINA', 28); // Ajusta este valor según tus necesidades
$registroCount = 0;
$totalBeneficiarios = 0;

// Iniciar la construcción del contenido HTML
$html = '
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
        height: 22px;
    }

    .page-break {
        page-break-before: always;
    }

    .margin-top {
        margin-top: 40px;
    }
</style>
<h1></h1>
<h1>Pacientes de tipo Beneficiarios</h1>
<h1></h1>

<h4 style="text-align: left; font-style: italic;">' . $mensaje . '</h4>';

$contenidoPagina = '
<table>
<tr>
    <th width="17%">Cédula Empleado</th>
    <th width="22%">Nombre Completo</th>
    <th width="20%">Cédula</th>
    <th width="20%">Parentesco</th>
    <th width="10%">Edad</th>
    <th width="10%">Género</th>
</tr>';

// Verificar si hay resultados
if (mysqli_num_rows($result) > 0) {
    while ($fila = mysqli_fetch_assoc($result)) {
        if ($registroCount % REGISTROS_POR_PAGINA == 0 && $registroCount > 0) {
            // Finaliza la tabla actual y añade un salto de página
            $contenidoPagina .= '</table>';
            $html .= $contenidoPagina;
            $html .= '<div class="page-break"></div>';

            // Inicia una nueva tabla para la siguiente página
            $contenidoPagina = '
            <div class="margin-top"></div>
            <div class="margin-top"></div>
            <div class="margin-top"></div>
            
            <table>
            <tr>
                <th width="17%">Cédula Empleado</th>
                <th width="22%">Nombre Completo</th>
                <th width="20%">Cédula</th>
                <th width="20%">Parentesco</th>
                <th width="10%">Edad</th>
                <th width="10%">Género</th>
            </tr>';
        }

        // Agregar una fila a la tabla
        $contenidoPagina .= '<tr>
            <td class="row-height">' . htmlspecialchars($fila['cedula_empleado']) . '</td>
            <td>' . htmlspecialchars($fila['nombre'] . ' ' . $fila['apellido']) . '</td>
            <td>';
        // Condición para mostrar "NO PRESENTADA" o "MENOR"
        if ($fila['cedula_beneficiario'] == 'N/T') {
            if ($fila['edad'] > 12) {
                $contenidoPagina .= 'NO PRESENTADA';
            } else {
                $contenidoPagina .= 'MENOR';
            }
        } else {
            $contenidoPagina .= htmlspecialchars($fila['cedula_beneficiario']);
        }
        $contenidoPagina .= '</td>
            <td>' . htmlspecialchars($fila['parentesco']) . '</td>
            <td>' . htmlspecialchars($fila['edad']) . '</td>
            <td>' . htmlspecialchars($fila['genero']) . '</td>
        </tr>';

        $registroCount++;
        $totalBeneficiarios++;
    }

    // Añadir el contenido final de la tabla con el total de registros solo en la última página
    $contenidoPagina .= '
    <tfoot>
        <tr>
            <td colspan="6" style="text-align: left; font-weight: bold; background-color: #f2f2f2;">Total de Beneficiarios: ' . $totalBeneficiarios . '</td>
        </tr>
    </tfoot>
    </table>';

    // Añadir el contenido de la última página
    $html .= $contenidoPagina;

    // Escribir el HTML en el PDF
    $pdf->writeHTML($html, true, false, false, false, 'C');
} else {
    $html .= '<tr><td colspan="6">No se encontraron resultados.</td></tr></table>';
    $pdf->writeHTML($html, true, false, false, false, 'C');
}

// Cierra el documento PDF
$pdf->Output($mensaje . ' - ' . date('d-m-Y') . '.pdf', 'I');
