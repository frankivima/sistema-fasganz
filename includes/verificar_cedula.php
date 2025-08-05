<?php
// Incluye el archivo de conexión a la base de datos
include('db.php');

if (isset($_POST['cedulaEmpleado'])) {
    $cedulaEmpleado = $_POST['cedulaEmpleado'];

    // Realiza una consulta SQL para verificar si la cédula existe en la tabla de pacientes
    $query = "SELECT * FROM pacientes WHERE cedula = '$cedulaEmpleado'";
    $result = mysqli_query($conexion, $query);

    if (mysqli_num_rows($result) > 0) {
        echo
        '<div class="alert alert-success mt-3 d-flex align-items-center alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i>
         Cédula válida!
      </div>';
    } else {
        echo '<div class="alert alert-danger mt-3 d-flex align-items-center alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-ban"></i>
        Cédula no encontrada en la Base de Datos.
      </div>';
    }
}
