    <footer class="footer mt-auto py-4 bg-white">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h3><b><?= APP_NAME ?></b></h3>
                    <p class="text-muted">Helping people reunite with their lost belongings. Report lost items or help return found items to their rightful owners.</p>
                </div>
                <div class="col-md-3 mb-3">
                    <h6>Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?= APP_URL ?>/" class="text-muted text-decoration-none">Home</a></li>
                        <li><a href="<?= APP_URL ?>/about" class="text-muted text-decoration-none">About Us</a></li>
                        <li><a href="<?= APP_URL ?>/login" class="text-muted text-decoration-none">Sign In</a></li>
                        <li><a href="<?= APP_URL ?>/register" class="text-muted text-decoration-none">Create Account</a></li>
                    </ul>
                </div>
                <div class="col-md-2 mb-3">
                    <h6>Support</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?= APP_URL ?>/about" class="text-muted text-decoration-none">How It Works</a></li>
                        <li><a href="<?= APP_URL ?>/about" class="text-muted text-decoration-none">FAQ</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-3">
                    <h6>Contact</h6>
                    <ul class="list-unstyled text-muted">
                        <li><i class="bi bi-envelope me-2"></i>support@lostandfound.com</li>
                        <li><i class="bi bi-telephone me-2"></i>+63 123 456 7890</li>
                    </ul>
                </div>
            </div>
            <hr class="my-3">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <small class="text-muted">&copy; <?= date('Y') ?> <?= APP_NAME ?>. All rights reserved.</small>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="#" class="text-muted me-3"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-muted me-3"><i class="bi bi-twitter"></i></a>
                    <a href="#" class="text-muted"><i class="bi bi-instagram"></i></a>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="<?= APP_URL ?>/assets/js/main.js"></script>
    
    <!-- Dark Mode Script -->
    <script>
    // Dark Mode Toggle
    function toggleDarkMode() {
        const body = document.body;
        const isDark = body.classList.toggle('dark-mode');
        
        // Update all theme icons
        const navThemeIcon = document.getElementById('navThemeIcon');
        const themeIcon = document.getElementById('themeIcon');
        const themeText = document.getElementById('themeText');
        const mobileThemeIcon = document.getElementById('mobileThemeIcon');
        const mobileThemeText = document.getElementById('mobileThemeText');
        const headerThemeIcon = document.getElementById('headerThemeIcon');
        
        if (isDark) {
            if (navThemeIcon) navThemeIcon.className = 'bi bi-sun';
            if (themeIcon) themeIcon.className = 'bi bi-sun';
            if (themeText) themeText.textContent = 'Light Mode';
            if (mobileThemeIcon) mobileThemeIcon.className = 'bi bi-sun';
            if (mobileThemeText) mobileThemeText.textContent = 'Light';
            if (headerThemeIcon) headerThemeIcon.className = 'bi bi-sun';
            localStorage.setItem('darkMode', 'true');
        } else {
            if (navThemeIcon) navThemeIcon.className = 'bi bi-moon';
            if (themeIcon) themeIcon.className = 'bi bi-moon';
            if (themeText) themeText.textContent = 'Dark Mode';
            if (mobileThemeIcon) mobileThemeIcon.className = 'bi bi-moon';
            if (mobileThemeText) mobileThemeText.textContent = 'Dark';
            if (headerThemeIcon) headerThemeIcon.className = 'bi bi-moon';
            localStorage.setItem('darkMode', 'false');
        }
    }

    // Apply saved dark mode preference on load
    document.addEventListener('DOMContentLoaded', function() {
        if (localStorage.getItem('darkMode') === 'true') {
            document.body.classList.add('dark-mode');
            
            const navThemeIcon = document.getElementById('navThemeIcon');
            const themeIcon = document.getElementById('themeIcon');
            const themeText = document.getElementById('themeText');
            const mobileThemeIcon = document.getElementById('mobileThemeIcon');
            const mobileThemeText = document.getElementById('mobileThemeText');
            const headerThemeIcon = document.getElementById('headerThemeIcon');
            
            if (navThemeIcon) navThemeIcon.className = 'bi bi-sun';
            if (themeIcon) themeIcon.className = 'bi bi-sun';
            if (themeText) themeText.textContent = 'Light Mode';
            if (mobileThemeIcon) mobileThemeIcon.className = 'bi bi-sun';
            if (mobileThemeText) mobileThemeText.textContent = 'Light';
            if (headerThemeIcon) headerThemeIcon.className = 'bi bi-sun';
        }
    });
    </script>
</body>
</html>
