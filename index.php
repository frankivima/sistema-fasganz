<?php
include('./config/validar_licencia.php');

if (validarLicencia()) {
    header('Location: ./includes/_sesion/login.php');
    exit();
} else {
    echo "Lo sentimos, la licencia de esta aplicación ha expirado. Por favor, contacte al proveedor para renovarla.";
}
?>
