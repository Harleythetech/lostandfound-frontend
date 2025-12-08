<?php $pageTitle = 'Pending Review - ' . APP_NAME; ?>
<?php include __DIR__ . '/../layouts/header-dashboard.php'; ?>
<?php $user = getCurrentUser(); ?>

<div class="dashboard-wrapper">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>

    <main class="dashboard-main">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="fw-bold mb-0">Pending Review</h5>
                <small class="text-muted">Items awaiting approval</small>
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

        <!-- Filter Tabs -->
        <ul class="nav nav-pills mb-4">
            <li class="nav-item">
                <a class="nav-link <?= ($type ?? 'all') === 'all' ? 'active' : '' ?>"
                    href="<?= APP_URL ?>/admin/pending?type=all">All</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($type ?? '') === 'lost' ? 'active' : '' ?>"
                    href="<?= APP_URL ?>/admin/pending?type=lost">Lost Items</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($type ?? '') === 'found' ? 'active' : '' ?>"
                    href="<?= APP_URL ?>/admin/pending?type=found">Found Items</a>
            </li>
        </ul>

        <?php if (empty($pending)): ?>
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">All Caught Up!</h5>
                    <p class="text-muted mb-0">No pending items to review.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($pending as $item): ?>
                    <?php
                    // Extract item data - using field names from API response
                    $itemId = $item['id'] ?? null;
                    $itemType = $item['type'] ?? 'lost';
                    $itemTitle = $item['title'] ?? 'Unknown';
                    $itemDesc = $item['description'] ?? '';
                    $itemDate = $item['created_at'] ?? '';
                    $userName = trim(($item['first_name'] ?? '') . ' ' . ($item['last_name'] ?? ''));
                    $schoolId = $item['school_id'] ?? '';
                    $categoryName = $item['category_name'] ?? '';
                    $locationName = $item['location_name'] ?? '';

                    // Skip if no valid ID
                    if (!$itemId)
                        continue;
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                                <span class="badge bg-<?= $itemType === 'lost' ? 'danger' : 'success' ?>">
                                    <?= ucfirst($itemType) ?>
                                </span>
                                <small class="text-muted"><?= formatDate($itemDate, 'M j, Y') ?></small>
                            </div>
                            <div class="card-body">
                                <h6 class="card-title fw-bold"><?= htmlspecialchars($itemTitle) ?></h6>
                                <p class="text-muted small mb-2"><?= htmlspecialchars(truncate($itemDesc, 80)) ?></p>
                                <?php if (!empty($categoryName)): ?>
                                    <p class="small mb-1">
                                        <i class="bi bi-tag me-1"></i><?= htmlspecialchars($categoryName) ?>
                                    </p>
                                <?php endif; ?>
                                <?php if (!empty($locationName)): ?>
                                    <p class="small mb-1">
                                        <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($locationName) ?>
                                    </p>
                                <?php endif; ?>
                                <?php if (!empty($userName)): ?>
                                    <p class="small mb-0">
                                        <i class="bi bi-person me-1"></i>
                                        <?= htmlspecialchars($userName) ?>
                                        <?php if (!empty($schoolId)): ?>
                                            <span class="text-muted">(<?= htmlspecialchars($schoolId) ?>)</span>
                                        <?php endif; ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer bg-transparent">
                                <div class="d-flex gap-2">
                                    <form action="<?= APP_URL ?>/admin/<?= $itemType ?>-items/<?= $itemId ?>/review"
                                        method="POST" class="flex-fill">
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="btn btn-success btn-sm w-100">
                                            <i class="bi bi-check-lg"></i> Approve
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-danger btn-sm flex-fill" data-bs-toggle="modal"
                                        data-bs-target="#rejectModal<?= $itemId ?>">
                                        <i class="bi bi-x-lg"></i> Reject
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reject Modal -->
                    <div class="modal fade" id="rejectModal<?= $itemId ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="<?= APP_URL ?>/admin/<?= $itemType ?>-items/<?= $itemId ?>/review" method="POST">
                                    <input type="hidden" name="action" value="reject">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Reject Item</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Reason for Rejection</label>
                                            <textarea class="form-control" name="rejection_reason" rows="3" required
                                                minlength="30"
                                                placeholder="Please provide a detailed reason (minimum 30 characters)..."></textarea>
                                            <div class="form-text">Minimum 30 characters required.</div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-danger">Reject</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<?php include __DIR__ . '/../layouts/footer-dashboard.php'; ?>