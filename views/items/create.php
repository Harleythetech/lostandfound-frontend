<?php $pageTitle = 'Report Item - ' . APP_NAME; ?>
<?php include __DIR__ . '/../layouts/header-dashboard.php'; ?>
<?php $user = getCurrentUser(); ?>

<div class="dashboard-wrapper">
    <?php include __DIR__ . '/../dashboard/partials/sidebar.php'; ?>
    
    <main class="dashboard-main">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 small">
                        <li class="breadcrumb-item"><a href="<?= APP_URL ?>/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active">Report Item</li>
                    </ol>
                </nav>
                <h4 class="fw-semibold mb-0"><i class="bi bi-plus-circle me-2 text-primary"></i>Report Item</h4>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="<?= APP_URL ?>/notifications" class="btn btn-outline-secondary btn-sm position-relative">
                    <i class="bi bi-bell"></i>
                    <?php if (getUnreadNotificationCount() > 0): ?><span class="notification-dot"></span><?php endif; ?>
                </a>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleDarkMode()" data-theme-toggle="true"><i class="bi bi-moon"></i></button>
            </div>
        </div>

        <?php displayFlash(); ?>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border">
                    <div class="card-body p-4">
                        <form action="<?= APP_URL ?>/items" method="POST" enctype="multipart/form-data">
                            <!-- Status Selection -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">What happened? <span class="text-danger">*</span></label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <input type="radio" class="btn-check" name="status" id="status_lost" value="lost" checked>
                                        <label class="btn btn-outline-danger w-100 py-3" for="status_lost">
                                            <i class="bi bi-exclamation-triangle d-block mb-1" style="font-size: 1.5rem;"></i>
                                            I Lost an Item
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <input type="radio" class="btn-check" name="status" id="status_found" value="found">
                                        <label class="btn btn-outline-success w-100 py-3" for="status_found">
                                            <i class="bi bi-box-seam d-block mb-1" style="font-size: 1.5rem;"></i>
                                            I Found an Item
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="title" class="form-label fw-semibold">Item Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title" placeholder="e.g., Black iPhone 14 Pro" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="category" class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                                    <select class="form-select" id="category" name="category" required>
                                        <option value="">Select a category</option>
                                        <option value="electronics">Electronics</option>
                                        <option value="documents">Documents</option>
                                        <option value="accessories">Accessories</option>
                                        <option value="clothing">Clothing</option>
                                        <option value="keys">Keys</option>
                                        <option value="bags">Bags</option>
                                        <option value="jewelry">Jewelry</option>
                                        <option value="pets">Pets</option>
                                        <option value="others">Others</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="description" class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="Describe distinguishing features, brand, color, etc." required></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label for="location" class="form-label fw-semibold">Location <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                        <input type="text" class="form-control" id="location" name="location" placeholder="Where was it?" required>
                                    </div>
                                </div>
                                <div class="col-md-6" id="date_lost_group">
                                    <label for="date_lost" class="form-label fw-semibold">Date Lost</label>
                                    <input type="date" class="form-control" id="date_lost" name="date_lost" max="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="col-md-6" id="date_found_group" style="display: none;">
                                    <label for="date_found" class="form-label fw-semibold">Date Found</label>
                                    <input type="date" class="form-control" id="date_found" name="date_found" max="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="contact_info" class="form-label fw-semibold">Contact Info <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                        <input type="text" class="form-control" id="contact_info" name="contact_info" placeholder="Phone or email" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="image" class="form-label fw-semibold">Item Image</label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                    <small class="text-muted">Optional but recommended</small>
                                    <div id="imagePreview" class="mt-2"></div>
                                </div>
                            </div>

                            <hr class="my-4">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-check-circle me-2"></i>Submit Report</button>
                                <a href="<?= APP_URL ?>/dashboard" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
document.querySelectorAll('input[name="status"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('date_lost_group').style.display = this.value === 'lost' ? 'block' : 'none';
        document.getElementById('date_found_group').style.display = this.value === 'found' ? 'block' : 'none';
    });
});

document.getElementById('image').addEventListener('change', function(e) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" class="img-thumbnail mt-2" style="max-height: 150px;">`;
        }
        reader.readAsDataURL(this.files[0]);
    }
});
</script>

<?php include __DIR__ . '/../layouts/footer-dashboard.php'; ?>
