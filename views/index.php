<?php
// Seguridad de sesiones
session_start();
error_reporting(0);

$varsesion = $_SESSION['fasganz'];

if ($varsesion == null || $varsesion == '') {
    header("Location: ../includes/_sesion/login.php");
    die();
}

include '../includes/header.php';

include '../includes/funtion_graficos.php';

// Asegúrate de que los datos del usuario estén disponibles en la sesión
if (isset($_SESSION['nombre']) && isset($_SESSION['apellido'])) {
    $nombreUsuario = $_SESSION['nombre'];
    $apellidoUsuario = $_SESSION['apellido'];
} else {
    // Si no están disponibles, puedes mostrar el nombre de usuario
    $nombreUsuario = $_SESSION['username'];
    $apellidoUsuario = ''; // Debes adaptar esto según la estructura de tu sesión
}

// Consultar la última fecha de actualización en pacientes
$SQL_pacientes = "SELECT MAX(fecha_registro) AS ultima_fecha FROM pacientes";
$resultado_pacientes = mysqli_query($conexion, $SQL_pacientes);
$fecha_pacientes = mysqli_fetch_assoc($resultado_pacientes)['ultima_fecha'];

// Consultar la última fecha de actualización en beneficiarios
$SQL_beneficiarios = "SELECT MAX(fecha_registro) AS ultima_fecha FROM beneficiarios";
$resultado_beneficiarios = mysqli_query($conexion, $SQL_beneficiarios);
$fecha_beneficiarios = mysqli_fetch_assoc($resultado_beneficiarios)['ultima_fecha'];

// Determinar la última fecha entre ambas tablas
$ultima_actualizacion = max($fecha_pacientes, $fecha_beneficiarios);

?>

<style>
    #institutionsChart {
        max-width: 100%;
        max-height: 400px;
    }
</style>


<!-- Begin Page Content -->
<div class="container-fluid mb-5">
    <h1 class="m-0 font-primary">Bienvenido <?php echo $nombreUsuario . ' ' . $apellidoUsuario; ?>!</h1>
    <br>


    <div class="card">
        <div class="card-body">
            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 font-secundary mb-0">Panel Administrativo</h1>

                <p class="alert bg-primario text-white text-center" role="alert">
                    Última actualización:
                    <strong><?php echo date("d-m-Y", strtotime($ultima_actualizacion)); ?>.</strong>
                </p>
            </div>

            <!-- Content Row -->
            <div class="row">

                <?php
                // Verifica el rol del usuario
                if ($_SESSION['id_rol'] == 1) {
                ?>

                    <!-- Pending Requests Card Example -->
                    <div class="col-xl-4 col-md-6 col-sm-6 col-12 mb-4">
                        <div class="card card-1 py-2 card-hover pointer">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <a href="usuarios.php" class="text-xs font-card1 mb-1">
                                            Total de Usuarios </a>
                                        <div class="h5 mb-0 font-h2">
                                            <?php
                                            include "../includes/db.php";

                                            $SQL = "SELECT id FROM usuarios ORDER BY id";
                                            $dato = mysqli_query($conexion, $SQL);
                                            $fila = mysqli_num_rows($dato);

                                            echo ($fila); ?>

                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fa-solid fa-user-lock fa-3x text-gray-600"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php
                };
                ?>

                <!-- Earnings (Monthly) Card Example -->
                <div class="col-xl-4 col-md-6 col-sm-6 col-12 mb-4">
                    <div class="card card-1 py-2 card-hover pointer">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <a href="pacientes.php" class="text-xs font-card1 mb-1">Total de Empleados</a>
                                    <div class="h5 mb-0 font-h2">
                                        <?php
                                        include "../includes/db.php";

                                        $SQL = "SELECT id FROM pacientes ORDER BY id";
                                        $dato = mysqli_query($conexion, $SQL);
                                        $fila = mysqli_num_rows($dato);

                                        // Formatear el total de empleados
                                        echo number_format($fila, 0, '', '.');
                                        ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fa-solid fa fa-male fa-3x text-gray-600" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-md-6 col-sm-6 col-12 mb-4">
                    <div class="card card-1 py-2 card-hover pointer">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <a href="beneficiarios.php" class="text-xs font-card1 mb-1">Total de Beneficiarios</a>
                                    <div class="h5 mb-0 font-h2">
                                        <?php
                                        include "../includes/db.php";

                                        $SQL = "SELECT id FROM beneficiarios ORDER BY id";
                                        $dato = mysqli_query($conexion, $SQL);
                                        $fila = mysqli_num_rows($dato);

                                        // Formatear el total de beneficiarios
                                        echo number_format($fila, 0, '', '.');
                                        ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fa fa-users fa-3x text-gray-600"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-md-6 col-sm-6 col-12 mb-4">
                    <div class="card card-1 py-2 card-hover pointer">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <a href="#" class="text-xs font-card1 mb-1">Total de Instituciones</a>
                                    <div class="h5 mb-0 font-h2">
                                        <?php
                                        include "../includes/db.php";

                                        $SQL = "SELECT COUNT(DISTINCT institucion) AS total_instituciones FROM pacientes";
                                        $dato = mysqli_query($conexion, $SQL);
                                        $fila = mysqli_fetch_assoc($dato);

                                        // Formatear el total de instituciones
                                        echo number_format($fila['total_instituciones'], 0, '', '.');
                                        ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fa-solid fa-building-shield fa-3x text-gray-600"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <hr>


            <div class="row">

                <?php if (!empty($totales) && array_sum($totales) > 0): ?>
                    <div class="card col-xl-6 col-md-12 col-sm-12 text-start">
                        <div class="card-header">
                            <h4 class="card-title bold mayus">Empleados por Institución</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="institutionsChart"></canvas>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($totalesGenero) && array_sum($totalesGenero) > 0): ?>
                    <div class="card col-xl-3 col-md-6 col-sm-6 text-start">
                        <div class="card-header">
                            <h4 class="card-title bold mayus">Empleados por Género</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="genderChart"></canvas>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($totalesBeneficiarios) && array_sum($totalesBeneficiarios) > 0): ?>
                    <div class="card col-xl-3 col-md-6 col-sm-6 text-start">
                        <div class="card-header">
                            <h4 class="card-title bold mayus">Beneficiarios por Género</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="beneficiariosGeneroChart"></canvas>
                        </div>
                    </div>
                <?php endif; ?>


            </div>

        </div>
    </div>
</div>

<!-- End of Content Wrapper -->

</div>

<!-- End of Page Wrapper -->

<script src="../vendor/ChartJS/chart.js"></script>


<script>
    var ctx = document.getElementById("institutionsChart").getContext('2d');
    var institutionsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($instituciones); ?>,
            datasets: [{
                label: '',
                data: <?php echo json_encode($totalesSinFormato); ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(255, 206, 86, 0.6)',
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(153, 102, 255, 0.6)',
                    'rgba(255, 159, 64, 0.6)',
                    'rgba(199, 0, 57, 0.6)',
                    'rgba(0, 123, 255, 0.6)',
                    'rgba(40, 167, 69, 0.6)',
                    'rgba(108, 117, 125, 0.6)',
                    'rgba(255, 87, 34, 0.6)',
                    'rgba(0, 201, 255, 0.6)',
                    'rgba(255, 193, 7, 0.6)',
                    'rgba(0, 255, 191, 0.6)',
                    'rgba(255, 105, 180, 0.6)',
                    'rgba(128, 0, 128, 0.6)',
                    'rgba(255, 69, 0, 0.6)',
                    'rgba(102, 51, 153, 0.6)',
                    'rgba(0, 255, 127, 0.6)',
                    'rgba(255, 165, 0, 0.6)',
                    'rgba(186, 85, 96, 0.6)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(199, 0, 57, 1)',
                    'rgba(0, 123, 255, 1)',
                    'rgba(40, 167, 69, 1)',
                    'rgba(108, 117, 125, 1)',
                    'rgba(255, 87, 34, 1)',
                    'rgba(0, 201, 255, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(0, 255, 191, 1)',
                    'rgba(255, 105, 180, 1)',
                    'rgba(128, 0, 128, 1)',
                    'rgba(255, 69, 0, 1)',
                    'rgba(102, 51, 153, 1)',
                    'rgba(0, 255, 127, 1)',
                    'rgba(255, 165, 0, 1)',
                    'rgba(186, 85, 96, 1)'
                ],
                borderWidth: 1.5
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false // Ocultar leyenda
                },
                tooltip: {
                    callbacks: {
                        title: function(tooltipItem) {
                            return tooltipItem[0].label; // Mostrar el nombre de la institución en el tooltip
                        },
                        label: function(tooltipItem) {
                            return 'Total de pacientes: ' + tooltipItem.raw; // Mostrar total de pacientes
                        }
                    }
                },
                datalabels: {
                    display: false // Ocultar etiquetas de datos en los puntos
                }
            },
            scales: {
                x: {
                    display: false // Ocultar etiquetas de instituciones en el eje x
                }
            }
        }
    });
</script>


<script>
    var ctx = document.getElementById("genderChart").getContext('2d');
    var genderChart = new Chart(ctx, {
        type: 'doughnut', // Cambia a 'bar' si prefieres un gráfico de barras
        data: {
            labels: <?php echo json_encode($generos); ?>, // F y M
            datasets: [{
                label: 'Cantidad de Pacientes por Género',
                data: <?php echo json_encode($totalesGenero); ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.6)', // Color para F
                    'rgba(54, 162, 235, 0.6)' // Color para M
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            maintainAspectRatio: false, // Mantener proporción del gráfico
            plugins: {
                legend: {
                    position: 'bottom' // Cambia la posición de la leyenda
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': ' + tooltipItem.raw; // Personaliza el texto del tooltip
                        }
                    }
                }
            }
        }
    });
</script>


<script>
    var ctx = document.getElementById("beneficiariosGeneroChart").getContext('2d');
    var beneficiariosGeneroChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($generosBeneficiarios); ?>,
            datasets: [{
                data: <?php echo json_encode($totalesBeneficiarios); ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.6)', // Color para Femenino
                    'rgba(54, 162, 235, 0.6)' // Color para Masculino
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)', // Borde para Femenino
                    'rgba(54, 162, 235, 1)' // Borde para Masculino
                ],
                borderWidth: 1
            }]
        },
        options: {
            maintainAspectRatio: false, // Mantener proporción del gráfico
            plugins: {
                legend: {
                    position: 'bottom', // Posición de la leyenda
                }
            }
        }
    });
</script>



<?php include '../includes/footer.php'; ?>

</html>