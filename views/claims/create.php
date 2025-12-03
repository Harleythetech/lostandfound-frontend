<?php $pageTitle = 'Claim Item - ' . APP_NAME; ?>
<?php include __DIR__ . '/../layouts/header-dashboard.php'; ?>
<?php $user = getCurrentUser(); ?>

<div class="dashboard-wrapper">
    <?php include __DIR__ . '/../dashboard/partials/sidebar.php'; ?>
    
    <main class="dashboard-main">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 small">
                        <li class="breadcrumb-item"><a href="<?= APP_URL ?>/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= APP_URL ?>/found-items">Found Items</a></li>
                        <li class="breadcrumb-item active">Claim</li>
                    </ol>
                </nav>
                <h4 class="fw-semibold mb-0"><i class="bi bi-hand-index me-2 text-warning"></i>Claim This Item</h4>
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

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border">
                    <div class="card-body p-4">
                        <!-- Item Summary -->
                        <?php $claimItem = $foundItem ?? $item; ?>
                        <div class="d-flex mb-4 pb-4 border-bottom">
                            <?php 
                            $itemImage = null;
                            if (!empty($claimItem['images'][0])) {
                                $img = $claimItem['images'][0];
                                $imgPath = is_array($img) ? ($img['url'] ?? $img['image_path'] ?? '') : $img;
                                $imgPath = str_replace('\\', '/', $imgPath);
                                $imgPath = preg_replace('#^(/api/|/|api/)#', '', $imgPath);
                                if (!empty($imgPath)) {
                                    $itemImage = API_BASE_URL . '/' . $imgPath;
                                }
                            }
                            ?>
                            <?php if ($itemImage): ?>
                            <img src="<?= htmlspecialchars($itemImage) ?>" class="rounded me-3" style="width: 100px; height: 100px; object-fit: cover;">
                            <?php else: ?>
                            <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                <i class="bi bi-image text-muted display-4"></i>
                            </div>
                            <?php endif; ?>
                            <div>
                                <h5 class="mb-2"><?= htmlspecialchars($claimItem['title'] ?? $claimItem['item_name'] ?? '') ?></h5>
                                <p class="text-muted mb-1 small"><i class="bi bi-tag me-1"></i><?= htmlspecialchars($claimItem['category']['name'] ?? 'N/A') ?></p>
                                <p class="text-muted mb-1 small"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($claimItem['location']['name'] ?? 'N/A') ?></p>
                                <p class="text-muted mb-0 small"><i class="bi bi-calendar me-1"></i>Found on <?= formatDate($claimItem['found_date'] ?? $claimItem['date_found'] ?? $claimItem['created_at']) ?></p>
                            </div>
                        </div>

                        <div class="alert alert-info py-2 mb-4 small">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Important:</strong> Provide accurate details about the item to verify ownership. Include any distinctive features only the true owner would know.
                        </div>

                        <form action="<?= APP_URL ?>/claims" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="found_item_id" value="<?= $claimItem['id'] ?>">

                            <div class="mb-4">
                                <label for="description" class="form-label">Describe the item in detail <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="description" name="description" rows="4" placeholder="Describe the item including brand, color, size, contents, any marks or damage, etc." minlength="20" required></textarea>
                                <small class="text-muted">Be as specific as possible (min. 20 characters)</small>
                            </div>

                            <div class="mb-4">
                                <label for="proof_details" class="form-label">Proof of Ownership Details <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="proof_details" name="proof_details" rows="4" placeholder="Serial numbers, unique marks, scratches, stickers, engravings, exact contents..." minlength="20" required></textarea>
                                <small class="text-muted">Details only the true owner would know (min. 20 characters)</small>
                            </div>

                            <div class="mb-4">
                                <label for="images" class="form-label">Proof Images (Optional)</label>
                                <input type="file" class="form-control" id="images" name="images[]" accept="image/jpeg,image/png,image/gif" multiple>
                                <small class="text-muted">Receipts, photos with the item, or other proof (max 5 images, 5MB each)</small>
                            </div>

                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="declaration" name="declaration" required>
                                <label class="form-check-label small" for="declaration">
                                    I declare that the information provided is true and accurate. I understand that false claims may result in account suspension.
                                </label>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-warning btn-lg"><i class="bi bi-send me-2"></i>Submit Claim</button>
                                <a href="<?= APP_URL ?>/found-items/<?= $claimItem['id'] ?>" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Claim Process Info -->
                <div class="card border">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Claim Process</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-3">
                            <span class="badge bg-primary rounded-pill me-2">1</span>
                            <div>
                                <strong class="d-block">Submit</strong>
                                <small class="text-muted">Fill out the claim form</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-start mb-3">
                            <span class="badge bg-primary rounded-pill me-2">2</span>
                            <div>
                                <strong class="d-block">Verify</strong>
                                <small class="text-muted">Finder reviews your claim</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-start mb-3">
                            <span class="badge bg-primary rounded-pill me-2">3</span>
                            <div>
                                <strong class="d-block">Schedule</strong>
                                <small class="text-muted">Arrange pickup time</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-start">
                            <span class="badge bg-success rounded-pill me-2">4</span>
                            <div>
                                <strong class="d-block">Collect</strong>
                                <small class="text-muted">Get your item back!</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include __DIR__ . '/../layouts/footer-dashboard.php'; ?>
