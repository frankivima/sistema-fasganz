<?php


session_start();
error_reporting(0);
$varsesion = $_SESSION['fasganz'];

if ($varsesion == null || $varsesion = '') {
    header("Location: login.php");
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registros</title>
    <link rel="stylesheet" href="../package/dist/sweetalert2.css">

</head>

<body id="page-top">
    <div class="modal fade" id="user" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h3 class="modal-title" id="exampleModalLabel">Agregar Usuario</h3>
                    <button type="button" class="btn btn-black" data-dismiss="modal">
                        <i class="fa fa-times" aria-hidden="true"></i></button>
                </div>
                <div class="modal-body">

                    <form action="registros.php" method="POST">
                        <div class="form-group">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" id="nombre" name="nombre" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="apellido" class="form-label">Apellido</label>
                            <input type="text" id="apellido" name="apellido" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="username">Usuario*</label><br>
                            <input type="text" name="username" id="username" class="form-control" placeholder="No se puede repetir">
                        </div>
                        <div class="form-group">
                            <label for="password">Contraseña</label><br>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="id_rol" class="form-label">Rol de usuario:</label>
                            <select name="id_rol" id="id_rol" class="form-control" required>
                                <option value="">--Selecciona una opcion--</option>
                                <option value="1">Administrador</option>
                                <option value="2">Recepcion</option>
                                <option value="3">Historias Medicas</option>
                                <option value="4">Medicos</option>
                            </select>
                        </div>
                        <br>

                        <div class="mb-3 d-flex justify-content-center">

                            <button type="submit" id="register" name="registrar" class="btn btn-info mr-2">Agregar Usuario</button>
                            <a href="usuarios.php" class="btn btn-danger ml-2">Cancelar</a>

                        </div>
                </div>
            </div>

            </form>
        </div>
    </div>
    </div>
    </div>
    </div>
    </form>



    <script src="../package/dist/sweetalert2.all.js"></script>
    <script src="../package/dist/sweetalert2.all.min.js"></script>

    <script type="text/javascript">
        $(function() {
            $('#register').click(function(e) {

                var valid = this.form.checkValidity();

                if (valid) {


                    var nombre = $('#nombre').val();
                    var apellido = $('#apellido').val();
                    var username = $('#username').val();
                    var password = $('#password').val();
                    var id_rol = $('#id_rol').val();


                    e.preventDefault();

                    $.ajax({
                        type: 'POST',
                        url: '../includes/_sesion/validar.php',
                        data: {
                            nombre: nombre,
                            apellido: apellido,
                            username: username,
                            password: password,
                            id_rol: id_rol
                        },
                        success: function(data) {
                            Swal.fire({
                                'title': '¡Success!',
                                'text': data,
                                'icon': 'success',
                                'showConfirmButton': 'false',
                                'timer': '1500'
                            }).then(function() {
                                window.location = "usuarios.php";
                            });

                        },

                        error: function(data) {
                            Swal.fire({
                                'title': 'Error',
                                'text': data,
                                'icon': 'error'
                            })
                        }
                    });


                } else {

                }





            });


        });
    </script>
</body>

</html>