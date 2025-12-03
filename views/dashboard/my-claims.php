<?php $pageTitle = 'My Claims - ' . APP_NAME; ?>
<?php include __DIR__ . '/../layouts/header-dashboard.php'; ?>

<?php
// Helper function to normalize image URL
function normalizeClaimImageUrl($imgPath) {
    if (empty($imgPath)) return '';
    $imgPath = str_replace('\\', '/', $imgPath);
    $imgPath = preg_replace('#^/api/#', '', $imgPath);
    $imgPath = ltrim($imgPath, '/');
    if (preg_match('/^https?:\/\//', $imgPath)) {
        return $imgPath;
    }
    if (strpos($imgPath, 'uploads/') === 0) {
        return API_BASE_URL . '/' . $imgPath;
    }
    return API_BASE_URL . '/uploads/' . $imgPath;
}
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
                <a href="<?= APP_URL ?>/notifications" class="btn ui-btn-secondary btn-sm position-relative" title="Notifications">
                    <i class="bi bi-bell"></i>
                    <?php if (getUnreadNotificationCount() > 0): ?><span class="notification-dot"></span><?php endif; ?>
                </a>
                <button type="button" class="btn ui-btn-secondary btn-sm" onclick="toggleDarkMode()" title="Toggle Dark Mode">
                    <i class="bi bi-moon" id="headerThemeIcon"></i>
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
                <a class="nav-link <?= $status === 'pending' ? 'active' : '' ?>" href="<?= APP_URL ?>/dashboard/my-claims?status=pending">
                    <span class="badge bg-warning me-1">●</span> Pending
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $status === 'approved' ? 'active' : '' ?>" href="<?= APP_URL ?>/dashboard/my-claims?status=approved">
                    <span class="badge bg-info me-1">●</span> Approved
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $status === 'completed' ? 'active' : '' ?>" href="<?= APP_URL ?>/dashboard/my-claims?status=completed">
                    <span class="badge bg-success me-1">●</span> Completed
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $status === 'rejected' ? 'active' : '' ?>" href="<?= APP_URL ?>/dashboard/my-claims?status=rejected">
                    <span class="badge bg-danger me-1">●</span> Rejected
                </a>
            </li>
        </ul>
        
        <!-- Claims List -->
        <?php 
        $claimsList = $claims['data'] ?? $claims;
        if (!is_array($claimsList)) $claimsList = [];
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
                    // Get item image from new structure
                    $claimImageUrl = '';
                    $itemImages = $claim['item_images'] ?? [];
                    if (!empty($itemImages)) {
                        // Find primary image or use first
                        $primaryImg = null;
                        foreach ($itemImages as $img) {
                            if (isset($img['is_primary']) && ($img['is_primary'] === true || $img['is_primary'] === 1 || $img['is_primary'] === '1')) {
                                $primaryImg = $img;
                                break;
                            }
                        }
                        $primaryImg = $primaryImg ?? $itemImages[0] ?? null;
                        if ($primaryImg) {
                            $claimImageUrl = normalizeClaimImageUrl($primaryImg['url'] ?? $primaryImg['file_name'] ?? '');
                        }
                    } elseif (!empty($claim['item_primary_image'])) {
                        $claimImageUrl = normalizeClaimImageUrl($claim['item_primary_image']);
                    }
                    
                    if (empty($claimImageUrl)) {
                        $claimImageUrl = APP_URL . '/assets/img/no-image.svg';
                    }
                    
                    $claimStatus = $claim['status'] ?? 'pending';
                    ?>
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        <img src="<?= htmlspecialchars($claimImageUrl) ?>" 
                                             class="img-fluid rounded" 
                                             style="object-fit: cover; height: 80px; width: 100%;"
                                             alt="<?= htmlspecialchars($claim['item_title'] ?? 'Item') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="mb-1">
                                            <a href="<?= APP_URL ?>/found-items/<?= $claim['item_id'] ?? $claim['found_item_id'] ?>" class="text-decoration-none">
                                                <?= htmlspecialchars($claim['item_title'] ?? 'Unknown Item') ?>
                                            </a>
                                        </h6>
                                        <p class="text-muted small mb-2">
                                            <i class="bi bi-tag me-1"></i><?= htmlspecialchars($claim['category_name'] ?? 'Uncategorized') ?>
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
                                        <a href="<?= APP_URL ?>/claims/<?= $claim['id'] ?>" class="btn btn-sm btn-outline-primary">
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
