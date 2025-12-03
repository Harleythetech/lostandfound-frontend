<?php $pageTitle = 'My Claims - ' . APP_NAME; ?>
<?php include __DIR__ . '/../layouts/header-dashboard.php'; ?>
<?php $user = getCurrentUser(); ?>

<div class="dashboard-wrapper">
    <?php include __DIR__ . '/../dashboard/partials/sidebar.php'; ?>
    
    <main class="dashboard-main">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <div>
                <h4 class="fw-semibold mb-1"><i class="bi bi-hand-index me-2"></i>My Claims</h4>
                <p class="text-muted mb-0 small">Track your claims on found items</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="<?= APP_URL ?>/notifications" class="btn btn-outline-secondary btn-sm position-relative">
                    <i class="bi bi-bell"></i>
                    <?php if (getUnreadNotificationCount() > 0): ?><span class="notification-dot"></span><?php endif; ?>
                </a>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleDarkMode()"><i class="bi bi-moon"></i></button>
            </div>
        </div>

        <?php displayFlash(); ?>

        <!-- Claims Status Tabs -->
        <ul class="nav nav-pills mb-4 gap-2">
            <li class="nav-item">
                <a class="nav-link <?= !isset($_GET['status']) || $_GET['status'] === '' ? 'active' : '' ?>" href="<?= APP_URL ?>/claims">All</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($_GET['status'] ?? '') === 'pending' ? 'active' : '' ?>" href="<?= APP_URL ?>/claims?status=pending">Pending</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($_GET['status'] ?? '') === 'verified' ? 'active' : '' ?>" href="<?= APP_URL ?>/claims?status=verified">Verified</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($_GET['status'] ?? '') === 'scheduled' ? 'active' : '' ?>" href="<?= APP_URL ?>/claims?status=scheduled">Scheduled</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($_GET['status'] ?? '') === 'completed' ? 'active' : '' ?>" href="<?= APP_URL ?>/claims?status=completed">Completed</a>
            </li>
        </ul>

        <!-- Claims List -->
        <?php if (!empty($claims)): ?>
        <div class="row g-3">
            <?php foreach ($claims as $claim): ?>
            <div class="col-md-6">
                <div class="card border h-100">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
                        <span class="badge <?= getStatusBadgeClass($claim['status']) ?>"><?= ucfirst($claim['status']) ?></span>
                        <small class="text-muted">Claim #<?= $claim['id'] ?></small>
                    </div>
                    <div class="card-body p-3">
                        <div class="d-flex mb-3">
                            <?php if (!empty($claim['found_item']['images'][0])): ?>
                            <img src="<?= API_BASE_URL ?>/uploads/<?= $claim['found_item']['images'][0] ?>" class="rounded me-3" style="width: 70px; height: 70px; object-fit: cover;">
                            <?php else: ?>
                            <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                                <i class="bi bi-image text-muted"></i>
                            </div>
                            <?php endif; ?>
                            <div>
                                <h6 class="mb-1"><?= htmlspecialchars($claim['found_item']['title'] ?? $claim['found_item']['item_name'] ?? 'Unknown Item') ?></h6>
                                <p class="text-muted small mb-0"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($claim['found_item']['location']['name'] ?? 'Unknown') ?></p>
                                <p class="text-muted small mb-0"><i class="bi bi-calendar me-1"></i><?= formatDate($claim['created_at']) ?></p>
                            </div>
                        </div>

                        <?php if ($claim['status'] === 'scheduled' && !empty($claim['pickup_date'])): ?>
                        <div class="alert alert-info py-2 mb-0 small">
                            <i class="bi bi-calendar-check me-1"></i><strong>Pickup:</strong> <?= formatDate($claim['pickup_date']) ?>
                            <?php if (!empty($claim['pickup_location'])): ?>
                            <br><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($claim['pickup_location']) ?>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <?php if ($claim['status'] === 'rejected' && !empty($claim['rejection_reason'])): ?>
                        <div class="alert alert-danger py-2 mb-0 small">
                            <i class="bi bi-x-circle me-1"></i><?= htmlspecialchars($claim['rejection_reason']) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer bg-white border-0 pt-0">
                        <div class="d-flex gap-2">
                            <a href="<?= APP_URL ?>/found-items/<?= $claim['found_item_id'] ?>" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-eye me-1"></i>View Item
                            </a>
                            <?php if ($claim['status'] === 'pending'): ?>
                            <form action="<?= APP_URL ?>/claims/<?= $claim['id'] ?>/cancel" method="POST" onsubmit="return confirm('Cancel this claim?');">
                                <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-x-lg me-1"></i>Cancel</button>
                            </form>
                            <?php endif; ?>
                        </div>
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
                    <a class="page-link" href="?page=<?= $i ?><?= isset($_GET['status']) ? '&status=' . $_GET['status'] : '' ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>

        <?php else: ?>
        <div class="text-center py-5">
            <i class="bi bi-inbox text-muted display-4"></i>
            <h5 class="mt-3">No Claims Found</h5>
            <p class="text-muted">You haven't made any claims yet.</p>
            <a href="<?= APP_URL ?>/found-items" class="btn btn-success mt-2">
                <i class="bi bi-box-seam me-2"></i>Browse Found Items
            </a>
        </div>
        <?php endif; ?>
    </main>
</div>

<?php include __DIR__ . '/../layouts/footer-dashboard.php'; ?>
