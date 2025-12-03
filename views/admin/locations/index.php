<?php $pageTitle = 'Locations - ' . APP_NAME; ?>
<?php include __DIR__ . '/../../layouts/header-dashboard.php'; ?>
<?php $user = getCurrentUser(); ?>

<div class="dashboard-wrapper">
    <?php include __DIR__ . '/../partials/sidebar.php'; ?>

    <main class="dashboard-main">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="fw-bold mb-0">Locations</h5>
                <small class="text-muted">Manage campus locations</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addLocationModal">
                    <i class="bi bi-plus-lg me-1"></i>Add Location
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

        <?php 
        // Sort locations by ID
        usort($locations, fn($a, $b) => ($a['id'] ?? 0) - ($b['id'] ?? 0));
        ?>

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Building</th>
                            <th>Floor</th>
                            <th>Storage</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($locations)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No locations found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($locations as $loc): ?>
                                <tr>
                                    <td class="small">#<?= $loc['id'] ?></td>
                                    <td class="fw-semibold"><?= htmlspecialchars($loc['name'] ?? '') ?></td>
                                    <td class="small"><?= htmlspecialchars($loc['building'] ?? 'N/A') ?></td>
                                    <td class="small"><?= htmlspecialchars($loc['floor'] ?? 'N/A') ?></td>
                                    <td>
                                        <?php if ($loc['is_storage_location'] ?? false): ?>
                                            <span class="badge bg-info">Storage</span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge <?= ($loc['is_active'] ?? true) ? 'bg-success' : 'bg-secondary' ?>">
                                            <?= ($loc['is_active'] ?? true) ? 'Active' : 'Inactive' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    data-bs-toggle="modal" data-bs-target="#editLocationModal<?= $loc['id'] ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>

                                            <form action="<?= APP_URL ?>/admin/locations/<?= $loc['id'] ?>/delete" method="POST" 
                                                  onsubmit="return confirm('Are you sure you want to delete this location?')">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editLocationModal<?= $loc['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="<?= APP_URL ?>/admin/locations/<?= $loc['id'] ?>/update" method="POST">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Location</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Name <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="name" 
                                                               value="<?= htmlspecialchars($loc['name'] ?? '') ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Building</label>
                                                        <input type="text" class="form-control" name="building" 
                                                               value="<?= htmlspecialchars($loc['building'] ?? '') ?>" 
                                                               placeholder="e.g., CCST">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Floor</label>
                                                        <input type="text" class="form-control" name="floor" 
                                                               value="<?= htmlspecialchars($loc['floor'] ?? '') ?>"
                                                               placeholder="e.g., 2nd Floor">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Description</label>
                                                        <textarea class="form-control" name="description" rows="2"><?= htmlspecialchars($loc['description'] ?? '') ?></textarea>
                                                    </div>
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox" name="is_storage_location" 
                                                               id="is_storage_edit<?= $loc['id'] ?>" 
                                                               <?= ($loc['is_storage_location'] ?? false) ? 'checked' : '' ?>>
                                                        <label class="form-check-label" for="is_storage_edit<?= $loc['id'] ?>">
                                                            This is a storage location
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="is_active" 
                                                               id="is_active_edit<?= $loc['id'] ?>" 
                                                               <?= ($loc['is_active'] ?? true) ? 'checked' : '' ?>>
                                                        <label class="form-check-label" for="is_active_edit<?= $loc['id'] ?>">
                                                            Active
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- Add Location Modal -->
<div class="modal fade" id="addLocationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= APP_URL ?>/admin/locations" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add Location</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" required placeholder="e.g., RM201">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Building</label>
                        <input type="text" class="form-control" name="building" placeholder="e.g., CCST">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Floor</label>
                        <input type="text" class="form-control" name="floor" placeholder="e.g., 2nd Floor">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="2"></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_storage_location" id="is_storage_add">
                        <label class="form-check-label" for="is_storage_add">
                            This is a storage location
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../layouts/footer-dashboard.php'; ?>
