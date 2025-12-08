<?php $pageTitle = 'Claim #' . ($claim['id'] ?? '') . ' - ' . APP_NAME; ?>
<?php include __DIR__ . '/../../layouts/header-dashboard.php'; ?>

<?php
// New API structure with proof_images and item_images arrays
$proofImages = $claim['proof_images'] ?? [];
$itemImages = $claim['item_images'] ?? [];

// Status config
$statusConfig = [
    'pending' => ['class' => 'bg-warning text-dark', 'icon' => 'bi-hourglass-split'],
    'approved' => ['class' => 'bg-success', 'icon' => 'bi-check-circle'],
    'rejected' => ['class' => 'bg-danger', 'icon' => 'bi-x-circle'],
    'completed' => ['class' => 'bg-primary', 'icon' => 'bi-check2-all'],
    'cancelled' => ['class' => 'bg-secondary', 'icon' => 'bi-dash-circle']
];
$currentStatus = $claim['status'] ?? 'pending';
$statusInfo = $statusConfig[$currentStatus] ?? $statusConfig['pending'];


// Get primary item image from item_images array or fallback to item_primary_image
$itemImageSrc = '';
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
    if ($primaryImage) {
        $itemImageSrc = normalizeImageUrl($primaryImage['url'] ?? $primaryImage['file_name'] ?? '');
    }
} elseif (!empty($claim['item_primary_image'])) {
    $itemImageSrc = normalizeImageUrl($claim['item_primary_image']);
}

$storageLocation = $claim['storage_location'] ?? 'Security Office';
?>

<div class="dashboard-wrapper">
    <?php include __DIR__ . '/../partials/sidebar.php'; ?>

    <main class="dashboard-main">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-3">
                <a href="<?= APP_URL ?>/admin/claims" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h5 class="fw-bold mb-0">Claim #<?= $claim['id'] ?></h5>
                    <small class="text-muted">Submitted <?= formatDate($claim['created_at'] ?? '', 'F j, Y \a\t g:i A') ?></small>
                </div>
            </div>
            <span class="badge <?= $statusInfo['class'] ?> fs-6 px-3 py-2">
                <i class="bi <?= $statusInfo['icon'] ?> me-1"></i>
                <?= ucfirst($currentStatus) ?>
            </span>
        </div>

        <?php displayFlash(); ?>

        <div class="row g-4">
            <!-- Left Column: Claim Details -->
            <div class="col-lg-8">
                <!-- Found Item Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent border-0 py-3">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-box-seam me-2 text-primary"></i>Found Item</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <?php if ($itemImageSrc): ?>
                                    <a href="<?= htmlspecialchars($itemImageSrc) ?>" target="_blank">
                                        <img src="<?= htmlspecialchars($itemImageSrc) ?>" class="img-fluid rounded" alt="Item Image">
                                    </a>
                                <?php else: ?>
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 150px;">
                                        <i class="bi bi-image text-muted display-4"></i>
                                    </div>
                                <?php endif; ?>
                                <?php if (count($itemImages) > 1): ?>
                                    <div class="row g-1 mt-2">
                                        <?php foreach (array_slice($itemImages, 1, 3) as $img): ?>
                                            <div class="col-4">
                                                <a href="<?= htmlspecialchars(normalizeImageUrl($img['url'] ?? '')) ?>" target="_blank">
                                                    <img src="<?= htmlspecialchars(normalizeImageUrl($img['url'] ?? '')) ?>" class="img-fluid rounded border" alt="<?= htmlspecialchars($img['file_name'] ?? 'Item image') ?>">
                                                </a>
                                            </div>
                                        <?php endforeach; ?>
                                        <?php if (count($itemImages) > 4): ?>
                                            <div class="col-12 text-center">
                                                <small class="text-muted">+<?= count($itemImages) - 4 ?> more</small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-8">
                                <h5 class="fw-bold mb-2"><?= htmlspecialchars($claim['item_title'] ?? 'Unknown Item') ?></h5>
                                <p class="text-muted small mb-3"><?= htmlspecialchars($claim['item_description'] ?? '') ?></p>
                                
                                <div class="row g-2 small">
                                    <div class="col-6">
                                        <span class="text-muted">Category:</span><br>
                                        <strong><?= htmlspecialchars($claim['category_name'] ?? 'N/A') ?></strong>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted">Found Date:</span><br>
                                        <strong><?= formatDate($claim['found_date'] ?? '', 'M j, Y') ?><?= !empty($claim['found_time']) ? ' at ' . $claim['found_time'] : '' ?></strong>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted">Found Location:</span><br>
                                        <strong><?= htmlspecialchars($claim['found_location'] ?? 'N/A') ?></strong>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted">Stored At:</span><br>
                                        <strong class="text-primary">
                                            <i class="bi bi-geo-alt me-1"></i>
                                            <?= htmlspecialchars($storageLocation) ?>
                                        </strong>
                                    </div>
                                    <?php if (!empty($claim['item_unique_identifiers'])): ?>
                                    <div class="col-12">
                                        <span class="text-muted">Unique Identifiers:</span><br>
                                        <strong><?= htmlspecialchars($claim['item_unique_identifiers']) ?></strong>
                                    </div>
                                    <?php endif; ?>
                                    <?php if (!empty($claim['storage_notes'])): ?>
                                    <div class="col-12">
                                        <span class="text-muted">Storage Notes:</span><br>
                                        <span class="text-info"><?= htmlspecialchars($claim['storage_notes']) ?></span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="mt-3">
                                    <span class="badge <?= getStatusBadgeClass($claim['item_status'] ?? 'pending') ?> me-2">
                                        Item: <?= ucfirst($claim['item_status'] ?? 'pending') ?>
                                    </span>
                                    <?php if (!empty($claim['item_condition'])): ?>
                                        <span class="badge bg-info"><?= ucfirst($claim['item_condition']) ?> condition</span>
                                    <?php endif; ?>
                                    <a href="<?= APP_URL ?>/admin/found-items/<?= $claim['item_id'] ?? $claim['found_item_id'] ?>" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                                        <i class="bi bi-eye me-1"></i>View Full Item
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Claim Description -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent border-0 py-3">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-chat-left-text me-2 text-primary"></i>Claim Description</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0"><?= nl2br(htmlspecialchars($claim['description'] ?? 'No description provided')) ?></p>
                    </div>
                </div>

                <!-- Proof of Ownership -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent border-0 py-3">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-shield-check me-2 text-primary"></i>Proof of Ownership</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-light border mb-3">
                            <strong class="small">Claimant's Statement:</strong>
                            <p class="mb-0 mt-2"><?= nl2br(htmlspecialchars($claim['proof_details'] ?? 'No proof details provided')) ?></p>
                        </div>
                        
                        <?php if (!empty($proofImages)): ?>
                            <strong class="small d-block mb-2">Proof Images (<?= count($proofImages) ?>):</strong>
                            <div class="row g-2">
                                <?php foreach ($proofImages as $image): ?>
                                    <?php $proofImgUrl = normalizeImageUrl($image['url'] ?? ''); ?>
                                    <div class="col-4 col-md-3">
                                        <a href="<?= htmlspecialchars($proofImgUrl) ?>" target="_blank" title="<?= htmlspecialchars($image['description'] ?? $image['file_name'] ?? 'Proof image') ?>">
                                            <img src="<?= htmlspecialchars($proofImgUrl) ?>" class="img-fluid rounded border" alt="<?= htmlspecialchars($image['file_name'] ?? 'Proof image') ?>">
                                        </a>
                                        <?php if (!empty($image['description'])): ?>
                                            <small class="text-muted d-block text-truncate" title="<?= htmlspecialchars($image['description']) ?>">
                                                <?= htmlspecialchars($image['description']) ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted small mb-0">No proof images uploaded</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Verification Notes (if approved/rejected) -->
                <?php if (!empty($claim['verification_notes']) || !empty($claim['rejection_reason'])): ?>
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent border-0 py-3">
                            <h6 class="mb-0 fw-bold">
                                <i class="bi bi-clipboard-check me-2 text-primary"></i>
                                <?= $currentStatus === 'rejected' ? 'Rejection Reason' : 'Verification Notes' ?>
                            </h6>
                        </div>
                        <div class="card-body">
                            <?php if ($currentStatus === 'rejected'): ?>
                                <div class="alert alert-danger mb-0">
                                    <?= nl2br(htmlspecialchars($claim['rejection_reason'] ?? '')) ?>
                                </div>
                            <?php else: ?>
                                <p class="mb-0"><?= nl2br(htmlspecialchars($claim['verification_notes'] ?? '')) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($claim['verified_at'])): ?>
                                <small class="text-muted d-block mt-2">
                                    Verified on <?= formatDate($claim['verified_at'], 'M j, Y \a\t g:i A') ?>
                                    <?php if (!empty($claim['verifier_name'])): ?>
                                        by <?= htmlspecialchars($claim['verifier_name']) ?>
                                    <?php endif; ?>
                                </small>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right Column: People & Actions -->
            <div class="col-lg-4">
                <!-- Claimant Info -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent border-0 py-3">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-person me-2 text-primary"></i>Claimant</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white me-3" style="width: 48px; height: 48px;">
                                <?= strtoupper(substr($claim['claimant_first_name'] ?? 'U', 0, 1) . substr($claim['claimant_last_name'] ?? '', 0, 1)) ?>
                            </div>
                            <div>
                                <strong><?= htmlspecialchars($claim['claimant_name'] ?? (($claim['claimant_first_name'] ?? '') . ' ' . ($claim['claimant_last_name'] ?? ''))) ?></strong>
                                <small class="d-block text-muted"><?= $claim['claimant_school_id'] ?? 'N/A' ?></small>
                            </div>
                        </div>
                        <div class="small">
                            <div class="mb-2">
                                <i class="bi bi-envelope me-2 text-muted"></i>
                                <?= htmlspecialchars($claim['claimant_email'] ?? 'N/A') ?>
                            </div>
                            <?php if (!empty($claim['claimant_contact'])): ?>
                                <div>
                                    <i class="bi bi-telephone me-2 text-muted"></i>
                                    <?= htmlspecialchars($claim['claimant_contact']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Finder Info -->
                <?php if (!empty($claim['finder_name']) || !empty($claim['finder_first_name'])): ?>
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent border-0 py-3">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-person-check me-2 text-success"></i>Finder</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle bg-success d-flex align-items-center justify-content-center text-white me-3" style="width: 48px; height: 48px;">
                                    <?= strtoupper(substr($claim['finder_first_name'] ?? 'U', 0, 1) . substr($claim['finder_last_name'] ?? '', 0, 1)) ?>
                                </div>
                                <div>
                                    <strong><?= htmlspecialchars($claim['finder_name'] ?? (($claim['finder_first_name'] ?? '') . ' ' . ($claim['finder_last_name'] ?? ''))) ?></strong>
                                    <small class="d-block text-muted"><?= $claim['finder_school_id'] ?? 'N/A' ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Pickup Info (if scheduled) -->
                <?php if (!empty($claim['pickup_scheduled'])): ?>
                    <div class="card border-0 shadow-sm mb-4 border-success">
                        <div class="card-header bg-success bg-opacity-10 border-0 py-3">
                            <h6 class="mb-0 fw-bold text-success">
                                <i class="bi bi-calendar-check me-2"></i>Pickup Scheduled
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center py-2">
                                <div class="display-6 text-success mb-2">
                                    <i class="bi bi-calendar-event"></i>
                                </div>
                                <strong class="d-block"><?= formatDate($claim['pickup_scheduled'], 'l, F j, Y') ?></strong>
                                <span class="text-muted"><?= formatDate($claim['pickup_scheduled'], 'g:i A') ?></span>
                            </div>
                            <hr>
                            <div class="small">
                                <i class="bi bi-geo-alt text-primary me-1"></i>
                                <strong>Location:</strong> <?= htmlspecialchars($storageLocation) ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Picked Up Info (if completed) -->
                <?php if (!empty($claim['picked_up_at'])): ?>
                    <div class="card border-0 shadow-sm mb-4 border-primary">
                        <div class="card-header bg-primary bg-opacity-10 border-0 py-3">
                            <h6 class="mb-0 fw-bold text-primary">
                                <i class="bi bi-check2-circle me-2"></i>Pickup Completed
                            </h6>
                        </div>
                        <div class="card-body small">
                            <div class="mb-2">
                                <i class="bi bi-calendar-check me-2 text-muted"></i>
                                <strong>Date:</strong> <?= formatDate($claim['picked_up_at'], 'M j, Y \a\t g:i A') ?>
                            </div>
                            <?php if (!empty($claim['picked_up_by_name'])): ?>
                            <div class="mb-2">
                                <i class="bi bi-person me-2 text-muted"></i>
                                <strong>Picked up by:</strong> <?= htmlspecialchars($claim['picked_up_by_name']) ?>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($claim['id_presented'])): ?>
                            <div>
                                <i class="bi bi-card-text me-2 text-muted"></i>
                                <strong>ID Presented:</strong> <?= htmlspecialchars($claim['id_presented']) ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Action Cards based on Status -->
                <?php if ($currentStatus === 'pending'): ?>
                    <!-- Approve Claim -->
                    <div class="card border-0 shadow-sm mb-4 border-success">
                        <div class="card-header bg-success bg-opacity-10 border-0 py-3">
                            <h6 class="mb-0 fw-bold text-success">
                                <i class="bi bi-check-circle me-2"></i>Approve Claim
                            </h6>
                        </div>
                        <div class="card-body">
                            <form action="<?= APP_URL ?>/admin/claims/<?= $claim['id'] ?>/approve" method="POST">
                                <div class="mb-3">
                                    <label class="form-label small">Verification Notes</label>
                                    <textarea name="verification_notes" class="form-control" rows="2" placeholder="e.g., Serial number verified, description matches..."></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small">Schedule Pickup Date</label>
                                    <input type="date" name="pickup_date" class="form-control" min="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small">Pickup Time</label>
                                    <input type="time" name="pickup_time" class="form-control" value="10:00">
                                </div>
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-check-lg me-2"></i>Approve Claim
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Reject Claim -->
                    <div class="card border-0 shadow-sm mb-4 border-danger">
                        <div class="card-header bg-danger bg-opacity-10 border-0 py-3">
                            <h6 class="mb-0 fw-bold text-danger">
                                <i class="bi bi-x-circle me-2"></i>Reject Claim
                            </h6>
                        </div>
                        <div class="card-body">
                            <form action="<?= APP_URL ?>/admin/claims/<?= $claim['id'] ?>/reject" method="POST">
                                <div class="mb-3">
                                    <label class="form-label small">Rejection Reason <span class="text-danger">*</span></label>
                                    <textarea name="rejection_reason" class="form-control" rows="3" required minlength="10" placeholder="Provide a detailed reason for rejection (min 10 characters)..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Are you sure you want to reject this claim?')">
                                    <i class="bi bi-x-lg me-2"></i>Reject Claim
                                </button>
                            </form>
                        </div>
                    </div>
                <?php elseif ($currentStatus === 'approved'): ?>
                    <!-- Schedule/Reschedule Pickup -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent border-0 py-3">
                            <h6 class="mb-0 fw-bold">
                                <i class="bi bi-calendar-plus me-2 text-primary"></i>
                                <?= !empty($claim['pickup_scheduled']) ? 'Reschedule' : 'Schedule' ?> Pickup
                            </h6>
                        </div>
                        <div class="card-body">
                            <form action="<?= APP_URL ?>/admin/claims/<?= $claim['id'] ?>/schedule" method="POST">
                                <div class="mb-3">
                                    <label class="form-label small">Pickup Date</label>
                                    <input type="date" name="pickup_date" class="form-control" min="<?= date('Y-m-d') ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small">Pickup Time</label>
                                    <input type="time" name="pickup_time" class="form-control" value="10:00" required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-calendar-check me-2"></i><?= !empty($claim['pickup_scheduled']) ? 'Reschedule' : 'Schedule' ?>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Record Pickup -->
                    <div class="card border-0 shadow-sm mb-4 border-primary">
                        <div class="card-header bg-primary bg-opacity-10 border-0 py-3">
                            <h6 class="mb-0 fw-bold text-primary">
                                <i class="bi bi-box-arrow-up me-2"></i>Record Item Pickup
                            </h6>
                        </div>
                        <div class="card-body">
                            <form action="<?= APP_URL ?>/admin/claims/<?= $claim['id'] ?>/pickup" method="POST">
                                <div class="mb-3">
                                    <label class="form-label small">Picked Up By <span class="text-danger">*</span></label>
                                    <input type="text" name="picked_up_by_name" class="form-control" required 
                                           value="<?= htmlspecialchars($claim['claimant_name'] ?? (($claim['claimant_first_name'] ?? '') . ' ' . ($claim['claimant_last_name'] ?? ''))) ?>"
                                           placeholder="Name of person picking up">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small">ID Presented</label>
                                    <input type="text" name="id_presented" class="form-control" 
                                           value="<?= htmlspecialchars($claim['claimant_school_id'] ?? '') ?>"
                                           placeholder="School ID or other ID presented">
                                </div>
                                <button type="submit" class="btn btn-primary w-100" onclick="return confirm('Confirm item handover? This action cannot be undone.')">
                                    <i class="bi bi-check2-all me-2"></i>Complete Handover
                                </button>
                            </form>
                        </div>
                    </div>
                <?php elseif ($currentStatus === 'completed'): ?>
                    <!-- Completed Info -->
                    <div class="card border-0 shadow-sm mb-4 bg-success bg-opacity-10">
                        <div class="card-body text-center py-4">
                            <div class="display-4 text-success mb-3">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                            <h5 class="text-success fw-bold">Item Handed Over</h5>
                            <?php if (!empty($claim['picked_up_by_name'])): ?>
                                <p class="mb-1">Picked up by: <strong><?= htmlspecialchars($claim['picked_up_by_name']) ?></strong></p>
                            <?php endif; ?>
                            <?php if (!empty($claim['id_presented'])): ?>
                                <p class="mb-1 small text-muted">ID: <?= htmlspecialchars($claim['id_presented']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($claim['picked_up_at'])): ?>
                                <p class="mb-0 small text-muted">
                                    Completed: <?= formatDate($claim['picked_up_at'], 'M j, Y g:i A') ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Quick Actions -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 py-3">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-lightning me-2 text-warning"></i>Quick Links</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <?php if (!empty($claim['claimant_user_id'])): ?>
                            <a href="<?= APP_URL ?>/admin/users/<?= $claim['claimant_user_id'] ?>" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-person me-2"></i>View Claimant Profile
                            </a>
                            <?php endif; ?>
                            <a href="<?= APP_URL ?>/found-items/<?= $claim['item_id'] ?? $claim['found_item_id'] ?>" class="btn btn-outline-secondary btn-sm" target="_blank">
                                <i class="bi bi-box-seam me-2"></i>View Found Item Page
                            </a>
                            <a href="<?= APP_URL ?>/admin/claims" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-arrow-left me-2"></i>Back to All Claims
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include __DIR__ . '/../../layouts/footer-dashboard.php'; ?>
