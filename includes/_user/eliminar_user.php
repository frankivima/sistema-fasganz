<?php

session_start();
error_reporting(0);
$varsesion = $_SESSION['fasganz'];
	if($varsesion== null || $varsesion= ''){

	    header("Location:_sesion/login.php");
	
	}
	$id = $_GET['id'];
	include "../db.php";
	$query = mysqli_query($conexion,"DELETE FROM usuarios WHERE id = '$id'");
	
	header ('Location: ../../views/usuarios.php?m=1');
?>
