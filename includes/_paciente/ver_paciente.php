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

$fechaIngreso = $fila['fecha_ingreso'];
$fechaActual = date('Y-m-d');

$fechaIngresoObj = new DateTime($fechaIngreso);
$fechaActualObj = new DateTime($fechaActual);

$años_servicios = $fechaIngresoObj->diff($fechaActualObj);


$cedulaEmpleado = $fila['cedula'];
$queryBeneficiarios = "SELECT COUNT(*) as total_beneficiarios FROM beneficiarios WHERE cedula_empleado = '$cedulaEmpleado'";
$resultBeneficiarios = mysqli_query($conexion, $queryBeneficiarios);
$totalBeneficiarios = 0;

if ($resultBeneficiarios) {
    $filaBeneficiarios = mysqli_fetch_assoc($resultBeneficiarios);
    $totalBeneficiarios = $filaBeneficiarios['total_beneficiarios'];
}

?>

<style>
    .custom-badge {
        font-size: 16px;
    }
</style>


<!-- Contenido del modal para ver la cita -->
<div class="modal fade" id="verPacienteModal<?php echo $fila['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="verPacienteModalLabel<?php echo $fila['id']; ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title bold mayus" id="verCitaModalLabel<?php echo $fila['id']; ?>">Detalles de Empleado</h5>
                <button type="button" class="close btn" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">

                    <div class="card mb-1 px-5">

                        <div class="row g-0">
                            <div class="col-lg-4 col-md-2 col-sm-0 mt-3">
                                <img src="../img/logo1.png" class="img-fluid rounded-start" alt="...">
                            </div>
                            <div class="col-lg-8 col-md-10 col-sm-12 text-justify">
                                <div class="card-body">
                                    <h2 class="card-title font-dark bold mayus ">Detalles de Empleado </h2>
                                    <h2 class="font-dark bold mayus">C.I: <span class="font-primary"><?php echo ($fila['cedula']); ?></span> </h2>
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
                                        <p><strong class="bold mayus">Nombre del Empleado:</strong></p>
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
                                        <p><?php echo $fila['cedula'] ?: 'N/T'; ?></p>
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
                                        <p><strong class="bold mayus">Institución:</strong></p>
                                    </div>
                                    <div class="col">
                                        <p><?php echo $fila['institucion'] ?: 'N/T'; ?></p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-5">
                                        <p><strong class="bold mayus">Cargo:</strong></p>
                                    </div>
                                    <div class="col">
                                        <p><?php echo $fila['cargo'] ?: 'Sin Especificar'; ?></p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-5">
                                        <p><strong class="bold mayus">Fecha de Ingreso:</strong></p>
                                    </div>
                                    <div class="col">
                                        <p><?php echo date('d-m-Y', strtotime($fila['fecha_ingreso'])) ?: 'N/T'; ?></p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-5">
                                        <p><strong class="bold mayus">Años de Servicio:</strong></p>
                                    </div>
                                    <div class="col">
                                        <p><?php echo $años_servicios->format('%y años, %m meses y %d días') ?: 'N/T'; ?></p>
                                    </div>
                                </div>

                                <hr>

                                <div class="row">
                                    <div class="col">
                                        <?php if ($totalBeneficiarios > 0) : ?>
                                            <p><small>Tiene un Total de <?php echo $totalBeneficiarios; ?> Beneficiarios Asociados.</small></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col">
                                        <?php if ($totalBeneficiarios > 0) : ?>
                                            <div class="d-flex justify-content-end">
                                                <button type="button" class="btn btn-infor" data-toggle="collapse" data-target="#infoBeneficiarios" aria-expanded="false" aria-controls="infoBeneficiarios">
                                                    <i class="fa-solid fa-plus"></i>
                                                    Mostrar Beneficiarios
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <?php
                                // Consulta para obtener los beneficiarios asociados
                                $cedulaEmpleado = $fila['cedula']; // Asume que $fila['cedula'] es la cédula del empleado actual
                                $consultaBeneficiarios = "SELECT * FROM beneficiarios WHERE cedula_empleado = '$cedulaEmpleado'";
                                $resultadoBeneficiarios = mysqli_query($conexion, $consultaBeneficiarios);
                                ?>

                                <div class="collapse" id="infoBeneficiarios">
                                    <div class="card card-body mt-3">
                                        <h5 class="bold mayus font-primary">Lista de Beneficiarios</h5>
                                        <ul class="list-group" style="line-height: 0.8;">
                                            <?php while ($beneficiario = mysqli_fetch_assoc($resultadoBeneficiarios)) : ?>
                                                <li class="list-group-item">
                                                    <h5 class="font-rojo bold mayus"><?php echo $beneficiario['parentesco']; ?></h5>

                                                    <div class="row">
                                                        <div class="col-5">
                                                            <strong>Nombre Completo: </strong>
                                                        </div>
                                                        <div class="col">
                                                            <p><?php echo $beneficiario['nombre'] . ' ' . $beneficiario['apellido']; ?></p>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-5">
                                                            <strong>Cédula de Identidad: </strong>
                                                        </div>
                                                        <div class="col">
                                                            <p><?php echo $beneficiario['cedula_beneficiario']; ?></p>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-5">
                                                            <strong>Edad: </strong>
                                                        </div>
                                                        <div class="col">
                                                            <p><?php echo $beneficiario['edad']; ?> Años.</p>
                                                        </div>
                                                    </div>
                                                </li>
                                            <?php endwhile; ?>
                                        </ul>
                                    </div>
                                </div>



                            </div>
                        </div>

                        <hr>

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