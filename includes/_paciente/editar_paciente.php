<?php


session_start();
error_reporting(0);
$varsesion = $_SESSION['fasganz'];

if ($varsesion == null || $varsesion = '') {
    header("Location: ../_sesion/login.php");
}

////////////////// CONEXION A LA BASE DE DATOS ////////////////////////////////////
$id = $_GET['id'];
include "../db.php";
$consulta = "SELECT * FROM pacientes WHERE id = $id";
$resultado = mysqli_query($conexion, $consulta);
$usuario = mysqli_fetch_assoc($resultado);
?>

<?php include_once "header.php"; ?>

<body>

    <form action="../functions.php" id="form" method="POST">

        <div class="container-fluid">

            <div class="card">

                <div class="card-header py-3">
                    <div class="row">
                        <h4 class="m-0 font-primary mayus">Modificar datos de empleado</h4>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-12 col-xl-6">

                            <p class="font-info bold mayus">Información Personal</p>
                            <hr class="custom-hr">

                            <div class="form-row">
                                <div class="form-group col">
                                    <label for="cedula" class="form-label label-span">Cédula:</label>
                                    <input type="text" id="cedula" name="cedula" class="form-control" placeholder="No se puede repetir" value="<?php echo $usuario['cedula']; ?>">
                                </div>
                                <div class="form-group col">
                                    <label for="nombre" class="form-label label-span">Nombre:</label>
                                    <input type="text" id="nombre" name="nombre" class="form-control" value="<?php echo $usuario['nombre']; ?>">
                                </div>
                                <div class="form-group col">
                                    <label for="apellido" class="form-label label-span">Apellido:</label>
                                    <input type="text" id="apellido" name="apellido" class="form-control" value="<?php echo $usuario['apellido']; ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col">
                                    <label for="fecha_nac" class="form-label label-span">Fecha de Nacimiento:</label>
                                    <input type="date" name="fecha_nac" id="fecha_nac" class="form-control" value="<?php echo $usuario['fecha_nac']; ?>">
                                </div>
                                <div class="form-group col">
                                    <label for="genero" class="form-label label-span">Género:</label>
                                    <select name="genero" id="genero" class="form-control" required>
                                        <option value="">--Selecciona una opción--</option>
                                        <option <?php echo $usuario['genero'] === 'F' ? "selected='selected' " : "" ?> value="F">Femenino</option>
                                        <option <?php echo $usuario['genero'] === 'M' ? "selected='selected' " : "" ?> value="M">Masculino</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="col-12 col-sm-12 col-md-12 col-xl-6">

                            <p class="font-info bold mayus">Información laboral</p>
                            <hr class="custom-hr">

                            <div class="form-row">
                                <div class="form-group col">
                                    <label for="institucion" class="form-label label-span">Institución:</label>
                                    <input type="text" id="institucion" name="institucion" class="form-control" value="<?php echo $usuario['institucion']; ?>">
                                </div>

                                <div class="form-group col">
                                    <label for="cargo" class="form-label label-span">Cargo:</label>
                                    <input type="text" id="cargo" name="cargo" class="form-control" value="<?php echo $usuario['cargo']; ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="fecha_ingreso" class="form-label label-span">Fecha de Ingreso:</label>
                                <input type="date" id="fecha_ingreso" name="fecha_ingreso" class="form-control" value="<?php echo $usuario['fecha_ingreso']; ?>">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="card-footer">
                    <input type="hidden" name="accion" value="editar_paciente">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">

                    <div class="mb-3 d-flex justify-content-center">

                        <button type="submit" id="form" name="form" class="btn btn-agg mayus mr-2">
                            <i class="fa-regular fa-floppy-disk"></i> Guardar Cambios</button>
                        <a href="javascript:history.back()" class="btn btn-delete mayus ml-2">
                            <i class="fa-solid fa-x"></i> Cancelar
                        </a>

                    </div>
                </div>

            </div>
        </div>
    </form>
    </div>

    <?php include_once "footer.php"; ?>
</body>

</html>