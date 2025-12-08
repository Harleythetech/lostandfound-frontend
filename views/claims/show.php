<?php $pageTitle = 'Claim #' . ($claim['id'] ?? '') . ' - ' . APP_NAME; ?>
<?php include __DIR__ . '/../layouts/header-dashboard.php'; ?>
<?php $user = getCurrentUser(); ?>

<?php
?>

<div class="dashboard-wrapper">
    <?php include __DIR__ . '/../dashboard/partials/sidebar.php'; ?>

    <main class="dashboard-main">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 small">
                        <li class="breadcrumb-item"><a href="<?= APP_URL ?>/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= APP_URL ?>/dashboard/my-claims">My Claims</a></li>
                        <li class="breadcrumb-item active">Claim #<?= $claim['id'] ?></li>
                    </ol>
                </nav>
                <h4 class="fw-semibold mb-0">Claim Details</h4>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="<?= APP_URL ?>/notifications" class="btn ui-btn-secondary btn-sm position-relative"
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
            <div class="col-lg-8">
                <!-- Claim Details -->
                <div class="card border mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h6 class="mb-0"><i class="bi bi-hand-index me-2"></i>Claim Details</h6>
                        <?php
                        $status = $claim['status'] ?? 'pending';
                        $statusClasses = [
                            'pending' => 'bg-warning text-dark',
                            'approved' => 'bg-info',
                            'completed' => 'bg-success',
                            'rejected' => 'bg-danger',
                            'cancelled' => 'bg-secondary'
                        ];
                        $statusClass = $statusClasses[$status] ?? 'bg-secondary';
                        ?>
                        <span class="badge <?= $statusClass ?>"><?= ucfirst($status) ?></span>
                    </div>
                    <div class="card-body">
                        <!-- Status Timeline -->
                        <div class="mb-4">
                            <small class="text-muted">Status Progress</small>
                            <div class="d-flex justify-content-between position-relative mt-3">
                                <div class="progress position-absolute"
                                    style="height: 4px; top: 12px; left: 12%; right: 12%; z-index: 0;">
                                    <?php
                                    $statusProgress = ['pending' => 0, 'approved' => 50, 'completed' => 100, 'rejected' => 0, 'cancelled' => 0];
                                    $progress = $statusProgress[$status] ?? 0;
                                    ?>
                                    <div class="progress-bar bg-success" style="width: <?= $progress ?>%;"></div>
                                </div>
                                <?php
                                $steps = ['Submitted', 'Approved', 'Completed'];
                                $stepStatus = ['pending' => 1, 'approved' => 2, 'completed' => 3, 'rejected' => 0, 'cancelled' => 0];
                                $currentStep = $stepStatus[$status] ?? 0;
                                foreach ($steps as $i => $step):
                                    $stepNum = $i + 1;
                                    $isComplete = $stepNum <= $currentStep;
                                    ?>
                                    <div class="text-center" style="z-index: 1;">
                                        <div class="rounded-circle <?= $isComplete ? 'bg-success' : 'bg-secondary' ?> text-white d-flex align-items-center justify-content-center mx-auto mb-1"
                                            style="width: 28px; height: 28px; font-size: 12px;">
                                            <?= $isComplete ? '<i class="bi bi-check-lg"></i>' : $stepNum ?>
                                        </div>
                                        <small class="text-muted"><?= $step ?></small>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <?php if ($status === 'rejected'): ?>
                            <div class="alert alert-danger py-2">
                                <i class="bi bi-x-circle me-1"></i><strong>Rejected:</strong>
                                <?= htmlspecialchars($claim['rejection_reason'] ?? 'No reason provided.') ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($status === 'cancelled'): ?>
                            <div class="alert alert-secondary py-2">
                                <i class="bi bi-x-circle me-1"></i><strong>Cancelled:</strong> This claim has been
                                cancelled.
                            </div>
                        <?php endif; ?>

                        <?php if ($status === 'approved' && !empty($claim['pickup_scheduled'])): ?>
                            <div class="alert alert-success py-2">
                                <i class="bi bi-calendar-check me-1"></i><strong>Pickup Scheduled</strong><br>
                                <strong>Date:</strong>
                                <?= date('F d, Y \a\t g:i A', strtotime($claim['pickup_scheduled'])) ?>
                                <?php if (!empty($claim['storage_location'])): ?><br><strong>Location:</strong>
                                    <?= htmlspecialchars($claim['storage_location']) ?>     <?php endif; ?>
                            </div>
                        <?php elseif ($status === 'approved'): ?>
                            <div class="alert alert-info py-2">
                                <i class="bi bi-info-circle me-1"></i>Your claim has been approved! Please wait for the
                                pickup schedule.
                            </div>
                        <?php endif; ?>

                        <?php if ($status === 'completed'): ?>
                            <div class="alert alert-success py-2">
                                <i class="bi bi-check-circle me-1"></i><strong>Item Retrieved</strong>
                                <?php if (!empty($claim['picked_up_at'])): ?>
                                    on <?= date('F d, Y', strtotime($claim['picked_up_at'])) ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Claim Description -->
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Why This Is Your Item</small>
                            <p class="mb-0"><?= nl2br(htmlspecialchars($claim['description'] ?? '')) ?></p>
                        </div>

                        <!-- Proof Images -->
                        <?php
                        $proofImages = $claim['proof_images'] ?? [];
                        if (!empty($proofImages)):
                            ?>
                            <div class="mb-3">
                                <small class="text-muted d-block mb-2">Proof of Ownership</small>
                                <div class="row g-2">
                                    <?php foreach ($proofImages as $image): ?>
                                        <?php
                                        $proofUrl = normalizeImageUrl($image['url'] ?? '');
                                        ?>
                                        <?php if (!empty($proofUrl)): ?>
                                            <div class="col-3 col-md-2">
                                                <a href="<?= htmlspecialchars($proofUrl) ?>" target="_blank">
                                                    <img src="<?= htmlspecialchars($proofUrl) ?>" class="img-thumbnail"
                                                        style="width: 100%; height: 60px; object-fit: cover;">
                                                </a>
                                                <?php if (!empty($image['description'])): ?>
                                                    <small
                                                        class="d-block text-muted text-truncate"><?= htmlspecialchars($image['description']) ?></small>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Timestamps -->
                        <div class="text-muted small">
                            <i class="bi bi-clock me-1"></i>Submitted
                            <?= date('M d, Y g:i A', strtotime($claim['created_at'])) ?>
                            <?php if (!empty($claim['verified_at'])): ?>
                                <br><i class="bi bi-check-circle me-1"></i>Approved
                                <?= date('M d, Y g:i A', strtotime($claim['verified_at'])) ?>
                                <?php if (!empty($claim['verifier_name'])): ?>
                                    by <?= htmlspecialchars($claim['verifier_name']) ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Item Card -->
                <div class="card border mb-4">
                    <div class="card-header bg-white py-2">
                        <h6 class="mb-0"><i class="bi bi-box-seam me-2"></i>Claimed Item</h6>
                    </div>
                    <?php
                    // Get item image from new structure
                    $itemImageUrl = '';
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
                            $itemImageUrl = normalizeImageUrl($primaryImg['url'] ?? $primaryImg['file_name'] ?? '');
                        }
                    } elseif (!empty($claim['item_primary_image'])) {
                        $itemImageUrl = normalizeImageUrl($claim['item_primary_image']);
                    }

                    if (empty($itemImageUrl)) {
                        $itemImageUrl = '';
                    }

                    $itemId = $claim['item_id'] ?? $claim['found_item_id'] ?? '';
                    ?>
                    <?php if (!empty($itemImageUrl)): ?>
                        <img src="<?= htmlspecialchars($itemImageUrl) ?>" class="card-img-top"
                            style="height: 150px; object-fit: cover;">
                    <?php else: ?>
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                            style="height: 150px;">
                            <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                        </div>
                    <?php endif; ?>
                    <div class="card-body p-3">
                        <h6 class="mb-1"><?= htmlspecialchars($claim['item_title'] ?? 'Unknown Item') ?></h6>
                        <p class="text-muted small mb-2 text-truncate">
                            <?= htmlspecialchars($claim['item_description'] ?? '') ?>
                        </p>
                        <?php if (!empty($claim['category_name'])): ?>
                            <span class="badge bg-light text-dark me-1"><i
                                    class="bi bi-tag me-1"></i><?= htmlspecialchars($claim['category_name']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($claim['found_location'])): ?>
                            <span class="badge bg-light text-dark"><i
                                    class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($claim['found_location']) ?></span>
                        <?php endif; ?>
                        <div class="mt-3">
                            <a href="<?= APP_URL ?>/found-items/<?= $itemId ?>"
                                class="btn btn-outline-primary btn-sm w-100">
                                <i class="bi bi-eye me-1"></i>View Item
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <?php
                $isOwner = ($claim['claimant_id'] ?? $claim['user_id'] ?? 0) == ($user['id'] ?? -1);
                $isFinder = ($claim['finder_id'] ?? 0) == ($user['id'] ?? -1);
                ?>

                <?php if ($isOwner && $status === 'pending'): ?>
                    <div class="card border mb-4">
                        <div class="card-header bg-white py-2">
                            <h6 class="mb-0"><i class="bi bi-gear me-2"></i>Actions</h6>
                        </div>
                        <div class="card-body">
                            <form action="<?= APP_URL ?>/claims/<?= $claim['id'] ?>/cancel" method="POST"
                                onsubmit="return confirm('Cancel this claim?');">
                                <button type="submit" class="btn btn-outline-danger w-100"><i
                                        class="bi bi-x-circle me-1"></i>Cancel Claim</button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Contact Info -->
                <?php if (!in_array($status, ['rejected', 'pending', 'cancelled'])): ?>
                    <div class="card border">
                        <div class="card-header bg-white py-2">
                            <h6 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>Contact</h6>
                        </div>
                        <div class="card-body">
                            <?php if ($isOwner && !empty($claim['finder_name'])): ?>
                                <p class="mb-1"><strong>Finder:</strong></p>
                                <p class="mb-1"><?= htmlspecialchars($claim['finder_name']) ?></p>
                                <?php if (!empty($claim['finder_school_id'])): ?>
                                    <p class="mb-0 small text-muted"><i
                                            class="bi bi-person-badge me-1"></i><?= htmlspecialchars($claim['finder_school_id']) ?>
                                    </p>
                                <?php endif; ?>
                            <?php elseif ($isFinder): ?>
                                <p class="mb-1"><strong>Claimant:</strong></p>
                                <p class="mb-1"><?= htmlspecialchars($claim['claimant_name'] ?? '') ?></p>
                                <?php if (!empty($claim['claimant_contact'])): ?>
                                    <p class="mb-0"><i
                                            class="bi bi-telephone me-1"></i><?= htmlspecialchars($claim['claimant_contact']) ?></p>
                                <?php endif; ?>
                                <?php if (!empty($claim['claimant_email'])): ?>
                                    <p class="mb-0"><i
                                            class="bi bi-envelope me-1"></i><?= htmlspecialchars($claim['claimant_email']) ?></p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Storage Info -->
                <?php if (!empty($claim['storage_location']) && $status === 'approved'): ?>
                    <div class="card border mt-4">
                        <div class="card-header bg-white py-2">
                            <h6 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Pickup Location</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-1"><strong><?= htmlspecialchars($claim['storage_location']) ?></strong></p>
                            <?php if (!empty($claim['storage_notes'])): ?>
                                <p class="mb-0 text-muted small"><?= htmlspecialchars($claim['storage_notes']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<?php include __DIR__ . '/../layouts/footer-dashboard.php'; ?>