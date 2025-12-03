<?php $pageTitle = 'Reports by Category - ' . APP_NAME; ?>
<?php include __DIR__ . '/../../layouts/header-dashboard.php'; ?>
<?php $user = getCurrentUser(); ?>

<div class="dashboard-wrapper">
    <?php include __DIR__ . '/../partials/sidebar.php'; ?>

    <main class="dashboard-main">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item"><a href="<?= APP_URL ?>/admin/reports" class="text-decoration-none">Reports</a></li>
                        <li class="breadcrumb-item active">By Category</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="<?= APP_URL ?>/notifications" class="btn btn-outline-secondary btn-sm position-relative">
                    <i class="bi bi-bell"></i>
                </a>
                <button class="btn btn-outline-secondary btn-sm" id="darkModeToggle">
                    <i class="bi bi-moon"></i>
                </button>
            </div>
        </div>

        <h5 class="fw-bold mb-4">Items by Category</h5>

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Category</th>
                            <th class="text-center">Lost Items</th>
                            <th class="text-center">Found Items</th>
                            <th class="text-center">Matched</th>
                            <th class="text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data)): ?>
                            <tr><td colspan="5" class="text-center py-4 text-muted">No data available</td></tr>
                        <?php else: ?>
                            <?php foreach ($data as $row): ?>
                                <?php 
                                $lostCount = $row['lost_count'] ?? 0;
                                $foundCount = $row['found_count'] ?? 0;
                                $lostResolved = $row['lost_resolved'] ?? 0;
                                $foundResolved = $row['found_resolved'] ?? 0;
                                $matched = $lostResolved + $foundResolved;
                                ?>
                                <tr>
                                    <td class="fw-semibold"><?= htmlspecialchars($row['category_name'] ?? $row['name'] ?? 'Unknown') ?></td>
                                    <td class="text-center"><?= $lostCount ?></td>
                                    <td class="text-center"><?= $foundCount ?></td>
                                    <td class="text-center"><?= $matched ?></td>
                                    <td class="text-center fw-bold"><?= $lostCount + $foundCount ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php include __DIR__ . '/../../layouts/footer-dashboard.php'; ?>
