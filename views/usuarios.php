

<?php
// Seguridad de sesiones
session_start();
error_reporting(0);
$varsesion = $_SESSION['fasganz'];

if ($varsesion == null || $varsesion == '' || ($_SESSION['id_rol'] != 1)) {
    echo "
    <script>
        alert('Acceso no autorizado. Debes ser Administrador para ver esta página.');
             location.assign('../views/index.php'); // Puedes redirigir a otra página
    </script>";
    die(); // Detiene la ejecución del código si no tiene permiso
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <script src="../js/jquery.min.js"></script>

</head>

<?php include "../includes/header.php"; ?>
<?php
//if( $actualsesion == "Administrador"){
?>


<body id="page-top">

    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">

                <div class="row">
                    <h4 class="m-0 font-primary mayus col-8">Lista de Usuarios</h4>
                    <div class="col-4 text-right">
                        <button type="button" class="btn btn-agg btn-md mayus" data-toggle="modal" data-target="#user">
                            <i class="fa fa-user-plus bold"></i> Agregar Usuario
                        </button>
                    </div>
                </div>

            </div>

            <!-- Vista Modo Lista - Para Celulares -->

            <ul class="list-group" id="listView" style="display: none;">
                <?php
                include "../includes/db.php";
                $result = mysqli_query($conexion, "SELECT usuarios.id, usuarios.nombre, usuarios.apellido, usuarios.username, usuarios.password, usuarios.id_rol, roles.rol FROM usuarios LEFT JOIN roles ON usuarios.id_rol= roles.id ");
                while ($fila = mysqli_fetch_assoc($result)) :
                ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1 font-h1"><?php echo $fila['nombre'] . ' ' . $fila['apellido']; ?></h5>
                            <p class="mb-1">

                                <strong>Usuario:</strong> <?php echo $fila['username']; ?> <br>
                                <strong>Rol:</strong> <?php echo $fila['rol']; ?>
                            </p>
                        </div>
                        <div class="btn-group">
                            <a class="btn btn-edit btn-sm mx-2" href="../includes/_user/editar_user.php?id=<?php echo $fila['id'] ?>">
                                <i class="fa fa-edit"></i>
                            </a>
                            <a href="../includes/_user/eliminar_user.php?id=<?php echo $fila['id'] ?>" class="btn btn-delete btn-sm btn-del">
                                <i class="fa fa-trash"></i>
                            </a>
                        </div>
                    </li>
                <?php endwhile; ?>
            </ul>


            <!-- Vista Modo Tabla - Para Dispositivos mas grandes -->

            <div id="cardView" class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead class="table table-comp shadow">
                            <tr class="mayus">
                                <th>Nombre / Apellido</th>
                                <th>Usuario</th>
                                <th>Rol</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>

                        <?php

                        include "../includes/db.php";
                        $result = mysqli_query($conexion, "SELECT  usuarios.id, usuarios.nombre, usuarios.apellido, usuarios.username, usuarios.password, usuarios.id_rol, roles.rol FROM usuarios 
LEFT JOIN roles ON usuarios.id_rol= roles.id ");
                        while ($fila = mysqli_fetch_assoc($result)) :

                        ?>
                            <tr>
                                <td><?php echo $fila['nombre'] . ' ' . $fila['apellido']; ?></td>
                                <td><?php echo $fila['username']; ?></td>
                                <td><?php echo $fila['rol']; ?></td>
                                <td class="text-center">
                                    <a class="btn btn-edit btn-sm" href="../includes/_user/editar_user.php?id=<?php echo $fila['id'] ?> ">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a href="../includes/eliminar_user.php?id=<?php echo $fila['id'] ?> " class="btn btn-delete btn-sm btn-del">
                                        <i class="fa fa-trash "></i>
                                    </a>
                                </td>
                            </tr>


                        <?php endwhile; ?>
                        <?php
                        //}

                        ?>
                        </tbody>
                    </table>



                    <script>
                        $('.btn-del').on('click', function(e) {
                            e.preventDefault();
                            const href = $(this).attr('href')

                            Swal.fire({
                                title: 'Estas seguro de eliminar este usuario?',
                                text: "¡No podrás revertir esto!!",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#034D81',
                                cancelButtonColor: '#8A021B',
                                confirmButtonText: 'Si, eliminar!',
                                cancelButtonText: 'Cancelar!',
                            }).then((result) => {
                                if (result.value) {
                                    if (result.isConfirmed) {
                                        Swal.fire(
                                            'Eliminado!',
                                            'El usuario fue eliminado.',
                                            'success'
                                        )
                                    }

                                    document.location.href = href;
                                }
                            })

                        })
                    </script>

                </div>
            </div>
        </div>


    </div>
    <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->
    <?php include "../includes/_user/insert_user.php"; ?>

    <?php include "../includes/footer.php"; ?>

    

</body>

</html>