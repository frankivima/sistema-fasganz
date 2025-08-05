
  
<?php
include "../db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $response = array();  // Crear un arreglo para la respuesta

    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $id_rol = trim($_POST['id_rol']);

    // Validar si el usuario ya existe
    $consulta_existencia = "SELECT COUNT(*) as total FROM usuarios WHERE username = '$username'";
    $resultado_existencia = mysqli_query($conexion, $consulta_existencia);

    if ($resultado_existencia) {
        $fila_existencia = mysqli_fetch_assoc($resultado_existencia);
        $total_existencia = $fila_existencia['total'];

        if ($total_existencia > 0) {
            // Usuario ya existe, enviar mensaje de error
            $response['status'] = 'error';
            $response['message'] = 'El usuario ya está en uso. Por favor, elige otro.';
        } else {
            // Insertar el nuevo usuario
            $consulta = "INSERT INTO usuarios (nombre, apellido, username,  password, id_rol)
                VALUES ('$nombre', '$apellido', '$username', '$password', '$id_rol')";
            $resultado = mysqli_query($conexion, $consulta);

            if ($resultado) {
                // Registro exitoso, enviar mensaje de éxito
                $response['status'] = 'success';
                $response['message'] = 'El registro fue guardado correctamente';
            } else {
                // Error al insertar en la base de datos, enviar mensaje de error
                $response['status'] = 'error';
                $response['message'] = 'Ocurrió un error al guardar los datos';
            }
        }
    } else {
        // Error al verificar la existencia del usuario, enviar mensaje de error
        $response['status'] = 'error';
        $response['message'] = 'Error al verificar la existencia del usuario';
    }

    // Devolver la respuesta como JSON
    echo json_encode($response);
}
?>






