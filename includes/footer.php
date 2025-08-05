<?php
session_start();
error_reporting(0);
$varsesion = $_SESSION['fasganz'];

if ($varsesion == null || $varsesion = '') {
    header("Location: _sesion/login.php");
}

?>
<!-- Footer -->
<footer class="sticky-footer bg-white">
    <div class="container my-auto">
        <div class="copyright text-center my-auto">
            <!-- Primera línea visible -->
            <p id="license-info" class="mayus bold">Modificaciones y mejoras por TSU. Frank Guiche, © <?php echo date("Y"); ?>. Todos los derechos reservados.</p>

            <hr>

            <!-- Información adicional oculta por defecto -->
            <div id="more-info" style="display: none;">
                <p>Sistema basado en el proyecto de tesis de <strong> Frank Guiche y Anderson Hernández.</strong></p>
                <p>Licencia de versión de <strong>Prueba (BETA) Adaptada para Uso Interno</strong> - Válida hasta: <span id="expiry-date" class="bold">01-06-2025</span> <strong>(<span id="days-left"></span> días restantes).</strong>
                </p>
            </div>

            
            <!-- Enlace para mostrar/ocultar información adicional -->
            <a href="javascript:void(0);" id="toggleLink" onclick="toggleInfo()">Ver más información</a>

        </div>

        <!-- JavaScript para mostrar u ocultar la información -->
        <script>
            function toggleInfo() {
                var extraInfo = document.getElementById("more-info");
                var toggleLink = document.getElementById("toggleLink");

                // Toggle display of extraInfo
                if (extraInfo.style.display === "none") {
                    extraInfo.style.display = "block";
                    toggleLink.innerText = "Ocultar información";
                } else {
                    extraInfo.style.display = "none";
                    toggleLink.innerText = "Ver más información";
                }
            }

            // Función para calcular los días restantes hasta la fecha de expiración
            function calculateDaysLeft() {
                var expiryDate = new Date('2025-06-1'); // Fecha de expiración
                var today = new Date(); // Fecha actual
                var timeDifference = expiryDate - today; // Diferencia en milisegundos
                var daysLeft = Math.floor(timeDifference / (1000 * 3600 * 24)); // Convierte a días

                // Muestra los días restantes junto a la fecha de expiración
                document.getElementById("days-left").innerHTML = daysLeft;
            }

            // Llama a la función para calcular los días restantes cuando la página cargue
            window.onload = calculateDaysLeft;
        </script>


    </div>
</footer>
<!-- End of Footer -->

<!-- SweetAlert2 -->
<script src="../vendor/SweetAlert2/js/sweetalert2.all.min.js"></script>

<!-- Bootstrap core JavaScript-->
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/bootstrap-5.3.2-dist/js/bootstrap.bundle.js"></script>

<!-- Custom scripts for all pages-->
<script src="../js/sb-admin-2.min.js"></script>

<!-- DataTables -->

<script src="../vendor/DataTables-1.13.6/js/jquery.dataTables.min.js"></script>
<script src="../vendor/DataTables-1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!--<script src="../js/demo/datatables-demo.js"></script>-->