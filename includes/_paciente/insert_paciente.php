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

<div class="modal fade" id="paciente" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h3 class="modal-title mayus bold" id="exampleModalLabel">Agregar Empleado</h3>
                <button type="button" class="btn btn-black" data-dismiss="modal">
                    <i class="fa fa-times" aria-hidden="true"></i></button>
            </div>

            <div class="modal-body">

                <form action="../includes/functions.php" method="POST">

                    <div>
                        <p class="font-info bold mayus">Información Personal</p>
                        <hr class="custom-hr">
                    </div>

                    <div class="form-group mt-3">
                        <label for="cedula" class="label-span">Cédula del Empleado</label>
                        <input type="text" id="cedula" name="cedula" class="form-control" style="max-width: 225px;" maxlength="10" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group col">
                            <label for="nombre" class="label-span">Nombres</label>
                            <input type="text" id="nombre" name="nombre" class="form-control" required>
                        </div>
                        <div class="form-group col">
                            <label for="apellido" class="label-span">Apellidos</label>
                            <input type="text" id="apellido" name="apellido" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col">
                            <label for="fecha_nac" class="label-span">Fecha de Nacimiento</label><br>
                            <input type="date" name="fecha_nac" id="fecha_nac" class="form-control" required>
                        </div>

                        <div class="form-group col">
                            <label for="genero" class="label-span">Género</label><br>
                            <select name="genero" id="genero" class="form-control" required>
                                <option value="">--Selecciona una opcion--</option>
                                <option <?php echo $usuario['genero'] === 'F' ? "selected='selected' " : "" ?> value="F">Femenino</option>
                                <option <?php echo $usuario['genero'] === 'M' ? "selected='selected' " : "" ?> value="M">Masculino</option>
                            </select>
                        </div>
                    </div>


                    <br>
                    <div>
                        <p class="font-info bold mayus">Información Laboral</p>
                        <hr class="custom-hr">
                    </div>
                    <div class="form-row">
                        <div class="form-group col">
                            <label for="institucion" class="label-span">Institución</label>
                            <input type="text" id="institucion" name="institucion" class="form-control" required>
                        </div>

                        <div class="form-group col">
                            <label for="cargo" class="label-span">Cargo</label>
                            <input type="text" id="cargo" name="cargo" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="fecha_ingreso" class="label-span">Fecha de Ingreso</label>
                        <input type="date" id="fecha_ingreso" name="fecha_ingreso" class="form-control" required>
                    </div>

                    <?php
                    date_default_timezone_set('America/Caracas');
                    ?>
                    <input type="hidden" id="fecha_registro" name="fecha_registro" value="<?php echo date('Y-m-d'); ?>">
                    <input type="hidden" id="encargado_registro" name="encargado_registro" value="<?php echo $idUsuario; ?>">

            </div>

            <div class="card-footer">
                <div class="mb-3 d-flex justify-content-center mt-2">

                    <input type="hidden" name="accion" value="insert_paciente">
                    <button type="submit" id="register" name="registrar" class="btn btn-agg mayus mr-2">Agregar Empleado</button>
                    <a href="pacientes.php" class="btn btn-delete mayus ml-2">Cancelar</a>

                </div>
            </div>
        </div>

        </form>
    </div>
</div>