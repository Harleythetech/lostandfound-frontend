<?php $pageTitle = 'Dashboard - ' . APP_NAME; ?>
<?php include __DIR__ . '/../layouts/header-dashboard.php'; ?>

<?php $user = getCurrentUser(); ?>
<?php $stats = $dashboard['stats'] ?? $dashboard ?? []; ?>
<?php $activeTab = $filters['tab'] ?? 'lost'; ?>

<?php
// Helper function to extract count from stats
if (!function_exists('getStatCount')) {
    function getStatCount($stats, $keys)
    {
        foreach ((array) $keys as $key) {
            if (isset($stats[$key])) {
                $value = $stats[$key];
                if (is_array($value)) {
                    return $value['count'] ?? $value['total'] ?? count($value);
                }
                if (is_numeric($value)) {
                    return (int) $value;
                }
            }
        }
        return 0;
    }
}

$lostItemsCount = getStatCount($stats, ['lost_items', 'lostItems']);
$foundItemsCount = getStatCount($stats, ['found_items', 'foundItems']);
$myClaimsCount = getStatCount($stats, ['my_claims', 'myClaims']);
$matchesCount = getStatCount($stats, ['matches', 'potentialMatches']);
?>

<div class="dashboard-wrapper">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>

    <main class="dashboard-main">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <div>
                <h4 class="fw-semibold mb-1">Home</h4>
                <p class="text-muted mb-0 small">Welcome back, <?= htmlspecialchars($user['first_name'] ?? 'User') ?>!
                </p>
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

        <!-- Quick Stats Row -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <a href="<?= APP_URL ?>/dashboard/my-lost-items"
                    class="card border-0 shadow-sm h-100 text-decoration-none stat-card-hover">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>
                            <div class="ms-3">
                                <h3 class="mb-0 fw-bold"><?= $lostItemsCount ?></h3>
                                <small class="text-muted">My Lost</small>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-lg-3">
                <a href="<?= APP_URL ?>/dashboard/my-found-items"
                    class="card border-0 shadow-sm h-100 text-decoration-none stat-card-hover">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-success bg-opacity-10 text-success">
                                <i class="bi bi-box-seam"></i>
                            </div>
                            <div class="ms-3">
                                <h3 class="mb-0 fw-bold"><?= $foundItemsCount ?></h3>
                                <small class="text-muted">My Found</small>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-lg-3">
                <a href="<?= APP_URL ?>/dashboard/my-claims"
                    class="card border-0 shadow-sm h-100 text-decoration-none stat-card-hover">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                                <i class="bi bi-hand-index"></i>
                            </div>
                            <div class="ms-3">
                                <h3 class="mb-0 fw-bold"><?= $myClaimsCount ?></h3>
                                <small class="text-muted">Claims</small>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-lg-3">
                <a href="<?= APP_URL ?>/dashboard/my-matches"
                    class="card border-0 shadow-sm h-100 text-decoration-none stat-card-hover">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-info bg-opacity-10 text-info">
                                <i class="bi bi-link-45deg"></i>
                            </div>
                            <div class="ms-3">
                                <h3 class="mb-0 fw-bold"><?= $matchesCount ?></h3>
                                <small class="text-muted">Matches</small>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form action="<?= APP_URL ?>/dashboard" method="GET" id="searchForm">
                    <input type="hidden" name="tab" value="<?= htmlspecialchars($activeTab) ?>" id="activeTabInput">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <div class="input-group">
                                <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" name="search" placeholder="Search items..."
                                    value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="category">
                                <option value="">All Categories</option>
                                <?php if (is_array($categories ?? null)): ?>
                                    <?php foreach ($categories as $cat): ?>
                                        <?php if (is_array($cat) && isset($cat['id'])): ?>
                                            <option value="<?= $cat['id'] ?>" <?= ($filters['category'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cat['name'] ?? '') ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="location">
                                <option value="">All Locations</option>
                                <?php if (is_array($locations ?? null)): ?>
                                    <?php foreach ($locations as $loc): ?>
                                        <?php if (is_array($loc) && isset($loc['id'])): ?>
                                            <option value="<?= $loc['id'] ?>" <?= ($filters['location'] ?? '') == $loc['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($loc['name'] ?? '') ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Browse Items Section with Tabs -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white p-0 border-bottom">
                <ul class="nav nav-tabs nav-fill border-0" id="itemTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-3 px-4 <?= $activeTab === 'lost' ? 'active' : '' ?>" id="lost-tab"
                            data-bs-toggle="tab" data-bs-target="#lost-items" type="button" role="tab"
                            aria-controls="lost-items" aria-selected="<?= $activeTab === 'lost' ? 'true' : 'false' ?>">
                            <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>
                            Lost Items
                            <span class="badge bg-danger ms-2"><?= count($lostItems ?? []) ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-3 px-4 <?= $activeTab === 'found' ? 'active' : '' ?>" id="found-tab"
                            data-bs-toggle="tab" data-bs-target="#found-items" type="button" role="tab"
                            aria-controls="found-items"
                            aria-selected="<?= $activeTab === 'found' ? 'true' : 'false' ?>">
                            <i class="bi bi-box-seam-fill text-success me-2"></i>
                            Found Items
                            <span class="badge bg-success ms-2"><?= count($foundItems ?? []) ?></span>
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-4">
                <div class="tab-content" id="itemTabsContent">
                    <!-- Lost Items Tab -->
                    <div class="tab-pane fade <?= $activeTab === 'lost' ? 'show active' : '' ?>" id="lost-items"
                        role="tabpanel" aria-labelledby="lost-tab">
                        <?php if (!empty($lostItems)): ?>
                            <div class="row g-3">
                                <?php foreach ($lostItems as $item): ?>
                                    <div class="col-md-6 col-lg-4">
                                        <a href="<?= APP_URL ?>/lost-items/<?= $item['id'] ?>"
                                            class="card h-100 border item-card text-decoration-none">
                                            <div class="position-relative">
                                                <?php
                                                $primaryImg = '';
                                                if (!empty($item['primary_image'])) {
                                                    $primaryImg = is_array($item['primary_image']) ? ($item['primary_image']['url'] ?? $item['primary_image']['file_name'] ?? '') : $item['primary_image'];
                                                } elseif (!empty($item['images'][0])) {
                                                    $imgData = $item['images'][0];
                                                    $primaryImg = is_array($imgData) ? ($imgData['url'] ?? $imgData['file_name'] ?? '') : $imgData;
                                                }
                                                $primaryImg = normalizeImageUrl($primaryImg);
                                                ?>
                                                <?php if (!empty($primaryImg)): ?>
                                                    <img src="<?= htmlspecialchars($primaryImg) ?>"
                                                        class="card-img-top item-card-img"
                                                        alt="<?= htmlspecialchars($item['title'] ?? '') ?>">
                                                <?php else: ?>
                                                    <div
                                                        class="card-img-top item-card-img bg-light d-flex align-items-center justify-content-center">
                                                        <i class="bi bi-image text-muted display-4"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <span class="badge bg-danger position-absolute top-0 start-0 m-2">Lost</span>
                                                <?php if (!empty($item['reward_offered'])): ?>
                                                    <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-2">
                                                        <i
                                                            class="bi bi-gift me-1"></i>₱<?= number_format($item['reward_offered']) ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="card-body p-3">
                                                <h6 class="card-title mb-1 text-truncate text-dark">
                                                    <?= htmlspecialchars($item['title'] ?? '') ?></h6>
                                                <p class="card-text small text-muted mb-2 text-truncate">
                                                    <?= htmlspecialchars($item['description'] ?? '') ?></p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">
                                                        <i
                                                            class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($item['last_seen_location'] ?? 'N/A') ?>
                                                    </small>
                                                    <small
                                                        class="text-muted"><?= formatDate($item['last_seen_date'] ?? $item['created_at'] ?? '') ?></small>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="text-center mt-4">
                                <a href="<?= APP_URL ?>/lost-items" class="btn btn-outline-danger">
                                    View All Lost Items <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-search text-muted display-4"></i>
                                <p class="text-muted mt-3 mb-0">No lost items found</p>
                                <small class="text-muted">Try adjusting your search filters</small>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Found Items Tab -->
                    <div class="tab-pane fade <?= $activeTab === 'found' ? 'show active' : '' ?>" id="found-items"
                        role="tabpanel" aria-labelledby="found-tab">
                        <?php if (!empty($foundItems)): ?>
                            <div class="row g-3">
                                <?php foreach ($foundItems as $item): ?>
                                    <div class="col-md-6 col-lg-4">
                                        <a href="<?= APP_URL ?>/found-items/<?= $item['id'] ?>"
                                            class="card h-100 border item-card text-decoration-none">
                                            <div class="position-relative">
                                                <?php
                                                $primaryImg = '';
                                                if (!empty($item['primary_image'])) {
                                                    $primaryImg = is_array($item['primary_image']) ? ($item['primary_image']['url'] ?? $item['primary_image']['file_name'] ?? '') : $item['primary_image'];
                                                } elseif (!empty($item['images'][0])) {
                                                    $imgData = $item['images'][0];
                                                    $primaryImg = is_array($imgData) ? ($imgData['url'] ?? $imgData['file_name'] ?? '') : $imgData;
                                                }
                                                $primaryImg = normalizeImageUrl($primaryImg);
                                                ?>
                                                <?php if (!empty($primaryImg)): ?>
                                                    <img src="<?= htmlspecialchars($primaryImg) ?>"
                                                        class="card-img-top item-card-img"
                                                        alt="<?= htmlspecialchars($item['title'] ?? '') ?>">
                                                <?php else: ?>
                                                    <div
                                                        class="card-img-top item-card-img bg-light d-flex align-items-center justify-content-center">
                                                        <i class="bi bi-image text-muted display-4"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <span class="badge bg-success position-absolute top-0 start-0 m-2">Found</span>
                                            </div>
                                            <div class="card-body p-3">
                                                <h6 class="card-title mb-1 text-truncate text-dark">
                                                    <?= htmlspecialchars($item['title'] ?? '') ?></h6>
                                                <p class="card-text small text-muted mb-2 text-truncate">
                                                    <?= htmlspecialchars($item['description'] ?? '') ?></p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">
                                                        <i
                                                            class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($item['found_location'] ?? 'N/A') ?>
                                                    </small>
                                                    <small
                                                        class="text-muted"><?= formatDate($item['found_date'] ?? $item['created_at'] ?? '') ?></small>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="text-center mt-4">
                                <a href="<?= APP_URL ?>/found-items" class="btn btn-outline-success">
                                    View All Found Items <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-search text-muted display-4"></i>
                                <p class="text-muted mt-3 mb-0">No found items found</p>
                                <small class="text-muted">Try adjusting your search filters</small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<style>
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .stat-card-hover:hover {
        transform: translateY(-2px);
        transition: transform 0.2s;
    }

    .stat-card-hover .card-body {
        transition: background 0.2s;
    }

    .item-card {
        transition: all 0.2s;
    }

    .item-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .item-card-img {
        height: 160px;
        object-fit: cover;
    }

    #itemTabs .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        color: #6b7280;
        font-weight: 500;
    }

    #itemTabs .nav-link:hover {
        border-color: #e5e7eb;
        color: #374151;
    }

    #itemTabs .nav-link.active {
        border-bottom-color: #0d6efd;
        color: #0d6efd;
        background: transparent;
    }
</style>

<script>
    // Update the hidden tab input when switching tabs (for search persistence)
    document.addEventListener('DOMContentLoaded', function () {
        const tabButtons = document.querySelectorAll('#itemTabs button[data-bs-toggle="tab"]');
        const tabInput = document.getElementById('activeTabInput');

        tabButtons.forEach(button => {
            button.addEventListener('shown.bs.tab', function (e) {
                const tabId = e.target.id.replace('-tab', '');
                if (tabInput) {
                    tabInput.value = tabId;
                }
            });
        });
    });
</script>

<?php include __DIR__ . '/../layouts/footer-dashboard.php'; ?>