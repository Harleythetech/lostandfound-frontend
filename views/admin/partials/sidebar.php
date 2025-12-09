<?php
$sidebarUser = getCurrentUser();
$currentPath = $_GET['url'] ?? '';
?>

<!-- Mobile Restriction Overlay for Admin Panel -->
<div class="admin-mobile-block">
    <div class="admin-mobile-block-content">
        <i class="bi bi-display text-primary" style="font-size: 4rem;"></i>
        <h4 class="fw-bold mt-3 mb-2">Desktop Required</h4>
        <p class="text-muted mb-4">The Admin Panel is optimized for desktop use. Please access it from a computer or
            tablet for the best experience.</p>
        <a href="<?= APP_URL ?>/dashboard" class="btn btn-primary">
            <i class="bi bi-arrow-left me-2"></i>Back to App
        </a>
    </div>
</div>

<style>
    /* Admin Mobile Block Overlay */
    .admin-mobile-block {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: #fff;
        z-index: 9999;
        justify-content: center;
        align-items: center;
        text-align: center;
        padding: 2rem;
    }

    .admin-mobile-block-content {
        max-width: 320px;
    }

    /* Show on mobile screens (less than 992px) */
    @media (max-width: 991.98px) {
        .admin-mobile-block {
            display: flex;
        }

        .dashboard-wrapper {
            display: none !important;
        }
    }

    /* Dark mode support */
    body.dark-mode .admin-mobile-block {
        background: #1a1a2e;
        color: #e4e6eb;
    }

    body.dark-mode .admin-mobile-block .text-muted {
        color: #8b8b8b !important;
    }
</style>

<!-- Admin Sidebar (Desktop) -->
<aside class="dashboard-sidebar" id="dashboardSidebar">
    <!-- Brand/Logo -->
    <a href="<?= APP_URL ?>/admin" class="sidebar-brand">
        <span>Lost & Found</span>
        <small class="sidebar-brand-sub">Admin Panel | <?= APP_VERSION ?></small>
    </a>

    <!-- Navigation -->
    <nav class="sidebar-nav">
        <a href="<?= APP_URL ?>/admin"
            class="sidebar-link <?= $currentPath === 'admin' || $currentPath === 'admin/dashboard' ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>

        <a href="<?= APP_URL ?>/admin/pending"
            class="sidebar-link <?= strpos($currentPath, 'admin/pending') !== false ? 'active' : '' ?>">
            <i class="bi bi-hourglass-split"></i>
            <span>Pending Review</span>
        </a>

        <div class="sidebar-divider"></div>

        <a href="<?= APP_URL ?>/admin/lost-items"
            class="sidebar-link <?= strpos($currentPath, 'admin/lost-items') !== false ? 'active' : '' ?>">
            <i class="bi bi-exclamation-triangle"></i>
            <span>Lost Items</span>
        </a>

        <a href="<?= APP_URL ?>/admin/found-items"
            class="sidebar-link <?= strpos($currentPath, 'admin/found-items') !== false ? 'active' : '' ?>">
            <i class="bi bi-box-seam"></i>
            <span>Found Items</span>
        </a>

        <a href="<?= APP_URL ?>/admin/claims"
            class="sidebar-link <?= strpos($currentPath, 'admin/claims') !== false ? 'active' : '' ?>">
            <i class="bi bi-hand-index"></i>
            <span>Claims</span>
        </a>

        <a href="<?= APP_URL ?>/admin/users"
            class="sidebar-link <?= strpos($currentPath, 'admin/users') !== false ? 'active' : '' ?>">
            <i class="bi bi-people"></i>
            <span>Users</span>
        </a>

        <div class="sidebar-divider"></div>

        <a href="<?= APP_URL ?>/admin/categories"
            class="sidebar-link <?= strpos($currentPath, 'admin/categories') !== false ? 'active' : '' ?>">
            <i class="bi bi-tags"></i>
            <span>Categories</span>
        </a>

        <a href="<?= APP_URL ?>/admin/locations"
            class="sidebar-link <?= strpos($currentPath, 'admin/locations') !== false ? 'active' : '' ?>">
            <i class="bi bi-geo-alt"></i>
            <span>Locations</span>
        </a>

        <div class="sidebar-divider"></div>

        <a href="<?= APP_URL ?>/admin/reports"
            class="sidebar-link <?= strpos($currentPath, 'admin/reports') !== false ? 'active' : '' ?>">
            <i class="bi bi-graph-up"></i>
            <span>Reports</span>
        </a>

        <a href="<?= APP_URL ?>/admin/activity"
            class="sidebar-link <?= strpos($currentPath, 'admin/activity') !== false ? 'active' : '' ?>">
            <i class="bi bi-list-ul"></i>
            <span>Activity Logs</span>
        </a>
    </nav>

    <!-- User Profile at Bottom with Dropdown -->
    <div class="sidebar-user" id="sidebarUserBtn">
        <div class="d-flex align-items-center justify-content-between w-100">
            <div class="d-flex align-items-center">
                <div class="sidebar-avatar bg-primary">
                    <?= strtoupper(substr($sidebarUser['first_name'] ?? 'A', 0, 1) . substr($sidebarUser['last_name'] ?? '', 0, 1)) ?>
                </div>
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name">
                        <?= htmlspecialchars(($sidebarUser['first_name'] ?? '') . ' ' . ($sidebarUser['last_name'] ?? '')) ?>
                    </div>
                    <div class="sidebar-user-id">Administrator</div>
                </div>
            </div>
            <i class="bi bi-chevron-up text-muted sidebar-chevron"></i>
        </div>

        <!-- User Dropdown Menu -->
        <div class="sidebar-user-menu" id="sidebarUserMenu">
            <a href="<?= APP_URL ?>/admin/profile" class="user-menu-item">
                <i class="bi bi-gear"></i>
                <span>Profile Settings</span>
            </a>
            <a href="<?= APP_URL ?>/logout" class="user-menu-item text-danger">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
</aside>

<script>
    // Sidebar User Menu Toggle
    document.addEventListener('DOMContentLoaded', function () {
        const userBtn = document.getElementById('sidebarUserBtn');
        const userMenu = document.getElementById('sidebarUserMenu');

        if (userBtn && userMenu) {
            userBtn.addEventListener('click', function (e) {
                if (!e.target.closest('.sidebar-user-menu')) {
                    userMenu.classList.toggle('show');
                    userBtn.classList.toggle('menu-open');
                }
            });

            // Close when clicking outside
            document.addEventListener('click', function (e) {
                if (!e.target.closest('.sidebar-user')) {
                    userMenu.classList.remove('show');
                    userBtn.classList.remove('menu-open');
                }
            });
        }
    });

</script>