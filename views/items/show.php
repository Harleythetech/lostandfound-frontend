<?php $pageTitle = ($item['title'] ?? 'Item Details') . ' - ' . APP_NAME; ?>
<?php include __DIR__ . '/../layouts/header-dashboard.php'; ?>
<?php $user = getCurrentUser(); ?>

<div class="dashboard-wrapper">
    <?php include __DIR__ . '/../dashboard/partials/sidebar.php'; ?>

    <main class="dashboard-main">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item"><a href="<?= APP_URL ?>/items"
                                class="text-decoration-none">Items</a></li>
                        <li class="breadcrumb-item active"><?= htmlspecialchars($item['title'] ?? 'Item Details') ?>
                        </li>
                    </ol>
                </nav>
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

        <!-- Item Header -->
        <div class="d-flex align-items-start justify-content-between mb-4">
            <div>
                <div class="mb-2">
                    <span
                        class="badge <?= ($item['status'] ?? '') === 'lost' ? 'bg-danger' : (($item['status'] ?? '') === 'found' ? 'bg-success' : 'bg-info') ?>">
                        <?= ucfirst($item['status'] ?? 'unknown') ?>
                    </span>
                    <span
                        class="badge bg-secondary ms-1"><?= htmlspecialchars($item['category'] ?? 'Uncategorized') ?></span>
                </div>
                <h4 class="fw-bold mb-0"><?= htmlspecialchars($item['title'] ?? 'Untitled') ?></h4>
            </div>
            <?php if (isLoggedIn() && (getCurrentUser()['id'] ?? null) == ($item['user_id'] ?? null)): ?>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button"
                        data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?= APP_URL ?>/items/<?= $item['id'] ?>/edit">
                                <i class="bi bi-pencil me-2"></i>Edit
                            </a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form action="<?= APP_URL ?>/items/<?= $item['id'] ?>/delete" method="POST" class="d-inline"
                                onsubmit="return confirm('Are you sure you want to delete this item?');">
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-trash me-2"></i>Delete
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>

        <div class="row g-4">
            <!-- Item Image -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm">
                    <?php
                    $displayImage = '';
                    if (!empty($item['image'])) {
                        $displayImage = is_array($item['image']) ? ($item['image']['url'] ?? $item['image']['image_path'] ?? $item['image']['file_name'] ?? '') : $item['image'];
                        $displayImage = normalizeImageUrl($displayImage);
                    }
                    ?>
                    <?php if (!empty($displayImage)): ?>
                        <img src="<?= htmlspecialchars($displayImage) ?>" class="card-img-top img-fluid"
                            alt="<?= htmlspecialchars($item['title']) ?>" style="max-height: 400px; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 300px;">
                            <div class="text-center text-muted">
                                <i class="bi bi-image" style="font-size: 3rem;"></i>
                                <p class="mb-0 mt-2">No image available</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Action Buttons -->
                <div class="d-grid gap-2 mt-3">
                    <?php if (isLoggedIn() && ($item['status'] ?? '') !== 'returned'): ?>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#claimModal">
                            <i class="bi bi-hand-thumbs-up me-2"></i>
                            <?= ($item['status'] ?? '') === 'lost' ? 'I Found This Item' : 'This Is My Item' ?>
                        </button>
                    <?php endif; ?>
                    <a href="<?= APP_URL ?>/items" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Items
                    </a>
                </div>
            </div>

            <!-- Item Details -->
            <div class="col-lg-7">
                <!-- Description Card -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <h6 class="card-title fw-bold mb-3">
                            <i class="bi bi-file-text text-primary me-2"></i>Description
                        </h6>
                        <p class="card-text mb-0">
                            <?= nl2br(sanitizeForDisplay($item['description'] ?? 'No description provided.')) ?>
                        </p>
                    </div>
                </div>

                <!-- Details Card -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <h6 class="card-title fw-bold mb-3">
                            <i class="bi bi-info-circle text-primary me-2"></i>Details
                        </h6>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="bi bi-geo-alt text-primary"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Location</small>
                                        <span
                                            class="fw-medium"><?= htmlspecialchars($item['location'] ?? 'Not specified') ?></span>
                                    </div>
                                </div>
                            </div>
                            <?php if (!empty($item['date_lost'])): ?>
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger bg-opacity-10 rounded-circle p-2 me-3">
                                            <i class="bi bi-calendar-x text-danger"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Date Lost</small>
                                            <span
                                                class="fw-medium"><?= date('M d, Y', strtotime($item['date_lost'])) ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($item['date_found'])): ?>
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                            <i class="bi bi-calendar-check text-success"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Date Found</small>
                                            <span
                                                class="fw-medium"><?= date('M d, Y', strtotime($item['date_found'])) ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center">
                                    <div class="bg-secondary bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="bi bi-clock text-secondary"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Posted</small>
                                        <span
                                            class="fw-medium"><?= date('M d, Y', strtotime($item['created_at'] ?? 'now')) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (!empty($item['contact_info'])): ?>
                    <!-- Contact Card -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title fw-bold mb-3">
                                <i class="bi bi-telephone text-primary me-2"></i>Contact Information
                            </h6>
                            <p class="card-text mb-0"><?= htmlspecialchars($item['contact_info']) ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<!-- Claim Modal -->
<div class="modal fade" id="claimModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <?= ($item['status'] ?? '') === 'lost' ? 'Report Found Item' : 'Claim This Item' ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>
                    <?php if (($item['status'] ?? '') === 'lost'): ?>
                        If you found this item, please contact the owner using the contact information provided above or
                        leave your contact details below.
                    <?php else: ?>
                        If this is your item, please provide details to verify ownership. The finder will be notified.
                    <?php endif; ?>
                </p>
                <?php if (!empty($item['contact_info'])): ?>
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Contact: <?= htmlspecialchars($item['contact_info']) ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer-dashboard.php'; ?>