<?php $pageTitle = 'Notifications - ' . APP_NAME; ?>
<?php include __DIR__ . '/../layouts/header-dashboard.php'; ?>
<?php $user = getCurrentUser(); ?>

<div class="dashboard-wrapper">
    <?php include __DIR__ . '/../admin/partials/sidebar.php'; ?>

    <main class="dashboard-main">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <div>
                <h4 class="fw-semibold mb-1"><i class="bi bi-bell me-2"></i>Notifications</h4>
                <p class="text-muted mb-0 small">Stay updated on your items and claims</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <?php if (!empty($notifications)): ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <form action="<?= APP_URL ?>/notifications/mark-all-read" method="POST" class="d-inline">
                                    <button type="submit" class="dropdown-item"><i class="bi bi-check-all me-2"></i>Mark all
                                        as read</button>
                                </form>
                            </li>
                            <li>
                                <form action="<?= APP_URL ?>/notifications/clear-read" method="POST" class="d-inline">
                                    <button type="submit" class="dropdown-item text-danger"><i
                                            class="bi bi-trash me-2"></i>Clear read</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                <?php endif; ?>
                <button type="button" class="btn ui-btn-secondary btn-sm" onclick="toggleDarkMode()"
                    data-theme-toggle="true" title="Toggle Dark Mode">
                    <i class="bi bi-moon header-theme-icon" id="headerThemeIcon"></i>
                </button>
            </div>
        </div>

        <?php displayFlash(); ?>

        <!-- Notifications List -->
        <?php if (!empty($notifications)): ?>
            <div class="list-group shadow-sm">
                <?php foreach ($notifications as $notification): ?>
                    <?php $isRead = $notification['is_read'] ?? $notification['read'] ?? false; ?>
                    <div class="list-group-item list-group-item-action <?= !$isRead ? 'bg-light' : '' ?>">
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                <?php
                                $iconClass = 'bi-bell';
                                $iconColor = 'text-primary';
                                switch ($notification['type'] ?? '') {
                                    case 'match':
                                        $iconClass = 'bi-link-45deg';
                                        $iconColor = 'text-info';
                                        break;
                                    case 'claim':
                                        $iconClass = 'bi-hand-index';
                                        $iconColor = 'text-warning';
                                        break;
                                    case 'claim_approved':
                                        $iconClass = 'bi-check-circle';
                                        $iconColor = 'text-success';
                                        break;
                                    case 'claim_rejected':
                                        $iconClass = 'bi-x-circle';
                                        $iconColor = 'text-danger';
                                        break;
                                    case 'item_found':
                                        $iconClass = 'bi-box-seam';
                                        $iconColor = 'text-success';
                                        break;
                                }
                                ?>
                                <i class="bi <?= $iconClass ?> <?= $iconColor ?> fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1 <?= !$isRead ? 'fw-bold' : '' ?>">
                                            <?= htmlspecialchars($notification['title'] ?? '') ?>
                                        </h6>
                                        <p class="mb-1 text-muted small"><?= htmlspecialchars($notification['message'] ?? '') ?>
                                        </p>
                                        <small class="text-muted"><i
                                                class="bi bi-clock me-1"></i><?= formatDate($notification['created_at'] ?? '') ?></small>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <?php if (!$isRead): ?>
                                            <span class="badge bg-primary">New</span>
                                        <?php endif; ?>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-link text-muted p-0" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <?php if (!$isRead): ?>
                                                    <li>
                                                        <form action="<?= APP_URL ?>/notifications/<?= $notification['id'] ?>/read"
                                                            method="POST">
                                                            <button type="submit" class="dropdown-item"><i
                                                                    class="bi bi-check me-2"></i>Mark as read</button>
                                                        </form>
                                                    </li>
                                                <?php endif; ?>
                                                <li>
                                                    <form
                                                        action="<?= APP_URL ?>/notifications/<?= $notification['id'] ?>/delete"
                                                        method="POST">
                                                        <button type="submit" class="dropdown-item text-danger"><i
                                                                class="bi bi-trash me-2"></i>Delete</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <?php if (!empty($notification['action_url'])): ?>
                                    <a href="<?= htmlspecialchars($notification['action_url']) ?>"
                                        class="btn btn-sm btn-outline-primary mt-2">
                                        View Details <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if (($pagination['totalPages'] ?? 1) > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $pagination['totalPages']; $i++): ?>
                            <li class="page-item <?= $i == ($pagination['currentPage'] ?? 1) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>

        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-bell-slash text-muted display-4"></i>
                <h5 class="mt-3">No Notifications</h5>
                <p class="text-muted">You're all caught up! New notifications will appear here.</p>
            </div>
        <?php endif; ?>
    </main>
</div>

<?php include __DIR__ . '/../layouts/footer-dashboard.php'; ?>