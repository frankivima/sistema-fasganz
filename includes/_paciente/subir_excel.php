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

?>


<?php include "header.php"; ?>


<body>

    <div class="container-fluid">

        <div class="card">
            <div class="card-header py-3">
                <div class="row">
                    <h4 class="m-0 font-primary mayus">Subir Archivo Excel para Procesar</h4>
                </div>
            </div>


            <div class="card-body">

                <div class="row">
                    <div class="col-5">
                        <form action="../_paciente/importar/leer.php" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="archivoExcel">Seleccione el archivo Excel:</label>
                                <input type="file" class="form-control-file" id="archivoExcel" name="archivoExcel" accept=".xlsx, .xls" required>
                            </div>

                    </div>

                    <div class="col-1 d-none d-lg-block" style="border-right: 1px solid #ccc;"></div>

                    <div class="col-6">
                        <em>· El archivo debe estar en formato <b>(.XLS) o (.XLSX).</b></em> <br>
                        <em>· Debe ser en el formato proporcionado por la institución.</em> <br>
                        <em>· Las celdas que contienen fechas (columna <b>F</b> y <b>G</b>) deben estar en formato de fecha y tener valores válidos.</em> <br>
                        <em>· No debe haber campos con fórmulas; todas las celdas deben contener valores exactos y específicos para cada columna.</em> <br>
                        <em>· El archivo no debe tener registros repetidos <b>(Campo "Cédula")</b> o estar registrados previamente en el sistema.</em> <br>
                        <em><strong class="mayus">**De no cumplir con los requisitos, se cancelará la importación.**</strong></em> <br>
                    </div>

                </div>

            </div>

            <div class="card-footer">
                <div class="my-2 d-flex justify-content-center">
                <button type="submit" class="btn btn-agg mayus" id="submitBtn" disabled>
                    <i class="fa-solid fa-file-import"></i> Subir y Procesar
                </button>
                </div>
            </div>
            </form>
        </div>
    </div>
    </div>


    <script>
        $(document).ready(function () {
            $('#archivoExcel').on('change', function () {
                if ($(this).val()) {
                    $('#submitBtn').prop('disabled', false);
                } else {
                    $('#submitBtn').prop('disabled', true);
                }
            });
        });
    </script>

</body>

<?php include "footer.php"; ?>

</html>