<?php $pageTitle = 'My Items - ' . APP_NAME; ?>
<?php include __DIR__ . '/../layouts/header-dashboard.php'; ?>
<?php $user = getCurrentUser(); ?>

<div class="dashboard-wrapper">
    <?php include __DIR__ . '/../dashboard/partials/sidebar.php'; ?>

    <main class="dashboard-main">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="fw-bold mb-0">My Items</h5>
                <small class="text-muted">Manage your reported items</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="<?= APP_URL ?>/items/create" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i>Report Item
                </a>
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

        <?php if (!empty($items)): ?>
            <!-- Items Table -->
            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Item</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Location</th>
                                <th>Posted</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($item['image'])):
                                                $imgSrc = is_array($item['image']) ? ($item['image']['url'] ?? $item['image']['image_path'] ?? $item['image']['file_name'] ?? '') : $item['image']; ?>
                                                <img src="<?= htmlspecialchars(normalizeImageUrl($imgSrc)) ?>" class="rounded me-3"
                                                    style="width: 40px; height: 40px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                                    style="width: 40px; height: 40px;">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <a href="<?= APP_URL ?>/items/<?= $item['id'] ?>"
                                                    class="text-decoration-none fw-semibold">
                                                    <?= htmlspecialchars($item['title'] ?? 'Untitled') ?>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-secondary"><?= htmlspecialchars($item['category'] ?? 'Uncategorized') ?></span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge <?= ($item['status'] ?? '') === 'lost' ? 'bg-danger' : (($item['status'] ?? '') === 'found' ? 'bg-success' : 'bg-info') ?>">
                                            <?= ucfirst($item['status'] ?? 'unknown') ?>
                                        </span>
                                    </td>
                                    <td class="text-muted small">
                                        <i class="bi bi-geo-alt me-1"></i>
                                        <?= htmlspecialchars($item['location'] ?? 'Unknown') ?>
                                    </td>
                                    <td class="text-muted small">
                                        <?= date('M d, Y', strtotime($item['created_at'] ?? 'now')) ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= APP_URL ?>/items/<?= $item['id'] ?>" class="btn btn-outline-primary"
                                                title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="<?= APP_URL ?>/items/<?= $item['id'] ?>/edit"
                                                class="btn btn-outline-secondary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="<?= APP_URL ?>/items/<?= $item['id'] ?>/delete" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Are you sure you want to delete this item?');">
                                                <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <!-- Empty State -->
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="mb-3">
                        <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="fw-bold">No items yet</h5>
                    <p class="text-muted mb-3">You haven't reported any items yet.</p>
                    <a href="<?= APP_URL ?>/items/create" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Report Your First Item
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </main>
</div>

<?php include __DIR__ . '/../layouts/footer-dashboard.php'; ?>