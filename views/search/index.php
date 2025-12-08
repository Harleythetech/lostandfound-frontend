<?php $pageTitle = 'Search - ' . APP_NAME; ?>
<?php include __DIR__ . '/../layouts/header-dashboard.php'; ?>
<?php $user = getCurrentUser(); ?>

<div class="dashboard-wrapper">
    <?php include __DIR__ . '/../dashboard/partials/sidebar.php'; ?>

    <main class="dashboard-main">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <div>
                <h4 class="fw-semibold mb-1"><i class="bi bi-search me-2"></i>Search Items</h4>
                <p class="text-muted mb-0 small">Find lost and found items across our campus</p>
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

        <!-- Search Form -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="<?= APP_URL ?>/search" id="searchForm">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" name="q" placeholder="What are you looking for?"
                                    value="<?= htmlspecialchars($query ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="type" class="form-select">
                                <option value="">All Items</option>
                                <option value="lost" <?= ($filters['type'] ?? '') === 'lost' ? 'selected' : '' ?>>Lost
                                    Items</option>
                                <option value="found" <?= ($filters['type'] ?? '') === 'found' ? 'selected' : '' ?>>Found
                                    Items</option>
                            </select>
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
                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-grow-1"><i
                                        class="bi bi-search me-1"></i>Search</button>
                                <a href="<?= APP_URL ?>/search" class="btn btn-outline-secondary"><i
                                        class="bi bi-x-lg"></i></a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Search Results -->
        <?php if (isset($results) && (!empty($query) || !empty($filters['category_id']) || !empty($filters['location_id']))): ?>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <p class="text-muted mb-0 small">
                    Found <strong><?= $results['total'] ?? 0 ?></strong> results
                    <?php if (!empty($query)): ?> for "<strong><?= htmlspecialchars($query) ?></strong>"<?php endif; ?>
                </p>
            </div>

            <?php if (!empty($results['data'])): ?>
                <div class="row g-3">
                    <?php foreach ($results['data'] as $item): ?>
                        <?php
                        $itemType = $item['type'] ?? $item['item_type'] ?? 'found';
                        $itemImage = '';
                        if (!empty($item['primary_image'])) {
                            $itemImage = is_array($item['primary_image']) ? ($item['primary_image']['url'] ?? $item['primary_image']['file_name'] ?? '') : $item['primary_image'];
                        } elseif (!empty($item['images'][0])) {
                            $imgData = $item['images'][0];
                            $itemImage = is_array($imgData) ? ($imgData['url'] ?? $imgData['file_name'] ?? '') : $imgData;
                        }
                        $itemImage = normalizeImageUrl($itemImage);
                        $itemLocation = $item['location_name'] ?? $item['last_seen_location'] ?? 'N/A';
                        ?>
                        <div class="col-md-6 col-lg-4">
                            <a href="<?= APP_URL ?>/<?= $itemType ?>-items/<?= $item['id'] ?>"
                                class="card h-100 border item-card text-decoration-none">
                                <div class="position-relative">
                                    <?php if (!empty($itemImage)): ?>
                                        <img src="<?= htmlspecialchars($itemImage) ?>" class="card-img-top"
                                            style="height: 160px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                                            style="height: 160px;">
                                            <i class="bi bi-image text-muted display-4"></i>
                                        </div>
                                    <?php endif; ?>
                                    <span
                                        class="badge <?= $itemType === 'lost' ? 'bg-danger' : 'bg-success' ?> position-absolute top-0 start-0 m-2"><?= ucfirst($itemType) ?></span>
                                </div>
                                <div class="card-body p-3">
                                    <h6 class="card-title mb-1 text-truncate text-dark">
                                        <?= htmlspecialchars($item['title'] ?? '') ?></h6>
                                    <p class="card-text text-muted small mb-2 text-truncate">
                                        <?= htmlspecialchars($item['description'] ?? '') ?></p>
                                    <div class="d-flex justify-content-between text-muted small">
                                        <span><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($itemLocation) ?></span>
                                        <span><?= formatDate($item['created_at'] ?? '') ?></span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php
                $pagination = $results['pagination'] ?? [];
                $currentPage = (int) ($pagination['page'] ?? 1);
                $totalPages = (int) ($pagination['pages'] ?? 1);
                ?>
                <?php if ($totalPages > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= min($totalPages, 10); $i++): ?>
                                <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                    <a class="page-link"
                                        href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>

            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-search text-muted display-4"></i>
                    <p class="text-muted mt-3">No results found</p>
                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <a href="<?= APP_URL ?>/lost-items" class="btn btn-outline-danger btn-sm">Browse Lost</a>
                        <a href="<?= APP_URL ?>/found-items" class="btn btn-outline-success btn-sm">Browse Found</a>
                    </div>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-search text-primary display-4"></i>
                <p class="text-muted mt-3">Enter keywords or use filters to find items</p>
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