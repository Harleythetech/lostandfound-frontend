<?php $pageTitle = 'Reports by Location - ' . APP_NAME; ?>
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
                        <li class="breadcrumb-item"><a href="<?= APP_URL ?>/admin/reports"
                                class="text-decoration-none">Reports</a></li>
                        <li class="breadcrumb-item active">By Location</li>
                    </ol>
                </nav>
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
        <script type="module">
            import { renderBarChart } from '<?= APP_URL ?>/assets/js/admin-reports.js';
            const rawLoc = <?= json_encode(array_map(function ($r) {
                return ['label' => $r['location_name'] ?? $r['name'] ?? 'Unknown', 'value' => intval(($r['lost_count'] ?? 0) + ($r['found_count'] ?? 0))];
            }, $data ?? [])); ?>;
            renderBarChart('#chartByLocation', rawLoc, { color: '#198754' });
        </script>

        <h5 class="fw-bold mb-4">Items by Location</h5>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div id="chartByLocation" style="min-height:340px;"></div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Location</th>
                            <th class="text-center">Lost Items</th>
                            <th class="text-center">Found Items</th>
                            <th class="text-center">Stored</th>
                            <th class="text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No data available</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($data as $row): ?>
                                <?php
                                $lostCount = $row['lost_count'] ?? 0;
                                $foundCount = $row['found_count'] ?? 0;
                                $storedCount = $row['stored_count'] ?? 0;
                                ?>
                                <tr>
                                    <td class="fw-semibold">
                                        <?= htmlspecialchars($row['location_name'] ?? $row['name'] ?? 'Unknown') ?>
                                        <?php if (!empty($row['building'])): ?>
                                            <small class="text-muted d-block"><?= htmlspecialchars($row['building']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center"><?= $lostCount ?></td>
                                    <td class="text-center"><?= $foundCount ?></td>
                                    <td class="text-center"><?= $storedCount ?></td>
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