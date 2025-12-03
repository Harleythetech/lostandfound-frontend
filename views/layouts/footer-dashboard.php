    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="<?= APP_URL ?>/assets/js/main.js"></script>
    
    <script>
    // Sidebar toggle for mobile
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('dashboardSidebar');
        const toggle = document.getElementById('sidebarToggle');
        const overlay = document.getElementById('sidebarOverlay');

        if (toggle) {
            toggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            });
        }

        if (overlay) {
            overlay.addEventListener('click', function() {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            });
        }
    });
    </script>
</body>
</html>
