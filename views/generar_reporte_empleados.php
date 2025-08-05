<?php
// Seguridad de sesiones
session_start();
error_reporting(E_ALL);
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

?>

<?php include "../includes/header.php"; ?>

<body id="page-top">

    <div class="container-fluid">

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <div class="row">
                    <h4 class="m-0 font-primary mayus col-8"><i class="fa-solid fa-print"></i> Generar reportes de empleados </h4>
                    <div class="col-4 text-right">
                    </div>
                </div>
            </div>



            <div class="card-body">
                <!-- Formulario para selección de reporte -->
                <form id="reportForm" method="POST" target="_blank">

                    <div class="form-row align-items-center">

                        <div class="form-group col-md-3">
                            <label for="institucion" class="label-span mayus">Institución:</label>
                            <select class="form-control form-select" id="institucion" name="institucion">
                                <option value="">--Selecciona una Institución--</option>
                                <option value="TODAS">Todas las Instituciones</option>
                                <?php
                                include("../includes/db.php");
                                // Consulta para seleccionar las distintas instituciones, sin duplicados
                                $sql = "SELECT DISTINCT institucion FROM pacientes WHERE institucion IS NOT NULL AND institucion != '' ORDER BY institucion ASC";
                                $resultado = mysqli_query($conexion, $sql);
                                if (mysqli_num_rows($resultado) > 0) {
                                    while ($fila = mysqli_fetch_assoc($resultado)) {
                                        echo '<option value="' . htmlspecialchars($fila['institucion']) . '">' . htmlspecialchars($fila['institucion']) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>


                        <div class="form-group col-md-3">
                            <label for="genero" class="label-span mayus">Género:</label>
                            <select class="form-control form-select" id="genero" name="genero" required>
                                <option value="">--Selecciona el Género--</option>
                                <option value="TODAS">Todos los Géneros</option>
                                <option value="F">Femenino</option>
                                <option value="M">Masculino</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <div class="card-deck">
                                <div class="card">
                                    <button type="button" class="btn btn-infor p-3" onclick="submitForm('../includes/_reportes/Reporte_Empleados.php')">
                                        <i class="fas fa-diagram-successor"></i> Generar Reporte en PDF
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2"></div>
                    </div>
                </form>
            </div>


            <script>
                // Función para enviar el formulario a la URL especificada
                function submitForm(url) {
                    // Obtener el formulario
                    var form = document.getElementById('reportForm');
                    // Establecer la URL de destino del formulario
                    form.action = url;
                    // Enviar el formulario
                    form.submit();
                }
            </script>

            <hr>

        </div>
    </div>

    </div>
    <!-- End of Main Content -->

    <?php include "../includes/footer.php"; ?>


</body>

</html>