<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="<?= APP_URL ?>/assets/js/main.js"></script>

<script>
    // Sidebar toggle for mobile
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.getElementById('dashboardSidebar');
        const toggle = document.getElementById('sidebarToggle');
        const overlay = document.getElementById('sidebarOverlay');

        if (toggle) {
            toggle.addEventListener('click', function () {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            });
        }

        if (overlay) {
            overlay.addEventListener('click', function () {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            });
        }
    });
</script>
<script>
    // Convert ISO UTC datetimes returned by the API into the user's local timezone for display.
    (function () {
        function formatLocal(el) {
            try {
                var iso = el.getAttribute('data-datetime') || el.dataset.datetime || el.textContent.trim();
                if (!iso) return;
                var d = new Date(iso);
                if (isNaN(d.getTime())) return;
                var fmt = el.getAttribute('data-format') || el.dataset.format || 'datetime';
                var out = '';
                if (fmt === 'date-long') {
                    out = d.toLocaleDateString(undefined, { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                } else if (fmt === 'date-short') {
                    out = d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
                } else if (fmt === 'time') {
                    out = d.toLocaleTimeString(undefined, { hour: 'numeric', minute: '2-digit' });
                } else { // datetime
                    out = d.toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' }) + ' at ' + d.toLocaleTimeString(undefined, { hour: 'numeric', minute: '2-digit' });
                }
                el.textContent = out;
            } catch (e) { }
        }

        document.addEventListener('DOMContentLoaded', function () {
            try {
                var nodes = document.querySelectorAll('.local-time');
                nodes.forEach(function (n) { formatLocal(n); });
            } catch (e) { }
        });
    })();
</script>
</body>

</html>