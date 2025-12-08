<?php $pageTitle = 'Found Items - ' . APP_NAME; ?>
<?php include __DIR__ . '/../layouts/header-dashboard.php'; ?>
<?php $user = getCurrentUser(); ?>

<div class="dashboard-wrapper">
    <?php include __DIR__ . '/../dashboard/partials/sidebar.php'; ?>

    <main class="dashboard-main">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <div>
                <h4 class="fw-semibold mb-1"><i class="bi bi-box-seam text-success me-2"></i>Found Items</h4>
                <p class="text-muted mb-0 small">Browse found items and claim yours</p>
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
                <?php if (isLoggedIn()): ?>
                    <a href="<?= APP_URL ?>/found-items/create" class="btn btn-success btn-sm">
                        <i class="bi bi-plus-lg me-1"></i>Report Found
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
                    <div class="col-md-2">
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
                    <div class="col-md-2">
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
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>
                                Pending</option>
                            <option value="approved" <?= ($filters['status'] ?? '') === 'approved' ? 'selected' : '' ?>>
                                Approved</option>
                            <option value="claimed" <?= ($filters['status'] ?? '') === 'claimed' ? 'selected' : '' ?>>
                                Claimed</option>
                        </select>
                    </div>
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
                        <a href="<?= APP_URL ?>/found-items/<?= $item['id'] ?>"
                            class="card h-100 border item-card text-decoration-none">
                            <div class="position-relative">
                                <?php
                                $img = '';
                                if (!empty($item['primary_image'])) {
                                    if (is_array($item['primary_image'])) {
                                        $img = $item['primary_image']['url'] ?? $item['primary_image']['file_name'] ?? '';
                                    } else {
                                        $img = $item['primary_image'];
                                    }
                                } elseif (!empty($item['images'][0])) {
                                    $img = is_array($item['images'][0]) ? ($item['images'][0]['url'] ?? $item['images'][0]['file_name'] ?? '') : $item['images'][0];
                                }
                                $img = normalizeImageUrl($img);
                                ?>
                                <?php if (!empty($img)): ?>
                                    <img src="<?= htmlspecialchars($img) ?>" class="card-img-top"
                                        style="height: 160px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                                        style="height: 160px;">
                                        <i class="bi bi-image text-muted display-4"></i>
                                    </div>
                                <?php endif; ?>
                                <span class="badge bg-success position-absolute top-0 start-0 m-2">Found</span>
                                <span
                                    class="badge <?= getStatusBadgeClass($item['status']) ?> position-absolute top-0 end-0 m-2"><?= ucfirst($item['status']) ?></span>
                            </div>
                            <div class="card-body p-3">
                                <h6 class="card-title mb-1 text-truncate text-dark">
                                    <?= htmlspecialchars($item['title'] ?? '') ?>
                                </h6>
                                <p class="card-text text-muted small mb-2 text-truncate">
                                    <?= htmlspecialchars($item['description'] ?? '') ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center text-muted small">
                                    <span><i
                                            class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($item['found_location'] ?? $item['location_name'] ?? 'N/A') ?></span>
                                    <span><?= formatDate($item['found_date'] ?? $item['created_at'] ?? '') ?></span>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php $totalPages = $items['pagination']['totalPages'] ?? $items['pagination']['pages'] ?? 1; ?>
            <?php $currentPage = $items['pagination']['page'] ?? 1; ?>
            <?php if ($totalPages > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                <a class="page-link"
                                    href="?page=<?= $i ?>&<?= http_build_query(array_diff_key($_GET, ['page' => ''])) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>

        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-box-seam text-muted display-4"></i>
                <p class="text-muted mt-3 mb-0">No found items</p>
                <small class="text-muted">Try adjusting your filters</small>
                <?php if (isLoggedIn()): ?>
                    <div class="mt-3">
                        <a href="<?= APP_URL ?>/found-items/create" class="btn btn-success"><i
                                class="bi bi-plus-lg me-2"></i>Report Found Item</a>
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