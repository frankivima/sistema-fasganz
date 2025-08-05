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
                    <h4 class="m-0 font-primary mayus col-5">Lista de Beneficiarios</h4>
                    <div class="col text-right">
                        <?php
                        // Verifica el rol del usuario
                        if ($_SESSION['id_rol'] == 1) {
                        ?>
                            <button type="button" class="btn btn-agg btn-md mayus" data-toggle="modal" data-target="#beneficiario">
                                <i class="fa fa-user-plus bold"></i> Agregar Beneficiario
                            </button>

                            <a href="../includes/_beneficiario/subir_excel.php" class="btn btn-infor btn-md bold mayus">
                                <i class="fa fa-file-import bold"></i> Importar Data
                            </a>

                            <a href="../views/generar_reporte_beneficiarios.php" class="btn btn-delete mayus <?php echo ($totalBeneficiarios == 0) ? 'disabled' : ''; ?>">
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
            $query = "SELECT * FROM beneficiarios LIMIT $inicio, $registrosPorPagina";
            $result = mysqli_query($conexion, $query);
            ?>

            <!-- Muestra la lista de pacientes -->
            <ul class="list-group" id="listView" style="display: none;">
                <?php while ($fila = mysqli_fetch_assoc($result)) : ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1 font-primary bold"><?php echo $fila['nombre'] . ' ' . $fila['apellido']; ?></h5>
                            <h5 class="mb-1 font-danger"> C.I:<?php echo $fila['cedula_beneficiario']; ?></h5>
                            <p class="mb-1">
                                <strong>Cedula Empleado:</strong>
                                <?php
                                $cedula = $fila['cedula_empleado'];
                                echo "<a class='font-h1' href='../includes/_paciente/ver_paciente.php?cedula=$cedula'>$cedula</a>";
                                ?> <br>
                                <strong>Parentesco:</strong> <?php echo $fila['parentesco']; ?> <br>
                                <strong>Edad:</strong> <?php echo $fila['edad']; ?>
                            </p>
                        </div>
                        <div class="btn-group">
                            <a class="btn btn-edit btn-sm mx-2" href="../includes/editar_paciente.php?cedula=<?php echo $fila['cedula'] ?> ">
                                <i class="fa fa-edit "></i>
                            </a>
                            <a href="../includes/eliminar_paciente.php?cedula=<?php echo $fila['cedula'] ?> " data-nombre=" <?php echo $fila['nombre'] ?> " data-apellido=" <?php echo $fila['apellido'] ?> " class="btn btn-delete btn-sm btn-del">
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
                                <th>ID_Empleado</th>
                                <th>Nombre / Apellido</th>
                                <th>Cédula</th>
                                <th class="text-center">Parentesco</th>
                                <th class="text-center">Edad</th>
                                <th class="text-center">Género</th>

                                <?php
                                // Verifica el rol del usuario
                                if ($_SESSION['id_rol'] == 1 || $_SESSION['id_rol'] == 3) {
                                ?>

                                    <th class="text-center">Acciones</th>
                                <?php
                                };
                                ?>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <!-- Ventana Modal para ver detalles del beneficiario -->
            <div class="modal fade" id="verBeneficiarioModal" tabindex="-1" role="dialog" aria-labelledby="verBeneficiarioModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="verBeneficiarioModalLabel">Detalles del Beneficiario</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="card mb-1 px-3">

                                    <div class="row g-0">
                                        <div class="col-lg-4 col-md-2 col-sm-0 mt-3">
                                            <img src="../img/logo1.png" class="img-fluid rounded-start" alt="...">
                                        </div>
                                        <div class="col-lg-8 col-md-10 col-sm-12 text-justify">
                                            <div class="card-body">
                                                <h2 class="card-title font-dark bold mayus ">Detalles de Beneficiario </h2>
                                                <h2 class="font-dark bold mayus">Titular C.I: <span class="font-primary" id="modalCedulaEmpleado"></span> </h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <div class="container">
                                    <div class="row g-0 mx-5">
                                        <div class="col-md-12 mt-3 text-justify font-dark">

                                            <div class="row">
                                                <div class="col-5">
                                                    <p><strong class="bold mayus">Nombre del Beneficiario:</strong></p>
                                                </div>
                                                <div class="col">
                                                    <span id="modalNombreCompleto"></span>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-5">
                                                    <p><strong class="bold mayus">cédula de Identidad:</strong></p>
                                                </div>
                                                <div class="col">
                                                    <span id="modalCedulaBeneficiario"></span>
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


                                            <div class="row">
                                                <div class="col-5">
                                                    <p><strong class="bold mayus">Parentesco:</strong></p>
                                                </div>
                                                <div class="col">
                                                    <span id="modalParentesco"></span>
                                                </div>
                                            </div>

                                            <hr>

                                            <!-- Botón para ver detalles del titular -->


                                            <div class="d-flex justify-content-end">
                                                <button id="verDetallesTitular" type="button" class="btn btn-info" data-target="#detallesTitular" aria-expanded="false" aria-controls="detallesTitular">
                                                    <span class="icono-detalle fa-solid fa-plus"></span>
                                                    <span id="toggleText">Mostrar Información del Titular</span>
                                                </button>
                                            </div>

                                            <div class="collapse mt-3" id="detallesTitular">
                                                <div class="card card-body">
                                                    <div class="row">
                                                        <div class="col-5">
                                                            <p><strong class="bold mayus">Nombre del Empleado:</strong></p>
                                                        </div>
                                                        <div class="col">
                                                            <p id="titularNombre"></p>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-5">
                                                            <p><strong class="bold mayus">Cédula del Titular:</strong></p>
                                                        </div>
                                                        <div class="col">
                                                            <p id="titularCedula"></p>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-5">
                                                            <p><strong class="bold mayus">Institución:</strong></p>
                                                        </div>
                                                        <div class="col">
                                                            <p id="titularInstitucion"></p>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-5">
                                                            <p><strong class="bold mayus">Cargo:</strong></p>
                                                        </div>
                                                        <div class="col">
                                                            <p id="titularCargo"></p>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>



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

            <script>
                $(document).ready(function() {
                    // Cargar los detalles del beneficiario
                    $('#dataTable').on('click', 'button[data-target="#verBeneficiarioModal"]', function() {
                        var beneficiarioId = $(this).data('id');

                        $.ajax({
                            url: '../includes/_beneficiario/detalles_beneficiario.php',
                            type: 'POST',
                            data: {
                                id: beneficiarioId
                            },
                            dataType: 'json',
                            success: function(data) {
                                if (data.success) {
                                    // Rellena la información del beneficiario en el modal
                                    $('#modalCedulaEmpleado').text(data.beneficiario.cedula_empleado);
                                    $('#modalNombreCompleto').text(data.beneficiario.nombre_completo);
                                    $('#modalCedulaBeneficiario').text(data.beneficiario.cedula_beneficiario);
                                    $('#modalParentesco').text(data.beneficiario.parentesco);
                                    $('#modalEdad').text(data.beneficiario.edad);
                                    $('#modalFechaNacimiento').text(data.beneficiario.fecha_nac);
                                    $('#modalGenero').text(data.beneficiario.genero);
                                    // Asegúrate de que el contenedor de detalles del titular esté oculto al inicio
                                    $('#detallesTitular').hide(); // Ocultar detalles del titular al abrir el modal
                                } else {
                                    Swal.fire('Error', 'No se pudo cargar la información del beneficiario.', 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error de conexión', 'No se pudo obtener la información del beneficiario.', 'error');
                            }
                        });
                    });

                    $('#verDetallesTitular').on('click', function() {
                        var cedulaEmpleado = $('#modalCedulaEmpleado').text();
                        if (!cedulaEmpleado) {
                            Swal.fire('Error', 'Cédula del empleado no encontrada.', 'error');
                            return;
                        }

                        $.ajax({
                            url: '../includes/_beneficiario/detalles_titular.php', // Ruta al archivo PHP
                            type: 'POST',
                            data: {
                                cedula_empleado: cedulaEmpleado
                            },
                            dataType: 'json',
                            success: function(response) {
                                console.log(response); // Verifica la respuesta en la consola
                                if (response.success) {
                                    // Muestra los detalles del titular en el contenedor
                                    $('#titularNombre').text(`${response.empleado.nombre_completo}`);
                                    $('#titularCedula').text(`${response.empleado.cedula}`);
                                    $('#titularInstitucion').text(`${response.empleado.institucion}`);
                                    $('#titularCargo').text(`${response.empleado.cargo}`);

                                    // Muestra el contenedor
                                    $('#detallesTitular').show();
                                } else {
                                    Swal.fire({
                                        title: 'Error',
                                        text: response.message || 'No se pudo cargar la información del titular.',
                                        icon: 'error',
                                        confirmButtonText: 'Aceptar'
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                console.log(xhr.responseText); // Mostrar el mensaje de error en la consola
                                Swal.fire({
                                    title: 'Error de conexión',
                                    text: 'No se pudo obtener la información del titular.',
                                    icon: 'error',
                                    confirmButtonText: 'Aceptar'
                                });
                            }
                        });
                    });

                    $('#verDetallesTitular').on('click', function() {
                        var $detallesTitular = $('#detallesTitular');
                        var $icono = $(this).find('.icono-detalle'); // Seleccionar el span del icono

                        // Cambiar el estado del botón y del texto
                        if ($detallesTitular.is(':visible')) {
                            $detallesTitular.collapse('hide'); // Ocultar los detalles
                            $icono.removeClass('fa-minus').addClass('fa-plus'); // Cambiar a "más"
                            $('#toggleText').text('Mostrar Información del Titular'); // Cambiar el texto
                        } else {
                            $detallesTitular.collapse('show'); // Mostrar los detalles
                            $icono.removeClass('fa-plus').addClass('fa-minus'); // Cambiar a "menos"
                            $('#toggleText').text('Ocultar Información del Titular'); // Cambiar el texto
                        }
                    });


                });
            </script>


            <script>
                $(document).ready(function() {
                    // Inicializa DataTable
                    $('#dataTable').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "ajax": {
                            "url": "../includes/_beneficiario/beneficiarios_server.php",
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
                        "order": [
                            [0, 'asc']
                        ],
                        "columns": [{
                                "data": "cedula_empleado"
                            },
                            {
                                "data": "nombre_completo"
                            },
                            {
                                "data": "cedula_beneficiario"
                            },
                            {
                                "data": "parentesco",
                                "className": "text-center"
                            },
                            {
                                "data": "edad",
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
                            title: '¿Estás seguro de eliminar este Beneficiario?',
                            text: "¡No podrás revertir esto!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#034D81',
                            cancelButtonColor: '#8A021B',
                            confirmButtonText: 'Sí, eliminar',
                            cancelButtonText: 'Cancelar',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Mostrar mensaje de eliminación exitosa por 2 segundos
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Eliminado',
                                    text: 'El registro fue eliminado exitosamente',
                                    confirmButtonText: 'Aceptar',
                                    confirmButtonColor: '#034D81',
                                    timer: 2000, // Duración en milisegundos
                                    timerProgressBar: true, // Barra de progreso en el temporizador
                                    showConfirmButton: true // Ocultar automáticamente el botón de confirmación
                                });

                                // Redirigir después de mostrar el mensaje
                                setTimeout(() => {
                                    document.location.href = href;
                                }, 2000); // 2 segundos de espera antes de redirigir
                            }
                        });
                    });


                });
            </script>

        </div>

    </div>
    <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->


    <?php include "../includes/footer.php"; ?>

    <?php include "../includes/_beneficiario/insert_beneficiario.php"; ?>


</body>

</html>