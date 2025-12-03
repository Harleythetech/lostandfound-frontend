<?php $pageTitle = 'Categories - ' . APP_NAME; ?>
<?php include __DIR__ . '/../../layouts/header-dashboard.php'; ?>
<?php $user = getCurrentUser(); ?>

<div class="dashboard-wrapper">
    <?php include __DIR__ . '/../partials/sidebar.php'; ?>

    <main class="dashboard-main">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="fw-bold mb-0">Categories</h5>
                <small class="text-muted">Manage item categories</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <i class="bi bi-plus-lg me-1"></i>Add Category
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
        // Sort categories by ID
        usort($categories, fn($a, $b) => ($a['id'] ?? 0) - ($b['id'] ?? 0));
        ?>

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Icon</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($categories)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No categories found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($categories as $cat): ?>
                                <tr class="<?= ($cat['is_active'] ?? true) ? '' : 'table-secondary' ?>">
                                    <td class="small">#<?= $cat['id'] ?></td>
                                    <td><i class="bi bi-<?= htmlspecialchars($cat['icon'] ?? 'tag') ?>"></i></td>
                                    <td class="fw-semibold"><?= htmlspecialchars($cat['name'] ?? '') ?></td>
                                    <td class="small text-muted"><?= htmlspecialchars($cat['description'] ?? 'N/A') ?></td>
                                    <td>
                                        <span class="badge <?= ($cat['is_active'] ?? true) ? 'bg-success' : 'bg-secondary' ?>">
                                            <?= ($cat['is_active'] ?? true) ? 'Active' : 'Inactive' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editCategoryModal<?= $cat['id'] ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="<?= APP_URL ?>/admin/categories/<?= $cat['id'] ?>/toggle" method="POST" class="d-inline">
                                                <button type="submit" class="btn btn-outline-warning">
                                                    <i class="bi bi-<?= ($cat['is_active'] ?? true) ? 'pause' : 'play' ?>"></i>
                                                </button>
                                            </form>
                                            <form action="<?= APP_URL ?>/admin/categories/<?= $cat['id'] ?>/delete" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                                <button type="submit" class="btn btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editCategoryModal<?= $cat['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="<?= APP_URL ?>/admin/categories/<?= $cat['id'] ?>/update" method="POST">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Category</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Name</label>
                                                        <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($cat['name'] ?? '') ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Description</label>
                                                        <textarea class="form-control" name="description" rows="2"><?= htmlspecialchars($cat['description'] ?? '') ?></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Icon (Bootstrap Icons name)</label>
                                                        <input type="text" class="form-control" name="icon" value="<?= htmlspecialchars($cat['icon'] ?? '') ?>" placeholder="e.g., phone, laptop, wallet">
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

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= APP_URL ?>/admin/categories" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Icon (Bootstrap Icons name)</label>
                        <input type="text" class="form-control" name="icon" placeholder="e.g., phone, laptop, wallet">
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
