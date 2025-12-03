<?php $pageTitle = 'Admin Dashboard - ' . APP_NAME; ?>
<?php include __DIR__ . '/../layouts/header-dashboard.php'; ?>
<?php $user = getCurrentUser(); ?>

<?php
// Helper function to extract count from stats - try multiple paths
if (!function_exists('getAdminStatCount')) {
    function getAdminStatCount($stats, $keys) {
        foreach ((array)$keys as $key) {
            // Check direct key
            if (isset($stats[$key])) {
                $value = $stats[$key];
                if (is_array($value)) {
                    return $value['count'] ?? $value['total'] ?? count($value);
                }
                if (is_numeric($value)) {
                    return (int)$value;
                }
            }
            // Check nested in 'counts' object
            if (isset($stats['counts'][$key])) {
                return (int)$stats['counts'][$key];
            }
            // Check nested in 'pending' object
            if (isset($stats['pending'][$key])) {
                return (int)$stats['pending'][$key];
            }
        }
        return 0;
    }
}

// Extract main counts
$lostItemsCount = getAdminStatCount($stats, ['lost_items', 'lostItems', 'totalLostItems', 'total_lost_items']);
$foundItemsCount = getAdminStatCount($stats, ['found_items', 'foundItems', 'totalFoundItems', 'total_found_items']);
$claimsCount = getAdminStatCount($stats, ['claims', 'totalClaims', 'total_claims']);
$usersCount = getAdminStatCount($stats, ['users', 'totalUsers', 'total_users']);

// Extract pending counts - try multiple possible locations
$pendingLostCount = getAdminStatCount($stats, ['pending_lost', 'pendingLost', 'pendingLostItems', 'pending_lost_items']);
$pendingFoundCount = getAdminStatCount($stats, ['pending_found', 'pendingFound', 'pendingFoundItems', 'pending_found_items']);
$pendingClaimsCount = getAdminStatCount($stats, ['pending_claims', 'pendingClaims', 'pending_claims_count']);

// Also check if pending counts are in items array structure
if ($pendingLostCount === 0 && isset($stats['pending_lost_items']) && is_array($stats['pending_lost_items'])) {
    $pendingLostCount = count($stats['pending_lost_items']);
}
if ($pendingFoundCount === 0 && isset($stats['pending_found_items']) && is_array($stats['pending_found_items'])) {
    $pendingFoundCount = count($stats['pending_found_items']);
}
// Check for pending_claims_count added by controller
if ($pendingClaimsCount === 0 && isset($stats['pending_claims_count'])) {
    $pendingClaimsCount = (int)$stats['pending_claims_count'];
}
?>

<div class="dashboard-wrapper">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>

    <main class="dashboard-main">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <div>
                <h4 class="fw-semibold mb-1">Admin Dashboard</h4>
                <p class="text-muted mb-0 small">Overview of system activity</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="<?= APP_URL ?>/notifications" class="btn btn-outline-secondary btn-sm position-relative" title="Notifications">
                    <i class="bi bi-bell"></i>
                </a>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleDarkMode()" title="Toggle Dark Mode">
                    <i class="bi bi-moon" id="headerThemeIcon"></i>
                </button>
            </div>
        </div>

        <?php displayFlash(); ?>

        <!-- Quick Stats Row -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <a href="<?= APP_URL ?>/admin/lost-items" class="card border-0 shadow-sm h-100 text-decoration-none stat-card-hover">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>
                            <div class="ms-3">
                                <h3 class="mb-0 fw-bold"><?= $lostItemsCount ?></h3>
                                <small class="text-muted">Lost Items</small>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-lg-3">
                <a href="<?= APP_URL ?>/admin/found-items" class="card border-0 shadow-sm h-100 text-decoration-none stat-card-hover">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-success bg-opacity-10 text-success">
                                <i class="bi bi-box-seam"></i>
                            </div>
                            <div class="ms-3">
                                <h3 class="mb-0 fw-bold"><?= $foundItemsCount ?></h3>
                                <small class="text-muted">Found Items</small>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                                <i class="bi bi-hand-index"></i>
                            </div>
                            <div class="ms-3">
                                <h3 class="mb-0 fw-bold"><?= $claimsCount ?></h3>
                                <small class="text-muted">Total Claims</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <a href="<?= APP_URL ?>/admin/users" class="card border-0 shadow-sm h-100 text-decoration-none stat-card-hover">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-info bg-opacity-10 text-info">
                                <i class="bi bi-people"></i>
                            </div>
                            <div class="ms-3">
                                <h3 class="mb-0 fw-bold"><?= $usersCount ?></h3>
                                <small class="text-muted">Total Users</small>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Pending Review Section -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-semibold mb-0">
                    <i class="bi bi-hourglass-split text-warning me-2"></i>Pending Review
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card border h-100 pending-card">
                            <div class="card-body text-center py-4">
                                <div class="stat-icon bg-warning bg-opacity-10 text-warning mx-auto mb-3">
                                    <i class="bi bi-exclamation-triangle"></i>
                                </div>
                                <h3 class="fw-bold mb-1"><?= $pendingLostCount ?></h3>
                                <p class="text-muted mb-3 small">Pending Lost Items</p>
                                <a href="<?= APP_URL ?>/admin/pending?type=lost" class="btn btn-sm btn-outline-warning">
                                    Review <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border h-100 pending-card">
                            <div class="card-body text-center py-4">
                                <div class="stat-icon bg-info bg-opacity-10 text-info mx-auto mb-3">
                                    <i class="bi bi-box-seam"></i>
                                </div>
                                <h3 class="fw-bold mb-1"><?= $pendingFoundCount ?></h3>
                                <p class="text-muted mb-3 small">Pending Found Items</p>
                                <a href="<?= APP_URL ?>/admin/pending?type=found" class="btn btn-sm btn-outline-info">
                                    Review <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border h-100 pending-card">
                            <div class="card-body text-center py-4">
                                <div class="stat-icon bg-primary bg-opacity-10 text-primary mx-auto mb-3">
                                    <i class="bi bi-clock-history"></i>
                                </div>
                                <h3 class="fw-bold mb-1"><?= $pendingClaimsCount ?></h3>
                                <p class="text-muted mb-3 small">Pending Claims</p>
                                <a href="<?= APP_URL ?>/claims?status=pending" class="btn btn-sm btn-outline-primary">
                                    Review <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-semibold mb-0">
                    <i class="bi bi-lightning text-warning me-2"></i>Quick Actions
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <a href="<?= APP_URL ?>/admin/users" class="card border h-100 text-decoration-none action-card">
                            <div class="card-body text-center py-4">
                                <div class="stat-icon bg-primary bg-opacity-10 text-primary mx-auto mb-2">
                                    <i class="bi bi-people"></i>
                                </div>
                                <span class="small text-dark fw-medium">Manage Users</span>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="<?= APP_URL ?>/admin/categories" class="card border h-100 text-decoration-none action-card">
                            <div class="card-body text-center py-4">
                                <div class="stat-icon bg-success bg-opacity-10 text-success mx-auto mb-2">
                                    <i class="bi bi-tags"></i>
                                </div>
                                <span class="small text-dark fw-medium">Categories</span>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="<?= APP_URL ?>/admin/locations" class="card border h-100 text-decoration-none action-card">
                            <div class="card-body text-center py-4">
                                <div class="stat-icon bg-info bg-opacity-10 text-info mx-auto mb-2">
                                    <i class="bi bi-geo-alt"></i>
                                </div>
                                <span class="small text-dark fw-medium">Locations</span>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="<?= APP_URL ?>/admin/reports" class="card border h-100 text-decoration-none action-card">
                            <div class="card-body text-center py-4">
                                <div class="stat-icon bg-warning bg-opacity-10 text-warning mx-auto mb-2">
                                    <i class="bi bi-graph-up"></i>
                                </div>
                                <span class="small text-dark fw-medium">Reports</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<style>
.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}
.stat-card-hover:hover {
    transform: translateY(-2px);
    transition: transform 0.2s;
}
.stat-card-hover .card-body {
    transition: background 0.2s;
}
.pending-card {
    transition: all 0.2s;
}
.pending-card:hover {
    border-color: #dee2e6 !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
.action-card {
    transition: all 0.2s;
}
.action-card:hover {
    transform: translateY(-2px);
    border-color: #dee2e6 !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
</style>

<?php include __DIR__ . '/../layouts/footer-dashboard.php'; ?>
