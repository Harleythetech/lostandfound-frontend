<?php 
$user = getCurrentUser();
$currentPath = $_GET['url'] ?? '';
$isMyStuffActive = strpos($currentPath, 'dashboard/my-') !== false;
?>

<!-- Dashboard Sidebar (Desktop) -->
<aside class="dashboard-sidebar" id="dashboardSidebar">
    <!-- Brand/Logo -->
    <a href="<?= APP_URL ?>/" class="sidebar-brand">
        <span>Lost & Found</span>
        <small class="sidebar-brand-sub">TSD-CREA.BUILD | V1.0.0</small>
    </a>

    <!-- Navigation -->
    <nav class="sidebar-nav">
        <a href="<?= APP_URL ?>/dashboard" class="sidebar-link <?= $currentPath === 'dashboard' ? 'active' : '' ?>">
            <i class="bi bi-house"></i>
            <span>Home</span>
        </a>
        
        <a href="<?= APP_URL ?>/lost-items" class="sidebar-link <?= $currentPath === 'lost-items' ? 'active' : '' ?>">
            <i class="bi bi-exclamation-triangle"></i>
            <span>Lost Items</span>
        </a>
        
        <a href="<?= APP_URL ?>/found-items" class="sidebar-link <?= $currentPath === 'found-items' ? 'active' : '' ?>">
            <i class="bi bi-box-seam"></i>
            <span>Found Items</span>
        </a>
        
        <div class="sidebar-divider"></div>
        
        <!-- My Stuff Dropdown -->
        <div class="sidebar-dropdown <?= $isMyStuffActive ? 'open' : '' ?>">
            <button class="sidebar-link sidebar-dropdown-toggle" type="button" onclick="toggleMyStuffMenu()">
                <i class="bi bi-folder"></i>
                <span>My Items</span>
                <i class="bi bi-chevron-down sidebar-dropdown-icon"></i>
            </button>
            <div class="sidebar-dropdown-menu" id="myStuffMenu">
                <a href="<?= APP_URL ?>/dashboard/my-lost-items" class="sidebar-link sidebar-sublink <?= strpos($currentPath, 'dashboard/my-lost-items') !== false ? 'active' : '' ?>">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span>My Lost Items</span>
                </a>
                <a href="<?= APP_URL ?>/dashboard/my-found-items" class="sidebar-link sidebar-sublink <?= strpos($currentPath, 'dashboard/my-found-items') !== false ? 'active' : '' ?>">
                    <i class="bi bi-box-seam-fill"></i>
                    <span>My Found Items</span>
                </a>
                <a href="<?= APP_URL ?>/dashboard/my-claims" class="sidebar-link sidebar-sublink <?= strpos($currentPath, 'dashboard/my-claims') !== false ? 'active' : '' ?>">
                    <i class="bi bi-hand-index"></i>
                    <span>My Claims</span>
                </a>
                <a href="<?= APP_URL ?>/dashboard/my-matches" class="sidebar-link sidebar-sublink <?= strpos($currentPath, 'dashboard/my-matches') !== false ? 'active' : '' ?>">
                    <i class="bi bi-link-45deg"></i>
                    <span>My Matches</span>
                </a>
            </div>
        </div>
        
        <div class="sidebar-divider"></div>
        
        <a href="<?= APP_URL ?>/dashboard/profile" class="sidebar-link <?= strpos($currentPath, 'dashboard/profile') !== false ? 'active' : '' ?>">
            <i class="bi bi-gear"></i>
            <span>Profile Settings</span>
        </a>
    </nav>

    <!-- Quick Actions -->
    <div class="sidebar-actions">
        <div class="sidebar-divider"></div>
        <a href="<?= APP_URL ?>/lost-items/create" class="sidebar-link text-danger">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span>Report Lost</span>
        </a>
        <a href="<?= APP_URL ?>/found-items/create" class="sidebar-link text-success">
            <i class="bi bi-box-seam-fill"></i>
            <span>Report Found</span>
        </a>
    </div>

    <!-- User Profile at Bottom with Dropdown -->
    <div class="sidebar-user" id="sidebarUserBtn">
        <div class="d-flex align-items-center justify-content-between w-100">
            <div class="d-flex align-items-center">
                <div class="sidebar-avatar">
                    <?= strtoupper(substr($user['first_name'] ?? 'U', 0, 1) . substr($user['last_name'] ?? '', 0, 1)) ?>
                </div>
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name"><?= htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?></div>
                    <div class="sidebar-user-id"><?= htmlspecialchars($user['school_id'] ?? '') ?></div>
                </div>
            </div>
            <i class="bi bi-chevron-up text-muted sidebar-chevron"></i>
        </div>
        
        <!-- User Dropdown Menu -->
        <div class="sidebar-user-menu" id="sidebarUserMenu">
            <a href="<?= APP_URL ?>/logout" class="user-menu-item text-danger">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
</aside>

<!-- Mobile Bottom Navigation (Instagram-style) -->
<nav class="mobile-bottom-nav">
    <div class="nav-items">
        <a href="<?= APP_URL ?>/dashboard" class="nav-item <?= $currentPath === 'dashboard' ? 'active' : '' ?>">
            <i class="bi bi-house-door<?= $currentPath === 'dashboard' ? '-fill' : '' ?>"></i>
            <span>Home</span>
        </a>
        
        <a href="<?= APP_URL ?>/notifications" class="nav-item <?= strpos($currentPath, 'notifications') !== false ? 'active' : '' ?>">
            <div class="position-relative">
                <i class="bi bi-bell<?= strpos($currentPath, 'notifications') !== false ? '-fill' : '' ?>"></i>
                <?php if (getUnreadNotificationCount() > 0): ?>
                    <span class="notification-dot"></span>
                <?php endif; ?>
            </div>
            <span>Alerts</span>
        </a>
        
        <!-- Big Report Button with Popup -->
        <div class="nav-item report-btn-wrapper">
            <button type="button" class="report-fab" id="reportFab" onclick="toggleReportMenu()">
                <i class="bi bi-plus-lg"></i>
            </button>
            <div class="report-menu" id="reportMenu">
                <a href="<?= APP_URL ?>/lost-items/create" class="report-menu-item report-lost">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span>Report Lost</span>
                </a>
                <a href="<?= APP_URL ?>/found-items/create" class="report-menu-item report-found">
                    <i class="bi bi-box-seam-fill"></i>
                    <span>Report Found</span>
                </a>
            </div>
        </div>
                <a href="<?= APP_URL ?>/notifications"
                    class="nav-item <?= strpos($currentPath, 'notifications') !== false ? 'active' : '' ?>">
                    <div class="position-relative">
                        <i class="bi bi-bell<?= strpos($currentPath, 'notifications') !== false ? '-fill' : '' ?>"></i>
                        <?php if (getUnreadNotificationCount() > 0): ?>
                            <span class="notification-dot"></span>
                        <?php endif; ?>
                    </div>
                    <span>Alerts</span>
                </a>
        <a href="<?= APP_URL ?>/dashboard/profile" class="nav-item profile-item <?= strpos($currentPath, 'profile') !== false ? 'active' : '' ?>">
            <div class="nav-avatar">
                <?= strtoupper(substr($user['first_name'] ?? 'U', 0, 1)) ?>
            </div>
            <span>Profile</span>
        </a>
    </div>
</nav>

<!-- Report Menu Overlay -->
<div class="report-overlay" id="reportOverlay" onclick="toggleReportMenu()"></div>

<script>
function toggleReportMenu() {
    const menu = document.getElementById('reportMenu');
    const overlay = document.getElementById('reportOverlay');
    const fab = document.getElementById('reportFab');
    
    menu.classList.toggle('show');
    overlay.classList.toggle('show');
    fab.classList.toggle('active');
}

// My Stuff Dropdown Toggle
function toggleMyStuffMenu() {
    const dropdown = document.querySelector('.sidebar-dropdown');
    dropdown.classList.toggle('open');
    localStorage.setItem('myStuffOpen', dropdown.classList.contains('open'));
}

// Close menu when clicking outside
document.addEventListener('click', function(e) {
    const menu = document.getElementById('reportMenu');
    const fab = document.getElementById('reportFab');
    if (menu && menu.classList.contains('show') && !e.target.closest('.report-btn-wrapper')) {
        toggleReportMenu();
    }
});

// Sidebar User Menu Toggle
document.addEventListener('DOMContentLoaded', function() {
    const userBtn = document.getElementById('sidebarUserBtn');
    const userMenu = document.getElementById('sidebarUserMenu');
    
    if (userBtn && userMenu) {
        userBtn.addEventListener('click', function(e) {
            if (!e.target.closest('.sidebar-user-menu')) {
                userMenu.classList.toggle('show');
                userBtn.classList.toggle('menu-open');
            }
        });
        
        // Close when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.sidebar-user')) {
                userMenu.classList.remove('show');
                userBtn.classList.remove('menu-open');
            }
        });
    }
    
    // Restore My Stuff dropdown state
    const dropdown = document.querySelector('.sidebar-dropdown');
    if (dropdown) {
        const isOpen = localStorage.getItem('myStuffOpen') === 'true';
        const hasActiveChild = dropdown.querySelector('.sidebar-sublink.active');
        if (isOpen || hasActiveChild) {
            dropdown.classList.add('open');
        }
    }
});

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
