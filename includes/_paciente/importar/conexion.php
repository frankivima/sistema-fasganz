<?php

$mysqli = new mysqli('localhost', 'root', '', 'bd_fasganz');

if($mysqli->connect_errno){
    echo 'Fallo la conexion ' . $mysqli->connect_error;
    die();
}


?>