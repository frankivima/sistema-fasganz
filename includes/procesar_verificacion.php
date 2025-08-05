<?php
// Conexión a la base de datos (ajusta los datos de conexión según tu configuración)
$conexion = mysqli_connect("localhost", "root", "", "bd_fasganz");
if (!$conexion) {
    die("Error al conectar con la base de datos: " . mysqli_connect_error());
}

// Función para formatear la cédula en formato de miles y millones
function formatearCedula($cedula) {
    if (ctype_digit($cedula) && !empty($cedula)) {
        return number_format($cedula, 0, '', '.');
    } else {
        return $cedula;
    }
}

// Obtener la cédula enviada desde el formulario
$cedula = $_POST['cedula_usuario'];

// Realizar la consulta a la base de datos para obtener los datos del usuario con esa cédula
$query_pacientes = "SELECT * FROM pacientes WHERE cedula = '$cedula'";
$resultado_pacientes = mysqli_query($conexion, $query_pacientes);

// Verificar si se encontraron resultados en la tabla de pacientes
if (mysqli_num_rows($resultado_pacientes) > 0) {
    // Se encontraron resultados en la tabla de pacientes

    // Mostrar los datos del titular
    echo "<ul class='list-group'>";
    while ($fila = mysqli_fetch_assoc($resultado_pacientes)) {
        echo "<li class='list-group-item d-flex justify-content-between align-items-center'>";
        echo "<div>";
        echo "<h3 class='mb-1 font-primary'>Datos del Titular:</h3>";

        echo "<br>";
        echo "<strong class='mb-1 font-secundary'>Nombre Completo:</strong> " . strtoupper($fila['nombre']) . ' ' . strtoupper($fila['apellido']) . "<br>";

        echo "<p class='mb-1'>";
        echo "<strong class='mb-1 font-secundary'>Cargo:</strong> " . $fila['cargo'] . "<br>";
        echo "<strong class='mb-1 font-secundary'>Institución:</strong> " . $fila['institucion'] . "<br>";

        // Cálculo de la edad del titular
        $fechaNacimiento = $fila['fecha_nac'];
        $fechaActual = date('Y-m-d');

        $fechaNacimientoObj = new DateTime($fechaNacimiento);
        $fechaActualObj = new DateTime($fechaActual);

        $edad = $fechaNacimientoObj->diff($fechaActualObj);

        echo "<strong class='mb-1 font-secundary'>Edad:</strong> " . $edad->format('%y años.') . "<br>";

        // Cálculo de los años y meses de servicio
        $fechaIngreso = $fila['fecha_ingreso'];
        $fechaActual = date('Y-m-d');

        $fechaIngresoObj = new DateTime($fechaIngreso);
        $fechaActualObj = new DateTime($fechaActual);

        $años_servicios = $fechaIngresoObj->diff($fechaActualObj);

        echo "<strong class='mb-1 font-secundary'>Años de Servicio:</strong> " . $años_servicios->format('%y años y %m meses.');

        echo "</p>";
        echo "</div>";
        echo "</li>";
    }
    echo "</ul>";

    // Realizar la consulta para obtener los beneficiarios asociados a esa cédula de usuario
    $query_beneficiarios = "SELECT * FROM beneficiarios WHERE cedula_empleado = '$cedula' ORDER BY
        CASE 
            WHEN parentesco = 'MADRE' THEN 1
            WHEN parentesco = 'PADRE' THEN 2
            WHEN parentesco = 'HIJO(A)' THEN 3
            WHEN parentesco IN ('ESPOSO(A)', 'CONYUGUE', 'CONCUBINO(A)') THEN 4
            ELSE 5
        END ASC";
    $resultado_beneficiarios = mysqli_query($conexion, $query_beneficiarios);

    // Verificar si se encontraron beneficiarios asociados al usuario principal
    if (mysqli_num_rows($resultado_beneficiarios) > 0) {
        // Se encontraron beneficiarios asociados al usuario principal
        echo "<div class='row mt-4'>";
        echo "<div class='col'>";
        echo "<p><small>Tiene un Total de " . mysqli_num_rows($resultado_beneficiarios) . " Beneficiarios Asociados.</small></p>";
        echo "</div>";
        echo "<div class='col'>";
        echo "<div class='d-flex justify-content-end'>";
        echo "<button type='button' class='btn btn-infor' data-toggle='collapse' data-target='#infoBeneficiarios' aria-expanded='false' aria-controls='infoBeneficiarios'>";
        echo "<i class='fa-solid fa-plus'></i> Mostrar Beneficiarios";
        echo "</button>";
        echo "</div>";
        echo "</div>";
        echo "</div>";

        echo "<div class='collapse' id='infoBeneficiarios'>";
        echo "<div class='card card-body mt-3'>";
        echo "<h5 class='bold mayus font-primary'>Lista de Beneficiarios</h5>";
        echo "<ul class='list-group' style='line-height: 1.5;'>";
        while ($fila_beneficiario = mysqli_fetch_assoc($resultado_beneficiarios)) {
            // Formatear la cédula del beneficiario
            $cedula_formateada = formatearCedula($fila_beneficiario['cedula_beneficiario']);
            
            // Calcular la edad del beneficiario
            $fechaNacimientoBeneficiario = $fila_beneficiario['fecha_nac'];
            $fechaNacimientoBeneficiarioObj = new DateTime($fechaNacimientoBeneficiario);
            $edadBeneficiario = $fechaNacimientoBeneficiarioObj->diff(new DateTime())->y;
            
            // Determinar el texto para cédula
            if ($fila_beneficiario['cedula_beneficiario'] === 'N/T') {
                $textoCedula = ($edadBeneficiario < 12) ? 'ES MENOR' : 'Cédula no presentada';
            } else {
                $textoCedula = $cedula_formateada;
            }

            echo "<li class='list-group-item'>";
            echo "<h5 class='font-rojo bold mayus'>" . $fila_beneficiario['parentesco'] . "</h5>";

            echo "<div class='row'>";
            echo "<div class='col'>";
            echo "<strong class='mb-1 font-secundary'>Nombre Completo:</strong> ". $fila_beneficiario['nombre'] . ' ' . $fila_beneficiario['apellido'] . "<br>";
            echo "</div>";
            echo "</div>";

            echo "<div class='row'>";
            echo "<div class='col'>";
            echo "<strong class='mb-1 font-secundary'>Cédula de Identidad:</strong> ". (isset($textoCedula) ? $textoCedula : 'No disponible') . "<br>";
            echo "</div>";
            echo "</div>";

            echo "<div class='row'>";
            echo "<div class='col-5'>";
            echo "<strong class='mb-1 font-secundary'>Edad:</strong> " . $fila_beneficiario['edad'] . " Años.<br>";
            echo "</div>";
            echo "</div>";

            echo "</li>";
        }
        echo "</ul>";
        echo "</div>";
        echo "</div>";
    } else {
        // No se encontraron beneficiarios asociados al usuario principal
        echo "<div class='alert alert-warning mt-3 d-flex align-items-center alert-dismissible fade show' role='alert'>";
        echo "<i class='fa-solid fa-info-circle'></i> Este usuario no tiene beneficiarios asociados.";
        echo "</div>";
    }
} else {
    // No se encontraron resultados en la tabla de pacientes
    echo "<div class='alert alert-danger mt-3 d-flex align-items-center alert-dismissible fade show' role='alert'>";
    echo "<i class='fa-solid fa-ban'></i> La cédula ingresada no se encuentra registrada en el Sistema.";
    echo "</div>";
}

// Cerrar la conexión a la base de datos
mysqli_close($conexion);
?>
