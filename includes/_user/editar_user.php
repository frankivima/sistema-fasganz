<?php

session_start();
error_reporting(0);

$varsesion = $_SESSION['fasganz'];

if ($varsesion == null || $varsesion = '') {
    header("Location: ../../_sesion/login.php");
}

////////////////// CONEXION A LA BASE DE DATOS ////////////////////////////////////
$id = $_GET['id'];
include "../db.php";
$consulta = "SELECT * FROM usuarios WHERE id = $id";
$resultado = mysqli_query($conexion, $consulta);
$usuario = mysqli_fetch_assoc($resultado);
?>

<?php include_once "header.php"; ?>

<body>

    <form action="../functions.php" id="form" method="POST">

        <div class="container">

            <div class="card">

                <div class="card-header py-3">
                    <div class="row">
                        <h4 class="m-0 font-primary mayus">Modificar Usuario</h4>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-12">

                            <div class="form-row">
                                <div class="form-group col">
                                    <label for="nombre" class="label-span">Nombre</label>
                                    <input type="text" id="nombre" name="nombre" class="form-control" value="<?php echo $usuario['nombre']; ?>" required>
                                </div>
                                <div class="form-group col">
                                    <label for="apellido" class="label-span">Apellido</label>
                                    <input type="text" id="apellido" name="apellido" class="form-control" value="<?php echo $usuario['apellido']; ?>">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col">
                                    <label for="username" class="label-span">Usuario</label><br>
                                    <input type="text" name="username" id="username" class="form-control" placeholder="Debe ser Unico" value="<?php echo $usuario['username']; ?>" required>
                                </div>
                                <div class="form-group col">
                                    <label for="password" class="label-span">Contraseña</label><br>
                                    <input type="text" name="password" id="password" class="form-control" value="<?php echo $usuario['password']; ?>" required>
                                </div>
                            </div>

                            <div class="form-group">
    <label for="id_rol" class="font-dark bold">Nivel:</label>
    <select name="id_rol" id="id_rol" class="form-control" required>
        <option value="">--Selecciona Rol--</option>
        <?php
        include("../db.php");
        
        // Obtener el id del usuario a editar (supongamos que está en una variable $id_usuario)
        $id_usuario = $_GET['id']; // Ajusta esto según cómo obtienes el id del usuario a editar
        
        // Consulta para obtener los datos del usuario incluyendo su id_rol actual
        $sql_usuario = "SELECT * FROM usuarios WHERE id = $id_usuario";
        $resultado_usuario = mysqli_query($conexion, $sql_usuario);
        
        if (mysqli_num_rows($resultado_usuario) > 0) {
            $usuario = mysqli_fetch_assoc($resultado_usuario);
            $id_rol_usuario = $usuario['id_rol']; // Obtener el id_rol del usuario
            
            // Consulta para obtener los roles
            $sql_roles = "SELECT * FROM roles";
            $resultado_roles = mysqli_query($conexion, $sql_roles);
            
            while ($consulta = mysqli_fetch_array($resultado_roles)) {
                $selected = ($consulta['id'] == $id_rol_usuario) ? 'selected' : '';
                echo '<option value="' . $consulta['id'] . '" ' . $selected . '>' . $consulta['rol'] . '</option>';
            }
        } else {
            echo '<option value="" disabled>-- No se encontraron roles --</option>';
        }
        ?>
    </select>
</div>

                        </div>
                    </div>
                </div>

                <div class="card-footer">

                    <input type="hidden" name="accion" value="editar_user">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">

                    <div class="mb-3 d-flex justify-content-center mt-2">

                        <button type="submit" id="form" name="form" class="btn btn-agg mayus mr-2">Guardar Cambios</button>
                        <a href="../../views/usuarios.php" class="btn btn-delete mayus ml-2">Cancelar</a>

                    </div>
                </div>
            </div>
        </div>

    </form>


</body>
</div>


<?php include_once "footer.php"; ?>