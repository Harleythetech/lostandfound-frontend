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
                        <li class="breadcrumb-item"><a href="<?= APP_URL ?>/lost-items">Lost Items</a></li>
                        <li class="breadcrumb-item active text-truncate" style="max-width: 200px;">
                            <?= htmlspecialchars($item['title'] ?? '') ?>
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
                                        $imageUrl = is_array($image) ? ($image['url'] ?? $image['file_name'] ?? '') : $image;
                                        $imageUrl = normalizeImageUrl($imageUrl);
                                        ?>
                                        <?php if (!empty($imageUrl)): ?>
                                            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                                <img src="<?= htmlspecialchars($imageUrl) ?>" class="d-block w-100"
                                                    style="height: 350px; object-fit: contain; background: #f8f9fa;">
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                                <?php if (count($item['images']) > 1): ?>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#itemCarousel"
                                        data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon bg-dark rounded-circle p-3"></span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#itemCarousel"
                                        data-bs-slide="next">
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
                            <span class="badge bg-danger"><i class="bi bi-exclamation-triangle me-1"></i>Lost
                                Item</span>
                            <span
                                class="badge <?= getStatusBadgeClass($item['status']) ?>"><?= ucfirst($item['status']) ?></span>
                        </div>

                        <h4 class="fw-bold mb-3"><?= htmlspecialchars($item['title'] ?? $item['item_name']) ?></h4>

                        <div class="mb-3">
                            <h6 class="text-muted mb-2 small">Description</h6>
                            <p class="mb-0"><?= nl2br(sanitizeForDisplay($item['description'] ?? '')) ?></p>
                        </div>

                        <?php if (!empty($item['distinctive_features'])): ?>
                            <div class="mb-3">
                                <h6 class="text-muted mb-2 small">Distinctive Features</h6>
                                <p class="mb-0"><?= nl2br(sanitizeForDisplay($item['distinctive_features'] ?? '')) ?></p>
                            </div>
                        <?php endif; ?>

                        <hr>

                        <div class="row g-3 small">
                            <div class="col-6">
                                <i class="bi bi-tag text-primary me-2"></i>
                                <span class="text-muted">Category:</span>
                                <span
                                    class="d-block fw-medium"><?= htmlspecialchars($item['category']['name'] ?? $item['category'] ?? 'N/A') ?></span>
                            </div>
                            <div class="col-6">
                                <i class="bi bi-geo-alt text-danger me-2"></i>
                                <span class="text-muted">Location:</span>
                                <span
                                    class="d-block fw-medium"><?= htmlspecialchars($item['location']['name'] ?? $item['last_seen_location'] ?? 'N/A') ?></span>
                            </div>
                            <div class="col-6">
                                <i class="bi bi-calendar text-success me-2"></i>
                                <span class="text-muted">Date Lost:</span>
                                <span
                                    class="d-block fw-medium"><?= formatDate($item['last_seen_date'] ?? $item['created_at']) ?></span>
                            </div>
                            <div class="col-6">
                                <i class="bi bi-clock text-warning me-2"></i>
                                <span class="text-muted">Posted:</span>
                                <span class="d-block fw-medium"><?= formatDate($item['created_at']) ?></span>
                            </div>
                        </div>

                        <hr>

                        <!-- Reporter Info -->
                        <?php
                        $reporterDisplay = '';
                        if (!empty($item['user']) && is_array($item['user'])) {
                            $u = $item['user'];
                            $reporterDisplay = $u['name'] ?? trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''));
                        }
                        if (empty($reporterDisplay)) {
                            $reporterDisplay = $item['reported_by_name'] ?? $item['reporter_name'] ?? trim(($item['reported_by_first_name'] ?? '') . ' ' . ($item['reported_by_last_name'] ?? ''));
                        }
                        ?>
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                style="width: 40px; height: 40px;">
                                <?= strtoupper(substr($reporterDisplay ?? 'U', 0, 1)) ?>
                            </div>
                            <div>
                                <small class="text-muted d-block">Reported by</small>
                                <span class="fw-medium"><?= sanitizeForDisplay($reporterDisplay) ?></span>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <?php
                        $hasEmail = !empty($item['email']) && ($item['contact_via_email'] ?? false);
                        $hasPhone = !empty($item['phone_number']) && ($item['contact_via_phone'] ?? false);
                        ?>

                        <!-- Actions -->
                        <div class="d-grid gap-2">
                            <?php if (isLoggedIn()): ?>
                                <?php if (getCurrentUser()['id'] === ($item['user_id'] ?? null)): ?>
                                    <a href="<?= APP_URL ?>/lost-items/<?= $item['id'] ?>/edit" class="btn btn-primary btn-sm">
                                        <i class="bi bi-pencil me-2"></i>Edit Item
                                    </a>
                                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#deleteModal">
                                        <i class="bi bi-trash me-2"></i>Delete Item
                                    </button>
                                <?php elseif ($hasEmail || $hasPhone): ?>
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#contactModal">
                                        <i class="bi bi-chat-dots me-2"></i>Contact Owner
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#noContactModal">
                                        <i class="bi bi-chat-dots me-2"></i>Contact Owner
                                    </button>
                                <?php endif; ?>
                            <?php else: ?>
                                <a href="<?= APP_URL ?>/login" class="btn btn-primary btn-sm">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Login to Contact
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
                                        <h6 class="mb-2"><?= htmlspecialchars($match['found_item']['title'] ?? '') ?></h6>
                                        <p class="small text-muted mb-2">
                                            <?= truncate($match['found_item']['description'] ?? '', 80) ?>
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-info"><?= $match['match_score'] ?? 0 ?>% Match</span>
                                            <a href="<?= APP_URL ?>/found-items/<?= $match['found_item']['id'] ?>"
                                                class="btn btn-sm btn-outline-primary">View</a>
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
                <form action="<?= APP_URL ?>/lost-items/<?= $item['id'] ?>/delete" method="POST">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Contact Owner Modal -->
<?php
$hasEmail = !empty($item['email']) && ($item['contact_via_email'] ?? false);
$hasPhone = !empty($item['phone_number']) && ($item['contact_via_phone'] ?? false);
?>
<?php if ($hasEmail || $hasPhone): ?>
    <div class="modal fade" id="contactModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-lines-fill me-2"></i>Contact Owner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">Choose how you'd like to contact
                        <?= sanitizeForDisplay(explode(' ', $reporterDisplay ?? 'the owner')[0]) ?>:
                    </p>

                    <?php if ($hasEmail): ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div>
                                        <i class="bi bi-envelope-fill text-primary me-2"></i>
                                        <strong>Email</strong>
                                    </div>
                                </div>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm bg-light"
                                        value="<?= htmlspecialchars($item['email']) ?>" id="emailToCopy" readonly>
                                    <button class="btn btn-outline-secondary btn-sm" type="button"
                                        onclick="copyToClipboard('emailToCopy', this)">
                                        <i class="bi bi-clipboard"></i> Copy
                                    </button>
                                </div>
                                <div class="mt-2">
                                    <a href="https://mail.google.com/mail/?view=cm&to=<?= urlencode($item['email']) ?>&su=<?= urlencode('Regarding your lost item: ' . ($item['title'] ?? '')) ?>"
                                        target="_blank" class="btn btn-danger btn-sm w-100">
                                        <i class="bi bi-google me-1"></i> Open in Gmail
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($hasPhone): ?>
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div>
                                        <i class="bi bi-phone-fill text-success me-2"></i>
                                        <strong>Phone</strong>
                                    </div>
                                </div>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm bg-light"
                                        value="<?= htmlspecialchars($item['phone_number']) ?>" id="phoneToCopy" readonly>
                                    <button class="btn btn-outline-secondary btn-sm" type="button"
                                        onclick="copyToClipboard('phoneToCopy', this)">
                                        <i class="bi bi-clipboard"></i> Copy
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(inputId, btn) {
            const input = document.getElementById(inputId);
            input.select();
            input.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(input.value).then(() => {
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<i class="bi bi-check"></i> Copied!';
                btn.classList.remove('btn-outline-secondary');
                btn.classList.add('btn-success');
                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-outline-secondary');
                }, 2000);
            });
        }
    </script>
<?php endif; ?>

<!-- No Contact Info Modal -->
<div class="modal fade" id="noContactModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center pt-0">
                <i class="bi bi-exclamation-circle text-warning" style="font-size: 3rem;"></i>
                <h5 class="mt-3">No Contact Information</h5>
                <p class="text-muted small mb-0">The owner has not provided any contact information for this item.</p>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer-dashboard.php'; ?>