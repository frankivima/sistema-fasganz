<?php


session_start();
error_reporting(0);
$varsesion = $_SESSION['fasganz'];

if ($varsesion == null || $varsesion = '') {
    header("Location: ../login.php");
}
?>

<div class="modal fade" id="beneficiario" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title mayus bold" id="exampleModalLabel">Agregar Beneficiario</h3>
                <button type="button" class="btn btn-black" data-dismiss="modal">
                    <i class="fa fa-times" aria-hidden="true"></i></button>
            </div>


            <div class="modal-body">

                <form action="../includes/functions.php" method="POST" id="beneficiarioForm">

                    <div class="row">
                        <div class="col-8">
                            <div class="form-group">
                                <label for="cedula_empleado" class="label-span">Cédula Empleado</label>
                                <div class="input-group">
                                    <input type="text" id="cedula_empleado" name="cedula_empleado" class="form-control" placeholder="Debe estar en la Data de ''Empleados''" required>
                                    <button type="button" id="verificar_cedula" class="btn btn-delete">Verificar Cédula</button>
                                </div>
                                <div id="verificacion_resultado"></div>
                            </div>
                        </div>
                        <hr class="col-12">
                    </div>

                    <div class="row">
                        <div class="col-12">

                            <p class="font-info bold mayus mt-2">Información Personal</p>
                            <hr class="custom-hr">

                            <div class="form-row">
                                <div class="form-group col-6">
                                    <label for="cedula_beneficiario" class="label-span">Cédula Beneficiario</label>
                                    <input type="text" id="cedula_beneficiario" name="cedula_beneficiario" class="form-control" placeholder="Debe ser Unica" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col">
                                    <label for="nombre" class="label-span">Nombre</label>
                                    <input type="text" id="nombre" name="nombre" class="form-control" required>
                                </div>
                                <div class="form-group col">
                                    <label for="apellido" class="label-span">Apellido</label>
                                    <input type="text" id="apellido" name="apellido" class="form-control" required>
                                </div>
                            </div>

                            <div class="form-row">

                                <div class="form-group col">
                                    <label for="fecha_nac" class="label-span">Fecha de Nacimiento:</label><br>
                                    <input type="date" name="fecha_nac" id="fecha_nac" class="form-control" required>
                                </div>

                                <div class="form-group col">
                                    <label for="parentesco" class="label-span">Parentesco:</label>
                                    <select name="parentesco" id="parentesco" class="form-control" required>
                                        <option value="">--Selecciona una opcion--</option>
                                        <option value="PADRE">Padre</option>
                                        <option value="MADRE">Madre</option>
                                        <option value="HIJO(A)">Hijo(a)</option>
                                        <option value="ESPOSO(A)">Esposo(a)</option>
                                        <option value="CÓNYUGE">Cónyuge</option>
                                        <option value="CONCUBINO">Concubino</option>
                                    </select>
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
                        </div>
                    </div>
                
            </div>

            <div class="card-footer">

                <?php
                date_default_timezone_set('America/Caracas');
                ?>
                <input type="hidden" id="fecha_registro" name="fecha_registro" value="<?php echo date('Y-m-d'); ?>">
                <input type="hidden" id="encargado_registro" name="encargado_registro" value="<?php echo $idUsuario; ?>">

                <input type="hidden" name="accion" value="insert_beneficiario">

                <div class="mb-3 d-flex justify-content-center mt-2">
                    <button type="submit" id="register" name="registrar" class="btn btn-agg mayus mr-2">Agregar Beneficiario</button>
                    <a href="beneficiarios.php" class="btn btn-delete mayus ml-2">Cancelar</a>
                </div>
            </div>

            </form>

        </div>

        <script src="../js/validar.js"></script>
    </div>
</div>