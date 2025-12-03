<?php $pageTitle = 'Trends Report - ' . APP_NAME; ?>
<?php include __DIR__ . '/../../layouts/header-dashboard.php'; ?>
<?php $user = getCurrentUser(); ?>

<?php
// Process the daily_stats into a date-grouped format
$dailyStats = $data['daily_stats'] ?? [];
$processedData = [];

foreach ($dailyStats as $stat) {
    $date = $stat['date'] ?? '';
    $type = $stat['type'] ?? '';
    $count = $stat['count'] ?? 0;
    
    if (!isset($processedData[$date])) {
        $processedData[$date] = [
            'date' => $date,
            'lost' => 0,
            'found' => 0,
            'claims' => 0,
            'matches' => 0
        ];
    }
    
    if ($type === 'lost') {
        $processedData[$date]['lost'] = $count;
    } elseif ($type === 'found') {
        $processedData[$date]['found'] = $count;
    } elseif ($type === 'claim') {
        $processedData[$date]['claims'] = $count;
    } elseif ($type === 'match') {
        $processedData[$date]['matches'] = $count;
    }
}

// Sort by date descending
krsort($processedData);
$processedData = array_values($processedData);

$resolutionSummary = $data['resolution_summary'] ?? [];
$days = $data['period_days'] ?? $_GET['days'] ?? 30;
?>

<div class="dashboard-wrapper">
    <?php include __DIR__ . '/../partials/sidebar.php'; ?>

    <main class="dashboard-main">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item"><a href="<?= APP_URL ?>/admin/reports" class="text-decoration-none">Reports</a></li>
                        <li class="breadcrumb-item active">Trends</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex align-items-center gap-2">
                <form method="GET" class="me-2">
                    <select name="days" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="7" <?= $days == 7 ? 'selected' : '' ?>>Last 7 days</option>
                        <option value="30" <?= $days == 30 ? 'selected' : '' ?>>Last 30 days</option>
                        <option value="90" <?= $days == 90 ? 'selected' : '' ?>>Last 90 days</option>
                    </select>
                </form>
                <a href="<?= APP_URL ?>/notifications" class="btn btn-outline-secondary btn-sm position-relative">
                    <i class="bi bi-bell"></i>
                </a>
                <button class="btn btn-outline-secondary btn-sm" id="darkModeToggle">
                    <i class="bi bi-moon"></i>
                </button>
            </div>
        </div>

        <h5 class="fw-bold mb-4">Reporting Trends</h5>

        <!-- Resolution Summary -->
        <?php if (!empty($resolutionSummary)): ?>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-3">
                        <div class="fs-3 fw-bold text-success"><?= $resolutionSummary['resolved'] ?? 0 ?></div>
                        <small class="text-muted">Items Resolved</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-3">
                        <div class="fs-3 fw-bold text-primary"><?= $resolutionSummary['claims_approved'] ?? 0 ?></div>
                        <small class="text-muted">Claims Approved</small>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <?php if (empty($processedData)): ?>
                    <p class="text-center text-muted py-4 mb-0">No activity in the selected period</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th class="text-center">Lost Reports</th>
                                    <th class="text-center">Found Reports</th>
                                    <th class="text-center">Claims</th>
                                    <th class="text-center">Matches</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($processedData as $row): ?>
                                    <tr>
                                        <td class="small"><?= !empty($row['date']) ? date('M j, Y', strtotime($row['date'])) : 'N/A' ?></td>
                                        <td class="text-center"><?= $row['lost'] ?></td>
                                        <td class="text-center"><?= $row['found'] ?></td>
                                        <td class="text-center"><?= $row['claims'] ?></td>
                                        <td class="text-center"><?= $row['matches'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<?php include __DIR__ . '/../../layouts/footer-dashboard.php'; ?>
