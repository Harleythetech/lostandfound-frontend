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
                            <option value="create" <?= ($action ?? '') === 'create' ? 'selected' : '' ?>>Create</option>
                            <option value="update" <?= ($action ?? '') === 'update' ? 'selected' : '' ?>>Update</option>
                            <option value="delete" <?= ($action ?? '') === 'delete' ? 'selected' : '' ?>>Delete</option>
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
                                            <?= htmlspecialchars(($log['user']['first_name'] ?? '') . ' ' . ($log['user']['last_name'] ?? '')) ?>
                                            <br><small class="text-muted"><?= $log['user']['school_id'] ?? '' ?></small>
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
    return $colors[$action] ?? 'secondary';
}
?>

<?php include __DIR__ . '/../layouts/footer-dashboard.php'; ?>