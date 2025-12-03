<?php $pageTitle = 'User Management - ' . APP_NAME; ?>
<?php include __DIR__ . '/../../layouts/header-dashboard.php'; ?>
<?php $currentUser = getCurrentUser(); ?>

<div class="dashboard-wrapper">
    <?php include __DIR__ . '/../partials/sidebar.php'; ?>

    <main class="dashboard-main">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="fw-bold mb-0">User Management</h5>
                <small class="text-muted">Manage all registered users</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="<?= APP_URL ?>/notifications" class="btn btn-outline-secondary btn-sm position-relative">
                    <i class="bi bi-bell"></i>
                </a>
                <button class="btn btn-outline-secondary btn-sm" id="darkModeToggle">
                    <i class="bi bi-moon"></i>
                </button>
            </div>
        </div>

        <?php displayFlash(); ?>

        <!-- Filters -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All Statuses</option>
                            <option value="pending" <?= ($status ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="active" <?= ($status ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="suspended" <?= ($status ?? '') === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                            <option value="declined" <?= ($status ?? '') === 'declined' ? 'selected' : '' ?>>Declined</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Role</label>
                        <select name="role" class="form-select form-select-sm">
                            <option value="">All Roles</option>
                            <option value="user" <?= ($role ?? '') === 'user' ? 'selected' : '' ?>>User</option>
                            <option value="security" <?= ($role ?? '') === 'security' ? 'selected' : '' ?>>Security</option>
                            <option value="admin" <?= ($role ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small mb-1">Search</label>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search name or school ID..." value="<?= htmlspecialchars($search ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-funnel me-1"></i>Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <?php 
        // Sort users by ID
        usort($users, fn($a, $b) => ($a['id'] ?? 0) - ($b['id'] ?? 0));
        ?>

        <!-- Users Table -->
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>School ID</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No users found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td class="small">#<?= $u['id'] ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                                <?= strtoupper(substr($u['first_name'] ?? 'U', 0, 1)) ?>
                                            </div>
                                            <div>
                                                <div class="fw-semibold small"><?= htmlspecialchars(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? '')) ?></div>
                                                <small class="text-muted"><?= htmlspecialchars($u['email'] ?? '') ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="small"><?= htmlspecialchars($u['school_id'] ?? '') ?></td>
                                    <td>
                                        <span class="badge bg-<?= ($u['role'] ?? 'user') === 'admin' ? 'danger' : (($u['role'] ?? 'user') === 'security' ? 'warning' : 'secondary') ?>">
                                            <?= ucfirst($u['role'] ?? 'user') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                        $statusClass = match($u['status'] ?? 'pending') {
                                            'active' => 'bg-success',
                                            'pending' => 'bg-warning text-dark',
                                            'suspended' => 'bg-danger',
                                            'declined' => 'bg-secondary',
                                            default => 'bg-secondary'
                                        };
                                        ?>
                                        <span class="badge <?= $statusClass ?>">
                                            <?= ucfirst($u['status'] ?? 'pending') ?>
                                        </span>
                                    </td>
                                    <td class="small"><?= formatDate($u['created_at'] ?? '', 'M j, Y') ?></td>
                                    <td>
                                        <a href="<?= APP_URL ?>/admin/users/<?= $u['id'] ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <?php if (!empty($pagination) && ($pagination['totalPages'] ?? 1) > 1): ?>
            <nav class="mt-4">
                <ul class="pagination pagination-sm justify-content-center">
                    <?php for ($i = 1; $i <= $pagination['totalPages']; $i++): ?>
                        <li class="page-item <?= $i == ($pagination['currentPage'] ?? 1) ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&status=<?= $status ?? '' ?>&role=<?= $role ?? '' ?>&search=<?= urlencode($search ?? '') ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </main>
</div>

<?php include __DIR__ . '/../../layouts/footer-dashboard.php'; ?>
