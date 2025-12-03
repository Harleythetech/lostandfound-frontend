<?php $pageTitle = 'Reports - ' . APP_NAME; ?>
<?php include __DIR__ . '/../../layouts/header-dashboard.php'; ?>
<?php $user = getCurrentUser(); ?>

<div class="dashboard-wrapper">
    <?php include __DIR__ . '/../partials/sidebar.php'; ?>

    <main class="dashboard-main">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="fw-bold mb-0">Reports</h5>
                <small class="text-muted">System analytics and reports</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-outline-secondary btn-sm" onclick="location.reload()" title="Refresh">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
                <a href="<?= APP_URL ?>/notifications" class="btn btn-outline-secondary btn-sm position-relative">
                    <i class="bi bi-bell"></i>
                </a>
                <button class="btn btn-outline-secondary btn-sm" id="darkModeToggle">
                    <i class="bi bi-moon"></i>
                </button>
            </div>
        </div>

        <?php displayFlash(); ?>

        <!-- Report Types -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center py-4">
                        <i class="bi bi-tags text-primary mb-3" style="font-size: 2.5rem;"></i>
                        <h6 class="fw-bold">By Category</h6>
                        <p class="text-muted small mb-3">View items grouped by category</p>
                        <a href="<?= APP_URL ?>/admin/reports/by-category" class="btn btn-outline-primary btn-sm">View Report</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center py-4">
                        <i class="bi bi-geo-alt text-success mb-3" style="font-size: 2.5rem;"></i>
                        <h6 class="fw-bold">By Location</h6>
                        <p class="text-muted small mb-3">View items grouped by location</p>
                        <a href="<?= APP_URL ?>/admin/reports/by-location" class="btn btn-outline-success btn-sm">View Report</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center py-4">
                        <i class="bi bi-graph-up text-warning mb-3" style="font-size: 2.5rem;"></i>
                        <h6 class="fw-bold">Trends</h6>
                        <p class="text-muted small mb-3">View reporting trends over time</p>
                        <a href="<?= APP_URL ?>/admin/reports/trends" class="btn btn-outline-warning btn-sm">View Report</a>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($health) && ($health['success'] ?? false)): ?>
        <!-- System Health Section -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-0"><i class="bi bi-heart-pulse me-2 text-danger"></i>System Health</h6>
                        <small class="text-muted">Generated: <?= date('M j, Y g:i A', strtotime($health['generatedAt'] ?? 'now')) ?></small>
                    </div>
                    <div>
                        <?php 
                        $overallStatus = $health['status']['overall'] ?? 'unknown';
                        $statusClass = $overallStatus === 'healthy' ? 'success' : ($overallStatus === 'degraded' ? 'warning' : 'danger');
                        ?>
                        <span class="badge bg-<?= $statusClass ?> fs-6">
                            <i class="bi bi-<?= $overallStatus === 'healthy' ? 'check-circle' : 'exclamation-triangle' ?> me-1"></i>
                            <?= ucfirst($overallStatus) ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <!-- Status Overview -->
                    <div class="col-md-6 col-lg-3">
                        <div class="border rounded p-3 h-100">
                            <h6 class="text-muted small text-uppercase mb-3"><i class="bi bi-activity me-1"></i>Status</h6>
                            <div class="d-flex flex-column gap-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>API</span>
                                    <span class="badge bg-<?= ($health['status']['api'] ?? '') === 'running' ? 'success' : 'danger' ?>">
                                        <?= ucfirst($health['status']['api'] ?? 'unknown') ?>
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Database</span>
                                    <span class="badge bg-<?= ($health['status']['database'] ?? '') === 'connected' ? 'success' : 'danger' ?>">
                                        <?= ucfirst($health['status']['database'] ?? 'unknown') ?>
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Environment</span>
                                    <span class="badge bg-secondary"><?= $health['system']['environment'] ?? 'unknown' ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Uptime -->
                    <div class="col-md-6 col-lg-3">
                        <div class="border rounded p-3 h-100">
                            <h6 class="text-muted small text-uppercase mb-3"><i class="bi bi-clock-history me-1"></i>Uptime</h6>
                            <div class="d-flex flex-column gap-2">
                                <div class="d-flex justify-content-between">
                                    <span>API Process</span>
                                    <strong class="text-success"><?= $health['uptime']['process'] ?? 'N/A' ?></strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>System</span>
                                    <strong><?= $health['uptime']['system'] ?? 'N/A' ?></strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Node.js</span>
                                    <span class="text-muted small"><?= $health['system']['nodeVersion'] ?? 'N/A' ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Database Stats -->
                    <div class="col-md-6 col-lg-3">
                        <div class="border rounded p-3 h-100">
                            <h6 class="text-muted small text-uppercase mb-3"><i class="bi bi-database me-1"></i>Database</h6>
                            <?php $db = $health['database'] ?? []; ?>
                            <div class="d-flex flex-column gap-2">
                                <div class="d-flex justify-content-between">
                                    <span>Response</span>
                                    <strong class="text-success"><?= $db['responseTime'] ?? 'N/A' ?></strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Connections</span>
                                    <span><?= ($db['connections']['current'] ?? 0) ?> / <?= ($db['connections']['max'] ?? 0) ?></span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Version</span>
                                    <span class="text-muted small text-truncate" style="max-width: 100px;" title="<?= $db['version'] ?? '' ?>"><?= explode('-', $db['version'] ?? 'N/A')[0] ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Memory Usage -->
                    <div class="col-md-6 col-lg-3">
                        <div class="border rounded p-3 h-100">
                            <h6 class="text-muted small text-uppercase mb-3"><i class="bi bi-memory me-1"></i>Memory</h6>
                            <?php 
                            $memory = $health['memory'] ?? [];
                            $sysMemPercent = floatval($memory['system']['usedPercent'] ?? 0);
                            $heapPercent = floatval($memory['process']['heapUsedPercent'] ?? 0);
                            ?>
                            <div class="d-flex flex-column gap-2">
                                <div>
                                    <div class="d-flex justify-content-between small">
                                        <span>System</span>
                                        <span><?= $memory['system']['used'] ?? 'N/A' ?> / <?= $memory['system']['total'] ?? 'N/A' ?></span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-<?= $sysMemPercent > 90 ? 'danger' : ($sysMemPercent > 70 ? 'warning' : 'success') ?>" style="width: <?= $sysMemPercent ?>%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="d-flex justify-content-between small">
                                        <span>Heap</span>
                                        <span><?= $memory['process']['heapUsed'] ?? 'N/A' ?></span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-<?= $heapPercent > 90 ? 'danger' : ($heapPercent > 70 ? 'warning' : 'info') ?>" style="width: <?= $heapPercent ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Second Row -->
                <div class="row g-4 mt-1">
                    <!-- Database Tables -->
                    <div class="col-md-6 col-lg-4">
                        <div class="border rounded p-3 h-100">
                            <h6 class="text-muted small text-uppercase mb-3"><i class="bi bi-table me-1"></i>Database Tables</h6>
                            <?php $tables = $health['database']['tables'] ?? []; ?>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="d-flex justify-content-between">
                                        <span class="small">Users</span>
                                        <strong><?= number_format($tables['users'] ?? 0) ?></strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex justify-content-between">
                                        <span class="small">Lost Items</span>
                                        <strong><?= number_format($tables['lost_items'] ?? 0) ?></strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex justify-content-between">
                                        <span class="small">Found Items</span>
                                        <strong><?= number_format($tables['found_items'] ?? 0) ?></strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex justify-content-between">
                                        <span class="small">Matches</span>
                                        <strong><?= number_format($tables['matches'] ?? 0) ?></strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex justify-content-between">
                                        <span class="small">Categories</span>
                                        <strong><?= number_format($tables['categories'] ?? 0) ?></strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex justify-content-between">
                                        <span class="small">Locations</span>
                                        <strong><?= number_format($tables['locations'] ?? 0) ?></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity (24h) -->
                    <div class="col-md-6 col-lg-4">
                        <div class="border rounded p-3 h-100">
                            <h6 class="text-muted small text-uppercase mb-3"><i class="bi bi-graph-up-arrow me-1"></i>Last 24 Hours</h6>
                            <?php $activity = $health['database']['recentActivity'] ?? []; ?>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="text-center p-2 bg-light rounded">
                                        <div class="fs-4 fw-bold text-primary"><?= $activity['new_users_24h'] ?? 0 ?></div>
                                        <small class="text-muted">New Users</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center p-2 bg-light rounded">
                                        <div class="fs-4 fw-bold text-danger"><?= $activity['lost_items_24h'] ?? 0 ?></div>
                                        <small class="text-muted">Lost Reports</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center p-2 bg-light rounded">
                                        <div class="fs-4 fw-bold text-success"><?= $activity['found_items_24h'] ?? 0 ?></div>
                                        <small class="text-muted">Found Reports</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center p-2 bg-light rounded">
                                        <div class="fs-4 fw-bold text-info"><?= $activity['matches_24h'] ?? 0 ?></div>
                                        <small class="text-muted">Matches</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Info -->
                    <div class="col-md-12 col-lg-4">
                        <div class="border rounded p-3 h-100">
                            <h6 class="text-muted small text-uppercase mb-3"><i class="bi bi-cpu me-1"></i>System Info</h6>
                            <?php $system = $health['system'] ?? []; $cpu = $health['cpu'] ?? []; ?>
                            <div class="d-flex flex-column gap-2">
                                <div class="d-flex justify-content-between">
                                    <span class="small">Hostname</span>
                                    <strong class="text-truncate" style="max-width: 150px;"><?= $system['hostname'] ?? 'N/A' ?></strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="small">Platform</span>
                                    <span><?= ucfirst($system['platform'] ?? 'N/A') ?> (<?= $system['arch'] ?? '' ?>)</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="small">CPU</span>
                                    <span class="text-truncate" style="max-width: 150px;" title="<?= trim($cpu['model'] ?? '') ?>"><?= $cpu['cores'] ?? 0 ?> cores @ <?= $cpu['speed'] ?? 'N/A' ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Disk Usage -->
                <?php if (!empty($health['disk'])): ?>
                <div class="row mt-4">
                    <div class="col-12">
                        <h6 class="text-muted small text-uppercase mb-3"><i class="bi bi-hdd me-1"></i>Disk Usage</h6>
                        <div class="row g-3">
                            <?php foreach ($health['disk'] as $disk): ?>
                            <?php $diskPercent = floatval($disk['usedPercent'] ?? 0); ?>
                            <div class="col-md-6 col-lg-3">
                                <div class="border rounded p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <strong><?= $disk['mount'] ?? 'Unknown' ?></strong>
                                        <span class="badge bg-<?= $diskPercent > 90 ? 'danger' : ($diskPercent > 70 ? 'warning' : 'success') ?>"><?= number_format($diskPercent, 1) ?>%</span>
                                    </div>
                                    <div class="progress mb-2" style="height: 8px;">
                                        <div class="progress-bar bg-<?= $diskPercent > 90 ? 'danger' : ($diskPercent > 70 ? 'warning' : 'primary') ?>" style="width: <?= $diskPercent ?>%"></div>
                                    </div>
                                    <div class="d-flex justify-content-between small text-muted">
                                        <span><?= $disk['used'] ?? 'N/A' ?> used</span>
                                        <span><?= $disk['free'] ?? 'N/A' ?> free</span>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Config Info -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="border rounded p-3 bg-light">
                            <h6 class="text-muted small text-uppercase mb-3"><i class="bi bi-gear me-1"></i>Configuration</h6>
                            <?php $config = $health['config'] ?? []; ?>
                            <div class="row g-2">
                                <div class="col-6 col-md-3">
                                    <small class="text-muted d-block">Port</small>
                                    <span><?= $config['port'] ?? 'N/A' ?></span>
                                </div>
                                <div class="col-6 col-md-3">
                                    <small class="text-muted d-block">JWT Expires</small>
                                    <span><?= $config['jwtExpiresIn'] ?? 'N/A' ?></span>
                                </div>
                                <div class="col-6 col-md-3">
                                    <small class="text-muted d-block">Rate Limit</small>
                                    <span><?= ($config['rateLimitMax'] ?? 0) ?> / <?= $config['rateLimitWindow'] ?? 'N/A' ?></span>
                                </div>
                                <div class="col-6 col-md-3">
                                    <small class="text-muted d-block">Services</small>
                                    <span>
                                        <i class="bi bi-envelope<?= ($config['emailConfigured'] ?? false) ? '-check text-success' : '-x text-muted' ?>" title="Email <?= ($config['emailConfigured'] ?? false) ? 'configured' : 'not configured' ?>"></i>
                                        <i class="bi bi-google<?= ($config['firebaseConfigured'] ?? false) ? ' text-success' : ' text-muted' ?> ms-1" title="Firebase <?= ($config['firebaseConfigured'] ?? false) ? 'configured' : 'not configured' ?>"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-muted small mt-3 text-end">
                    <i class="bi bi-stopwatch me-1"></i>Report generated in <?= $health['reportGenerationTime'] ?? 'N/A' ?>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>Unable to fetch system health data. The API may be unavailable.
        </div>
        <?php endif; ?>
    </main>
</div>

<?php include __DIR__ . '/../../layouts/footer-dashboard.php'; ?>
