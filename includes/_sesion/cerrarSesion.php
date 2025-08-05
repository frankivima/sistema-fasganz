<?php
session_start();

// Eliminar solo la sesión específica para tu aplicación
unset($_SESSION['fasganz']);

// Destruir otras variables de sesión si es necesario
// unset($_SESSION['otra_variable_de_sesion']);

// Destruir la sesión si no quedan variables de sesión
if (empty($_SESSION)) {
    session_destroy();
}

// Redirigir al usuario a la página de inicio de sesión
header("Location: login.php");
?>
