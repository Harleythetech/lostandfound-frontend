<?php $pageTitle = 'View User - ' . APP_NAME; ?>
<?php include __DIR__ . '/../../layouts/header-dashboard.php'; ?>
<?php $currentUser = getCurrentUser(); ?>

<div class="dashboard-wrapper">
    <?php include __DIR__ . '/../partials/sidebar.php'; ?>

    <main class="dashboard-main">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item"><a href="<?= APP_URL ?>/admin/users"
                                class="text-decoration-none">Users</a></li>
                        <li class="breadcrumb-item active">
                            <?= htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?>
                        </li>
                    </ol>
                </nav>
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

        <div class="row g-4">
            <div class="col-lg-4">
                <!-- User Info Card -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body text-center py-4">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                            style="width: 64px; height: 64px; font-size: 1.5rem;">
                            <?= strtoupper(substr($user['first_name'] ?? 'U', 0, 1)) ?>
                        </div>
                        <h6 class="fw-bold mb-1">
                            <?= htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?>
                        </h6>
                        <p class="text-muted small mb-2"><?= htmlspecialchars($user['school_id'] ?? '') ?></p>
                        <div>
                            <span
                                class="badge bg-<?= ($user['role'] ?? 'user') === 'admin' ? 'danger' : 'secondary' ?>">
                                <?= ucfirst($user['role'] ?? 'user') ?>
                            </span>
                            <?php
                            $statusClass = match ($user['status'] ?? 'pending') {
                                'active' => 'bg-success',
                                'pending' => 'bg-warning text-dark',
                                'suspended' => 'bg-danger',
                                'declined' => 'bg-secondary',
                                default => 'bg-secondary'
                            };
                            ?>
                            <span class="badge <?= $statusClass ?>">
                                <?= ucfirst($user['status'] ?? 'pending') ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body border-top small">
                        <p class="mb-2"><i
                                class="bi bi-envelope me-2 text-muted"></i><?= htmlspecialchars($user['email'] ?? 'N/A') ?>
                        </p>
                        <p class="mb-2"><i
                                class="bi bi-phone me-2 text-muted"></i><?= htmlspecialchars($user['contact_number'] ?? 'N/A') ?>
                        </p>
                        <p class="mb-0"><i class="bi bi-calendar me-2 text-muted"></i>Joined
                            <?= formatDate($user['created_at'] ?? '') ?>
                        </p>
                    </div>
                </div>

                <!-- Role Management -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-transparent border-0 pb-0">
                        <h6 class="fw-bold mb-0"><i class="bi bi-person-gear me-2"></i>Change Role</h6>
                    </div>
                    <div class="card-body">
                        <form action="<?= APP_URL ?>/admin/users/<?= $user['id'] ?>/role" method="POST">
                            <div class="mb-3">
                                <select name="role" class="form-select form-select-sm">
                                    <option value="user" <?= ($user['role'] ?? 'user') === 'user' ? 'selected' : '' ?>>User
                                    </option>
                                    <option value="admin" <?= ($user['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin
                                    </option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm w-100">Update Role</button>
                        </form>
                    </div>
                </div>

                <!-- Account Actions -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 pb-0">
                        <h6 class="fw-bold mb-0"><i class="bi bi-gear me-2"></i>Account Actions</h6>
                    </div>
                    <div class="card-body">
                        <?php $status = $user['status'] ?? 'pending'; ?>

                        <?php if ($status === 'pending'): ?>
                            <form action="<?= APP_URL ?>/admin/users/<?= $user['id'] ?>/manage" method="POST" class="mb-2">
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" class="btn btn-success btn-sm w-100">
                                    <i class="bi bi-check-circle me-1"></i>Approve User
                                </button>
                            </form>
                            <button type="button" class="btn btn-danger btn-sm w-100" data-bs-toggle="modal"
                                data-bs-target="#declineModal">
                                <i class="bi bi-x-circle me-1"></i>Decline User
                            </button>
                        <?php elseif ($status === 'active'): ?>
                            <button type="button" class="btn btn-warning btn-sm w-100" data-bs-toggle="modal"
                                data-bs-target="#suspendModal">
                                <i class="bi bi-pause-circle me-1"></i>Suspend User
                            </button>
                        <?php elseif ($status === 'suspended'): ?>
                            <form action="<?= APP_URL ?>/admin/users/<?= $user['id'] ?>/manage" method="POST">
                                <input type="hidden" name="action" value="unsuspend">
                                <button type="submit" class="btn btn-success btn-sm w-100">
                                    <i class="bi bi-play-circle me-1"></i>Unsuspend User
                                </button>
                            </form>
                            <?php if (!empty($user['suspension_reason'])): ?>
                                <div class="alert alert-warning mt-3 mb-0 small">
                                    <strong>Suspension Reason:</strong><br><?= htmlspecialchars($user['suspension_reason']) ?>
                                    <?php if (!empty($user['suspension_end_date'])): ?>
                                        <br><strong>Until:</strong> <?= formatDate($user['suspension_end_date'], 'M d, Y H:i') ?>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php elseif ($status === 'declined'): ?>
                            <form action="<?= APP_URL ?>/admin/users/<?= $user['id'] ?>/manage" method="POST">
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" class="btn btn-success btn-sm w-100">
                                    <i class="bi bi-check-circle me-1"></i>Approve User
                                </button>
                            </form>
                            <?php if (!empty($user['decline_reason'])): ?>
                                <div class="alert alert-danger mt-3 mb-0 small">
                                    <strong>Decline Reason:</strong><br><?= htmlspecialchars($user['decline_reason']) ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <!-- User Statistics -->
                <div class="row g-3 mb-4">
                    <div class="col-4">
                        <div class="card border-0 shadow-sm bg-danger text-white h-100">
                            <div class="card-body text-center py-3">
                                <h4 class="mb-0"><?= $user['lost_items_count'] ?? 0 ?></h4>
                                <small>Lost Items</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card border-0 shadow-sm bg-success text-white h-100">
                            <div class="card-body text-center py-3">
                                <h4 class="mb-0"><?= $user['found_items_count'] ?? 0 ?></h4>
                                <small>Found Items</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card border-0 shadow-sm bg-primary text-white h-100">
                            <div class="card-body text-center py-3">
                                <h4 class="mb-0"><?= $user['claims_count'] ?? 0 ?></h4>
                                <small>Claims</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 pb-0">
                        <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"></i>Recent Activity</h6>
                    </div>
                    <div class="card-body">
                        <?php if (empty($user['recent_activity'])): ?>
                            <p class="text-muted text-center py-4 mb-0">No recent activity</p>
                        <?php else: ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($user['recent_activity'] ?? [] as $activity): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span class="small"><?= htmlspecialchars($activity['description'] ?? '') ?></span>
                                        <small class="text-muted"><?= formatDate($activity['created_at'] ?? '') ?></small>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Suspend Modal -->
<div class="modal fade" id="suspendModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= APP_URL ?>/admin/users/<?= $user['id'] ?>/manage" method="POST">
                <input type="hidden" name="action" value="suspend">
                <div class="modal-header">
                    <h5 class="modal-title">Suspend User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Reason for Suspension <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="reason" rows="3" required
                            placeholder="Explain why this user is being suspended..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Duration (days)</label>
                        <input type="number" class="form-control" name="duration_days" min="1"
                            placeholder="Leave empty for permanent suspension">
                        <small class="text-muted">User will be automatically unsuspended after this period</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Suspend User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Decline Modal -->
<div class="modal fade" id="declineModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= APP_URL ?>/admin/users/<?= $user['id'] ?>/manage" method="POST">
                <input type="hidden" name="action" value="decline">
                <div class="modal-header">
                    <h5 class="modal-title">Decline User Registration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        This will reject the user's registration request.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason for Declining <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="reason" rows="3" required
                            placeholder="Explain why this registration is being declined..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Decline Registration</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../layouts/footer-dashboard.php'; ?>