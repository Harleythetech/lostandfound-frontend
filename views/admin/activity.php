<?php $pageTitle = 'Activity Logs - ' . APP_NAME; ?>
<?php include __DIR__ . '/../layouts/header-dashboard.php'; ?>
<?php $user = getCurrentUser(); ?>

<div class="dashboard-wrapper">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>

    <main class="dashboard-main">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="fw-bold mb-0">Activity Logs</h5>
                <small class="text-muted">System activity history</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="<?= APP_URL ?>/admin/notifications" class="btn ui-btn-secondary btn-sm position-relative"
                    title="Notifications">
                    <i class="bi bi-bell"></i>
                    <?php if (getUnreadNotificationCount() > 0): ?><span class="notification-dot"></span><?php endif; ?>
                </a>
                <button type="button" class="btn ui-btn-secondary btn-sm" onclick="toggleDarkMode()"
                    data-theme-toggle="true" title="Toggle Dark Mode">
                    <i class="bi bi-moon header-theme-icon" id="headerThemeIcon"></i>
                </button>
            </div>
        </div>

        <?php displayFlash(); ?>

        <!-- Filters -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small mb-1">Action Type</label>
                        <select name="action" class="form-select form-select-sm">
                            <option value="">All Actions</option>
                            <option value="login" <?= ($action ?? '') === 'login' ? 'selected' : '' ?>>Login</option>
                            <option value="logout" <?= ($action ?? '') === 'logout' ? 'selected' : '' ?>>Logout</option>
                            <option value="register" <?= ($action ?? '') === 'register' ? 'selected' : '' ?>>Register
                            </option>
                            <option value="approve_user" <?= ($action ?? '') === 'approve_user' ? 'selected' : '' ?>>
                                Approve User</option>
                            <option value="decline_user" <?= ($action ?? '') === 'decline_user' ? 'selected' : '' ?>>
                                Decline User</option>
                            <option value="suspend_user" <?= ($action ?? '') === 'suspend_user' ? 'selected' : '' ?>>
                                Suspend User</option>
                            <option value="unsuspend_user" <?= ($action ?? '') === 'unsuspend_user' ? 'selected' : '' ?>>
                                Unsuspend User</option>
                            <option value="create" <?= ($action ?? '') === 'create' ? 'selected' : '' ?>>Create</option>
                            <option value="create_found_item" <?= ($action ?? '') === 'create_found_item' ? 'selected' : '' ?>>Create Found Item</option>
                            <option value="create_lost_item" <?= ($action ?? '') === 'create_lost_item' ? 'selected' : '' ?>>Create Lost Item</option>
                            <option value="update" <?= ($action ?? '') === 'update' ? 'selected' : '' ?>>Update</option>
                            <option value="update_found_item" <?= ($action ?? '') === 'update_found_item' ? 'selected' : '' ?>>Update Found Item</option>
                            <option value="update_lost_item" <?= ($action ?? '') === 'update_lost_item' ? 'selected' : '' ?>>Update Lost Item</option>
                            <option value="approve" <?= ($action ?? '') === 'approve' ? 'selected' : '' ?>>Approve</option>
                            <option value="review_found_item" <?= ($action ?? '') === 'review_found_item' ? 'selected' : '' ?>>Review Found Item</option>
                            <option value="pickup" <?= ($action ?? '') === 'pickup' ? 'selected' : '' ?>>Pickup</option>
                            <option value="delete" <?= ($action ?? '') === 'delete' ? 'selected' : '' ?>>Delete</option>
                            <option value="delete_found_item" <?= ($action ?? '') === 'delete_found_item' ? 'selected' : '' ?>>Delete Found Item</option>
                            <option value="delete_lost_item" <?= ($action ?? '') === 'delete_lost_item' ? 'selected' : '' ?>>Delete Lost Item</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small mb-1">Show Entries</label>
                        <select name="limit" class="form-select form-select-sm">
                            <option value="50" <?= ($limit ?? 50) == 50 ? 'selected' : '' ?>>50 entries</option>
                            <option value="100" <?= ($limit ?? 50) == 100 ? 'selected' : '' ?>>100 entries</option>
                            <option value="200" <?= ($limit ?? 50) == 200 ? 'selected' : '' ?>>200 entries</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-funnel me-1"></i>Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <?php
        // Sort logs by ID (newest first)
        $logs = $logs ?? [];
        usort($logs, fn($a, $b) => ($b['id'] ?? 0) - ($a['id'] ?? 0));
        ?>

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Timestamp</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Details</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No activity logs found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td class="small"><?= formatDate($log['created_at'] ?? '', 'M j, Y H:i:s') ?></td>
                                    <td>
                                        <?php if (!empty($log['user'])): ?>
                                            <?php
                                            $lu = $log['user'];
                                            $logUserDisplay = $lu['name'] ?? trim(($lu['first_name'] ?? '') . ' ' . ($lu['last_name'] ?? ''));
                                            ?>
                                            <?= sanitizeForDisplay($logUserDisplay) ?>
                                            <br><small class="text-muted"><?= htmlspecialchars($lu['school_id'] ?? '') ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">System</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= getActionBadgeColor($log['action'] ?? '') ?>">
                                            <?= htmlspecialchars($log['action'] ?? 'Unknown') ?>
                                        </span>
                                    </td>
                                    <td class="small"><?= htmlspecialchars($log['description'] ?? $log['details'] ?? '') ?></td>
                                    <td class="small text-muted"><?= htmlspecialchars($log['ip_address'] ?? 'N/A') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php
function getActionBadgeColor($action)
{
    $colors = [
        'login' => 'success',
        'logout' => 'secondary',
        'create' => 'primary',
        'update' => 'info',
        'delete' => 'danger',
        'approve' => 'success',
        'reject' => 'warning'
    ];
    // treat unsuspend_user action as success
    $colors['unsuspend_user'] = 'success';
    return $colors[$action] ?? 'secondary';
}
?>

<?php include __DIR__ . '/../layouts/footer-dashboard.php'; ?>