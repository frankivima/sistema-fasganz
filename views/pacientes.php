<?php
// Seguridad de sesiones
session_start();
error_reporting(0);
$varsesion = $_SESSION['fasganz'];

if ($varsesion == null || $varsesion == '' || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 3)) {
    echo '
    <script>
        alert("No tienes permiso para acceder a esta página.");
        // Redirecciona al usuario a la página de inicio de sesión
        window.location.href = "../includes/_sesion/login.php";
    </script>';
    die(); // Detiene la ejecución del código si no tiene permiso
}
?>

<?php
include "../includes/header.php";
include "../includes/convert.php";

?>

<body id="page-top">

    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <div class="row">
                    <h4 class="m-0 font-primary mayus col-6">Lista de Empleados</h4>
                    <div class="col text-right">
                        <?php
                        // Verifica el rol del usuario
                        if ($_SESSION['id_rol'] == 1) {
                        ?>

                            <button type="button" class="btn btn-agg btn-md bold mayus" data-toggle="modal" data-target="#paciente">
                                <i class="fa fa-user-plus bold"></i> Agregar Empleado
                            </button>

                            <a href="../includes/_paciente/subir_excel.php" class="btn btn-infor btn-md bold mayus">
                                <i class="fa fa-file-import bold"></i> Importar Data
                            </a>
                            
                            <a href="../views/generar_reporte_empleados.php" class="btn btn-delete mayus <?php echo ($totalPacientes == 0) ? 'disabled' : ''; ?>">
                                <i class="fa fa-print"></i> Generar Reporte en PDF
                            </a>

                        <?php
                        };
                        ?>
                    </div>
                </div>
            </div>

            <?php
            include "../includes/db.php";

            // Número de registros por página
            $registrosPorPagina = 5;

            // Página actual (si no se establece, por defecto es la primera página)
            $paginaActual = isset($_GET['pagina']) ? $_GET['pagina'] : 1;

            // Calcular el inicio del conjunto de resultados para la paginación
            $inicio = ($paginaActual - 1) * $registrosPorPagina;

            // Consulta SQL con LIMIT
            $query = "SELECT * FROM pacientes LIMIT $inicio, $registrosPorPagina";
            $result = mysqli_query($conexion, $query);
            ?>

            <!-- Muestra la lista de pacientes -->
            <ul class="list-group" id="listView" style="display: none;">
                <?php while ($fila = mysqli_fetch_assoc($result)) : ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1 font-h1"><?php echo $fila['nombre'] . ' ' . $fila['apellido']; ?></h5>
                            <p class="mb-1">
                                <strong>Cédula:</strong> <?php echo $fila['cedula']; ?> <br>
                                <strong>Cargo:</strong> <?php echo $fila['cargo']; ?> <br>
                                <strong>Institución:</strong> <?php echo $fila['institucion']; ?> <br>
                                <strong>Edad:</strong> <?php echo $fila['edad']; ?> |
                                <strong>Años de Servicio:</strong> <?php echo $fila['años_servicio']; ?>
                            </p>
                        </div>
                        <div class="btn-group">
                            <a class="btn btn-edit btn-sm mx-2" href="../includes/_paciente/editar_paciente.php?cedula=<?php echo $fila['cedula'] ?> ">
                                <i class="fa fa-edit "></i>
                            </a>
                            <a href="../includes/_paciente/eliminar_paciente.php?cedula=<?php echo $fila['cedula'] ?> " data-nombre=" <?php echo $fila['nombre'] ?> " data-apellido=" <?php echo $fila['apellido'] ?> " class="btn btn-delete btn-sm btn-del">
                                <i class="fa fa-trash "></i>
                            </a>
                        </div>
                    </li>
                <?php endwhile; ?>
            </ul>


            <div class="d-block d-md-none">

                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-end">
                        <?php
                        $query = "SELECT COUNT(*) as total FROM pacientes";
                        $result = mysqli_query($conexion, $query);
                        $fila = mysqli_fetch_assoc($result);
                        $totalRegistros = $fila['total'];

                        $totalPaginas = ceil($totalRegistros / $registrosPorPagina);
                        // Botón "Previous"
                        if ($paginaActual > 1) {
                            echo "<li class='page-item'><a class='page-link' href='pacientes.php?pagina=" . ($paginaActual - 1) . "' aria-label='Previous'><span aria-hidden='true'>&laquo;</span></a></li>";
                        } else {
                            echo "<li class='page-item disabled'><span class='page-link' aria-hidden='true'>&laquo;</span></li>";
                        }

                        // Botón "Next"
                        if ($paginaActual < $totalPaginas) {
                            echo "<li class='page-item'><a class='page-link' href='pacientes.php?pagina=" . ($paginaActual + 1) . "' aria-label='Next'><span aria-hidden='true'>&raquo;</span></a></li>";
                        } else {
                            echo "<li class='page-item disabled'><span class='page-link' aria-hidden='true'>&raquo;</span></li>";
                        }
                        ?>
                    </ul>
                </nav>

            </div>



            <div id="cardView" class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead class="table table-comp">
                            <tr class="mayus">
                                <th>Cédula</th>
                                <th>Nombre Completo</th>
                                <th>Institución</th>
                                <th class="text-center">Edad</th>
                                <th class="text-center">Servicio</th>
                                <th class="text-center">Género</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <!-- Ventana Modal para ver detalles del paciente -->
            <div class="modal fade" id="verPacienteModal" tabindex="-1" role="dialog" aria-labelledby="verPacienteModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="verPacienteModalLabel">Detalles del Paciente</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="card mb-1 px-5">

                                    <div class="row g-0">
                                        <div class="col-lg-4 col-md-2 col-sm-0 mt-3">
                                            <img src="../img/logo1.png" class="img-fluid rounded-start" alt="...">
                                        </div>
                                        <div class="col-lg-8 col-md-10 col-sm-12 text-justify">
                                            <div class="card-body">
                                                <h2 class="card-title font-dark bold mayus ">Detalles de Empleado </h2>
                                                <h2 class="font-dark bold mayus">C.I: <span class="font-primary" id="modalCedulaE"></span> </h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <div class="container">
                                    <div class="row g-0 mx-5 mt-3">
                                        <div class="col-md-12 text-justify font-dark">

                                            <div class="row">
                                                <div class="col-5">
                                                    <p><strong class="bold mayus">Nombre del Empleado:</strong></p>
                                                </div>
                                                <div class="col">
                                                    <span id="modalNombreCompleto"></span>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-5">
                                                    <p><strong class="bold mayus">F/ Nacimiento:</strong></p>
                                                </div>
                                                <div class="col">
                                                    <span id="modalFechaNacimiento"></span>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-5">
                                                    <p><strong class="bold mayus">Edad:</strong></p>
                                                </div>
                                                <div class="col">
                                                    <span id="modalEdad"></span>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-5">
                                                    <p><strong class="bold mayus">Género:</strong></p>
                                                </div>
                                                <div class="col">
                                                    <span id="modalGenero"></span>
                                                </div>
                                            </div>

                                            <hr>


                                            <div class="row">
                                                <div class="col-5">
                                                    <p><strong class="bold mayus">Institución:</strong></p>
                                                </div>
                                                <div class="col">
                                                    <span id="modalInstitucion"></span>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-5">
                                                    <p><strong class="bold mayus">Cargo:</strong></p>
                                                </div>
                                                <div class="col">
                                                    <span id="modalCargo"></span>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-5">
                                                    <p><strong class="bold mayus">Fecha de Ingreso:</strong></p>
                                                </div>
                                                <div class="col">
                                                    <span id="modalFechaIngreso"></span>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-5">
                                                    <p><strong class="bold mayus">Años de Servicio:</strong></p>
                                                </div>
                                                <div class="col">
                                                    <span id="modalAñosServicio"></span>
                                                </div>
                                            </div>

                                            <hr>

                                            <div id="beneficiariosTotalContainer" class="row mb-3" style="display: none;">
                                                <div class="col-8">
                                                    <small id="beneficiariosCountText"></small>
                                                </div>
                                                <div class="col-4 text-right">
                                                    <button id="verBeneficiariosTitular" type="button" class="btn btn-infor">
                                                        <i class="fa-solid fa-plus"></i>
                                                        Ver Beneficiarios
                                                    </button>

                                                </div>
                                            </div>

                                            <div id="noBeneficiariosMessage" class="alert alert-info" style="display: none;">
                                                Este empleado no tiene beneficiarios asociados.
                                            </div>


                                            <div id="beneficiariosListContainer" style="display: none;"></div> <!-- Contenedor para los beneficiarios -->

                                        </div>
                                    </div>

                                </div>


                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn mayus btn-secondary" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>




        </div>

        <script>
            $(document).ready(function() {

                // Reiniciar el estado del modal al abrir
                $('#verPacienteModal').on('show.bs.modal', function() {
                    // Reiniciar el texto del botón y ocultar el contenedor de beneficiarios
                    $('#verBeneficiariosTitular').html('<i class="fa-solid fa-plus"></i> Ver Beneficiarios');
                    $('#beneficiariosListContainer').hide();
                    $('#beneficiariosTotalContainer').hide();
                    $('#noBeneficiariosMessage').hide();
                    $('#beneficiariosCountText').text('');
                });

                $('#dataTable').on('click', 'button[data-target="#verPacienteModal"]', function() {
                    var pacienteId = $(this).data('id');

                    $.ajax({
                        url: '../includes/_paciente/detalles_paciente.php',
                        type: 'POST',
                        data: {
                            id: pacienteId
                        },
                        dataType: 'json',
                        success: function(data) {
                            if (data.success) {
                                $('#modalCedula').text(data.paciente.cedula);
                                $('#modalCedulaE').text(data.paciente.cedula);
                                $('#modalNombreCompleto').text(data.paciente.nombre_completo);
                                $('#modalInstitucion').text(data.paciente.institucion);
                                $('#modalCargo').text(data.paciente.cargo);
                                $('#modalEdad').text(data.paciente.edad);
                                $('#modalFechaNacimiento').text(data.paciente.fecha_nac);
                                $('#modalFechaIngreso').text(data.paciente.fecha_ingreso);
                                $('#modalAñosServicio').text(data.paciente.años_servicio);
                                $('#modalGenero').text(data.paciente.genero);
                                $('#modalTotalBeneficiarios').text(data.paciente.total_beneficiarios);

                                // Mostrar total de beneficiarios y botón solo si hay beneficiarios
                                if (data.paciente.total_beneficiarios > 0) {
                                    $('#beneficiariosCountText').text(`Tiene un total de ${data.paciente.total_beneficiarios} beneficiarios asociados.`);
                                    $('#beneficiariosTotalContainer').show();
                                    $('#verBeneficiariosTitular').show();
                                    $('#noBeneficiariosMessage').hide(); // Ocultar mensaje si hay beneficiarios
                                } else {
                                    $('#beneficiariosTotalContainer').hide();
                                    $('#verBeneficiariosTitular').hide();
                                    $('#noBeneficiariosMessage').show(); // Mostrar mensaje si no hay beneficiarios
                                    $('#beneficiariosListContainer').hide(); // Asegurarse de ocultar la lista
                                }
                            } else {
                                Swal.fire({
                                    title: 'Error',
                                    text: 'No se pudo cargar la información del paciente.',
                                    icon: 'error',
                                    confirmButtonText: 'Aceptar'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error al obtener los detalles del paciente:", error);
                            Swal.fire({
                                title: 'Error de conexión',
                                text: 'No se pudo obtener la información del paciente.',
                                icon: 'error',
                                confirmButtonText: 'Aceptar'
                            });
                        }
                    });
                });

                $('#verBeneficiariosTitular').on('click', function() {
                    var cedulaEmpleado = $('#modalCedulaE').text();

                    if (!cedulaEmpleado) {
                        Swal.fire('Error', 'Cédula del titular no encontrada.', 'error');
                        return;
                    }

                    $.ajax({
                        url: '../includes/_paciente/lista_beneficiarios.php',
                        type: 'POST',
                        data: {
                            cedula_empleado: cedulaEmpleado
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                mostrarBeneficiarios(response.beneficiarios);
                                $('#noBeneficiariosMessage').hide(); // Ocultar mensaje si hay beneficiarios

                                const contenedor = $('#beneficiariosListContainer');
                                if (contenedor.is(':visible')) {
                                    $('#toggleIcon').text('+'); // Cambiar a "+" si se oculta
                                    $('#verBeneficiariosTitular').html(' <i class="fa-solid fa-plus"></i> Ver Beneficiarios'); // Cambiar el texto del botón
                                    contenedor.hide(); // Ocultar la lista
                                } else {
                                    $('#toggleIcon').text('-'); // Cambiar a "-" si se muestra
                                    $('#verBeneficiariosTitular').html(' <i class="fa-solid fa-minus"></i> Ocultar Beneficiarios'); // Cambiar el texto del botón
                                    contenedor.show(); // Mostrar la lista
                                }
                            } else {
                                $('#beneficiariosListContainer').html('<p>No se encontraron beneficiarios para este titular.</p>').show();
                                $('#noBeneficiariosMessage').show(); // Mostrar mensaje si no hay beneficiarios
                            }
                        },
                        error: function() {
                            Swal.fire('Error de conexión', 'No se pudo obtener la lista de beneficiarios.', 'error');
                        }
                    });
                });
            });

            // Función para mostrar beneficiarios en el formato requerido
            function mostrarBeneficiarios(beneficiarios) {
                const contenedor = $('#beneficiariosListContainer');
                contenedor.empty(); // Limpiar el contenedor

                beneficiarios.forEach(beneficiario => {
                    const divBeneficiario = $('<div class="card mb-3"></div>');
                    const cardBody = $('<div class="card-body"></div>');

                    const parentesco = $('<h5></h5>').text(beneficiario.parentesco);
                    parentesco.addClass('font-rojo font-weight-bold');

                    const datos = `
                <p class="card-text"><strong class="mayus">Nombre:</strong> ${beneficiario.nombre_completo}</p>
                <p class="card-text"><strong class="mayus">Cédula:</strong> ${beneficiario.cedula}</p>
                <p class="card-text"><strong class="mayus">Edad:</strong> ${beneficiario.edad}</p>
                <p class="card-text"><strong class="mayus">Género:</strong> ${beneficiario.genero}</p>
            `;

                    cardBody.append(parentesco);
                    cardBody.append(datos);
                    divBeneficiario.append(cardBody);
                    contenedor.append(divBeneficiario);
                });
            }
        </script>

        <script>
            $(document).ready(function() {
                // Inicializa DataTable
                $('#dataTable').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                        "url": "../includes/_paciente/pacientes_server.php",
                        "type": "POST",
                        "dataSrc": function(json) {
                            console.log("JSON recibido desde el servidor:", json);
                            if (!json || typeof json !== "object") {
                                console.error("Respuesta inválida del servidor:", json);
                                Swal.fire({
                                    title: 'Error',
                                    text: 'El servidor devolvió una respuesta inválida.',
                                    icon: 'error',
                                    confirmButtonText: 'Aceptar'
                                });
                            }
                            return json.data;
                        },
                        "error": function(xhr, error, thrown) {
                            console.error("Error al obtener los datos:", error, xhr.responseText);
                            Swal.fire({
                                title: 'Error de conexión',
                                text: 'No se pudo obtener la respuesta del servidor.',
                                icon: 'error',
                                confirmButtonText: 'Aceptar'
                            });
                        }
                    },
                    "columns": [{
                            "data": "cedula"
                        },
                        {
                            "data": "nombre_completo"
                        },
                        {
                            "data": "institucion"
                        },
                        {
                            "data": "edad",
                            "className": "text-center" // Agregamos la clase 'text-center'
                        },
                        {
                            "data": "años_servicio",
                            "className": "text-center"
                        },
                        {
                            "data": "genero",
                            "className": "text-center"
                        },
                        {
                            "data": "acciones",
                            "className": "text-center",
                            "orderable": false
                        }
                    ]
                });

                // Añadir el manejador de eventos para los botones de eliminar
                $(document).on('click', '.btn-del', function(e) {
                    e.preventDefault();
                    const href = $(this).attr('href');
                    var pacienteNombre = $(this).data('nombre');
                    var pacienteApellido = $(this).data('apellido');

                    Swal.fire({
                        title: '¿Estás seguro?',
                        html: `¿Deseas eliminar al paciente <span style="color: red;">${pacienteNombre} ${pacienteApellido}</span>?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#034D81',
                        cancelButtonColor: '#8A021B',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: '¿Estás completamente seguro?',
                                html: `Al eliminar este registro, también se eliminarán todos los beneficiarios asociados. <br>`,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#034D81',
                                cancelButtonColor: '#8A021B',
                                confirmButtonText: 'Sí, continuar',
                                cancelButtonText: 'Cancelar',
                            }).then((secondResult) => {
                                if (secondResult.isConfirmed) {
                                    Swal.fire({
                                        title: 'Eliminado',
                                        text: 'El paciente y sus beneficiarios fueron eliminados correctamente.',
                                        icon: 'success',
                                        confirmButtonColor: '#034D81',
                                        timer: 2000,
                                        timerProgressBar: true,
                                        showConfirmButton: true
                                    }).then(() => {
                                        window.location.href = href;
                                    });
                                }
                            });
                        }
                    });
                });
            });
        </script>

        <script>
            // Pasar el valor de id_user desde PHP a JavaScript
            //const idUser = <?php echo isset($_SESSION['id_user']) ? $_SESSION['id_user'] : 'null'; ?>;

            //document.addEventListener("DOMContentLoaded", function() {
                // Si id_user no es 1, ocultar la columna de "Acciones"
            //    if (idUser !== 1) {
            //        const actionsColumn = document.querySelectorAll("#dataTable th:nth-child(7), #dataTable td:nth-child(7)");
            //        actionsColumn.forEach(cell => cell.style.display = "none");
           //     }
           // });
        </script>



    </div>
    <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->

    <?php include "../includes/footer.php"; ?>

    <?php include "../includes/_paciente/insert_paciente.php"; ?>

</body>

</html>