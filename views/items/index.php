<?php $pageTitle = 'Browse Items - ' . APP_NAME; ?>
<?php include __DIR__ . '/../layouts/header-dashboard.php'; ?>
<?php $user = getCurrentUser(); ?>

<div class="dashboard-wrapper">
    <?php include __DIR__ . '/../dashboard/partials/sidebar.php'; ?>
    
    <main class="dashboard-main">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <div>
                <h4 class="fw-semibold mb-1"><i class="bi bi-grid me-2"></i>Browse Items</h4>
                <p class="text-muted mb-0 small">Find lost items or report found items</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="<?= APP_URL ?>/items/create" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Report Item</a>
                <a href="<?= APP_URL ?>/notifications" class="btn btn-outline-secondary btn-sm position-relative">
                    <i class="bi bi-bell"></i>
                    <?php if (getUnreadNotificationCount() > 0): ?><span class="notification-dot"></span><?php endif; ?>
                </a>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleDarkMode()"><i class="bi bi-moon"></i></button>
            </div>
        </div>

        <?php displayFlash(); ?>

        <!-- Filters -->
        <div class="card border mb-4">
            <div class="card-body">
                <form action="<?= APP_URL ?>/items" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" name="search" placeholder="Search items..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="category">
                            <option value="">All Categories</option>
                            <option value="electronics" <?= ($_GET['category'] ?? '') === 'electronics' ? 'selected' : '' ?>>Electronics</option>
                            <option value="documents" <?= ($_GET['category'] ?? '') === 'documents' ? 'selected' : '' ?>>Documents</option>
                            <option value="accessories" <?= ($_GET['category'] ?? '') === 'accessories' ? 'selected' : '' ?>>Accessories</option>
                            <option value="clothing" <?= ($_GET['category'] ?? '') === 'clothing' ? 'selected' : '' ?>>Clothing</option>
                            <option value="keys" <?= ($_GET['category'] ?? '') === 'keys' ? 'selected' : '' ?>>Keys</option>
                            <option value="bags" <?= ($_GET['category'] ?? '') === 'bags' ? 'selected' : '' ?>>Bags</option>
                            <option value="others" <?= ($_GET['category'] ?? '') === 'others' ? 'selected' : '' ?>>Others</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="status">
                            <option value="">All Status</option>
                            <option value="lost" <?= ($_GET['status'] ?? '') === 'lost' ? 'selected' : '' ?>>Lost</option>
                            <option value="found" <?= ($_GET['status'] ?? '') === 'found' ? 'selected' : '' ?>>Found</option>
                            <option value="returned" <?= ($_GET['status'] ?? '') === 'returned' ? 'selected' : '' ?>>Returned</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-funnel me-1"></i>Filter</button>
                            <a href="<?= APP_URL ?>/items" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Items Grid -->
        <?php if (!empty($items)): ?>
        <div class="row g-3">
            <?php foreach ($items as $item): ?>
            <div class="col-md-6 col-lg-4">
                <a href="<?= APP_URL ?>/items/<?= $item['id'] ?>" class="card h-100 border item-card text-decoration-none">
                    <div class="position-relative">
                        <?php if (!empty($item['image'])): ?>
                        <img src="<?= APP_URL ?>/<?= htmlspecialchars($item['image']) ?>" class="card-img-top" style="height: 160px; object-fit: cover;">
                        <?php else: ?>
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 160px;">
                            <i class="bi bi-image text-muted display-4"></i>
                        </div>
                        <?php endif; ?>
                        <span class="badge position-absolute top-0 end-0 m-2 <?= ($item['status'] ?? '') === 'lost' ? 'bg-danger' : (($item['status'] ?? '') === 'found' ? 'bg-success' : 'bg-info') ?>">
                            <?= ucfirst($item['status'] ?? 'unknown') ?>
                        </span>
                    </div>
                    <div class="card-body p-3">
                        <span class="badge bg-secondary mb-2 small"><?= htmlspecialchars($item['category'] ?? 'Uncategorized') ?></span>
                        <h6 class="card-title text-dark mb-1"><?= htmlspecialchars($item['title'] ?? 'Untitled') ?></h6>
                        <p class="card-text text-muted small mb-2 text-truncate"><?= htmlspecialchars($item['description'] ?? '') ?></p>
                        <div class="d-flex justify-content-between text-muted small">
                            <span><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($item['location'] ?? 'Unknown') ?></span>
                            <span><?= date('M d', strtotime($item['created_at'] ?? 'now')) ?></span>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="bi bi-inbox text-muted display-4"></i>
            <h5 class="mt-3">No items found</h5>
            <p class="text-muted">Try adjusting your filters or report a new item.</p>
            <a href="<?= APP_URL ?>/items/create" class="btn btn-primary mt-2">
                <i class="bi bi-plus-circle me-2"></i>Report Item
            </a>
        </div>
        <?php endif; ?>
    </main>
</div>

<style>
.item-card { transition: all 0.2s; }
.item-card:hover { transform: translateY(-4px); box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important; }
</style>

<?php include __DIR__ . '/../layouts/footer-dashboard.php'; ?>
