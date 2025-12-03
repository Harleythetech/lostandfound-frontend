<?php $pageTitle = htmlspecialchars($item['title'] ?? $item['item_name']) . ' - ' . APP_NAME; ?>
<?php include __DIR__ . '/../layouts/header-dashboard.php'; ?>
<?php $user = getCurrentUser(); ?>

<div class="dashboard-wrapper">
    <?php include __DIR__ . '/../dashboard/partials/sidebar.php'; ?>
    
    <main class="dashboard-main">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="<?= APP_URL ?>/dashboard">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= APP_URL ?>/found-items">Found Items</a></li>
                        <li class="breadcrumb-item active text-truncate" style="max-width: 200px;"><?= htmlspecialchars($item['title'] ?? '') ?></li>
                    </ol>
                </nav>
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
            <!-- Image Gallery -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <?php if (!empty($item['images']) && count($item['images']) > 0): ?>
                        <div id="itemCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <?php foreach ($item['images'] as $index => $image): ?>
                                <?php 
                                    // Get image URL from the image object
                                    $imageUrl = is_array($image) ? ($image['url'] ?? '') : $image;
                                    // Replace backslashes with forward slashes for URL
                                    $imageUrl = str_replace('\\', '/', $imageUrl);
                                    // Remove any leading slashes or /api/ prefix to normalize
                                    $imageUrl = preg_replace('#^(/api/|/|api/)#', '', $imageUrl);
                                ?>
                                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                    <img src="<?= API_BASE_URL ?>/<?= htmlspecialchars($imageUrl) ?>" class="d-block w-100" style="height: 350px; object-fit: contain; background: #f8f9fa;">
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if (count($item['images']) > 1): ?>
                            <button class="carousel-control-prev" type="button" data-bs-target="#itemCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon bg-dark rounded-circle p-3"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#itemCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon bg-dark rounded-circle p-3"></span>
                            </button>
                            <?php endif; ?>
                        </div>
                        <?php else: ?>
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 350px;">
                            <div class="text-center text-muted">
                                <i class="bi bi-image display-1"></i>
                                <p class="mt-2">No images available</p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Item Details -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge bg-success"><i class="bi bi-box-seam me-1"></i>Found Item</span>
                            <span class="badge <?= getStatusBadgeClass($item['status']) ?>"><?= ucfirst($item['status']) ?></span>
                        </div>

                        <h4 class="fw-bold mb-3"><?= htmlspecialchars($item['title'] ?? $item['item_name']) ?></h4>

                        <div class="mb-3">
                            <h6 class="text-muted mb-2 small">Description</h6>
                            <p class="mb-0"><?= nl2br(htmlspecialchars($item['description'] ?? '')) ?></p>
                        </div>

                        <?php if (!empty($item['distinctive_features'])): ?>
                        <div class="mb-3">
                            <h6 class="text-muted mb-2 small">Distinctive Features</h6>
                            <p class="mb-0"><?= nl2br(htmlspecialchars($item['distinctive_features'])) ?></p>
                        </div>
                        <?php endif; ?>

                        <hr>

                        <div class="row g-3 small">
                            <div class="col-6">
                                <i class="bi bi-tag text-primary me-2"></i>
                                <span class="text-muted">Category:</span>
                                <span class="d-block fw-medium"><?= htmlspecialchars($item['category']['name'] ?? $item['category'] ?? 'N/A') ?></span>
                            </div>
                            <div class="col-6">
                                <i class="bi bi-geo-alt text-success me-2"></i>
                                <span class="text-muted">Location:</span>
                                <span class="d-block fw-medium"><?= htmlspecialchars($item['location']['name'] ?? $item['found_location'] ?? 'N/A') ?></span>
                            </div>
                            <div class="col-6">
                                <i class="bi bi-calendar text-success me-2"></i>
                                <span class="text-muted">Date Found:</span>
                                <span class="d-block fw-medium"><?= formatDate($item['found_date'] ?? $item['created_at']) ?></span>
                            </div>
                            <div class="col-6">
                                <i class="bi bi-clock text-warning me-2"></i>
                                <span class="text-muted">Posted:</span>
                                <span class="d-block fw-medium"><?= formatDate($item['created_at']) ?></span>
                            </div>
                        </div>

                        <hr>

                        <!-- Finder Info -->
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <?= strtoupper(substr($item['user']['first_name'] ?? 'U', 0, 1)) ?>
                            </div>
                            <div>
                                <small class="text-muted d-block">Found by</small>
                                <span class="fw-medium"><?= htmlspecialchars(($item['user']['first_name'] ?? '') . ' ' . ($item['user']['last_name'] ?? '')) ?></span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="d-grid gap-2">
                            <?php if (isLoggedIn()): ?>
                                <?php if (getCurrentUser()['id'] === ($item['user_id'] ?? null)): ?>
                                <a href="<?= APP_URL ?>/found-items/<?= $item['id'] ?>/edit" class="btn btn-primary btn-sm">
                                    <i class="bi bi-pencil me-2"></i>Edit Item
                                </a>
                                <a href="<?= APP_URL ?>/claims?item_id=<?= $item['id'] ?>" class="btn btn-outline-info btn-sm">
                                    <i class="bi bi-hand-index me-2"></i>View Claims (<?= $item['claims_count'] ?? 0 ?>)
                                </a>
                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="bi bi-trash me-2"></i>Delete Item
                                </button>
                                <?php else: ?>
                                <?php if (($item['status'] ?? '') === 'approved'): ?>
                                <a href="<?= APP_URL ?>/claims/create?found_item_id=<?= $item['id'] ?>" class="btn btn-warning">
                                    <i class="bi bi-hand-index me-2"></i>Claim This Item
                                </a>
                                <?php endif; ?>
                                <a href="<?= APP_URL ?>/messages/new?to=<?= $item['user_id'] ?>&item=<?= $item['id'] ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-chat-dots me-2"></i>Contact Finder
                                </a>
                                <?php endif; ?>
                            <?php else: ?>
                            <a href="<?= APP_URL ?>/login" class="btn btn-warning">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Login to Claim
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Potential Matches -->
        <?php if (!empty($matches)): ?>
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-link-45deg text-primary me-2"></i>Potential Matches</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <?php foreach ($matches as $match): ?>
                    <div class="col-md-4">
                        <div class="card h-100 border">
                            <div class="card-body p-3">
                                <h6 class="mb-2"><?= htmlspecialchars($match['lost_item']['title'] ?? '') ?></h6>
                                <p class="small text-muted mb-2"><?= truncate($match['lost_item']['description'] ?? '', 80) ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-info"><?= $match['match_score'] ?? 0 ?>% Match</span>
                                    <a href="<?= APP_URL ?>/lost-items/<?= $match['lost_item']['id'] ?>" class="btn btn-sm btn-outline-primary">View</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </main>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this item? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="<?= APP_URL ?>/found-items/<?= $item['id'] ?>/delete" method="POST">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer-dashboard.php'; ?>
