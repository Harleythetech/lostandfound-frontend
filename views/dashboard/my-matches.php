<?php $pageTitle = 'My Matches - ' . APP_NAME; ?>
<?php include __DIR__ . '/../layouts/header-dashboard.php'; ?>

<?php
// Helper to get image URL from various structures
function extractImageUrl($img)
{
    if (empty($img))
        return '';
    if (is_string($img))
        return $img;
    if (!is_array($img))
        return '';

    // Common fields
    $fields = ['url', 'image_path', 'file_name', 'file', 'filename', 'path', 'src', 'link', 'href', 'filePath', 'file_path'];
    foreach ($fields as $f) {
        if (!empty($img[$f]))
            return $img[$f];
    }

    // Some APIs return nested structures
    if (!empty($img['data'])) {
        $data = $img['data'];
        if (is_array($data)) {
            if (!empty($data['url']))
                return $data['url'];
            if (!empty($data['image_path']))
                return $data['image_path'];
            if (!empty($data['file_name']))
                return $data['file_name'];
            // If data is numeric-indexed array, inspect first element
            if (isset($data[0]) && is_array($data[0])) {
                return extractImageUrl($data[0]);
            }
        }
    }

    if (!empty($img['attributes']) && is_array($img['attributes'])) {
        return extractImageUrl($img['attributes']);
    }

    return '';
}

function getMatchItemImage($item, $images = null)
{
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
            $url = extractImageUrl($primary);
            if ($url)
                return normalizeImageUrl($url);
        }
    }

    // Check for primary_image field (may be array or string)
    if (!empty($item['primary_image'])) {
        $primaryImg = $item['primary_image'];
        $url = extractImageUrl($primaryImg);
        if ($url)
            return normalizeImageUrl($url);
    }

    return '';
}
?>

<div class="dashboard-wrapper">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>

    <main class="dashboard-main">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <div>
                <h4 class="fw-semibold mb-1">My Matches</h4>
                <p class="text-muted mb-0 small">Potential matches between your items and others</p>
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

        <ul class="nav nav-tabs mb-4" role="tablist">
            <li class="nav-item">
                <a class="nav-link <?= empty($type) ? 'active' : '' ?>" href="<?= APP_URL ?>/dashboard/my-matches">
                    All Matches
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($type ?? '') === 'lost' ? 'active' : '' ?>"
                    href="<?= APP_URL ?>/dashboard/my-matches?type=lost">
                    <i class="bi bi-search me-1"></i>For My Lost Items
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($type ?? '') === 'found' ? 'active' : '' ?>"
                    href="<?= APP_URL ?>/dashboard/my-matches?type=found">
                    <i class="bi bi-box-seam me-1"></i>For My Found Items
                </a>
            </li>
        </ul>

        <?php
        $matchesList = $matches['data'] ?? $matches;
        if (!is_array($matchesList))
            $matchesList = [];

        // --- FILTER: remove matches for items that are completed/claimed/returned ---
        // We filter the array BEFORE checking if it's empty so the empty-state shows correctly.
        $matchesList = array_filter($matchesList, function ($match) {
            // Normalize common status fields
            $found = $match['found_item'] ?? null;
            $status = strtolower((string) ($match['found_item']['status'] ?? $match['found_item_status'] ?? $found['status'] ?? $match['status'] ?? ''));

            // Treat these statuses as non-claimable / already handled
            // Include 'resolved' which the backend uses when an item was already handled
            $excludeStatuses = ['completed', 'claimed', 'returned', 'reserved', 'unavailable', 'resolved'];
            if (in_array($status, $excludeStatuses, true)) {
                return false;
            }

            // Exclude matches explicitly dismissed by backend or user
            $matchStatus = strtolower((string) ($match['status'] ?? ''));
            if ($matchStatus === 'dismissed')
                return false;

            // Check common boolean or flag fields that indicate an item was claimed
            $flagFields = [
                $found['is_claimed'] ?? null,
                $found['claimed'] ?? null,
                $match['is_claimed'] ?? null,
                $match['claimed'] ?? null,
            ];
            foreach ($flagFields as $f) {
                if ($f === true || $f === 1 || $f === '1' || $f === 'true') {
                    return false;
                }
            }

            // Check for timestamps that imply claim/pickup
            if (!empty($found['claimed_at']) || !empty($found['picked_up_at']) || !empty($match['picked_up_at']) || !empty($match['claimed_at'])) {
                return false;
            }

            // Passed checks — keep this match
            return true;
        });
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
                <?php $matchIndex = 0;
                foreach ($matchesList as $match):
                    // Normalize match score to percentage:
                    $rawScore = $match['match_score'] ?? $match['confidence'] ?? $match['similarity_score'] ?? 0.75;
                    if (!is_numeric($rawScore)) {
                        $rawScore = 0;
                    }
                    if ($rawScore > 1) {
                        // Already a percent-like value (e.g., 73 means 73%)
                        $percentScore = round($rawScore);
                    } else {
                        // Fractional (0.73) -> multiply by 100
                        $percentScore = round($rawScore * 100);
                    }
                    // Clamp to sensible range
                    if ($percentScore < 0)
                        $percentScore = 0;
                    if ($percentScore > 100)
                        $percentScore = 100;
                    ?>
                    <?php if ($matchIndex === 0): ?>
                    <?php endif; ?>
                    <div class="col-12">
                        <div class="card shadow-sm border-start border-5 border-info">
                            <div class="card-body">
                                <div class="row align-items-center">
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
                                            $lostImageUrl = '';

                                            if (!empty($lostItemImages)) {
                                                $lostImageUrl = getMatchItemImage(['images' => $lostItemImages]);
                                            } elseif (!empty($lostItemPrimary)) {
                                                $primaryUrl = extractImageUrl($lostItemPrimary);
                                                $lostImageUrl = normalizeImageUrl($primaryUrl);
                                            } elseif ($lostItem) {
                                                $lostImageUrl = getMatchItemImage($lostItem);
                                            }
                                            // Fallbacks for single-image fields that some endpoints return
                                            if (empty($lostImageUrl) && !empty($match['lost_item_image'])) {
                                                $lostImageUrl = normalizeImageUrl(extractImageUrl($match['lost_item_image']));
                                            }
                                            if (empty($lostImageUrl) && !empty($lostItem['image'])) {
                                                $lostImageUrl = normalizeImageUrl(extractImageUrl($lostItem['image']));
                                            }
                                            if (empty($lostImageUrl) && !empty($match['lost_item']['image'])) {
                                                $lostImageUrl = normalizeImageUrl(extractImageUrl($match['lost_item']['image']));
                                            }
                                            ?>

                                            <?php if (!empty($lostImageUrl)): ?>
                                                <img src="<?= htmlspecialchars($lostImageUrl) ?>" class="rounded me-3"
                                                    style="width: 80px; height: 80px; object-fit: cover;"
                                                    alt="<?= htmlspecialchars($lostItemTitle) ?>">
                                            <?php else: ?>
                                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                                    style="width: 80px; height: 80px;">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>

                                            <div>
                                                <span class="badge bg-danger mb-1">
                                                    Lost Item
                                                </span>
                                                <h6 class="mb-1">
                                                    <a href="<?= APP_URL ?>/lost-items/<?= $lostItemId ?>"
                                                        class="text-decoration-none">
                                                        <?= htmlspecialchars($lostItemTitle) ?>
                                                    </a>
                                                </h6>
                                                <p class="text-muted small mb-0 text-truncate" style="max-width: 200px;">
                                                    <?= sanitizeForDisplay($lostItemDesc) ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2 text-center">
                                        <div class="match-indicator">
                                            <i class="bi bi-arrow-left-right text-info" style="font-size: 1.5rem;"></i>
                                            <div class="mt-1">
                                                <span class="badge bg-info">
                                                    <?= htmlspecialchars($percentScore) ?>%
                                                    Match
                                                </span>
                                            </div>
                                        </div>
                                    </div>

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
                                                $foundImageUrl = '';

                                                if (!empty($foundItemImages)) {
                                                    $foundImageUrl = getMatchItemImage(['images' => $foundItemImages]);
                                                } elseif (!empty($foundItemPrimary)) {
                                                    $primaryUrl = extractImageUrl($foundItemPrimary);
                                                    $foundImageUrl = normalizeImageUrl($primaryUrl);
                                                } elseif ($foundItem) {
                                                    $foundImageUrl = getMatchItemImage($foundItem);
                                                }
                                                // Fallbacks for single-image fields that some endpoints return
                                                if (empty($foundImageUrl) && !empty($match['found_item_image'])) {
                                                    $foundImageUrl = normalizeImageUrl(extractImageUrl($match['found_item_image']));
                                                }
                                                if (empty($foundImageUrl) && !empty($foundItem['image'])) {
                                                    $foundImageUrl = normalizeImageUrl(extractImageUrl($foundItem['image']));
                                                }
                                                if (empty($foundImageUrl) && !empty($match['found_item']['image'])) {
                                                    $foundImageUrl = normalizeImageUrl(extractImageUrl($match['found_item']['image']));
                                                }
                                                ?>
                                                <span class="badge bg-success mb-1">
                                                    Found Item
                                                </span>
                                                <h6 class="mb-1">
                                                    <a href="<?= APP_URL ?>/found-items/<?= $foundItemId ?>"
                                                        class="text-decoration-none">
                                                        <?= htmlspecialchars($foundItemTitle) ?>
                                                    </a>
                                                </h6>
                                                <p class="text-muted small mb-0 text-truncate" style="max-width: 200px;">
                                                    <?= sanitizeForDisplay($foundItemDesc) ?>
                                                </p>
                                            </div>

                                            <?php if (!empty($foundImageUrl)): ?>
                                                <img src="<?= htmlspecialchars($foundImageUrl) ?>" class="rounded ms-3"
                                                    style="width: 80px; height: 80px; object-fit: cover;"
                                                    alt="<?= htmlspecialchars($foundItemTitle) ?>">
                                            <?php else: ?>
                                                <div class="bg-light rounded ms-3 d-flex align-items-center justify-content-center"
                                                    style="width: 80px; height: 80px;">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

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
                                                <a href="<?= APP_URL ?>/claims/create?found_item_id=<?= $foundItemId ?>"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="bi bi-hand-index me-1"></i>Claim This Item
                                                </a>
                                            <?php else: ?>
                                                <span class="btn btn-secondary btn-sm disabled"
                                                    title="This item has already been claimed">
                                                    <i class="bi bi-check-circle me-1"></i>Already Claimed
                                                </span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php $matchIndex++; endforeach; ?>
            </div>

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

<?php include __DIR__ . '/../layouts/footer-dashboard.php'; ?>