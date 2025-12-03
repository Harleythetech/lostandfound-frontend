<?php $pageTitle = 'My Matches - ' . APP_NAME; ?>
<?php include __DIR__ . '/../layouts/header-dashboard.php'; ?>

<?php
// Helper function to normalize image URL for matches
function normalizeMatchImageUrl($imgPath) {
    if (empty($imgPath)) return '';
    $imgPath = str_replace('\\', '/', $imgPath);
    $imgPath = preg_replace('#^/api/#', '', $imgPath);
    $imgPath = ltrim($imgPath, '/');
    if (preg_match('/^https?:\/\//', $imgPath)) {
        return $imgPath;
    }
    if (strpos($imgPath, 'uploads/') === 0) {
        return API_BASE_URL . '/' . $imgPath;
    }
    return API_BASE_URL . '/uploads/' . $imgPath;
}

// Helper to get image URL from various structures
function getMatchItemImage($item, $images = null) {
    // Check for images array in item or passed separately
    $imgArray = $images ?? $item['images'] ?? [];
    
    if (!empty($imgArray)) {
        // Find primary or use first
        $primary = null;
        foreach ($imgArray as $img) {
            if (isset($img['is_primary']) && ($img['is_primary'] === true || $img['is_primary'] === 1 || $img['is_primary'] === '1')) {
                $primary = $img;
                break;
            }
        }
        $primary = $primary ?? $imgArray[0] ?? null;
        if ($primary) {
            $url = $primary['url'] ?? $primary['image_path'] ?? $primary['file_name'] ?? '';
            if ($url) return normalizeMatchImageUrl($url);
        }
    }
    
    // Check for primary_image field
    if (!empty($item['primary_image'])) {
        return normalizeMatchImageUrl($item['primary_image']);
    }
    
    return APP_URL . '/assets/img/no-image.svg';
}
?>

<div class="dashboard-wrapper">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    
    <main class="dashboard-main">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <div>
                <h4 class="fw-semibold mb-1">My Matches</h4>
                <p class="text-muted mb-0 small">Potential matches between your items and others</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="<?= APP_URL ?>/notifications" class="btn ui-btn-secondary btn-sm position-relative" title="Notifications">
                    <i class="bi bi-bell"></i>
                    <?php if (getUnreadNotificationCount() > 0): ?><span class="notification-dot"></span><?php endif; ?>
                </a>
                <button type="button" class="btn ui-btn-secondary btn-sm" onclick="toggleDarkMode()" title="Toggle Dark Mode">
                    <i class="bi bi-moon" id="headerThemeIcon"></i>
                </button>
            </div>
        </div>
        
        <?php displayFlash(); ?>
            
        <!-- Filter Tabs -->
        <ul class="nav nav-tabs mb-4" role="tablist">
            <li class="nav-item">
                <a class="nav-link <?= empty($type) ? 'active' : '' ?>" href="<?= APP_URL ?>/dashboard/my-matches">
                    All Matches
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($type ?? '') === 'lost' ? 'active' : '' ?>" href="<?= APP_URL ?>/dashboard/my-matches?type=lost">
                    <i class="bi bi-search me-1"></i>For My Lost Items
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($type ?? '') === 'found' ? 'active' : '' ?>" href="<?= APP_URL ?>/dashboard/my-matches?type=found">
                    <i class="bi bi-box-seam me-1"></i>For My Found Items
                </a>
            </li>
        </ul>
        
        <!-- Matches List -->
        <?php 
        $matchesList = $matches['data'] ?? $matches;
        if (!is_array($matchesList)) $matchesList = [];
        ?>
        <?php if (empty($matchesList)): ?>
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-link-45deg display-1 text-muted mb-3"></i>
                    <h5>No Matches Yet</h5>
                    <p class="text-muted mb-4">
                        The system automatically finds potential matches between lost and found items. 
                        Check back later or report more items to increase matching chances.
                    </p>
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="<?= APP_URL ?>/lost-items/create" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i>Report Lost Item
                        </a>
                        <a href="<?= APP_URL ?>/found-items/create" class="btn btn-success">
                            <i class="bi bi-plus-lg me-1"></i>Report Found Item
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($matchesList as $match): ?>
                    <div class="col-12">
                        <div class="card shadow-sm border-start border-5 border-info">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <!-- Your Item (Lost Item) -->
                                    <div class="col-md-5">
                                        <div class="d-flex align-items-center">
                                            <?php 
                                            // Handle both nested and flattened structures
                                            $lostItem = $match['lost_item'] ?? null;
                                            $lostItemId = $lostItem['id'] ?? $match['lost_item_id'] ?? '';
                                            $lostItemTitle = $lostItem['item_name'] ?? $lostItem['title'] ?? $match['lost_item_title'] ?? 'Unknown Item';
                                            $lostItemDesc = $lostItem['description'] ?? $match['lost_item_description'] ?? '';
                                            
                                            // Get lost item image
                                            $lostItemImages = $match['lost_item_images'] ?? $lostItem['images'] ?? [];
                                            $lostItemPrimary = $match['lost_item_primary_image'] ?? $lostItem['primary_image'] ?? '';
                                            
                                            if (!empty($lostItemImages)) {
                                                $lostImageUrl = getMatchItemImage(['images' => $lostItemImages]);
                                            } elseif (!empty($lostItemPrimary)) {
                                                $lostImageUrl = normalizeMatchImageUrl($lostItemPrimary);
                                            } elseif ($lostItem) {
                                                $lostImageUrl = getMatchItemImage($lostItem);
                                            } else {
                                                $lostImageUrl = APP_URL . '/assets/img/no-image.svg';
                                            }
                                            ?>
                                            <img src="<?= htmlspecialchars($lostImageUrl) ?>" 
                                                 class="rounded me-3" 
                                                 style="width: 80px; height: 80px; object-fit: cover;"
                                                 alt="<?= htmlspecialchars($lostItemTitle) ?>">
                                            <div>
                                                <span class="badge bg-danger mb-1">
                                                    Lost Item
                                                </span>
                                                <h6 class="mb-1">
                                                    <a href="<?= APP_URL ?>/lost-items/<?= $lostItemId ?>" class="text-decoration-none">
                                                        <?= htmlspecialchars($lostItemTitle) ?>
                                                    </a>
                                                </h6>
                                                <p class="text-muted small mb-0 text-truncate" style="max-width: 200px;">
                                                    <?= htmlspecialchars($lostItemDesc) ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Match Indicator -->
                                    <div class="col-md-2 text-center">
                                        <div class="match-indicator">
                                            <i class="bi bi-arrow-left-right text-info" style="font-size: 1.5rem;"></i>
                                            <div class="mt-1">
                                                <span class="badge bg-info">
                                                    <?= number_format(($match['match_score'] ?? $match['confidence'] ?? $match['similarity_score'] ?? 0.75) * 100) ?>% Match
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Matched Item (Found Item) -->
                                    <div class="col-md-5">
                                        <div class="d-flex align-items-center justify-content-end">
                                            <div class="text-end">
                                                <?php 
                                                // Handle both nested and flattened structures for found item
                                                $foundItem = $match['found_item'] ?? null;
                                                $foundItemId = $foundItem['id'] ?? $match['found_item_id'] ?? '';
                                                $foundItemTitle = $foundItem['item_name'] ?? $foundItem['title'] ?? $match['found_item_title'] ?? 'Unknown Item';
                                                $foundItemDesc = $foundItem['description'] ?? $match['found_item_description'] ?? '';
                                                
                                                // Get found item image
                                                $foundItemImages = $match['found_item_images'] ?? $foundItem['images'] ?? [];
                                                $foundItemPrimary = $match['found_item_primary_image'] ?? $foundItem['primary_image'] ?? '';
                                                
                                                if (!empty($foundItemImages)) {
                                                    $foundImageUrl = getMatchItemImage(['images' => $foundItemImages]);
                                                } elseif (!empty($foundItemPrimary)) {
                                                    $foundImageUrl = normalizeMatchImageUrl($foundItemPrimary);
                                                } elseif ($foundItem) {
                                                    $foundImageUrl = getMatchItemImage($foundItem);
                                                } else {
                                                    $foundImageUrl = APP_URL . '/assets/img/no-image.svg';
                                                }
                                                ?>
                                                <span class="badge bg-success mb-1">
                                                    Found Item
                                                </span>
                                                <h6 class="mb-1">
                                                    <a href="<?= APP_URL ?>/found-items/<?= $foundItemId ?>" class="text-decoration-none">
                                                        <?= htmlspecialchars($foundItemTitle) ?>
                                                    </a>
                                                </h6>
                                                <p class="text-muted small mb-0 text-truncate" style="max-width: 200px;">
                                                    <?= htmlspecialchars($foundItemDesc) ?>
                                                </p>
                                            </div>
                                            <img src="<?= htmlspecialchars($foundImageUrl) ?>" 
                                                 class="rounded ms-3" 
                                                 style="width: 80px; height: 80px; object-fit: cover;"
                                                 alt="<?= htmlspecialchars($foundItemTitle) ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Match Details -->
                                <div class="row mt-3 pt-3 border-top">
                                    <div class="col-md-6">
                                        <div class="small text-muted">
                                            <strong>Match Criteria:</strong>
                                            <ul class="mb-0 ps-3">
                                                <?php if (!empty($match['match_reasons'])): ?>
                                                    <?php foreach ($match['match_reasons'] as $reason): ?>
                                                        <li><?= htmlspecialchars($reason) ?></li>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <li>Similar category</li>
                                                    <li>Similar description</li>
                                                    <li>Matching timeframe</li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <small class="text-muted d-block mb-2">
                                            Matched on <?= date('M d, Y', strtotime($match['created_at'] ?? 'now')) ?>
                                        </small>
                                        <?php 
                                        // Check if item can be claimed (not already claimed or completed)
                                        $foundItemStatus = $match['found_item']['status'] ?? $match['found_item_status'] ?? 'available';
                                        $canClaim = !in_array($foundItemStatus, ['claimed', 'returned', 'completed']);
                                        ?>
                                        <?php if (!empty($foundItemId)): ?>
                                            <?php if ($canClaim): ?>
                                                <a href="<?= APP_URL ?>/claims/create?found_item_id=<?= $foundItemId ?>" class="btn btn-primary btn-sm">
                                                    <i class="bi bi-hand-index me-1"></i>Claim This Item
                                                </a>
                                            <?php else: ?>
                                                <span class="btn btn-secondary btn-sm disabled" title="This item has already been claimed">
                                                    <i class="bi bi-check-circle me-1"></i>Already Claimed
                                                </span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <button class="btn btn-outline-secondary btn-sm" onclick="dismissMatch(<?= $match['id'] ?? 0 ?>)">
                                            <i class="bi bi-x-lg me-1"></i>Dismiss
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php 
            $pagination = $matches['pagination'] ?? null;
            if (!empty($pagination)): 
                $currentPage = $pagination['page'] ?? $pagination['currentPage'] ?? 1;
                $totalPages = $pagination['pages'] ?? $pagination['totalPages'] ?? 1;
            ?>
                <?php if ($totalPages > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php 
                        $baseUrl = '/dashboard/my-matches?' . http_build_query(array_filter([
                            'type' => $type ?? ''
                        ]));
                        ?>
                        
                        <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= $baseUrl ?>&page=<?= $currentPage - 1 ?>">Previous</a>
                        </li>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                <a class="page-link" href="<?= $baseUrl ?>&page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= $baseUrl ?>&page=<?= $currentPage + 1 ?>">Next</a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </main>
</div>

<script>
function dismissMatch(matchId) {
    if (confirm('Are you sure you want to dismiss this match?')) {
        fetch(`<?= APP_URL ?>/api/matches/${matchId}/dismiss`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to dismiss match. Please try again.');
            }
        })
        .catch(() => {
            alert('Failed to dismiss match. Please try again.');
        });
    }
}
</script>

<?php include __DIR__ . '/../layouts/footer-dashboard.php'; ?>
