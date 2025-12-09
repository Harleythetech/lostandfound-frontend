<?php $pageTitle = ($itemType === 'lost' ? 'Lost' : 'Found') . ' Items Management - ' . APP_NAME; ?>
<?php include __DIR__ . '/../../layouts/header-dashboard.php'; ?>
<?php $user = getCurrentUser(); ?>

<div class="dashboard-wrapper">
    <?php include __DIR__ . '/../partials/sidebar.php'; ?>

    <main class="dashboard-main">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="fw-bold mb-0"><?= $itemType === 'lost' ? 'Lost' : 'Found' ?> Items</h5>
                <small class="text-muted">Manage all <?= $itemType ?> item reports</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="<?= APP_URL ?>/admin/notifications" class="btn ui-btn-secondary btn-sm position-relative"
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

        <!-- Filters -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All Statuses</option>
                            <option value="pending" <?= ($status ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="approved" <?= ($status ?? '') === 'approved' ? 'selected' : '' ?>>Approved
                            </option>
                            <option value="rejected" <?= ($status ?? '') === 'rejected' ? 'selected' : '' ?>>Rejected
                            </option>
                            <option value="matched" <?= ($status ?? '') === 'matched' ? 'selected' : '' ?>>Matched</option>
                            <option value="resolved" <?= ($status ?? '') === 'resolved' ? 'selected' : '' ?>>Resolved
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small mb-1">Category</label>
                        <select name="category_id" class="form-select form-select-sm">
                            <option value="">All Categories</option>
                            <?php foreach ($categories ?? [] as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($categoryId ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name'] ?? '') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small mb-1">Search</label>
                        <input type="text" name="search" class="form-control form-control-sm"
                            placeholder="Search items..." value="<?= htmlspecialchars($search ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-funnel me-1"></i>Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <?php
        // Sort items by ID
        usort($items, fn($a, $b) => ($a['id'] ?? 0) - ($b['id'] ?? 0));
        ?>

        <!-- Items Table -->
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Item</th>
                            <th>User</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($items)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No items found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td class="small">#<?= $item['id'] ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php
                                            $img = '';
                                            if (!empty($item['primary_image'])) {
                                                $img = is_array($item['primary_image']) ? ($item['primary_image']['url'] ?? $item['primary_image']['file_name'] ?? '') : $item['primary_image'];
                                            } elseif (!empty($item['images'][0])) {
                                                $img = is_array($item['images'][0]) ? ($item['images'][0]['url'] ?? $item['images'][0]['file_name'] ?? $item['images'][0]['image_path'] ?? '') : $item['images'][0];
                                            }
                                            ?>
                                            <?php if (!empty($img)): ?>
                                                <img src="<?= htmlspecialchars(normalizeImageUrl($img)) ?>" class="rounded me-2"
                                                    style="width: 36px; height: 36px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center"
                                                    style="width: 36px; height: 36px;">
                                                    <i class="bi bi-image text-muted small"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <div class="fw-semibold small">
                                                    <?= htmlspecialchars($item['title'] ?? $item['item_name'] ?? 'Unknown') ?>
                                                </div>
                                                <small
                                                    class="text-muted"><?= truncate($item['description'] ?? '', 30) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="small">
                                        <?php
                                        // Robust user display: prefer nested user.name, then common flattened fields
                                        $adminUserDisplay = '';
                                        if (!empty($item['user']) && is_array($item['user'])) {
                                            $u = $item['user'];
                                            $adminUserDisplay = $u['name'] ?? trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''));
                                        }

                                        // Additional flattened fallbacks from various API shapes
                                        if (empty($adminUserDisplay)) {
                                            $adminUserDisplay = $item['reported_by_name'] ?? $item['found_by_name'] ?? $item['reporter_name'] ?? $item['user_name'] ?? $item['name'] ?? '';
                                        }

                                        if (empty($adminUserDisplay)) {
                                            $adminUserDisplay = trim(($item['user_first_name'] ?? $item['first_name'] ?? '') . ' ' . ($item['user_last_name'] ?? $item['last_name'] ?? ''));
                                        }

                                        // Final fallback: show nothing (we'll still display school/email if present)
                                        ?>
                                        <?= sanitizeForDisplay($adminUserDisplay) ?>
                                        <?php
                                        // Determine user school fallback sources
                                        $userSchool = $item['user']['school_id'] ?? $item['user_school_id'] ?? $item['school_id'] ?? $item['found_by_school'] ?? $item['reporter_school_id'] ?? null;
                                        if (!empty($userSchool)): ?>
                                            <br><small class="text-muted"><?= htmlspecialchars($userSchool) ?></small>
                                        <?php endif; ?>
                                        <?php
                                        // Show reporter/finder email when present (from item or nested user)
                                        $userEmail = $item['email'] ?? $item['user']['email'] ?? $item['found_by_email'] ?? $item['reporter_email'] ?? null;
                                        if (!empty($userEmail)): ?>
                                            <br><small class="text-muted"><?= htmlspecialchars($userEmail) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="small">
                                        <?php
                                        // Category fallbacks: nested category object or flattened fields
                                        $catName = $item['category']['name'] ?? $item['category_name'] ?? $item['categoryName'] ?? $item['category_title'] ?? null;
                                        echo htmlspecialchars($catName ?? '');
                                        // Show location name when available from API (found/admin listing may provide location_name)
                                        $locName = $item['location_name'] ?? $item['found_location_name'] ?? $item['found_location'] ?? $item['location']['name'] ?? null;
                                        if (!empty($locName)): ?>
                                            <br><small class="text-muted"><?= htmlspecialchars($locName) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><span
                                            class="badge <?= getStatusBadgeClass($item['status'] ?? 'pending') ?>"><?= ucfirst($item['status'] ?? 'pending') ?></span>
                                    </td>
                                    <td class="small"><?= formatDate($item['created_at'] ?? '', 'M j, Y') ?></td>
                                    <td>
                                        <a href="<?= APP_URL ?>/admin/<?= $itemType ?>-items/<?= $item['id'] ?>"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <?php if (!empty($pagination) && ($pagination['totalPages'] ?? 1) > 1): ?>
            <nav class="mt-4">
                <ul class="pagination pagination-sm justify-content-center">
                    <?php for ($i = 1; $i <= $pagination['totalPages']; $i++): ?>
                        <li class="page-item <?= $i == ($pagination['currentPage'] ?? 1) ? 'active' : '' ?>">
                            <a class="page-link"
                                href="?page=<?= $i ?>&status=<?= $status ?? '' ?>&category_id=<?= $categoryId ?? '' ?>&search=<?= urlencode($search ?? '') ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </main>
</div>

<?php include __DIR__ . '/../../layouts/footer-dashboard.php'; ?>