<?php
error_reporting(0);
session_start();
$actualsesion = $_SESSION['fasganz'];

if ($actualsesion == null || $actualsesion == '') {
    header("Location: _sesion/login.php");
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

    <title>Sistema Gestión de Citas - FASGANZ</title>


    <link rel="stylesheet" href="../../css/style.css">

    <!-- Custom fonts for this template-->
    <link rel="stylesheet" href="../../vendor/fontawesome-free/css/all.min.css" type="text/css">
    <script src="../../vendor/fontawesome-free/js/all.min.js"></script>

    <!-- Custom styles for this template-->
    <link href="../../css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link rel="stylesheet" href="../../vendor/DataTables-1.13.6/css/dataTables.bootstrap5.min.css">

    <script src="../../js/jquery.min.js"></script>


    <link rel="icon" href="../../img/logo1.png" type="image/x-icon" />

    <style>
        .dataTables_length {
            display: none;
        }
    </style>

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav custom-navbar sidebar accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="../../views/index.php">
                <div class="sidebar-brand-text mx-1 ">
                    <img src="../../img/logo1.png" style="max-height: 70px; padding:10px;" alt="Logo Clinica">
                </div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="../../views/index.php">
                    <i class="fa-solid fa-home" aria-hidden="true"></i>
                    <span>Inicio</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Interface
            </div>

            <?php
            // Verifica el rol del usuario
            if ($_SESSION['id_rol'] == 1) {
            ?>

                <!-- Nav Item - user -->
                <li class="nav-item">
                    <a class="nav-link" href="../../views/usuarios.php">
                        <i class="fa fa-user" aria-hidden="true"></i>
                        <span>Usuarios</span>
                    </a>
                </li>

            <?php
            };
            ?>

            <!-- Nav Item - Utilities Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities" aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fa fa-users" aria-hidden="true"></i>
                    <span>Pacientes</span>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Ver Pacientes:</h6>

                        <?php
                        if ($_SESSION['id_rol'] == 1 || $_SESSION['id_rol'] == 2) {
                        ?>

                            <a class="collapse-item" href="../../views/buscar_pacientes.php">Buscar</a>

                        <?php
                        };
                        ?>

                        <?php
                        if ($_SESSION['id_rol'] == 1 || $_SESSION['id_rol'] == 3) {
                        ?>
                            <a class="collapse-item" href="../../views/pacientes.php">Empleados</a>
                            <a class="collapse-item" href="../../views/beneficiarios.php">Beneficiarios</a>

                        <?php
                        };
                        ?>
                    </div>
                </div>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Otros
            </div>

            <?php
            //};

            ?>

            <!-- Nav Item - infor -->
            <li class="nav-item">
                <a class="nav-link" href="../../views/acerca.php">
                    <i class="fa fa-question-circle" aria-hidden="true"></i>
                    <span>Manual de Usuario</span></a>
            </li>


            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

            <!-- Sidebar Message -->


        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-light topbar mb-4 static-top shadow">

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                                <span class="mr-2 d-none d-lg-inline text-gray-600 small mayus">
                                    <?php echo $_SESSION['fasganz']; ?></span>
                                <img class="img-profile rounded-circle" src="../../img/profile.png">
                            </a>


                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right text-light shadow" aria-labelledby="userDropdown">

                                <a class="dropdown-item" href="perfil.php">
                                    <i class="fas fa-user fa-sm fa-fw mr-2"></i>
                                    Perfil
                                </a>

                                <div class="dropdown-divider"></div>

                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2"></i>
                                    Salir
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>

                <!-- End of Topbar -->
                <?php  //endwhile;
                ?>

                <?php include "../../views/salir.php"; ?>



</body>

</html>