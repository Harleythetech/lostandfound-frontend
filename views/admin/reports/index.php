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

        <!-- Report Types removed: charts are embedded below -->

        <!-- Combined Charts: Category, Location, Trends -->
        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">Items by Category</h6>
                        <div id="reportsCategoryChart" style="min-height:320px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">Items by Location</h6>
                        <div id="reportsLocationChart" style="min-height:320px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">Reporting Trends (Lost / Found)</h6>
                        <div class="row">
                            <div class="col-12">
                                <div id="reportsTrendsMulti" style="min-height:320px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
        // Try to reuse server-side data used by the individual report pages.
        $catSource = $data_category ?? $categoryData ?? $data_by_category ?? $byCategory ?? $data ?? [];
        $locSource = $data_location ?? $locationData ?? $data_by_location ?? $byLocation ?? $data ?? [];
        $trendsSource = $trends ?? $trendsData ?? $data_trends ?? $data ?? [];

        // Prepare trends processedData if daily_stats present (same logic as trends.php)
        $dailyStats = [];
        if (!empty($trendsSource['daily_stats'])) {
            $dailyStats = $trendsSource['daily_stats'];
        } elseif (!empty($data['daily_stats'])) {
            $dailyStats = $data['daily_stats'];
        }

        $processedData = [];
        if (!empty($dailyStats)) {
            foreach ($dailyStats as $stat) {
                $date = $stat['date'] ?? '';
                $type = $stat['type'] ?? '';
                $count = $stat['count'] ?? 0;
                if (!isset($processedData[$date])) {
                    $processedData[$date] = ['date' => $date, 'lost' => 0, 'found' => 0, 'claims' => 0, 'matches' => 0];
                }
                if ($type === 'lost')
                    $processedData[$date]['lost'] = $count;
                if ($type === 'found')
                    $processedData[$date]['found'] = $count;
                if ($type === 'claim')
                    $processedData[$date]['claims'] = $count;
                if ($type === 'match')
                    $processedData[$date]['matches'] = $count;
            }
            krsort($processedData);
            $processedData = array_values($processedData);
        }
        ?>

        <script type="module">
            import { renderBarChart, renderLineChart, renderMultiLineChart, registerChartRerender } from '<?= APP_URL ?>/assets/js/admin-reports.js';

            // Embedded arrays from server-side (may be empty if controller didn't provide them)
            let rawCategory = <?= json_encode(array_map(function ($r) {
                return ['label' => $r['category_name'] ?? $r['name'] ?? 'Unknown', 'value' => intval(($r['lost_count'] ?? 0) + ($r['found_count'] ?? 0))];
            }, is_array($catSource) ? $catSource : [])); ?>;
            let rawLocation = <?= json_encode(array_map(function ($r) {
                return ['label' => $r['location_name'] ?? $r['name'] ?? 'Unknown', 'value' => intval(($r['lost_count'] ?? 0) + ($r['found_count'] ?? 0))];
            }, is_array($locSource) ? $locSource : [])); ?>;
            const processed = <?= json_encode(is_array($processedData) ? array_values($processedData) : []); ?>;

            async function ensureDataAndRender() {
                try {
                    // If server didn't embed category data, attempt to fetch JSON endpoint as fallback
                    if (!rawCategory || rawCategory.length === 0) {
                        console.debug('rawCategory empty, fetching /admin/reports/by-category as JSON');
                        const res = await fetch('<?= APP_URL ?>/admin/reports/by-category', { headers: { 'Accept': 'application/json' } });
                        if (res.ok) {
                            const json = await res.json();
                            const arr = (json.data || json || []).map(r => ({ label: r.category_name ?? r.name ?? 'Unknown', value: (r.lost_count || 0) + (r.found_count || 0) }));
                            rawCategory = arr;
                        } else {
                            console.warn('Category JSON fetch returned', res.status);
                        }
                    }

                    if (!rawLocation || rawLocation.length === 0) {
                        console.debug('rawLocation empty, fetching /admin/reports/by-location as JSON');
                        const res = await fetch('<?= APP_URL ?>/admin/reports/by-location', { headers: { 'Accept': 'application/json' } });
                        if (res.ok) {
                            const json = await res.json();
                            const arr = (json.data || json || []).map(r => ({ label: r.location_name ?? r.name ?? 'Unknown', value: (r.lost_count || 0) + (r.found_count || 0) }));
                            rawLocation = arr;
                        } else {
                            console.warn('Location JSON fetch returned', res.status);
                        }
                    }

                    // Render charts (use processed if available, otherwise try fetching trends endpoint)
                    renderBarChart('#reportsCategoryChart', rawCategory || [], { color: '#0d6efd' });
                    renderBarChart('#reportsLocationChart', rawLocation || [], { color: '#198754' });

                    let lostSeries = (processed || []).map(r => ({ date: r.date, value: r.lost || 0 }));
                    let foundSeries = (processed || []).map(r => ({ date: r.date, value: r.found || 0 }));

                    if ((!lostSeries || lostSeries.length === 0) && (!foundSeries || foundSeries.length === 0)) {
                        console.debug('No processed trends embedded — attempting to fetch /admin/reports/trends as JSON');
                        try {
                            const resT = await fetch('<?= APP_URL ?>/admin/reports/trends?days=30', { headers: { 'Accept': 'application/json' } });
                            if (resT.ok) {
                                const tj = await resT.json();
                                const daily = (tj.data && tj.data.daily_stats) ? tj.data.daily_stats : (tj.daily_stats || tj.data || tj || []);
                                const grouped = {};
                                (daily || []).forEach(s => {
                                    const date = s.date || s.day || null;
                                    if (!date) return;
                                    if (!grouped[date]) grouped[date] = { date, lost: 0, found: 0 };
                                    if (s.type === 'lost') grouped[date].lost = s.count || 0;
                                    if (s.type === 'found') grouped[date].found = s.count || 0;
                                });
                                const dates = Object.keys(grouped).sort();
                                lostSeries = dates.map(d => ({ date: d, value: grouped[d].lost }));
                                foundSeries = dates.map(d => ({ date: d, value: grouped[d].found }));
                            } else {
                                console.warn('Trends JSON fetch returned', resT.status);
                            }
                        } catch (e) {
                            console.error('Trends fetch failed', e);
                        }
                    }

                    // render a combined multi-line chart for lost + found
                    const multiSeries = [
                        { name: 'Lost', color: '#dc3545', data: lostSeries || [] },
                        { name: 'Found', color: '#0d6efd', data: foundSeries || [] }
                    ];
                    // initial render
                    renderMultiLineChart('#reportsTrendsMulti', multiSeries, {});
                    // register for responsive redraw
                    registerChartRerender('#reportsTrendsMulti', () => renderMultiLineChart('#reportsTrendsMulti', multiSeries, {}), []);

                    console.debug('Charts rendered — category', rawCategory && rawCategory.length, 'location', rawLocation && rawLocation.length, 'lostSeries', lostSeries && lostSeries.length, 'foundSeries', foundSeries && foundSeries.length);
                } catch (err) {
                    console.error('Failed to load/ render admin reports', err);
                }
            }

            document.addEventListener('DOMContentLoaded', ensureDataAndRender);
        </script>

        <?php if (!empty($health) && ($health['success'] ?? false)): ?>
            <!-- System Health Section -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="fw-bold mb-0"><i class="bi bi-heart-pulse me-2 text-danger"></i>System Health</h6>
                            <small class="text-muted">Generated:
                                <?= date('M j, Y g:i A', strtotime($health['generatedAt'] ?? 'now')) ?></small>
                        </div>
                        <div>
                            <?php
                            $overallStatus = $health['status']['overall'] ?? 'unknown';
                            $statusClass = $overallStatus === 'healthy' ? 'success' : ($overallStatus === 'degraded' ? 'warning' : 'danger');
                            ?>
                            <span class="badge bg-<?= $statusClass ?> fs-6">
                                <i
                                    class="bi bi-<?= $overallStatus === 'healthy' ? 'check-circle' : 'exclamation-triangle' ?> me-1"></i>
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
                                <h6 class="text-muted small text-uppercase mb-3"><i class="bi bi-activity me-1"></i>Status
                                </h6>
                                <div class="d-flex flex-column gap-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>API</span>
                                        <span
                                            class="badge bg-<?= ($health['status']['api'] ?? '') === 'running' ? 'success' : 'danger' ?>">
                                            <?= ucfirst($health['status']['api'] ?? 'unknown') ?>
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>Database</span>
                                        <span
                                            class="badge bg-<?= ($health['status']['database'] ?? '') === 'connected' ? 'success' : 'danger' ?>">
                                            <?= ucfirst($health['status']['database'] ?? 'unknown') ?>
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>Environment</span>
                                        <span
                                            class="badge bg-secondary"><?= $health['system']['environment'] ?? 'unknown' ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Uptime -->
                        <div class="col-md-6 col-lg-3">
                            <div class="border rounded p-3 h-100">
                                <h6 class="text-muted small text-uppercase mb-3"><i
                                        class="bi bi-clock-history me-1"></i>Uptime</h6>
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
                                        <span
                                            class="text-muted small"><?= $health['system']['nodeVersion'] ?? 'N/A' ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Database Stats -->
                        <div class="col-md-6 col-lg-3">
                            <div class="border rounded p-3 h-100">
                                <h6 class="text-muted small text-uppercase mb-3"><i class="bi bi-database me-1"></i>Database
                                </h6>
                                <?php $db = $health['database'] ?? []; ?>
                                <div class="d-flex flex-column gap-2">
                                    <div class="d-flex justify-content-between">
                                        <span>Response</span>
                                        <strong class="text-success"><?= $db['responseTime'] ?? 'N/A' ?></strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Connections</span>
                                        <span><?= ($db['connections']['current'] ?? 0) ?> /
                                            <?= ($db['connections']['max'] ?? 0) ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Version</span>
                                        <span class="text-muted small text-truncate" style="max-width: 100px;"
                                            title="<?= $db['version'] ?? '' ?>"><?= explode('-', $db['version'] ?? 'N/A')[0] ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Memory Usage -->
                        <div class="col-md-6 col-lg-3">
                            <div class="border rounded p-3 h-100">
                                <h6 class="text-muted small text-uppercase mb-3"><i class="bi bi-memory me-1"></i>Memory
                                </h6>
                                <?php
                                $memory = $health['memory'] ?? [];
                                $sysMemPercent = floatval($memory['system']['usedPercent'] ?? 0);
                                $heapPercent = floatval($memory['process']['heapUsedPercent'] ?? 0);
                                ?>
                                <div class="d-flex flex-column gap-2">
                                    <div>
                                        <div class="d-flex justify-content-between small">
                                            <span>System</span>
                                            <span><?= $memory['system']['used'] ?? 'N/A' ?> /
                                                <?= $memory['system']['total'] ?? 'N/A' ?></span>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-<?= $sysMemPercent > 90 ? 'danger' : ($sysMemPercent > 70 ? 'warning' : 'success') ?>"
                                                style="width: <?= $sysMemPercent ?>%"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="d-flex justify-content-between small">
                                            <span>Heap</span>
                                            <span><?= $memory['process']['heapUsed'] ?? 'N/A' ?></span>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-<?= $heapPercent > 90 ? 'danger' : ($heapPercent > 70 ? 'warning' : 'info') ?>"
                                                style="width: <?= $heapPercent ?>%"></div>
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
                                <h6 class="text-muted small text-uppercase mb-3"><i class="bi bi-table me-1"></i>Database
                                    Tables</h6>
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
                                <h6 class="text-muted small text-uppercase mb-3"><i
                                        class="bi bi-graph-up-arrow me-1"></i>Last 24 Hours</h6>
                                <?php $activity = $health['database']['recentActivity'] ?? []; ?>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="text-center p-2 bg-light rounded">
                                            <div class="fs-4 fw-bold text-primary"><?= $activity['new_users_24h'] ?? 0 ?>
                                            </div>
                                            <small class="text-muted">New Users</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center p-2 bg-light rounded">
                                            <div class="fs-4 fw-bold text-danger"><?= $activity['lost_items_24h'] ?? 0 ?>
                                            </div>
                                            <small class="text-muted">Lost Reports</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center p-2 bg-light rounded">
                                            <div class="fs-4 fw-bold text-success"><?= $activity['found_items_24h'] ?? 0 ?>
                                            </div>
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
                                <h6 class="text-muted small text-uppercase mb-3"><i class="bi bi-cpu me-1"></i>System Info
                                </h6>
                                <?php $system = $health['system'] ?? [];
                                $cpu = $health['cpu'] ?? []; ?>
                                <div class="d-flex flex-column gap-2">
                                    <div class="d-flex justify-content-between">
                                        <span class="small">Hostname</span>
                                        <strong class="text-truncate"
                                            style="max-width: 150px;"><?= $system['hostname'] ?? 'N/A' ?></strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="small">Platform</span>
                                        <span><?= ucfirst($system['platform'] ?? 'N/A') ?>
                                            (<?= $system['arch'] ?? '' ?>)</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="small">CPU</span>
                                        <span class="text-truncate" style="max-width: 150px;"
                                            title="<?= trim($cpu['model'] ?? '') ?>"><?= $cpu['cores'] ?? 0 ?> cores @
                                            <?= $cpu['speed'] ?? 'N/A' ?></span>
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
                                                    <span
                                                        class="badge bg-<?= $diskPercent > 90 ? 'danger' : ($diskPercent > 70 ? 'warning' : 'success') ?>"><?= number_format($diskPercent, 1) ?>%</span>
                                                </div>
                                                <div class="progress mb-2" style="height: 8px;">
                                                    <div class="progress-bar bg-<?= $diskPercent > 90 ? 'danger' : ($diskPercent > 70 ? 'warning' : 'primary') ?>"
                                                        style="width: <?= $diskPercent ?>%"></div>
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
                                <h6 class="text-muted small text-uppercase mb-3"><i
                                        class="bi bi-gear me-1"></i>Configuration</h6>
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
                                        <span><?= ($config['rateLimitMax'] ?? 0) ?> /
                                            <?= $config['rateLimitWindow'] ?? 'N/A' ?></span>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <small class="text-muted d-block">Services</small>
                                        <span>
                                            <i class="bi bi-envelope<?= ($config['emailConfigured'] ?? false) ? '-check text-success' : '-x text-muted' ?>"
                                                title="Email <?= ($config['emailConfigured'] ?? false) ? 'configured' : 'not configured' ?>"></i>
                                            <i class="bi bi-google<?= ($config['firebaseConfigured'] ?? false) ? ' text-success' : ' text-muted' ?> ms-1"
                                                title="Firebase <?= ($config['firebaseConfigured'] ?? false) ? 'configured' : 'not configured' ?>"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-muted small mt-3 text-end">
                        <i class="bi bi-stopwatch me-1"></i>Report generated in
                        <?= $health['reportGenerationTime'] ?? 'N/A' ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-2"></i>Unable to fetch system health data. The API may be
                unavailable.
            </div>
        <?php endif; ?>
    </main>
</div>

<?php include __DIR__ . '/../../layouts/footer-dashboard.php'; ?>