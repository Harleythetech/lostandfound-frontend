<?php $pageTitle = 'Report Found Item - ' . APP_NAME; ?>
<?php include __DIR__ . '/../layouts/header-dashboard.php'; ?>

<div class="dashboard-wrapper">
    <?php include __DIR__ . '/../dashboard/partials/sidebar.php'; ?>

    <main class="dashboard-main">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-semibold mb-1"><i class="bi bi-box-seam-fill text-success me-2"></i>Report Found Item</h4>
                <p class="text-muted mb-0 small">Help reunite this item with its owner</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="<?= APP_URL ?>/notifications" class="btn ui-btn-secondary btn-sm position-relative"
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

        <!-- Optional error banner -->
        <?php $old = $old ?? []; ?>

        <?php if (!empty($message) || !empty($errors)): ?>
            <div class="alert alert-danger" role="alert">
                <strong><?= htmlspecialchars($message ?? 'Please check the form for errors') ?></strong>
                <?php if (!empty($errors) && is_array($errors)): ?>
                    <div class="mt-2 small">
                        <?php foreach ($errors as $err): ?>
                            <div>- <?= htmlspecialchars($err) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Thank you banner -->
        <div class="thank-you-banner">
            <div class="icon"><i class="bi bi-heart-fill"></i></div>
            <div class="content">
                <strong>Thank you for being a good Samaritan!</strong>
                <p class="mb-0">Your honesty helps build a better community.</p>
            </div>
        </div>

        <form action="<?= APP_URL ?>/found-items" method="POST" enctype="multipart/form-data" id="foundItemForm">
            <div class="row g-4">
                <!-- Left Column - Main Info -->
                <div class="col-lg-8">
                    <!-- Basic Information Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h6 class="text-uppercase text-muted fw-semibold mb-3 small">
                                <i class="bi bi-info-circle me-2"></i>Item Details
                            </h6>

                            <div class="mb-3">
                                <label for="title" class="form-label">What did you find? <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg" id="title" name="title"
                                    placeholder="e.g., Black Wallet, iPhone, Student ID Card" required
                                    value="<?= htmlspecialchars($old['title'] ?? '') ?>">
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" id="description" name="description" rows="3"
                                    placeholder="Describe the item - brand, color, size. Don't include personal info found inside!"
                                    required><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                                <div class="form-text"><i class="bi bi-shield-lock me-1"></i>Keep some details private
                                    to verify the true owner</div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="category_id" class="form-label">Category <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="category_id" name="category_id" required>
                                        <option value="">Select category</option>
                                        <?php foreach ($categories ?? [] as $cat): ?>
                                            <option value="<?= $cat['id'] ?>" <?= (isset($old['category_id']) && $old['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cat['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="found_location_id" class="form-label">Where Found <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="found_location_id" name="found_location_id"
                                        required>
                                        <option value="">Select location</option>
                                        <?php foreach ($locations ?? [] as $loc): ?>
                                            <option value="<?= $loc['id'] ?>" <?= (isset($old['found_location_id']) && $old['found_location_id'] == $loc['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($loc['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- When Found Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h6 class="text-uppercase text-muted fw-semibold mb-3 small">
                                <i class="bi bi-clock me-2"></i>When Found
                            </h6>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="found_date" class="form-label">Date Found <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="found_date" name="found_date"
                                        max="<?= date('Y-m-d') ?>" required
                                        value="<?= htmlspecialchars($old['found_date'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="found_time" class="form-label">Approximate Time</label>
                                    <input type="time" class="form-control" id="found_time" name="found_time"
                                        value="<?= htmlspecialchars($old['found_time'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Item Condition & Identifiers Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h6 class="text-uppercase text-muted fw-semibold mb-3 small">
                                <i class="bi bi-fingerprint me-2"></i>Verification Details
                            </h6>

                            <div class="mb-3">
                                <label for="unique_identifiers" class="form-label">Unique Identifiers <span
                                        class="badge bg-secondary">Hidden from public</span></label>
                                <textarea class="form-control" id="unique_identifiers" name="unique_identifiers"
                                    rows="2"
                                    placeholder="Note unique features to verify ownership (won't be shown publicly)"><?= htmlspecialchars($old['unique_identifiers'] ?? '') ?></textarea>
                                <div class="form-text"><i class="bi bi-lock me-1"></i>Use these to confirm the real
                                    owner</div>
                            </div>

                            <div class="mb-0">
                                <label for="condition_notes" class="form-label">Item Condition</label>
                                <textarea class="form-control" id="condition_notes" name="condition_notes" rows="2"
                                    placeholder="Describe the condition of the item (e.g., good condition, screen cracked, wet)"><?= htmlspecialchars($old['condition_notes'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Media & Actions -->
                <div class="col-lg-4">
                    <!-- Images Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h6 class="text-uppercase text-muted fw-semibold mb-3 small">
                                <i class="bi bi-images me-2"></i>Photos
                            </h6>

                            <div class="upload-zone" id="uploadZone">
                                <input type="file" class="form-control d-none" id="images" name="images[]"
                                    accept="image/jpeg,image/png,image/gif" multiple data-no-default-preview>
                                <label for="images" class="upload-label">
                                    <i class="bi bi-cloud-arrow-up fs-1 text-muted"></i>
                                    <p class="mb-1 text-muted">Click to upload</p>
                                    <small class="text-muted">JPG, PNG, GIF (max 5MB)</small>
                                </label>
                            </div>
                            <div id="imagePreview" class="row g-2 mt-2"></div>
                        </div>
                    </div>

                    <!-- Item Storage Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h6 class="text-uppercase text-muted fw-semibold mb-3 small">
                                <i class="bi bi-geo-alt me-2"></i>Item Storage
                            </h6>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="turned_in_to_security"
                                    name="turned_in_to_security" value="1" <?= isset($old['turned_in_to_security']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="turned_in_to_security">
                                    <i class="bi bi-shield-fill me-1 text-warning"></i>Turned in to Security
                                </label>
                            </div>

                            <div class="mb-3">
                                <label for="storage_location_id" class="form-label">Storage Location</label>
                                <select class="form-select" id="storage_location_id" name="storage_location_id">
                                    <option value="">Select where item is stored</option>
                                    <?php foreach ($locations ?? [] as $loc): ?>
                                        <option value="<?= $loc['id'] ?>" <?= (isset($old['storage_location_id']) && $old['storage_location_id'] == $loc['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($loc['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-0">
                                <label for="storage_notes" class="form-label">Storage Notes</label>
                                <input type="text" class="form-control" id="storage_notes" name="storage_notes"
                                    placeholder="e.g., Left at guard station, Room 101"
                                    value="<?= htmlspecialchars($old['storage_notes'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Card -->
                    <div class="card border-0 shadow-sm" style="border-top: 3px solid #198754 !important;">
                        <div class="card-body p-4">
                            <button type="submit" class="btn btn-success w-100 mb-2">
                                <i class="bi bi-send me-2"></i>Submit Report
                            </button>
                            <a href="<?= APP_URL ?>/dashboard" class="btn btn-light w-100">
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </main>
</div>

<style>
    .upload-zone {
        border: 2px dashed #dee2e6;
        border-radius: 0.5rem;
        padding: 2rem;
        text-align: center;
        transition: all 0.2s;
        cursor: pointer;
    }

    .upload-zone:hover {
        border-color: #adb5bd;
        background: #f8f9fa;
    }

    .upload-label {
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        margin: 0;
    }
</style>

<script src="<?= APP_URL ?>/assets/js/image-preview.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof initImagePreview === 'function') {
            initImagePreview('images', 'imagePreview', 'uploadZone', { maxFiles: 5, toastTimeoutMs: 8000 });
        }
    });
</script>

<?php include __DIR__ . '/../layouts/footer-dashboard.php'; ?>