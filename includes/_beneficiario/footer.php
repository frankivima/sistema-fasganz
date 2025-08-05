<?php
session_start();
error_reporting(0);
$varsesion = $_SESSION['username'];

if ($varsesion == null || $varsesion = '') {
    header("Location: ../_sesion/login.php");
}

?> 

<!-- Footer -->
<footer class="sticky-footer bg-white">
    <div class="container my-auto">
        <div class="copyright text-center my-auto">
        <span>Copyright &copy; All rights reserved <a href="https://" target="_blank">Frank Guiche.</span></a>
        </div>
    </div>
</footer>
<!-- End of Footer -->

<!-- SweetAlert2 --> 
<script src="../../vendor/SweetAlert2/js/sweetalert2.all.min.js"></script>

<!-- Bootstrap core JavaScript-->
<script src="../../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../../vendor/bootstrap-5.3.2-dist/js/bootstrap.bundle.js"></script>

<!-- Custom scripts for all pages-->
<script src="../../js/sb-admin-2.min.js"></script>

<!-- DataTables -->

<script src="../../vendor/DataTables-1.13.6/js/jquery.dataTables.min.js"></script>
<script src="../../vendor/DataTables-1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script src="../../js/demo/datatables-demo.js"></script>