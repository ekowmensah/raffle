    <!-- Main Footer -->
    <footer class="main-footer">
        <strong>Copyright &copy; <?= date('Y') ?> <a href="#"><?= APP_NAME ?></a>.</strong>
        All rights reserved.
        <div class="float-right d-none d-sm-inline-block">
            <b>Version</b> <?= APP_VERSION ?>
        </div>
    </footer>
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="<?= vendor('jquery/jquery.min.js') ?>"></script>
<!-- Bootstrap 4 -->
<script src="<?= vendor('bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<!-- AdminLTE App -->
<script src="<?= vendor('adminlte/js/adminlte.min.js') ?>"></script>
<!-- Custom JS -->
<script src="<?= asset('js/custom.js') ?>?v=<?= time() ?>"></script>

<script>
    // Prevent any auto-hiding of flash messages
    $(document).ready(function() {
        // Stop all timers that might hide alerts
        $('.alert').stop(true, true);
        
        // Ensure alerts are visible
        $('.alert').show();
        
        // Flash messages will stay on screen until manually dismissed
        // Users can click the X button to close them
    });
</script>
</body>
</html>
