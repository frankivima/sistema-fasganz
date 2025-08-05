<?php
session_start();

// Incluir el archivo de validación de licencia
include('../../config/validar_licencia.php');

// Verificar si la licencia está activa
if (!validarLicencia()) {
    // Si la licencia ha expirado, mostrar un mensaje y explicar la situación
    echo "Lo sentimos, la licencia de esta aplicación ha expirado. Por favor, contacte al proveedor para renovarla.";
    exit(); // Finalizar la ejecución del script
}

// Verificar si ya hay fasganz sesión iniciada
if(isset($_SESSION['fasganz']) && !empty($_SESSION['fasganz'])) {
    // Redireccionar a la página principal si hay una sesión activa
    header("Location: ../../views/index.php");
    exit(); // Finalizar la ejecución del script
}

?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie-edge">
    <title>Sistema Gestion de Citas - FASGANZ // Iniciar Sesion</title>

    <link rel="stylesheet" type="text/css " href="../../css/login-style.css">

    <link rel="stylesheet" href="../../vendor/bootstrap-5.3.2-dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="../../vendor/fontawesome-free/css/all.min.css" type="text/css">
    <script src="../../vendor/fontawesome-free/js/all.min.js"></script>

    <link rel="icon" href="../../img/logo1.png" type="image/x-icon" />

</head>

<body>

<video autoplay muted loop>
        <source src="../../img/bg.mp4" type="video/mp4">
        Tu navegador no soporta el elemento de video.
    </video>

    <div class="login-box p-5 rounded-5">

        <div class="logo">
            <img src="../../img/logo1.png" alt="logo"/>
        </div>

        <form action="../functions.php" method="POST">

            <div class="input-group mt-5">
                <div class="input-group-text icon">
                    <i class="fa-solid fa-user" alt="username-icon"></i>
                </div>
                <input class="form-control" type="text" name="username" placeholder="Usuario">
            </div>

            <div class="input-group mt-3">
                <div class="input-group-text icon" id="eye">
                    <i class="fa-solid fa-lock" alt="password-icon"></i>
                </div>
                <input class="form-control" type="password" name="password" placeholder="Contraseña" id="input">
                <input type="hidden" name="accion" value="acceso_user">
            </div>

            <div class="text-center">
                <button class="btn w-50 " type="submit">
                    INGRESAR
                </button>
            </div>
        </form>

    </div>

    <script src="../../js/code.js"></script>

</body>

</html>