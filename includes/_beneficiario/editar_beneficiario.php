<?php


session_start();
error_reporting(0);
$varsesion = $_SESSION['fasganz'];

if ($varsesion == null || $varsesion = '') {
    header("Location: ../includes/_sesion/login.php");
}

////////////////// CONEXION A LA BASE DE DATOS ////////////////////////////////////
$id = $_GET['id'];
include "../db.php";
$consulta = "SELECT * FROM beneficiarios WHERE id = $id";
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
                        <h4 class="m-0 font-primary mayus">Modificar datos de beneficiario</h4>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-12 col-xl-12">

                            <div class="form-group">
                                <label for="cedula_empleado" class="label-span">Cédula Empleado</label>
                                <input type="text" id="cedula_empleado" name="cedula_empleado" class="form-control col-4" placeholder="Debe estar en la Data de ''Empleados''" value="<?php echo $usuario['cedula_empleado']; ?>" readonly>
                            </div>

                            <p class="font-info bold mayus">Información Personal</p>
                            <hr class="custom-hr">

                            <div class="form-row">

                                <div class="form-group col">
                                    <label for="nombre" class="label-span">Nombre</label>
                                    <input type="text" id="nombre" name="nombre" class="form-control" value="<?php echo $usuario['nombre']; ?>">
                                </div>
                                <div class="form-group col">
                                    <label for="apellido" class="label-span">Apellido</label>
                                    <input type="text" id="apellido" name="apellido" class="form-control" value="<?php echo $usuario['apellido']; ?>">
                                </div>
                                <div class="form-group col">
                                    <label for="cedula_beneficiario" class="label-span">Cédula Beneficiario</label>
                                    <input type="number" id="cedula_beneficiario" name="cedula_beneficiario" class="form-control text-center" placeholder="No se puede repetir" value="<?php echo $usuario['cedula_beneficiario']; ?>">
                                </div>
                            </div>

                            <div class="form-row">

                                <div class="form-group col">
                                    <label for="fecha_nac" class="label-span">Fecha de Nacimiento</label><br>
                                    <input type="date" name="fecha_nac" id="fecha_nac" class="form-control text-center" value="<?php echo $usuario['fecha_nac']; ?>">
                                </div>

                                <div class="form-group col">
                                    <label for="parentesco" class="label-span">Parentesco</label>
                                    <select name="parentesco" id="parentesco" class="form-control" required>
                                        <option value="">--Selecciona una opcion--</option>
                                        <option <?php echo $usuario['parentesco'] === 'PADRE' ? "selected='selected' " : "" ?> value="PADRE">PADRE</option>
                                        <option <?php echo $usuario['parentesco'] === 'MADRE' ? "selected='selected' " : "" ?> value="MADRE">MADRE</option>
                                        <option <?php echo $usuario['parentesco'] === 'HIJO(A)' ? "selected='selected' " : "" ?> value="HIJO(A)">HIJO(A)</option>
                                        <option <?php echo $usuario['parentesco'] === 'ESPOSO(A)' ? "selected='selected' " : "" ?> value="ESPOSO(A)">ESPOSO(A)</option>
                                        <option <?php echo $usuario['parentesco'] === 'CONYUGUE' ? "selected='selected' " : "" ?> value="CONYUGUE">CONYUGUE</option>
                                        <option <?php echo $usuario['parentesco'] === 'CONCUBINO(A)' ? "selected='selected' " : "" ?> value="CONCUBINO(A)">CONCUBINO(A)</option>

                                    </select>
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
                    </div>
                </div>

                <div class="card-footer">
                    <div class="my-2 d-flex justify-content-center">

                        <input type="hidden" name="accion" value="editar_beneficiario">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">

                        <button type="submit" id="form" name="form" class="btn btn-agg mayus mr-2">
                            <i class="fa-regular fa-floppy-disk"></i> Guardar Cambios</button>
                        <a href="javascript:history.back()" class="btn btn-delete mayus ml-2">
                            <i class="fa-solid fa-x"></i> Cancelar
                        </a>
                    </div>
                </div>


            </div>
        </div>

        </div>
    </form>

    <?php include_once "footer.php"; ?>



</body>

</html>