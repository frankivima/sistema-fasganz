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
$pdf = new TOC_TCPDF('V', 'mm', array(220, 280), true, 'UTF-8', false);

// Establece la información del documento
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('');
$pdf->SetTitle('Reporte de Pacientes (Empleados)');

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

// Establece la fuente y el tamaño del texto para el título
$pdf->SetFont('helvetica', 'B', 16); // Negrita, tamaño de letra 16

// Agrega un salto de línea y luego imprime el título
$pdf->Ln(15); // Espacio de 15 unidades antes del título
$pdf->Cell(0, 10, 'PACIENTES DE TIPO EMPLEADOS', 0, false, 'C', 0, '', 0, false, 'M', 'M');
$pdf->Ln(20); // Espacio de 20 unidades después del título



// Mensaje
$mensaje = '';

// Incluir el archivo de conexión a la base de datos
include "../db.php";

// Capturar los datos del formulario
$institucion = $_POST['institucion'];
$genero = $_POST['genero'];

// Construir la consulta SQL base
$sql = "SELECT * FROM pacientes";

// Construir el mensaje según las selecciones del usuario
$generoTexto = ($genero == 'F') ? 'Femenino' : (($genero == 'M') ? 'Masculino' : 'Indefinido');
if ($institucion == 'TODAS' && $genero == 'TODAS') {
    $mensaje = 'Reporte General de Pacientes Empleados.';
} elseif ($institucion != 'TODAS' && $genero != 'TODAS') {
    $mensaje = 'Empleados de la institución ' . $institucion . ', y género ' . $generoTexto . '.';
} elseif ($institucion != 'TODAS' && $genero == 'TODAS') {
    $mensaje = 'Empleados de la institución ' . $institucion . '.';
} elseif ($institucion == 'TODAS' && $genero != 'TODAS') {
    $mensaje = 'Empleados de Género ' . $generoTexto . '.';
}

// Agregar condiciones de filtrado si se seleccionó un departamento o un tipo de equipo
if (!empty($institucion) && $institucion != 'TODAS') {
    $sql .= " WHERE institucion = '$institucion'";
    if (!empty($genero) && $genero != 'TODAS') {
        $sql .= " AND genero = '$genero'";
    }
} elseif (!empty($genero) && $genero != 'TODAS') {
    $sql .= " WHERE genero = '$genero'";
}

// Ordenar los resultados
$sql .= " ORDER BY institucion ASC, CAST(cedula AS UNSIGNED) ASC";

// Ejecutar la consulta SQL
$result = mysqli_query($conexion, $sql);

// Imprimir el mensaje en la primera página antes de la tabla
$pdf->SetY(60); // Ajusta la posición vertical para el mensaje
$pdf->SetX(15); // Ajusta la posición horizontal para el mensaje
$pdf->SetFont('helvetica', 'BI', 10); // Negrita, cursiva, tamaño de letra 12
$pdf->MultiCell(0, 10, $mensaje, 0, 'L'); // Imprime el mensaje con salto de línea automático

// Restablece la fuente y el tamaño original
$pdf->SetFont('helvetica', '', 10);

// Inicializar el contador de registros
$contador_registros = 0;

// Dividir el resultado en múltiples páginas con un límite de registros por página
while ($fila = mysqli_fetch_assoc($result)) {
    // Si el contador de registros es múltiplo del límite deseado, agregar el contenido actual a la tabla y reiniciarla
    if ($contador_registros % 25 == 0) {
        // Si no es la primera página, cerrar la tabla actual y forzar un salto de página
        if ($contador_registros != 0) {
            $html .= '</table>';
            $pdf->writeHTML($html, true, false, false, false, 'C');
            $pdf->AddPage();
            $pdf->Ln(20); // Espacio de 20 unidades después del título
        }
        // Iniciar una nueva tabla para la próxima página
        $html = '<style>
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
                font-size: 9px;
            }
    
            .row-height {
                height: 22px;
            }
        </style>
        <table>
            <tr>
                <th width="10%">Cédula</th>
                <th width="28%">Nombre Completo</th>
                <th width="20%">Cargo</th>
                <th width="18%">Institución</th>
                <th width="7%">Edad</th>
                <th width="9%">Años Servicio</th>
                <th width="8%">Género</th>
            </tr>';
    }

    // Agregar la fila actual a la tabla
    $html .= '<tr>';
    $html .= '<td class="row-height">' . ($fila['cedula'] ? $fila['cedula'] : 'N/T') . '</td>';
    $html .= '<td>' . ($fila['nombre'] ? $fila['nombre'] . ' ' . $fila['apellido'] : 'N/T') . '</td>';
    $html .= '<td>' . ($fila['cargo'] ? $fila['cargo'] : 'N/T') . '</td>';
    $html .= '<td>' . ($fila['institucion'] ? $fila['institucion'] : 'N/T') . '</td>';
    $html .= '<td>' . ($fila['edad'] ? $fila['edad'] : 'N/T') . '</td>';
    $html .= '<td>' . ($fila['años_servicio'] ? $fila['años_servicio'] : 'N/T') . '</td>';
    $html .= '<td>' . ($fila['genero'] ? $fila['genero'] : 'N/T') . '</td>';
    $html .= '</tr>';

    // Incrementar el contador de registros
    $contador_registros++;
}

// Si no hay resultados, mostrar un mensaje
if ($contador_registros == 0) {
    $html .= '<tr>';
    $html .= '<td colspan="7" class="text-center">No se encontraron registros.</td>';
    $html .= '</tr>';
}


$html .= '<tr><td colspan="7" style="text-align: left; font-weight: bold; background-color: #f2f2f2; border: 1px solid black;">Total de Empleados: <strong>' . $contador_registros . '</strong></td></tr>';

// Cerrar la tabla actual
$html .= '</table>';


// Escribir el HTML final al PDF
$pdf->writeHTML($html, true, false, false, false, 'C');

// Salida del PDF
ob_end_clean();
$pdf->Output($mensaje . ' - ' . date('d-m-Y') . '.pdf', 'I');
