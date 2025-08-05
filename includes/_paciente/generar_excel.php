<?php


session_start();
error_reporting(0);
$varsesion = $_SESSION['fasganz'];

if ($varsesion == null || $varsesion = '') {
    header("Location: _sesion/login.php");
}

if (isset($_SESSION['user_id'])) {
    $idUsuario = $_SESSION['user_id'];
} else {
    // Si no están disponibles, puedes mostrar el nombre de usuario
    $nombreUsuario = $_SESSION['fasganz'];
}

?>

<?php include "header.php"; ?>


<body>

    <div class="container-fluid">

        <div class="card">
            <div class="card-header py-3">
                <div class="row">
                    <h4 class="m-0 font-primary mayus">Generar Reporte Hoja de Calculo Excel</h4>
                </div>
            </div>


            <div class="card-body">

                <form action="../_paciente/importar/reporte.php" method="post">
                    <label for="">Institucion</label>
                    <select name="institucion" id="institucion">
                        <option value="FASGANZ">FASGANZ</option>
                    </select>

                    <label for="">Género</label>
                    <select name="genero" id="genero">
                        <option value="M">M</option>
                        <option value="F">F</option>
                    </select>

                    <br>

            </div>

            <div class="card-footer">
            <div class="my-2 d-flex justify-content-center">
                    <button type="submit" class="btn btn-agg mayus">
                        <i class="fa-solid fa-file-import"></i> Generar Reporte
                    </button>
                </div>
            </div>

            </form>

        </div>
    </div>
    </div>


</body>

<?php include "footer.php"; ?>

</html>