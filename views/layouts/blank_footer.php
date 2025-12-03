    
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
