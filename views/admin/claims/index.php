<?php $pageTitle = 'Claims Management - ' . APP_NAME; ?>
<?php include __DIR__ . '/../../layouts/header-dashboard.php'; ?>

<div class="dashboard-wrapper">
    <?php include __DIR__ . '/../partials/sidebar.php'; ?>

    <main class="dashboard-main">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="fw-bold mb-0">Claims Management</h5>
                <small class="text-muted">Review and process item claims</small>
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

        <!-- Status Tabs -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-2">
                <ul class="nav nav-pills nav-fill">
                    <li class="nav-item">
                        <a class="nav-link <?= ($status ?? 'all') === 'all' ? 'active' : '' ?>"
                            href="<?= APP_URL ?>/admin/claims?status=all">
                            <i class="bi bi-list me-1"></i>All
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($status ?? '') === 'pending' ? 'active' : '' ?>"
                            href="<?= APP_URL ?>/admin/claims?status=pending">
                            <i class="bi bi-hourglass-split me-1"></i>Pending
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($status ?? '') === 'approved' ? 'active' : '' ?>"
                            href="<?= APP_URL ?>/admin/claims?status=approved">
                            <i class="bi bi-check-circle me-1"></i>Approved
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($status ?? '') === 'completed' ? 'active' : '' ?>"
                            href="<?= APP_URL ?>/admin/claims?status=completed">
                            <i class="bi bi-check2-all me-1"></i>Completed
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($status ?? '') === 'rejected' ? 'active' : '' ?>"
                            href="<?= APP_URL ?>/admin/claims?status=rejected">
                            <i class="bi bi-x-circle me-1"></i>Rejected
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Claims Table -->
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="60">ID</th>
                            <th>Found Item</th>
                            <th>Claimant</th>
                            <th>Storage Location</th>
                            <th>Status</th>
                            <th>Pickup</th>
                            <th>Submitted</th>
                            <th width="100">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($claims)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                        <p class="mb-0">No claims found</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($claims as $claim): ?>
                                <?php
                                // Get image URL from item_images array or fallback to item_primary_image
                                $imageSrc = '';
                                $itemImages = $claim['item_images'] ?? [];
                                if (!empty($itemImages)) {
                                    // Find primary image or use first one
                                    $primaryImage = null;
                                    foreach ($itemImages as $img) {
                                        if (isset($img['is_primary']) && ($img['is_primary'] === true || $img['is_primary'] === 1 || $img['is_primary'] === '1')) {
                                            $primaryImage = $img;
                                            break;
                                        }
                                    }
                                    $primaryImage = $primaryImage ?? $itemImages[0] ?? null;
                                    $imgPath = $primaryImage ? ($primaryImage['url'] ?? $primaryImage['file_name'] ?? '') : '';
                                } else {
                                    $imgPath = $claim['item_primary_image'] ?? '';
                                }

                                if (!empty($imgPath)) {
                                    $imageSrc = normalizeImageUrl($imgPath);
                                }
                                ?>
                                <tr>
                                    <td class="small text-muted">#<?= $claim['id'] ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if ($imageSrc): ?>
                                                <img src="<?= htmlspecialchars($imageSrc) ?>" class="rounded me-2"
                                                    style="width: 40px; height: 40px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center"
                                                    style="width: 40px; height: 40px;">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <div class="fw-semibold small">
                                                    <?= htmlspecialchars($claim['item_title'] ?? 'Unknown Item') ?>
                                                </div>
                                                <small class="text-muted">ID:
                                                    #<?= $claim['item_id'] ?? $claim['found_item_id'] ?? 'N/A' ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small fw-medium">
                                            <?= htmlspecialchars($claim['claimant_name'] ?? (($claim['claimant_first_name'] ?? '') . ' ' . ($claim['claimant_last_name'] ?? ''))) ?>
                                        </div>
                                        <small class="text-muted"><?= $claim['claimant_school_id'] ?? 'N/A' ?></small>
                                    </td>
                                    <td>
                                        <small>
                                            <i class="bi bi-geo-alt text-primary me-1"></i>
                                            <?= htmlspecialchars($claim['storage_location'] ?? 'Not specified') ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php
                                        $statusClasses = [
                                            'pending' => 'bg-warning text-dark',
                                            'approved' => 'bg-success',
                                            'rejected' => 'bg-danger',
                                            'completed' => 'bg-primary',
                                            'cancelled' => 'bg-secondary'
                                        ];
                                        $statusClass = $statusClasses[$claim['status'] ?? 'pending'] ?? 'bg-secondary';
                                        ?>
                                        <span class="badge <?= $statusClass ?>">
                                            <?= ucfirst($claim['status'] ?? 'pending') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($claim['pickup_scheduled'])): ?>
                                            <small class="d-block">
                                                <i class="bi bi-calendar-check text-success me-1"></i>
                                                <span class="local-time"
                                                    data-datetime="<?= htmlspecialchars($claim['pickup_scheduled']) ?>"
                                                    data-format="datetime"><?= htmlspecialchars(formatDate($claim['pickup_scheduled'], 'M j, g:i A')) ?></span>
                                            </small>
                                        <?php elseif ($claim['status'] === 'approved'): ?>
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-clock me-1"></i>Not scheduled
                                            </span>
                                        <?php else: ?>
                                            <small class="text-muted">—</small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="small text-muted">
                                        <?= formatDate($claim['created_at'] ?? '', 'M j, Y') ?>
                                        <?php if (!empty($claim['found_date'])): ?>
                                            <br><small class="text-muted">Found:
                                                <?= formatDate($claim['found_date'], 'M j') ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= APP_URL ?>/admin/claims/<?= $claim['id'] ?>"
                                            class="btn btn-sm btn-primary">
                                            <i class="bi bi-eye me-1"></i>View
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
        <?php if (!empty($pagination) && ($pagination['pages'] ?? $pagination['totalPages'] ?? 1) > 1): ?>
            <?php $totalPages = $pagination['pages'] ?? $pagination['totalPages'] ?? 1; ?>
            <?php $currentPage = $pagination['page'] ?? $pagination['currentPage'] ?? 1; ?>
            <nav class="mt-4">
                <ul class="pagination pagination-sm justify-content-center">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&status=<?= urlencode($status ?? 'all') ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>

        <!-- Summary Stats -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 bg-light">
                    <div class="card-body py-3">
                        <div class="row text-center">
                            <div class="col">
                                <small class="text-muted d-block">Total Claims</small>
                                <strong><?= $pagination['total'] ?? $pagination['totalItems'] ?? count($claims) ?></strong>
                            </div>
                            <div class="col">
                                <small class="text-muted d-block">Current Page</small>
                                <strong><?= $pagination['page'] ?? $pagination['currentPage'] ?? 1 ?></strong>
                            </div>
                            <div class="col">
                                <small class="text-muted d-block">Filter</small>
                                <strong><?= ucfirst($status ?? 'All') ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include __DIR__ . '/../../layouts/footer-dashboard.php'; ?>