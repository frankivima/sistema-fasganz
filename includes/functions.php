<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Gestión de Citas - FASGANZ</title>

    <script src="../vendor/SweetAlert2/js/sweetalert2.all.min.js"></script>
    <script src="../vendor/JQuery/jquery-3.7.1.min.js"></script>

    <link rel="icon" href="../img/logo1.png" type="image/x-icon" />

</head>

<body>

</body>

</html>

<?php

require_once("db.php");

if (isset($_POST['accion'])) {
    switch ($_POST['accion']) {
            //casos de registros

        case 'acceso_user';
            acceso_user();
            break;

        case 'editar_user':
            editar_user();
            break;

        case 'insert_paciente':
            insert_paciente();
            break;

        case 'editar_paciente':
            editar_paciente();
            break;

        case 'insert_beneficiario':
            insert_beneficiario();
            break;

        case 'editar_beneficiario':
            editar_beneficiario();
            break;

    }
}


function acceso_user()
{
    include("db.php");
    extract($_POST);

    // Verifica si los campos de usuario y contraseña no están vacíos
    if (empty($username) || empty($password)) {
        echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Por favor, completa todos los campos',
            confirmButtonText: 'Aceptar',
            confirmButtonColor: '#034D81'
        }).then(function() {
            location.assign('./_sesion/login.php');
        });
        </script>";
        return;
    }

    $username = $conexion->real_escape_string($username);
    $password = $conexion->real_escape_string($password);
    session_start();
    $_SESSION['fasganz'] = $username;

    // Modifica la consulta para incluir nombre y apellido
    $consulta = "SELECT id, nombre, apellido, id_rol FROM usuarios WHERE username='$username' AND password='$password'";
    $resultado = mysqli_query($conexion, $consulta);
    $filas = mysqli_fetch_array($resultado);

    if (isset($filas['id_rol'])) { // Si el campo 'id_rol' existe en las filas

        $_SESSION['user_id'] = $filas['id']; // Almacena el ID del usuario en la sesión
        $_SESSION['nombre'] = $filas['nombre']; // Almacena el nombre del usuario en la sesión
        $_SESSION['apellido'] = $filas['apellido']; // Almacena el apellido del usuario en la sesión
        $_SESSION['id_rol'] = $filas['id_rol'];

        // Muestra una alerta de éxito antes de redireccionar
        echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: 'Inicio de sesión exitoso. Redirigiendo...',
            showConfirmButton: true,
            confirmButtonText: 'Aceptar',
            confirmButtonColor: '#034D81',
            timer: 3000,
        }).then(function() {
            location.assign('../views/index.php');
        });
        </script>";
    } else {
        // Muestra alerta específica según la situación
        if (empty($filas['id'])) {
            $mensaje = "Usuario o contraseña incorrectos. Por favor, verifica tus credenciales.";
        } elseif (empty($filas['id_rol'])) {
            $mensaje = "Usuario sin rol asignado. Comunícate con el administrador del sistema.";
        } else {
            $mensaje = "Contraseña incorrecta. Por favor, verifica tu contraseña.";
        }

        echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '$mensaje',
            confirmButtonColor: '#034D81'
        }).then(function() {
            location.assign('./_sesion/login.php');
        });
        </script>";
        session_destroy();
    }
}


function editar_user()
{
    include "db.php";
    extract($_POST);

    $consulta_verificar = "SELECT * FROM usuarios WHERE username = ? AND id != ?";
    $stmt_verificar = mysqli_prepare($conexion, $consulta_verificar);

    if (!$stmt_verificar) {
        // Manejo de error si la consulta preparada falla
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: 'Se ha producido un problema al verificar el nombre de usuario.',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#034D81'
                }).then(function() {
                    window.location.href = '../views/usuarios.php';
                });
              </script>";
        return;
    }

    mysqli_stmt_bind_param($stmt_verificar, "si", $username, $id);
    mysqli_stmt_execute($stmt_verificar);
    $resultado_verificar = mysqli_stmt_get_result($stmt_verificar);

    if (mysqli_num_rows($resultado_verificar) > 0) {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: '¡Lo sentimos!',
                    text: 'El nombre de usuario ya está en uso. Por favor, elige otro nombre de usuario.',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#034D81'
                }).then(function() {
                    window.location.href = '../views/usuarios.php';
                });
              </script>";
    } else {
        // Si el nombre de usuario no está repetido, proceder con la actualización
        $consulta_actualizar_usuario = "UPDATE usuarios SET nombre = '$nombre', apellido = '$apellido', username = '$username', password = '$password', id_rol ='$id_rol' WHERE id = '$id' ";
        $resultado_actualizar_usuario = mysqli_query($conexion, $consulta_actualizar_usuario);

        if ($resultado_actualizar_usuario) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: '¡Registro actualizado exitosamente!',
                     text: 'La actualización del registro se ha completado satisfactoriamente.',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#034D81'
                }).then(function() {
                    window.location.href = '../views/usuarios.php';
                });
              </script>";
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: 'Se ha producido un problema al actualizar el registro. Por favor, verifica la información ingresada y vuelve a intentarlo.',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#034D81'
                }).then(function() {
                    window.location.href = '../views/usuarios.php';
                });
              </script>";
        }
    }
}


function insert_paciente()
{
    include "db.php";
    extract($_POST);

    // Verificar si la cédula ya existe en la tabla 'pacientes'
    $consulta_verificar = "SELECT * FROM pacientes WHERE cedula = '$cedula'";
    $resultado_verificar = mysqli_query($conexion, $consulta_verificar);

    if (mysqli_num_rows($resultado_verificar) > 0) {
        // La cédula ya existe, muestra un mensaje de error con SweetAlert
        echo "<script>
             Swal.fire({
                 icon: 'warning',
                 title: '¡Lo sentimos!',
                 html: '<span style=\"font-size: 16px; color: red;\">Ya existe un empleado con esta cédula en nuestra base de datos. Por favor, verifica la información ingresada.</span>',
                 confirmButtonText: 'Aceptar',
                confirmButtonColor: '#034D81'
             }).then(function() {
                 location.assign('../views/pacientes.php');
             });
             </script>";
    } else {
        // La cédula no existe, procede con la inserción
        $consulta = "INSERT INTO pacientes (cedula, nombre, apellido, cargo, institucion, fecha_nac, genero, fecha_ingreso, fecha_registro, encargado_registro)
                     VALUES ('$cedula', '$nombre', '$apellido', '$cargo', '$institucion', '$fecha_nac', '$genero', '$fecha_ingreso', '$fecha_registro', '$encargado_registro')";
        $resultado = mysqli_query($conexion, $consulta);

        if ($resultado) {
            echo "<script>
                 Swal.fire({
                     icon: 'success',
                     title: '¡Registro insertado exitosamente!',
                     text: 'La inserción del registro se ha completado satisfactoriamente.',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#034D81'
                 }).then(function() {
                     location.assign('../views/pacientes.php');
                 });
                 </script>";
        } else {
            echo "<script>
                 Swal.fire({
                     icon: 'error',
                     title: 'Error',
                     text: '<span style=\"font-size: 16px; color: red;\">Se ha producido un problema al insertar el registro. Por favor, verifica la información ingresada y vuelve a intentarlo.</span>',
                     confirmButtonText: 'Aceptar',
            confirmButtonColor: '#034D81',
                 }).then(function() {
                     location.assign('../views/pacientes.php');
                 });
                 </script>";
        }
    }
}

function editar_paciente()
{
    include "db.php";
    extract($_POST);

    // Verificar si la nueva cédula ya existe en la tabla 'pacientes' (excluyendo el propio registro)
    $consulta_verificar = "SELECT * FROM pacientes WHERE cedula = ? AND id != ?";
    $stmt_verificar = mysqli_prepare($conexion, $consulta_verificar);
    mysqli_stmt_bind_param($stmt_verificar, "si", $cedula, $id);
    mysqli_stmt_execute($stmt_verificar);
    $resultado_verificar = mysqli_stmt_get_result($stmt_verificar);

    if (mysqli_num_rows($resultado_verificar) > 0) {
        echo "<script>
             Swal.fire({
                 icon: 'error',
                 title: 'Lo sentimos',
                 text: 'Ya existe un empleado con esta cédula en nuestra base de datos. Por favor, verifica la información ingresada.',
                 confirmButtonText: 'Aceptar',
            confirmButtonColor: '#034D81',
             }).then(function() {
                 location.assign('../views/pacientes.php');
             });
             </script>";
    } else {
        // La cédula no existe, procede con la actualización
        $consulta = "UPDATE pacientes SET cedula = ?, nombre = ?, apellido = ?, cargo = ?, institucion = ?, fecha_nac = ?, fecha_ingreso = ?, genero = ? WHERE id = ?";
        $stmt = mysqli_prepare($conexion, $consulta);
        mysqli_stmt_bind_param($stmt, "ssssssssi", $cedula, $nombre, $apellido, $cargo, $institucion, $fecha_nac, $fecha_ingreso, $genero, $id);

        try {
            // Intenta realizar la actualización en la base de datos
            mysqli_stmt_execute($stmt);

            if (mysqli_stmt_affected_rows($stmt) > 0) {
                // Éxito: la actualización se realizó correctamente con SweetAlert
                echo "<script>
                 Swal.fire({
                     icon: 'success',
                     title: '¡Registro actualizado exitosamente!',
                     text: 'La actualización del registro se ha completado satisfactoriamente.',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#034D81'
                 }).then(function() {
                     location.assign('../views/pacientes.php');
                 });
                 </script>";
                exit();
            } else {
                // No se realizó ninguna actualización
                echo "<script>
                 Swal.fire({
                     icon: 'info',
                     title: 'Información',
                     text: 'No se realizaron cambios. Verifique los datos ingresados.',
                     confirmButtonText: 'Aceptar',
            confirmButtonColor: '#034D81',
                 }).then(function() {
                     location.assign('../views/pacientes.php');
                 });
                 </script>";
            }
        } catch (mysqli_sql_exception $e) {
            // Error en la base de datos
            echo "<script>
             Swal.fire({
                 icon: 'error',
                 title: 'Error',
                 text: 'No se pudo realizar el registro. Error: " . $e->getMessage() . "',
                 confirmButtonText: 'Aceptar',
            confirmButtonColor: '#034D81',
             }).then(function() {
                 location.assign('../views/pacientes.php');
             });
         </script>";
        }
    }
}


function insert_beneficiario()
{
    include "db.php";
    extract($_POST);

    // Validación de cédula del beneficiario
    if (empty($cedula_beneficiario) || $cedula_beneficiario == '0' || !ctype_digit($cedula_beneficiario)) {
        $cedula_beneficiario = "N/T"; // Asigna "N/T" si la cédula es inválida
    }

    // Verificación previa de la cédula del beneficiario para evitar duplicados con la misma cédula de empleado
    if ($cedula_beneficiario !== "N/T") {
        $consulta_verificar = $conexion->prepare("SELECT * FROM beneficiarios WHERE cedula_beneficiario = ? AND cedula_empleado = ?");
        $consulta_verificar->bind_param("ss", $cedula_beneficiario, $cedula_empleado);
        $consulta_verificar->execute();
        $resultado_verificar = $consulta_verificar->get_result();
        if ($resultado_verificar->num_rows > 0) {
            echo "<script>
                 Swal.fire({
                     icon: 'error',
                     title: '¡Lo sentimos!',
                     text: 'La cédula del beneficiario '$cedula_beneficiario' ya está registrada con el empleado '$cedula_empleado'. No se pueden duplicar estos registros.',
                     confirmButtonText: 'Aceptar',
                     confirmButtonColor: '#034D81'
                 }).then(function() {
                     location.assign('../views/beneficiarios.php');
                 });
                 </script>";
            exit();
        }
        $consulta_verificar->close();
    }

    // Verificación previa del parentesco
    if ($parentesco !== "N/T") {
        $consultaParentesco = $conexion->prepare("SELECT * FROM beneficiarios WHERE cedula_empleado = ? AND parentesco = ? AND (parentesco = 'MADRE' OR parentesco = 'PADRE' OR parentesco = 'CONYUGUE' OR parentesco = 'ESPOSO(A)' OR parentesco = 'CONCUBINO(A)')");
        $consultaParentesco->bind_param("ss", $cedula_empleado, $parentesco);
        $consultaParentesco->execute();
        $resultadoParentesco = $consultaParentesco->get_result();
        if ($resultadoParentesco->num_rows > 0) {
            echo "<script>
                 Swal.fire({
                     icon: 'error',
                     title: '¡Lo sentimos!',
                     text: 'El empleado '$cedula_empleado' ya tiene un beneficiario registrado con el parentesco '$parentesco'. No se permiten duplicados en este parentesco.',
                     confirmButtonText: 'Aceptar',
                     confirmButtonColor: '#034D81'
                 }).then(function() {
                     location.assign('../views/beneficiarios.php');
                 });
                 </script>";
            exit();
        }
        $consultaParentesco->close();
    }

    try {
        // Intenta realizar la inserción en la base de datos
        $consulta = "INSERT INTO beneficiarios (cedula_empleado, nombre, apellido, cedula_beneficiario, parentesco, fecha_nac, genero, fecha_registro, encargado_registro) 
                   VALUES ('$cedula_empleado', '$nombre', '$apellido', '$cedula_beneficiario', '$parentesco', '$fecha_nac', '$genero', '$fecha_registro', '$encargado_registro')";
        $resultado = mysqli_query($conexion, $consulta);

        if ($resultado) {
            echo "<script>
                 Swal.fire({
                     icon: 'success',
                     title: '¡Registro insertado exitosamente!',
                     text: 'La inserción del registro se ha completado satisfactoriamente.',
                     confirmButtonText: 'Aceptar',
                     confirmButtonColor: '#034D81'
                 }).then(function() {
                     location.assign('../views/beneficiarios.php');
                 });
             </script>";
            exit();
        } else {
            // Error en la inserción
            throw new Exception("No se pudo realizar el registro. Por favor, verifique los datos ingresados.");
        }
    } catch (mysqli_sql_exception $e) {
        // Error en la base de datos
        echo "<script>
             Swal.fire({
                 icon: 'error',
                 title: 'Error',
                 text: 'No se pudo realizar el registro. Error: " . $e->getMessage() . "',
             }).then(function() {
                 location.assign('../views/beneficiarios.php');
             });
         </script>";
    }
}



function editar_beneficiario()
{
    include "db.php";
    extract($_POST);

    // Validación de cédula del beneficiario
    if (empty($cedula_beneficiario) || $cedula_beneficiario == '0' || !ctype_digit($cedula_beneficiario)) {
        $cedula_beneficiario = "N/T"; // Asigna "N/T" si la cédula es inválida
    }

    // Verificación si la nueva cédula_beneficiario ya existe en la tabla 'beneficiarios' (excluyendo al beneficiario actual)
    if ($cedula_beneficiario !== "N/T") {
        $consulta_verificar_cedula = $conexion->prepare("SELECT * FROM beneficiarios WHERE cedula_beneficiario = ? AND id != ?");
        $consulta_verificar_cedula->bind_param("si", $cedula_beneficiario, $id);
        $consulta_verificar_cedula->execute();
        $resultado_verificar_cedula = $consulta_verificar_cedula->get_result();
        if ($resultado_verificar_cedula->num_rows > 0) {
            echo "<script>
                 Swal.fire({
                    icon: 'error',
                    title: '¡Lo sentimos!',
                    text: 'Ya existe un beneficiario con esta cédula en nuestra base de datos. Por favor, verifica la información ingresada.',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#034D81'
                 }).then(function() {
                     location.assign('../views/beneficiarios.php');
                 });
                 </script>";
            exit();
        }
        $consulta_verificar_cedula->close();
    }

    // Verificación previa del parentesco
    if ($parentesco !== "N/T") {
        $consultaParentesco = $conexion->prepare("SELECT * FROM beneficiarios WHERE cedula_empleado = ? AND parentesco = ? AND (parentesco = 'MADRE' OR parentesco = 'PADRE' OR parentesco = 'CONYUGUE' OR parentesco = 'ESPOSO(A)' OR parentesco = 'CONCUBINO(A)') AND id != ?");
        $consultaParentesco->bind_param("ssi", $cedula_empleado, $parentesco, $id);
        $consultaParentesco->execute();
        $resultadoParentesco = $consultaParentesco->get_result();
        if ($resultadoParentesco->num_rows > 0) {
            echo "<script>
                 Swal.fire({
                    icon: 'error',
                    title: '¡Lo sentimos!',
                    text: 'El empleado '$cedula_empleado' ya tiene un beneficiario registrado con el parentesco '$parentesco'. No se permiten duplicados en este parentesco.',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#034D81'
                 }).then(function() {
                     location.assign('../views/beneficiarios.php');
                 });
                 </script>";
            exit();
        }
        $consultaParentesco->close();
    }

    try {
        // Intenta realizar la actualización en la base de datos
        $consulta = "UPDATE beneficiarios SET cedula_empleado='$cedula_empleado', nombre='$nombre', apellido='$apellido', cedula_beneficiario='$cedula_beneficiario', parentesco='$parentesco', fecha_nac='$fecha_nac', genero='$genero' WHERE id=$id";
        $resultado = mysqli_query($conexion, $consulta);

        if ($resultado) {
            echo "<script>
                 Swal.fire({
                    icon: 'success',
                    title: '¡Registro actualizado exitosamente!',
                    text: 'La actualización del registro se ha completado satisfactoriamente.',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#034D81'
                 }).then(function() {
                     location.assign('../views/beneficiarios.php');
                 });
             </script>";
            exit();
        } else {
            // Error en la actualización
            throw new Exception("No se pudo realizar la actualización. Por favor, verifique los datos ingresados.");
        }
    } catch (mysqli_sql_exception $e) {
        // Error en la base de datos
        echo "<script>
             Swal.fire({
                 icon: 'error',
                 title: 'Error',
                 text: 'No se pudo realizar la actualización. Error: " . $e->getMessage() . "',
             }).then(function() {
                 location.assign('../views/beneficiarios.php');
             });
         </script>";
    }
}


