<?php
include "../includes/db.php";

// Consulta para obtener las instituciones y la cantidad de empleados por cada una
$sqlInstituciones = "SELECT institucion, COUNT(*) as total FROM pacientes GROUP BY institucion";
$resultadoInstituciones = mysqli_query($conexion, $sqlInstituciones);

// Arrays para almacenar los datos
$instituciones = [];
$totales = [];
$totalesSinFormato = [];

// Verifica que la consulta haya devuelto resultados
if ($resultadoInstituciones) {
    // Recorre los resultados y los almacena en los arrays
    while ($fila = mysqli_fetch_assoc($resultadoInstituciones)) {
        $instituciones[] = $fila['institucion'];
        
        // Formato con puntos para mostrar en tooltips
        $totalFormateado = number_format($fila['total'], 0, ',', '.');
        $totales[] = $totalFormateado;
        
        // Sin formato para el gráfico (valor numérico)
        $totalesSinFormato[] = (int) str_replace('.', '', $totalFormateado);
    }
} else {
    // En caso de error, manejarlo aquí
    echo "Error en la consulta: " . mysqli_error($conexion);
}


////////////////////////////////////////////////////////////////////////////

// Consulta para obtener el total de pacientes por género
$sqlGenero = "SELECT genero, COUNT(*) as total FROM pacientes GROUP BY genero";
$resultadoGenero = mysqli_query($conexion, $sqlGenero);

// Arrays para almacenar los datos
$generos = [];
$totalesGenero = [];

// Verifica que la consulta haya devuelto resultados
if ($resultadoGenero) {
    // Recorre los resultados y los almacena en los arrays
    while ($fila = mysqli_fetch_assoc($resultadoGenero)) {
        $genero = ($fila['genero'] == 'F') ? 'Femenino' : 'Masculino';
        $generos[] = $genero;
        $totalesGenero[] = number_format($fila['total'], 0, '', '.'); // Formato miles/millones
    }
} else {
    // En caso de error, manejarlo aquí
    echo "Error en la consulta: " . mysqli_error($conexion);
}

////////////////////////////////////////////////////////////////////////////////

// Consulta para obtener el total de beneficiarios por género
$sqlGeneroBeneficiarios = "SELECT genero, COUNT(*) as total FROM beneficiarios GROUP BY genero";
$resultadoGeneroBeneficiarios = mysqli_query($conexion, $sqlGeneroBeneficiarios);

// Arrays para almacenar los datos
$generosBeneficiarios = [];
$totalesBeneficiarios = [];

// Verifica que la consulta haya devuelto resultados
if ($resultadoGeneroBeneficiarios) {
    // Recorre los resultados y los almacena en los arrays
    while ($fila = mysqli_fetch_assoc($resultadoGeneroBeneficiarios)) {
        if ($fila['genero'] == 'F') {
            $generosBeneficiarios[] = 'Femenino';
        } else if ($fila['genero'] == 'M') {
            $generosBeneficiarios[] = 'Masculino';
        }
        $totalesBeneficiarios[] = number_format($fila['total'], 0, '', '.'); // Formato miles/millones
    }
} else {
    echo "Error en la consulta: " . mysqli_error($conexion);
}


?>
