<?php
session_start();
error_reporting(0);
$varsesion = $_SESSION['fasganz'];

if ($varsesion == null || $varsesion == '') {
    header("Location: ../_sesion/login.php");
}

// Realizar la consulta para obtener el nombre y apellido del encargado
$idEncargado = $fila['encargado_registro'];
$consultaEncargado = "SELECT nombre, apellido FROM usuarios WHERE id = $idEncargado";
$resultadoEncargado = mysqli_query($conexion, $consultaEncargado);

if ($resultadoEncargado) {
    $filaEncargado = mysqli_fetch_assoc($resultadoEncargado);
    $nombreEncargado = $filaEncargado['nombre'];
    $apellidoEncargado = $filaEncargado['apellido'];
} else {
    // Manejar el caso de error si la consulta no se ejecuta correctamente
    $nombreEncargado = "No disponible";
    $apellidoEncargado = "";
}

$fechaNacimiento = $fila['fecha_nac'];
$fechaActual = date('Y-m-d');

$fechaNacimientoObj = new DateTime($fechaNacimiento);
$fechaActualObj = new DateTime($fechaActual);

$edad = $fechaNacimientoObj->diff($fechaActualObj);

// Suponiendo que $cedula_empleado es la cédula del empleado obtenida previamente
$cedula_empleado = $fila['cedula_empleado'];

// Consulta para obtener la información del titular
$query_titular = "SELECT nombre AS nombre_titular, apellido AS apellido_titular, cargo, institucion FROM pacientes WHERE cedula = ?";
$stmtTitular = $conexion->prepare($query_titular);
$stmtTitular->bind_param("s", $cedula_empleado);
$stmtTitular->execute();
$result_titular = $stmtTitular->get_result();
$titular_info = $result_titular->fetch_assoc();

// Asignar valores a variables para su uso en el HTML
$nombre_titular = $titular_info['nombre_titular'] ?? 'Sin Especificar';
$apellido_titular = $titular_info['apellido_titular'] ?? 'Sin Especificar';
$cargo_titular = $titular_info['cargo'] ?? 'Sin Especificar';
$institucion_titular = $titular_info['institucion'] ?? 'Sin Especificar';
?>

<style>
    .custom-badge {
        font-size: 16px;
    }
</style>


<!-- Contenido del modal para ver la cita -->
<div class="modal fade" id="verBeneficiarioModal<?php echo $fila['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="verBeneficiarioModalLabel<?php echo $fila['id']; ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title bold mayus" id="verCitaModalLabel<?php echo $fila['id']; ?>">Detalles de Beneficiario</h5>
                <button type="button" class="close btn" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">

                    <div class="card mb-1 px-5">

                        <div class="row g-0">
                            <div class="col-lg-3 col-md-2 col-sm-0 mt-3">
                                <img src="../img/logo1.png" class="img-fluid rounded-start" alt="...">
                            </div>
                            <div class="col-lg-9 col-md-10 col-sm-12 text-left">
                                <div class="card-body">
                                    <h2 class="card-title font-dark bold mayus">Detalles de Beneficiario</h2>
                                </div>
                            </div>
                        </div>
                    </div>


                    <hr class="m-0">

                    <div class="container mt-4">

                        <div class="row g-0 mx-5">
                            <div class="col-md-12 text-justify font-dark">

                                <div class="row">
                                    <div class="col-5">
                                        <p><strong class="bold mayus">Nombre Completo:</strong></p>
                                    </div>
                                    <div class="col">
                                        <p><?php echo ($fila['nombre'] && $fila['apellido']) ? $fila['nombre'] . ' ' . $fila['apellido'] : 'N/T'; ?></p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-5">
                                        <p><strong class="bold mayus">cédula de Identidad:</strong></p>
                                    </div>
                                    <div class="col">
                                        <p><?php echo $fila['cedula_beneficiario'] ?: 'N/T'; ?></p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-5">
                                        <p><strong class="bold mayus">F/ Nacimiento:</strong></p>
                                    </div>
                                    <div class="col">
                                        <p><?php echo date('d-m-Y', strtotime($fila['fecha_nac'])) ?: 'N/T'; ?></p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-5">
                                        <p><strong class="bold mayus">Edad:</strong></p>
                                    </div>
                                    <div class="col">
                                        <p><?php echo $edad->format('%y años') ?: 'N/T'; ?></p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-5">
                                        <p><strong class="bold mayus">Género:</strong></p>
                                    </div>
                                    <div class="col">
                                        <p>
                                            <?php
                                            $genero = $fila['genero'] ?? ''; // Obtén el valor de genero, o una cadena vacía si no está definido
                                            if (strtolower($genero) === 'f') {
                                                echo 'Femenino';
                                            } elseif (strtolower($genero) === 'm') {
                                                echo 'Masculino';
                                            } else {
                                                echo 'Sin Especificar';
                                            }
                                            ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-5">
                                        <p><strong class="bold mayus">Parentesco:</strong></p>
                                    </div>
                                    <div class="col">
                                        <p><?php echo $fila['parentesco'] ?: 'Sin Especificar'; ?></p>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#infoTitular" aria-expanded="false" aria-controls="infoTitular">
                                        <i class="fa-solid fa-plus"></i>
                                        Mostrar Información del Titular
                                    </button>
                                </div>


                                <div class="collapse mt-3" id="infoTitular">
                                    <div class="card card-body">
                                        <div class="row">
                                            <div class="col-5">
                                                <p><strong class="bold mayus">Nombre del Empleado:</strong></p>
                                            </div>
                                            <div class="col">
                                                <p><?php echo "$nombre_titular $apellido_titular"; ?></p>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-5">
                                                <p><strong class="bold mayus">Cédula del Titular:</strong></p>
                                            </div>
                                            <div class="col">
                                                <p><?php echo $fila['cedula_empleado'] ?: 'Sin Especificar'; ?></p>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-5">
                                                <p><strong class="bold mayus">Cargo:</strong></p>
                                            </div>
                                            <div class="col">
                                                <p><?php echo $cargo_titular; ?></p>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-5">
                                                <p><strong class="bold mayus">Institución:</strong></p>
                                            </div>
                                            <div class="col">
                                                <p><?php echo $institucion_titular; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>

                        <hr>
                        <br>


                        <div class="card mb-3 mx-3">
                            <div class="row g-0">
                                <div class="col-md-12 text-left font-dark">
                                    <div class="card-body">

                                        <div class="row">
                                            <div class="col-6">
                                                <p class="card-text font-dark bold mayus" style="font-size: 13px; line-height: 0.5;">
                                                    Fecha de registro: <?php echo date('d-m-Y h:i A', strtotime($fila['fecha_registro'])); ?>
                                                </p>
                                                <p class="card-text font-dark bold mayus" style="font-size: 13px; line-height: 0.5;">
                                                    Encargado de registro: <?php echo $nombreEncargado . ' ' . $apellidoEncargado; ?>
                                                </p>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-delete mayus" data-dismiss="modal">Cerrar</button>
            </div>

        </div>
    </div>
</div>