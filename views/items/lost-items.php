<?php $pageTitle = 'Lost Items - ' . APP_NAME; ?>
<?php include __DIR__ . '/../layouts/header-dashboard.php'; ?>
<?php $user = getCurrentUser(); ?>

<div class="dashboard-wrapper">
    <?php include __DIR__ . '/../dashboard/partials/sidebar.php'; ?>

    <main class="dashboard-main">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <div>
                <h4 class="fw-semibold mb-1"><i class="bi bi-exclamation-triangle text-danger me-2"></i>Lost Items</h4>
                <p class="text-muted mb-0 small">Browse reported lost items and help reunite owners</p>
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
                <?php if (isLoggedIn()): ?>
                    <a href="<?= APP_URL ?>/lost-items/create" class="btn btn-danger btn-sm">
                        <i class="bi bi-plus-lg me-1"></i>Report Lost
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <?php displayFlash(); ?>

        <!-- Filters -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" name="search" placeholder="Search..."
                                value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="category_id" class="form-select">
                            <option value="">All Categories</option>
                            <?php foreach ($categories ?? [] as $cat): ?>
                                <?php if (is_array($cat)): ?>
                                    <option value="<?= $cat['id'] ?? '' ?>" <?= ($filters['category_id'] ?? '') == ($cat['id'] ?? '') ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name'] ?? '') ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="location_id" class="form-select">
                            <option value="">All Locations</option>
                            <?php foreach ($locations ?? [] as $loc): ?>
                                <?php if (is_array($loc)): ?>
                                    <option value="<?= $loc['id'] ?? '' ?>" <?= ($filters['location_id'] ?? '') == ($loc['id'] ?? '') ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($loc['name'] ?? '') ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <!-- status filter removed per UI update -->
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i
                                class="bi bi-filter me-1"></i>Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Items Grid -->
        <?php if (!empty($items['data'])): ?>
            <div class="row g-3">
                <?php foreach ($items['data'] as $item): ?>
                    <div class="col-md-6 col-lg-4">
                        <a href="<?= APP_URL ?>/lost-items/<?= $item['id'] ?>"
                            class="card h-100 border item-card text-decoration-none">
                            <div class="position-relative">
                                <?php
                                $primaryImg = '';
                                if (!empty($item['primary_image'])) {
                                    if (is_array($item['primary_image'])) {
                                        $primaryImg = $item['primary_image']['url'] ?? $item['primary_image']['file_name'] ?? '';
                                    } else {
                                        $primaryImg = $item['primary_image'];
                                    }
                                }
                                $primaryImg = normalizeImageUrl($primaryImg);
                                ?>
                                <?php if (!empty($primaryImg)): ?>
                                    <img src="<?= htmlspecialchars($primaryImg) ?>" class="card-img-top"
                                        style="height: 160px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                                        style="height: 160px;">
                                        <i class="bi bi-image text-muted display-4"></i>
                                    </div>
                                <?php endif; ?>
                                <span class="badge bg-danger position-absolute top-0 start-0 m-2">Lost</span>
                                <span
                                    class="badge <?= getStatusBadgeClass($item['status']) ?> position-absolute top-0 end-0 m-2"><?= ucfirst($item['status']) ?></span>
                                <?php if (!empty($item['reward_offered'])): ?>
                                    <span class="badge bg-warning text-dark position-absolute bottom-0 end-0 m-2"><i
                                            class="bi bi-gift me-1"></i>₱<?= number_format($item['reward_offered']) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body p-3">
                                <h6 class="card-title mb-1 text-truncate text-dark">
                                    <?= htmlspecialchars($item['title'] ?? '') ?>
                                </h6>
                                <p class="card-text text-muted small mb-2 text-truncate">
                                    <?= sanitizeForDisplay($item['description'] ?? '') ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center text-muted small">
                                    <span><i
                                            class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($item['last_seen_location'] ?? 'N/A') ?></span>
                                    <span><?= formatDate($item['last_seen_date'] ?? $item['created_at'] ?? '') ?></span>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php $totalPages = $items['pagination']['pages'] ?? $items['pagination']['totalPages'] ?? 1; ?>
            <?php $currentPage = $items['pagination']['page'] ?? 1; ?>
            <?php if ($totalPages > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                <a class="page-link"
                                    href="?page=<?= $i ?>&<?= http_build_query(array_diff_key($_GET, ['page' => '', 'status' => ''])) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>

        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-search text-muted display-4"></i>
                <p class="text-muted mt-3 mb-0">No lost items found</p>
                <small class="text-muted">Try adjusting your filters</small>
                <?php if (isLoggedIn()): ?>
                    <div class="mt-3">
                        <a href="<?= APP_URL ?>/lost-items/create" class="btn btn-danger"><i
                                class="bi bi-plus-lg me-2"></i>Report Lost Item</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<style>
    .item-card {
        transition: all 0.2s;
    }

    .item-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
    }
</style>

<?php include __DIR__ . '/../layouts/footer-dashboard.php'; ?>