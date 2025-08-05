<?php
// Seguridad de sesiones
session_start();
error_reporting(0);
$varsesion = $_SESSION['fasganz'];

if ($varsesion == null || $varsesion == '' || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 2 && $_SESSION['id_rol'] != 3)) {
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

    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <div class="row">
                    <h4 class="m-0 font-primary mayus col-8">Verificación de Beneficiarios</h4>
                    <div class="col-4 text-right">
                    </div>
                </div>
            </div>

            <div class="card-body">
                <form id="verificar_form">
                    <div class="row">

                        <div class="form-group col-3">
                        </div>

                        <div class="form-group mt-5 col-6">
                            <label for="cedula_usuario" class="label-span mayus bold">Cédula del Titular</label>
                            <div class="input-group">
                                <input type="text" id="cedula_usuario" name="cedula_usuario" class="form-control bold text-center" placeholder="" required>
                                <button type="submit" class="btn btn-delete mayus">
                                <i class="fa-solid fa-magnifying-glass"></i> Buscar Registro</button>
                            </div>
                        </div>

                        <div class="form-group col-3">
                        </div>
                    </div>
                </form>
                <br>

                <hr>

                <div class="row">

                    <div class="form-group col-2">
                    </div>

                    <div class="form-group col-8" id="resultado_usuario"></div>

                    <div class="form-group col-2">
                    </div>

                </div>
            </div>

        </div>

    </div>
    <!-- End of Main Content -->

    </div>

    <?php include "../includes/footer.php"; ?>

    <script>
        document.getElementById('verificar_form').addEventListener('submit', function(event) {
            event.preventDefault();
            var cedula = document.getElementById('cedula_usuario').value;

            // Realizar la solicitud al servidor usando AJAX
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '../includes/procesar_verificacion.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    document.getElementById('resultado_usuario').innerHTML = xhr.responseText;
                }
            };
            xhr.send('cedula_usuario=' + cedula);
        });
    </script>

</body>

</html>