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

date_default_timezone_set('America/Caracas');

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
$pdf->SetTitle('Reporte de Citas (Edades por Categoria)');

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
<h1>Reporte de Citas (Edades por Categoria)</h1>
<h5> </h5>';

// Aquí agregamos el bloque de código para mostrar las fechas seleccionadas por el usuario o el mensaje de "Reporte general"
if (!empty($_POST['fecha_inicio']) && !empty($_POST['fecha_fin']) && !isset($_POST['ignore_dates'])) {
    $fecha_inicio = date("d-m-Y", strtotime($_POST['fecha_inicio']));
    $fecha_fin = date("d-m-Y", strtotime($_POST['fecha_fin']));
    $html .= '<h3 align="left">Se han seleccionado las citas desde el ' . $fecha_inicio . ' hasta el ' . $fecha_fin . '.</h3>';
} elseif (isset($_POST['ignore_dates'])) {
    $html .= '<h3 align="left">Se han generado citas sin filtro de fecha, mostrando un reporte general de todos los registros.</h3>';
}

$html .= '
<table border="1" cellspacing="0">
<thead>
<tr style="background-color: #f2f2f2; font-weight: bold; font-size: 14px;">
    <th rowspan="4">Pacientes</th>
    <th class="text-center" colspan="12">Edades por Categoría</th>
    <th class="text-center" rowspan="4">Total</th>
</tr>
<tr style="background-color: #f2f2f2; font-weight: bold; font-size: 14px;">
    <th class="text-center" colspan="4">Menores</th>
    <th class="text-center" colspan="4">Adultos</th>
    <th class="text-center" colspan="4">Adulto Mayor</th>
</tr>
<tr style="background-color: #f2f2f2; font-weight: bold; font-size: 14px;">
    <th class="text-center" colspan="2">P</th>
    <th class="text-center" colspan="2">S</th>
    <th class="text-center" colspan="2">P</th>
    <th class="text-center" colspan="2">S</th>
    <th class="text-center" colspan="2">P</th>
    <th class="text-center" colspan="2">S</th>
</tr>
<tr style="background-color: #f2f2f2; font-weight: bold; font-size: 14px;">
    <th>F</th>
    <th>M</th>
    <th>F</th>
    <th>M</th>
    <th>F</th>
    <th>M</th>
    <th>F</th>
    <th>M</th>
    <th>F</th>
    <th>M</th>
    <th>F</th>
    <th>M</th>
</tr>
</thead>';

// Obtener las fechas y la opción de ignorar fechas del formulario
$fecha_inicio = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : null;
$fecha_fin = isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : null;
$ignore_dates = isset($_POST['ignore_dates']) ? true : false;

// Verificar si se han enviado las fechas desde el formulario
if ($fecha_inicio && $fecha_fin && !$ignore_dates) {
    // Conexión a la base de datos (debes incluir tus propios datos de conexión)
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "bd_fasganz";

    // Crear conexión
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar la conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Definir consultas parametrizadas
    $consultas = array(
        "empleados_menorPF" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Empleado'  AND genero = 'F' AND categoria = 'Primario' AND DATEDIFF(NOW(), fecha_nac) / 365.25 < 18 AND fecha BETWEEN ? AND ?",
        "empleados_menorPM" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Empleado'  AND genero = 'M' AND categoria = 'Primario' AND DATEDIFF(NOW(), fecha_nac) / 365.25 < 18 AND fecha BETWEEN ? AND ?",
        "empleados_menorSF" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Empleado' AND genero = 'F'  AND categoria = 'Sucesivo' AND DATEDIFF(NOW(), fecha_nac) / 365.25 < 18 AND fecha BETWEEN ? AND ?",
        "empleados_menorSM" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Empleado' AND genero = 'M'  AND categoria = 'Sucesivo' AND DATEDIFF(NOW(), fecha_nac) / 365.25 < 18 AND fecha BETWEEN ? AND ?",
        // Agrega aquí el resto de consultas con nombres descriptivos
        "empleados_mayorPF" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Empleado' AND genero = 'F'  AND categoria = 'Primario' AND DATEDIFF(NOW(), fecha_nac) / 365.25 >= 18 AND DATEDIFF(NOW(), fecha_nac) / 365.25 < 60 AND fecha BETWEEN ? AND ?",
        "empleados_mayorPM" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Empleado' AND genero = 'M'  AND categoria = 'Primario' AND DATEDIFF(NOW(), fecha_nac) / 365.25 >= 18 AND DATEDIFF(NOW(), fecha_nac) / 365.25 < 60 AND fecha BETWEEN ? AND ?",
        "empleados_mayorSF" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Empleado' AND genero = 'F'  AND categoria = 'Sucesivo' AND DATEDIFF(NOW(), fecha_nac) / 365.25 >= 18 AND DATEDIFF(NOW(), fecha_nac) / 365.25 < 60 AND fecha BETWEEN ? AND ?",
        "empleados_mayorSM" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Empleado' AND genero = 'M'  AND categoria = 'Sucesivo' AND DATEDIFF(NOW(), fecha_nac) / 365.25 >= 18 AND DATEDIFF(NOW(), fecha_nac) / 365.25 < 60 AND fecha BETWEEN ? AND ?",
        // Agrega aquí el resto de consultas con nombres descriptivos
        "empleados_adultoMayorPF" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Empleado' AND genero = 'F'  AND categoria = 'Primario' AND DATEDIFF(NOW(), fecha_nac) / 365.25 >= 60 AND fecha BETWEEN ? AND ?",
        "empleados_adultoMayorPM" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Empleado' AND genero = 'M'  AND categoria = 'Primario' AND DATEDIFF(NOW(), fecha_nac) / 365.25 >= 60 AND fecha BETWEEN ? AND ?",
        "empleados_adultoMayorSF" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Empleado' AND genero = 'F'  AND categoria = 'Sucesivo' AND DATEDIFF(NOW(), fecha_nac) / 365.25 >= 60 AND fecha BETWEEN ? AND ?",
        "empleados_adultoMayorSM" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Empleado' AND genero = 'M'  AND categoria = 'Sucesivo' AND DATEDIFF(NOW(), fecha_nac) / 365.25 >= 60 AND fecha BETWEEN ? AND ?",

        "beneficiarios_menorPF" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Beneficiario' AND genero = 'F'  AND categoria = 'Primario' AND DATEDIFF(NOW(), fecha_nac) / 365.25 < 18 AND fecha BETWEEN ? AND ?",
        "beneficiarios_menorPM" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Beneficiario' AND genero = 'M'  AND categoria = 'Primario' AND DATEDIFF(NOW(), fecha_nac) / 365.25 < 18 AND fecha BETWEEN ? AND ?",
        "beneficiarios_menorSF" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Beneficiario' AND genero = 'F'  AND categoria = 'Sucesivo' AND DATEDIFF(NOW(), fecha_nac) / 365.25 < 18 AND fecha BETWEEN ? AND ?",
        "beneficiarios_menorSM" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Beneficiario' AND genero = 'M'  AND categoria = 'Sucesivo' AND DATEDIFF(NOW(), fecha_nac) / 365.25 < 18 AND fecha BETWEEN ? AND ?",
        // Agrega aquí el resto de consultas con nombres descriptivos
        "beneficiarios_mayorPF" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Beneficiario' AND genero = 'F'  AND categoria = 'Primario' AND DATEDIFF(NOW(), fecha_nac) / 365.25 >= 18 AND DATEDIFF(NOW(), fecha_nac) / 365.25 < 60 AND fecha BETWEEN ? AND ?",
        "beneficiarios_mayorPM" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Beneficiario' AND genero = 'M'  AND categoria = 'Primario' AND DATEDIFF(NOW(), fecha_nac) / 365.25 >= 18 AND DATEDIFF(NOW(), fecha_nac) / 365.25 < 60 AND fecha BETWEEN ? AND ?",
        "beneficiarios_mayorSF" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Beneficiario' AND genero = 'F'  AND categoria = 'Sucesivo' AND DATEDIFF(NOW(), fecha_nac) / 365.25 >= 18 AND DATEDIFF(NOW(), fecha_nac) / 365.25 < 60 AND fecha BETWEEN ? AND ?",
        "beneficiarios_mayorSM" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Beneficiario' AND genero = 'M'  AND categoria = 'Sucesivo' AND DATEDIFF(NOW(), fecha_nac) / 365.25 >= 18 AND DATEDIFF(NOW(), fecha_nac) / 365.25 < 60 AND fecha BETWEEN ? AND ?",
        // Agrega aquí el resto de consultas con nombres descriptivos
        "beneficiarios_adultoMayorPF" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Beneficiario' AND genero = 'F'  AND categoria = 'Primario' AND DATEDIFF(NOW(), fecha_nac) / 365.25 >= 60 AND fecha_registro BETWEEN ? AND ?",
        "beneficiarios_adultoMayorPM" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Beneficiario' AND genero = 'M'  AND categoria = 'Primario' AND DATEDIFF(NOW(), fecha_nac) / 365.25 >= 60 AND fecha_registro BETWEEN ? AND ?",
        "beneficiarios_adultoMayorSF" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Beneficiario' AND genero = 'F'  AND categoria = 'Sucesivo' AND DATEDIFF(NOW(), fecha_nac) / 365.25 >= 60 AND fecha BETWEEN ? AND ?",
        "beneficiarios_adultoMayorSM" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Beneficiario' AND genero = 'M'  AND categoria = 'Sucesivo' AND DATEDIFF(NOW(), fecha_nac) / 365.25 >= 60 AND fecha BETWEEN ? AND ?",
    );

    // Ejecutar consultas
    $resultados = array();
    foreach ($consultas as $nombreConsulta => $consulta) {
        $stmt = $conn->prepare($consulta);
        $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
        $stmt->execute();
        $resultados[$nombreConsulta] = $stmt->get_result()->fetch_assoc()['total'];
        $stmt->close();
    }

    $total_empleados = $resultados['empleados_menorPF'] + $resultados['empleados_menorPM'] + $resultados['empleados_menorSF'] + $resultados['empleados_menorSM'] + $resultados['empleados_mayorPF'] + $resultados['empleados_mayorPM'] + $resultados['empleados_mayorSF'] + $resultados['empleados_mayorSM'] + $resultados['empleados_adultoMayorPF'] + $resultados['empleados_adultoMayorPM'] + $resultados['empleados_adultoMayorSF'] + $resultados['empleados_adultoMayorSM'];

    $total_beneficiarios = $resultados['beneficiarios_menorPF'] + $resultados['beneficiarios_menorPM'] + $resultados['beneficiarios_menorSF'] + $resultados['beneficiarios_menorSM'] + $resultados['beneficiarios_mayorPF'] + $resultados['beneficiarios_mayorPM'] + $resultados['beneficiarios_mayorSF'] + $resultados['beneficiarios_mayorSM'] + $resultados['beneficiarios_adultoMayorPF'] + $resultados['beneficiarios_adultoMayorPM'] + $resultados['beneficiarios_adultoMayorSF'] + $resultados['beneficiarios_adultoMayorSM'];

    // Totales por columnas

    // Sumar total menores primarios
    $total_menoresPF = $resultados['empleados_menorPF'] + $resultados['beneficiarios_menorPF'];
    $total_menoresPM = $resultados['empleados_menorPM'] + $resultados['beneficiarios_menorPM'];
    // Sumar total menores sucesivos
    $total_menoresSF = $resultados['empleados_menorSF'] + $resultados['beneficiarios_menorSF'];
    $total_menoresSM = $resultados['empleados_menorSM'] + $resultados['beneficiarios_menorSM'];

    // Sumar total mayores primarios
    $total_mayoresPF = $resultados['empleados_mayorPF'] + $resultados['beneficiarios_mayorPF'];
    $total_mayoresPM = $resultados['empleados_mayorPM'] + $resultados['beneficiarios_mayorPM'];
    // Sumar total mayores sucesivos
    $total_mayoresSF = $resultados['empleados_mayorSF'] + $resultados['beneficiarios_mayorSF'];
    $total_mayoresSM = $resultados['empleados_mayorSM'] + $resultados['beneficiarios_mayorSM'];

    // Sumar total adultos mayores primarios
    $total_adultosMayoresPF = $resultados['empleados_adultoMayorPF'] + $resultados['beneficiarios_adultoMayorPF'];
    $total_adultosMayoresPM = $resultados['empleados_adultoMayorPM'] + $resultados['beneficiarios_adultoMayorPM'];
    // Sumar total adultos mayores sucesivos
    $total_adultosMayoresSF = $resultados['empleados_adultoMayorSF'] + $resultados['beneficiarios_adultoMayorSF'];
    $total_adultosMayoresSM = $resultados['empleados_adultoMayorSM'] + $resultados['beneficiarios_adultoMayorSM'];

    // Sumar total menores
    $total_menoresP = $total_menoresPF + $total_menoresPM;
    $total_menoresS = $total_menoresSF + $total_menoresSM;
    $total_menores = $total_menoresP + $total_menoresS;

    // Sumar total mayores
    $total_mayoresP = $total_mayoresPF + $total_mayoresPM;
    $total_mayoresS = $total_mayoresSF + $total_mayoresSM;
    $total_mayores = $total_mayoresP + $total_mayoresS;

    // Sumar total adultos mayores
    $total_adultos_mayoresP = $total_adultosMayoresPF + $total_adultosMayoresPM;
    $total_adultos_mayoresS = $total_adultosMayoresSF + $total_adultosMayoresSM;
    $total_adultos_mayores = $total_adultosMayoresP + $total_adultosMayoresS;


    // Total general
    $total_general = $total_empleados + $total_beneficiarios;


    // Cerrar conexión
    $conn->close();
} else {
    // Manejar caso de fechas no enviadas
    // Conexión a la base de datos (debes incluir tus propios datos de conexión)
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "bd_fasganz";

    // Crear conexión
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar la conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }
    // Definir consultas parametrizadas
    $consultas = array(
        "empleados_menorPF" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Empleado' AND genero = 'F' AND categoria = 'Primario' AND DATEDIFF(NOW(), fecha_nac) / 365.25 < 18",
        "empleados_menorPM" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Empleado' AND genero = 'M' AND categoria = 'Primario' AND DATEDIFF(NOW(), fecha_nac) / 365.25 < 18",
        "empleados_menorSF" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Empleado' AND genero = 'F' AND categoria = 'Sucesivo' AND DATEDIFF(NOW(), fecha_nac) / 365.25 < 18",
        "empleados_menorSM" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Empleado' AND genero = 'M' AND categoria = 'Sucesivo' AND DATEDIFF(NOW(), fecha_nac) / 365.25 < 18",
        // Agrega aquí el resto de consultas con nombres descriptivos
        "empleados_mayorPF" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Empleado' AND genero = 'F' AND categoria = 'Primario' AND DATEDIFF(NOW(), fecha_nac) / 365.25 >= 18 AND DATEDIFF(NOW(), fecha_nac) / 365.25 < 60",
        "empleados_mayorPM" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Empleado' AND genero = 'M' AND categoria = 'Primario' AND DATEDIFF(NOW(), fecha_nac) / 365.25 >= 18 AND DATEDIFF(NOW(), fecha_nac) / 365.25 < 60",
        "empleados_mayorSF" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Empleado' AND genero = 'F' AND categoria = 'Sucesivo' AND DATEDIFF(NOW(), fecha_nac) / 365.25 >= 18 AND DATEDIFF(NOW(), fecha_nac) / 365.25 < 60",
        "empleados_mayorSM" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Empleado' AND genero = 'M' AND categoria = 'Sucesivo' AND DATEDIFF(NOW(), fecha_nac) / 365.25 >= 18 AND DATEDIFF(NOW(), fecha_nac) / 365.25 < 60",
        // Agrega aquí el resto de consultas con nombres descriptivos
        "empleados_adultoMayorPF" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Empleado' AND genero = 'F' AND categoria = 'Primario' AND DATEDIFF(NOW(), fecha_nac) / 365.25 >= 60",
        "empleados_adultoMayorPM" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Empleado' AND genero = 'M' AND categoria = 'Primario' AND DATEDIFF(NOW(), fecha_nac) / 365.25 >= 60",
        "empleados_adultoMayorSF" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Empleado' AND genero = 'F' AND categoria = 'Sucesivo' AND DATEDIFF(NOW(), fecha_nac) / 365.25 >= 60",
        "empleados_adultoMayorSM" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Empleado' AND genero = 'M' AND categoria = 'Sucesivo' AND DATEDIFF(NOW(), fecha_nac) / 365.25 >= 60",

        "beneficiarios_menorPF" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Beneficiario' AND genero = 'F'  AND categoria = 'Primario' AND DATEDIFF(NOW(), fecha_nac) / 365.25 < 18",
        "beneficiarios_menorPM" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Beneficiario' AND genero = 'M'  AND categoria = 'Primario' AND DATEDIFF(NOW(), fecha_nac) / 365.25 < 18",
        "beneficiarios_menorSF" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Beneficiario' AND genero = 'F'  AND categoria = 'Sucesivo' AND DATEDIFF(NOW(), fecha_nac) / 365.25 < 18",
        "beneficiarios_menorSM" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Beneficiario' AND genero = 'M'  AND categoria = 'Sucesivo' AND DATEDIFF(NOW(), fecha_nac) / 365.25 < 18",
        // Agrega aquí el resto de consultas con nombres descriptivos
        "beneficiarios_mayorPF" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Beneficiario' AND genero = 'F'  AND categoria = 'Primario' AND DATEDIFF(NOW(), fecha_nac) / 365.25 >= 18 AND DATEDIFF(NOW(), fecha_nac) / 365.25 < 60",
        "beneficiarios_mayorPM" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Beneficiario' AND genero = 'M'  AND categoria = 'Primario' AND DATEDIFF(NOW(), fecha_nac) / 365.25 >= 18 AND DATEDIFF(NOW(), fecha_nac) / 365.25 < 60",
        "beneficiarios_mayorSF" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Beneficiario' AND genero = 'F'  AND categoria = 'Sucesivo' AND DATEDIFF(NOW(), fecha_nac) / 365.25 >= 18 AND DATEDIFF(NOW(), fecha_nac) / 365.25 < 60",
        "beneficiarios_mayorSM" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Beneficiario' AND genero = 'M'  AND categoria = 'Sucesivo' AND DATEDIFF(NOW(), fecha_nac) / 365.25 >= 18 AND DATEDIFF(NOW(), fecha_nac) / 365.25 < 60",
        // Agrega aquí el resto de consultas con nombres descriptivos
        "beneficiarios_adultoMayorPF" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Beneficiario' AND genero = 'F'  AND categoria = 'Primario' AND DATEDIFF(NOW(), fecha_nac) / 365.25 >= 60",
        "beneficiarios_adultoMayorPM" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Beneficiario' AND genero = 'M'  AND categoria = 'Primario' AND DATEDIFF(NOW(), fecha_nac) / 365.25 >= 60",
        "beneficiarios_adultoMayorSF" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Beneficiario' AND genero = 'F'  AND categoria = 'Sucesivo' AND DATEDIFF(NOW(), fecha_nac) / 365.25 >= 60",
        "beneficiarios_adultoMayorSM" => "SELECT COUNT(*) AS total FROM citas WHERE tipo_paciente = 'Beneficiario' AND genero = 'M'  AND categoria = 'Sucesivo' AND DATEDIFF(NOW(), fecha_nac) / 365.25 >= 60",
    );

    // Ejecutar consultas
    $resultados = array();
    foreach ($consultas as $nombreConsulta => $consulta) {
        $stmt = $conn->prepare($consulta);
        $stmt->execute();
        $resultados[$nombreConsulta] = $stmt->get_result()->fetch_assoc()['total'];
        $stmt->close();
    }

    $total_empleados = $resultados['empleados_menorPF'] + $resultados['empleados_menorPM'] + $resultados['empleados_menorSF'] + $resultados['empleados_menorSM'] + $resultados['empleados_mayorPF'] + $resultados['empleados_mayorPM'] + $resultados['empleados_mayorSF'] + $resultados['empleados_mayorSM'] + $resultados['empleados_adultoMayorPF'] + $resultados['empleados_adultoMayorPM'] + $resultados['empleados_adultoMayorSF'] + $resultados['empleados_adultoMayorSM'];

    $total_beneficiarios = $resultados['beneficiarios_menorPF'] + $resultados['beneficiarios_menorPM'] + $resultados['beneficiarios_menorSF'] + $resultados['beneficiarios_menorSM'] + $resultados['beneficiarios_mayorPF'] + $resultados['beneficiarios_mayorPM'] + $resultados['beneficiarios_mayorSF'] + $resultados['beneficiarios_mayorSM'] + $resultados['beneficiarios_adultoMayorPF'] + $resultados['beneficiarios_adultoMayorPM'] + $resultados['beneficiarios_adultoMayorSF'] + $resultados['beneficiarios_adultoMayorSM'];

    // Totales por columnas

    // Sumar total menores primarios
    $total_menoresPF = $resultados['empleados_menorPF'] + $resultados['beneficiarios_menorPF'];
    $total_menoresPM = $resultados['empleados_menorPM'] + $resultados['beneficiarios_menorPM'];
    // Sumar total menores sucesivos
    $total_menoresSF = $resultados['empleados_menorSF'] + $resultados['beneficiarios_menorSF'];
    $total_menoresSM = $resultados['empleados_menorSM'] + $resultados['beneficiarios_menorSM'];

    // Sumar total mayores primarios
    $total_mayoresPF = $resultados['empleados_mayorPF'] + $resultados['beneficiarios_mayorPF'];
    $total_mayoresPM = $resultados['empleados_mayorPM'] + $resultados['beneficiarios_mayorPM'];
    // Sumar total mayores sucesivos
    $total_mayoresSF = $resultados['empleados_mayorSF'] + $resultados['beneficiarios_mayorSF'];
    $total_mayoresSM = $resultados['empleados_mayorSM'] + $resultados['beneficiarios_mayorSM'];

    // Sumar total adultos mayores primarios
    $total_adultosMayoresPF = $resultados['empleados_adultoMayorPF'] + $resultados['beneficiarios_adultoMayorPF'];
    $total_adultosMayoresPM = $resultados['empleados_adultoMayorPM'] + $resultados['beneficiarios_adultoMayorPM'];
    // Sumar total adultos mayores sucesivos
    $total_adultosMayoresSF = $resultados['empleados_adultoMayorSF'] + $resultados['beneficiarios_adultoMayorSF'];
    $total_adultosMayoresSM = $resultados['empleados_adultoMayorSM'] + $resultados['beneficiarios_adultoMayorSM'];

    // Sumar total menores
    $total_menoresP = $total_menoresPF + $total_menoresPM;
    $total_menoresS = $total_menoresSF + $total_menoresSM;
    $total_menores = $total_menoresP + $total_menoresS;

    // Sumar total mayores
    $total_mayoresP = $total_mayoresPF + $total_mayoresPM;
    $total_mayoresS = $total_mayoresSF + $total_mayoresSM;
    $total_mayores = $total_mayoresP + $total_mayoresS;

    // Sumar total adultos mayores
    $total_adultos_mayoresP = $total_adultosMayoresPF + $total_adultosMayoresPM;
    $total_adultos_mayoresS = $total_adultosMayoresSF + $total_adultosMayoresSM;
    $total_adultos_mayores = $total_adultosMayoresP + $total_adultosMayoresS;


    // Total general
    $total_general = $total_empleados + $total_beneficiarios;


    // Cerrar conexión
    $conn->close();
}
// Verificar si el total general es igual a 0
if ($total_general == 0) {
    // Mostrar fila indicando que no hay registros
    $html .= '<tr><td colspan="14" style="text-align: center;">No hay registros para las fechas seleccionadas.</td></tr>';
} else {
// Aquí continúa el resto del HTML
$html .= '
<tbody>
        <!-- Aquí van los datos de la tabla -->
        <tr>
            <td style="background-color: #f2f2f2; font-weight: bold;">Empleado</td>
            <td>' . $resultados['empleados_menorPF'] . '</td>
            <td>' . $resultados['empleados_menorPM'] . '</td>
            <td>' . $resultados['empleados_menorSF'] . '</td>
            <td>' . $resultados['empleados_menorSM'] . '</td>
            <td>' . $resultados['empleados_mayorPF'] . '</td>
            <td>' . $resultados['empleados_mayorPM'] . '</td>
            <td>' . $resultados['empleados_mayorSF'] . '</td>
            <td>' . $resultados['empleados_mayorSM'] . '</td>
            <td>' . $resultados['empleados_adultoMayorPF'] . '</td>
            <td>' . $resultados['empleados_adultoMayorPM'] . '</td>
            <td>' . $resultados['empleados_adultoMayorSF'] . '</td>
            <td>' . $resultados['empleados_adultoMayorSM'] . '</td>
            <td>' . $total_empleados . '</td>
        </tr>
        <tr>
            <td style="background-color: #f2f2f2; font-weight: bold;">Beneficiario</td>
            <td>' . $resultados['beneficiarios_menorPF'] . '</td>
            <td>' . $resultados['beneficiarios_menorPM'] . '</td>
            <td>' . $resultados['beneficiarios_menorSF'] . '</td>
            <td>' . $resultados['beneficiarios_menorSM'] . '</td>
            <td>' . $resultados['beneficiarios_mayorPF'] . '</td>
            <td>' . $resultados['beneficiarios_mayorPM'] . '</td>
            <td>' . $resultados['beneficiarios_mayorSF'] . '</td>
            <td>' . $resultados['beneficiarios_mayorSM'] . '</td>
            <td>' . $resultados['beneficiarios_adultoMayorPF'] . '</td>
            <td>' . $resultados['beneficiarios_adultoMayorPM'] . '</td>
            <td>' . $resultados['beneficiarios_adultoMayorSF'] . '</td>
            <td>' . $resultados['beneficiarios_adultoMayorSM'] . '</td>
            <td>' . $total_beneficiarios . '</td>
        </tr>
    </tbody>

    <tfoot>
        <tr style="background-color: #f2f2f2; font-weight: bold; font-size: 14px;">
            <th></th>
            <td>' . $total_menoresPF . '</td>
            <td>' . $total_menoresPM . '</td>
            <td>' . $total_menoresSF . '</td>
            <td>' . $total_menoresSM . '</td>
            <td>' . $total_mayoresPF . '</td>
            <td>' . $total_mayoresPM . '</td>
            <td>' . $total_mayoresSF . '</td>
            <td>' . $total_mayoresSM . '</td>
            <td>' . $total_adultosMayoresPF . '</td>
            <td>' . $total_adultosMayoresPM . '</td>
            <td>' . $total_adultosMayoresSF . '</td>
            <td>' . $total_adultosMayoresSM . '</td>
            <td rowspan="4">' . $total_general . '</td>
        </tr>
        <tr style="background-color: #f2f2f2; font-weight: bold; font-size: 14px;">
            <td></td>
            <td colspan="2">' . $total_menoresP . '</td>
            <td colspan="2">' . $total_menoresS . '</td>
            <td colspan="2">' . $total_mayoresP . '</td>
            <td colspan="2">' . $total_mayoresS . '</td>
            <td colspan="2">' . $total_adultos_mayoresP . '</td>
            <td colspan="2">' . $total_adultos_mayoresS . '</td>
        </tr>
        <tr style="background-color: #f2f2f2; font-weight: bold; font-size: 14px;">
            <td></td>
            <td colspan="4">' . $total_menores . '</td>
            <td colspan="4">' . $total_mayores . '</td>
            <td colspan="4">' . $total_adultos_mayores . '</td>
        </tr>
        <tr>
            <th colspan="13" style="text-align: right; background-color: #f2f2f2; font-weight: bold; font-size: 14px;">Total Citas</th>
        </tr>
    </tfoot>';
}
$html .= '</tbody>
</table>';

$pdf->writeHTML($html, true, false, false, false, 'C');

// Mueve el puntero a la última página
$pdf->lastPage();
ob_end_clean();

// Close and output PDF document
$pdf->Output('reporte_citas_edadesCategoria.pdf', 'I');
