<?php $pageTitle = 'My Found Items - ' . APP_NAME; ?>
<?php include __DIR__ . '/../layouts/header-dashboard.php'; ?>

<div class="dashboard-wrapper">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    
    <main class="dashboard-main">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <div>
                <h4 class="fw-semibold mb-1">My Found Items</h4>
                <p class="text-muted mb-0 small">Manage items you've found and reported</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="<?= APP_URL ?>/found-items/create" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-lg me-1"></i>Report Found
                </a>
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
            
            <!-- Filters -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form action="<?= APP_URL ?>/dashboard/my-found-items" method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending Approval</option>
                                <option value="approved" <?= ($filters['status'] ?? '') === 'approved' ? 'selected' : '' ?>>Approved</option>
                                <option value="matched" <?= ($filters['status'] ?? '') === 'matched' ? 'selected' : '' ?>>Matched</option>
                                <option value="claimed" <?= ($filters['status'] ?? '') === 'claimed' ? 'selected' : '' ?>>Claimed</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Search items...">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary w-100">
                                <i class="bi bi-filter me-1"></i>Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Items List -->
            <?php if (empty($items['data'] ?? $items)): ?>
                <div class="card shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-box-seam display-1 text-muted mb-3"></i>
                        <h5>No Found Items Yet</h5>
                        <p class="text-muted mb-4">You haven't reported any found items. Found something? Help reunite it with its owner!</p>
                        <a href="<?= APP_URL ?>/found-items/create" class="btn btn-success">
                            <i class="bi bi-plus-lg me-1"></i>Report Found Item
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach (($items['data'] ?? $items) as $item): ?>
                        <div class="col-md-6">
                            <div class="card shadow-sm h-100">
                                <div class="row g-0">
                                    <div class="col-4">
                                        <?php 
                                        $imgData = $item['images'][0] ?? null;
                                        $imageUrl = APP_URL . '/assets/img/no-image.svg';
                                        if ($imgData) {
                                            $imgPath = is_array($imgData) ? ($imgData['url'] ?? '') : $imgData;
                                            $imgPath = str_replace('\\', '/', $imgPath);
                                            if (!empty($imgPath)) {
                                                $imageUrl = API_BASE_URL . '/' . ltrim($imgPath, '/');
                                            }
                                        }
                                        ?>
                                        <img src="<?= htmlspecialchars($imageUrl) ?>" 
                                             class="img-fluid rounded-start h-100" 
                                             style="object-fit: cover; min-height: 150px;"
                                             alt="<?= htmlspecialchars($item['title'] ?? $item['item_name'] ?? 'Item') ?>">
                                    </div>
                                    <div class="col-8">
                                        <div class="card-body d-flex flex-column h-100">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title mb-0"><?= htmlspecialchars($item['title'] ?? $item['item_name'] ?? 'Unknown Item') ?></h6>
                                                <?php
                                                $status = $item['status'] ?? 'pending';
                                                $statusClasses = [
                                                    'pending' => 'bg-warning',
                                                    'approved' => 'bg-success',
                                                    'matched' => 'bg-info',
                                                    'claimed' => 'bg-primary'
                                                ];
                                                $statusClass = $statusClasses[$status] ?? 'bg-secondary';
                                                ?>
                                                <span class="badge <?= $statusClass ?>"><?= ucfirst($status) ?></span>
                                            </div>
                                            
                                            <p class="card-text text-muted small mb-2 text-truncate-2">
                                                <?= htmlspecialchars($item['description'] ?? '') ?>
                                            </p>
                                            
                                            <div class="small text-muted mb-2">
                                                <i class="bi bi-calendar me-1"></i>
                                                Found: <?= date('M d, Y', strtotime($item['found_date'] ?? $item['found_date'] ?? $item['date_found'] ?? $item['created_at'])) ?>
                                            </div>
                                            
                                            <div class="small text-muted mb-2">
                                                <i class="bi bi-geo-alt me-1"></i>
                                                <?php
                                                $possession = $item['possession'] ?? 'with_me';
                                                $possessionLabels = [
                                                    'with_me' => 'With Finder',
                                                    'security' => 'At Security Office',
                                                    'admin_office' => 'At Admin Office'
                                                ];
                                                echo $possessionLabels[$possession] ?? 'Unknown';
                                                ?>
                                            </div>
                                            
                                            <!-- Claims Count -->
                                            <?php if (!empty($item['claims_count'])): ?>
                                                <div class="small text-info mb-2">
                                                    <i class="bi bi-hand-index me-1"></i>
                                                    <?= $item['claims_count'] ?> claim(s)
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="mt-auto">
                                                <div class="btn-group btn-group-sm w-100">
                                                    <a href="<?= APP_URL ?>/found-items/<?= $item['id'] ?>" class="btn btn-outline-primary">
                                                        <i class="bi bi-eye"></i> View
                                                    </a>
                                                    <?php if ($status === 'pending' || $status === 'approved'): ?>
                                                        <a href="<?= APP_URL ?>/found-items/<?= $item['id'] ?>/edit" class="btn btn-outline-secondary">
                                                            <i class="bi bi-pencil"></i> Edit
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if (!empty($item['claims_count']) && $item['claims_count'] > 0): ?>
                                                        <a href="<?= APP_URL ?>/claims?found_item_id=<?= $item['id'] ?>" class="btn btn-outline-info">
                                                            <i class="bi bi-list-check"></i> Claims
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php if (!empty($items['pagination'])): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php 
                            $currentPage = $items['pagination']['currentPage'] ?? 1;
                            $totalPages = $items['pagination']['totalPages'] ?? 1;
                            $baseUrl = '/dashboard/my-found-items?' . http_build_query(array_filter([
                                'status' => $filters['status'] ?? '',
                                'search' => $_GET['search'] ?? ''
                            ]));
                            ?>
                            
                            <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= $baseUrl ?>&page=<?= $currentPage - 1 ?>">Previous</a>
                            </li>
                            
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
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
    </main>
</div>

<?php include __DIR__ . '/../layouts/footer-dashboard.php'; ?>
