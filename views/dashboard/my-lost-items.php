<?php $pageTitle = 'My Lost Items - ' . APP_NAME; ?>
<?php include __DIR__ . '/../layouts/header-dashboard.php'; ?>

<div class="dashboard-wrapper">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    
    <main class="dashboard-main">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <div>
                <h4 class="fw-semibold mb-1">My Lost Items</h4>
                <p class="text-muted mb-0 small">Manage items you've reported as lost</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="<?= APP_URL ?>/lost-items/create" class="btn btn-danger btn-sm">
                    <i class="bi bi-plus-lg me-1"></i>Report Lost
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
                    <form action="<?= APP_URL ?>/dashboard/my-lost-items" method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending Approval</option>
                                <option value="approved" <?= ($filters['status'] ?? '') === 'approved' ? 'selected' : '' ?>>Approved</option>
                                <option value="matched" <?= ($filters['status'] ?? '') === 'matched' ? 'selected' : '' ?>>Matched</option>
                                <option value="claimed" <?= ($filters['status'] ?? '') === 'claimed' ? 'selected' : '' ?>>Claimed</option>
                                <option value="expired" <?= ($filters['status'] ?? '') === 'expired' ? 'selected' : '' ?>>Expired</option>
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
                        <i class="bi bi-search display-1 text-muted mb-3"></i>
                        <h5>No Lost Items Yet</h5>
                        <p class="text-muted mb-4">You haven't reported any lost items. Lost something? Report it now!</p>
                        <a href="<?= APP_URL ?>/lost-items/create" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i>Report Lost Item
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
                                            $imgPath = ltrim($imgPath, '/');
                                            if (!empty($imgPath)) {
                                                $imageUrl = 'http://localhost:8080/api/' . $imgPath;
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
                                                    'claimed' => 'bg-primary',
                                                    'expired' => 'bg-secondary'
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
                                                Lost: <?= date('M d, Y', strtotime($item['last_seen_date'] ?? $item['last_seen_date'] ?? $item['date_lost'] ?? $item['created_at'])) ?>
                                            </div>
                                            
                                            <?php if (!empty($item['reward_offered'] ?? $item['reward_offered']?? null)): ?>
                                                <div class="small text-success mb-2">
                                                    <i class="bi bi-gift me-1"></i>
                                                    Reward: ₱<?= number_format($item['reward_offered'] ?? $item['reward_offered'] ?? null, 2) ?>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="mt-auto">
                                                <div class="btn-group btn-group-sm w-100">
                                                    <a href="<?= APP_URL ?>/lost-items/<?= $item['id'] ?>" class="btn btn-outline-primary">
                                                        <i class="bi bi-eye"></i> View
                                                    </a>
                                                    <?php if ($status === 'pending' || $status === 'approved'): ?>
                                                        <a href="<?= APP_URL ?>/lost-items/<?= $item['id'] ?>/edit" class="btn btn-outline-secondary">
                                                            <i class="bi bi-pencil"></i> Edit
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if ($status === 'matched'): ?>
                                                        <a href="<?= APP_URL ?>/dashboard/my-matches?item_id=<?= $item['id'] ?>" class="btn btn-outline-info">
                                                            <i class="bi bi-link-45deg"></i> Matches
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
                            $baseUrl = '/dashboard/my-lost-items?' . http_build_query(array_filter([
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
