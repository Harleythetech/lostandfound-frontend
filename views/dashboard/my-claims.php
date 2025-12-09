<?php $pageTitle = 'My Claims - ' . APP_NAME; ?>
<?php include __DIR__ . '/../layouts/header-dashboard.php'; ?>

<?php
?>

<div class="dashboard-wrapper">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>

    <main class="dashboard-main">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <div>
                <h4 class="fw-semibold mb-1">My Claims</h4>
                <p class="text-muted mb-0 small">Track claims you've submitted for found items</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="<?= notificationUrl() ?>" class="btn ui-btn-secondary btn-sm position-relative"
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
        <ul class="nav nav-tabs mb-4" role="tablist">
            <li class="nav-item">
                <a class="nav-link <?= empty($status) ? 'active' : '' ?>" href="<?= APP_URL ?>/dashboard/my-claims">
                    All
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $status === 'pending' ? 'active' : '' ?>"
                    href="<?= APP_URL ?>/dashboard/my-claims?status=pending">
                    <span class="badge bg-warning me-1">●</span> Pending
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $status === 'approved' ? 'active' : '' ?>"
                    href="<?= APP_URL ?>/dashboard/my-claims?status=approved">
                    <span class="badge bg-info me-1">●</span> Approved
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $status === 'completed' ? 'active' : '' ?>"
                    href="<?= APP_URL ?>/dashboard/my-claims?status=completed">
                    <span class="badge bg-success me-1">●</span> Completed
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $status === 'rejected' ? 'active' : '' ?>"
                    href="<?= APP_URL ?>/dashboard/my-claims?status=rejected">
                    <span class="badge bg-danger me-1">●</span> Rejected
                </a>
            </li>
        </ul>

        <!-- Claims List -->
        <?php
        $claimsList = $claims['data'] ?? $claims;
        if (!is_array($claimsList))
            $claimsList = [];
        ?>
        <?php if (empty($claimsList)): ?>
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-hand-index display-1 text-muted mb-3"></i>
                    <h5>No Claims Yet</h5>
                    <p class="text-muted mb-4">You haven't submitted any claims. Looking for your lost item?</p>
                    <a href="<?= APP_URL ?>/found-items" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i>Browse Found Items
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($claimsList as $claim): ?>
                    <?php
                    // Get item image - handle multiple shapes returned by API
                    // Priority: item_primary_image > item_images (primary or first) > found_item.primary_image > found_item.images[0]
                    $claimImageUrl = '';

                    // Direct primary image or object
                    if (!empty($claim['item_primary_image'])) {
                        if (is_array($claim['item_primary_image'])) {
                            $claimImageUrl = $claim['item_primary_image']['url'] ?? $claim['item_primary_image']['file_name'] ?? '';
                        } else {
                            $claimImageUrl = $claim['item_primary_image'];
                        }
                    }

                    // item_images array (flattened API response)
                    if (empty($claimImageUrl) && !empty($claim['item_images'])) {
                        $itemImages = $claim['item_images'];
                        $primaryImg = null;
                        foreach ($itemImages as $img) {
                            if (isset($img['is_primary']) && ($img['is_primary'] === true || $img['is_primary'] === 1 || $img['is_primary'] === '1')) {
                                $primaryImg = $img;
                                break;
                            }
                        }
                        $primaryImg = $primaryImg ?? $itemImages[0] ?? null;
                        if ($primaryImg) {
                            $claimImageUrl = is_array($primaryImg) ? ($primaryImg['url'] ?? $primaryImg['file_name'] ?? '') : $primaryImg;
                        }
                    }

                    // single image field fallback (some endpoints return 'item_image')
                    if (empty($claimImageUrl) && !empty($claim['item_image'])) {
                        if (is_array($claim['item_image'])) {
                            $claimImageUrl = $claim['item_image']['url'] ?? $claim['item_image']['file_name'] ?? '';
                        } else {
                            $claimImageUrl = $claim['item_image'];
                        }
                    }

                    // Nested found_item (dashboard/claims endpoints often return this shape)
                    if (empty($claimImageUrl) && !empty($claim['found_item'])) {
                        $found = $claim['found_item'];
                        if (!empty($found['primary_image'])) {
                            if (is_array($found['primary_image'])) {
                                $claimImageUrl = $found['primary_image']['url'] ?? $found['primary_image']['file_name'] ?? '';
                            } else {
                                $claimImageUrl = $found['primary_image'];
                            }
                        }

                        if (empty($claimImageUrl) && !empty($found['images'])) {
                            $fi = $found['images'];
                            $primaryImg = null;
                            foreach ($fi as $img) {
                                if (isset($img['is_primary']) && ($img['is_primary'] === true || $img['is_primary'] === 1 || $img['is_primary'] === '1')) {
                                    $primaryImg = $img;
                                    break;
                                }
                            }
                            $primaryImg = $primaryImg ?? $fi[0] ?? null;
                            if ($primaryImg) {
                                $claimImageUrl = is_array($primaryImg) ? ($primaryImg['url'] ?? $primaryImg['file_name'] ?? '') : $primaryImg;
                            }
                        }
                    }

                    $claimImageUrl = normalizeImageUrl($claimImageUrl);

                    $claimStatus = $claim['status'] ?? 'pending';
                    ?>
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        <?php if (!empty($claimImageUrl)): ?>
                                            <img src="<?= htmlspecialchars($claimImageUrl) ?>" class="img-fluid rounded"
                                                style="object-fit: cover; height: 80px; width: 100%;"
                                                alt="<?= htmlspecialchars($claim['item_title'] ?? 'Item') ?>">
                                        <?php else: ?>
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                style="height: 80px; width: 100%;">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="mb-1">
                                            <a href="<?= APP_URL ?>/claims/<?= $claim['id'] ?>" class="text-decoration-none">
                                                <?= htmlspecialchars($claim['item_title'] ?? 'Unknown Item') ?>
                                            </a>
                                        </h6>
                                        <p class="text-muted small mb-2">
                                            <i
                                                class="bi bi-tag me-1"></i><?= htmlspecialchars($claim['category_name'] ?? 'Uncategorized') ?>
                                            <span class="mx-2">•</span>
                                            Claimed on <?= date('M d, Y', strtotime($claim['created_at'])) ?>
                                        </p>
                                        <p class="small mb-0 text-truncate-2">
                                            <?= htmlspecialchars($claim['description'] ?? '') ?>
                                        </p>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <?php
                                        $claimStatusClasses = [
                                            'pending' => 'bg-warning text-dark',
                                            'approved' => 'bg-info',
                                            'completed' => 'bg-success',
                                            'rejected' => 'bg-danger',
                                            'cancelled' => 'bg-secondary'
                                        ];
                                        $claimStatusClass = $claimStatusClasses[$claimStatus] ?? 'bg-secondary';
                                        ?>
                                        <span class="badge <?= $claimStatusClass ?>"><?= ucfirst($claimStatus) ?></span>

                                        <?php if (!empty($claim['pickup_scheduled'])): ?>
                                            <div class="small text-muted mt-1">
                                                <i class="bi bi-calendar-event"></i>
                                                <?= date('M d, Y', strtotime($claim['pickup_scheduled'])) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <a href="<?= APP_URL ?>/claims/<?= $claim['id'] ?>"
                                            class="btn btn-sm btn-outline-primary">
                                            View Details
                                        </a>
                                    </div>
                                </div>

                                <?php if ($claimStatus === 'rejected' && !empty($claim['rejection_reason'])): ?>
                                    <div class="alert alert-danger mt-3 mb-0 py-2">
                                        <strong>Rejection Reason:</strong> <?= htmlspecialchars($claim['rejection_reason']) ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ($claimStatus === 'approved' && empty($claim['pickup_scheduled'])): ?>
                                    <div class="alert alert-info mt-3 mb-0 py-2">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Your claim has been approved! Please wait for the pickup schedule.
                                    </div>
                                <?php endif; ?>

                                <?php if ($claimStatus === 'approved' && !empty($claim['pickup_scheduled'])): ?>
                                    <div class="alert alert-success mt-3 mb-0 py-2">
                                        <i class="bi bi-calendar-check me-1"></i>
                                        Pickup scheduled for <?= date('F d, Y \a\t g:i A', strtotime($claim['pickup_scheduled'])) ?>
                                        <?php if (!empty($claim['storage_location'])): ?>
                                            at <strong><?= htmlspecialchars($claim['storage_location']) ?></strong>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ($claimStatus === 'completed'): ?>
                                    <div class="alert alert-success mt-3 mb-0 py-2">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Item successfully retrieved
                                        <?php if (!empty($claim['picked_up_at'])): ?>
                                            on <?= date('F d, Y', strtotime($claim['picked_up_at'])) ?>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php
            $pagination = $claims['pagination'] ?? null;
            if (!empty($pagination)):
                $currentPage = $pagination['page'] ?? $pagination['currentPage'] ?? 1;
                $totalPages = $pagination['pages'] ?? $pagination['totalPages'] ?? 1;
                ?>
                <?php if ($totalPages > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php
                            $baseUrl = '/dashboard/my-claims?' . http_build_query(array_filter([
                                'status' => $status ?? ''
                            ]));
                            ?>

                            <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= $baseUrl ?>&page=<?= $currentPage - 1 ?>">Previous</a>
                            </li>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= $baseUrl ?>&page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= $baseUrl ?>&page=<?= $currentPage + 1 ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </main>
</div>

<?php include __DIR__ . '/../layouts/footer-dashboard.php'; ?>