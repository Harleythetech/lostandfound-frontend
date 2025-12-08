<?php $pageTitle = 'Report Lost Item - ' . APP_NAME; ?>
<?php include __DIR__ . '/../layouts/header-dashboard.php'; ?>

<div class="dashboard-wrapper">
    <?php include __DIR__ . '/../dashboard/partials/sidebar.php'; ?>

    <main class="dashboard-main">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-semibold mb-1"><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Report Lost
                    Item</h4>
                <p class="text-muted mb-0 small">Fill in the details to help us find your item</p>
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

        <form action="<?= APP_URL ?>/lost-items" method="POST" enctype="multipart/form-data" id="lostItemForm">
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
                                <label for="title" class="form-label">What did you lose? <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg" id="title" name="title"
                                    placeholder="e.g., Black Leather Wallet, iPhone 13, Student ID" required
                                    value="<?= htmlspecialchars($old['title'] ?? '') ?>">
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" id="description" name="description" rows="3"
                                    placeholder="Describe your item in detail - brand, color, size, contents, etc."
                                    required><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
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
                                    <label for="last_seen_location_id" class="form-label">Last Seen Location <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="last_seen_location_id" name="last_seen_location_id"
                                        required>
                                        <option value="">Select location</option>
                                        <?php foreach ($locations ?? [] as $loc): ?>
                                            <option value="<?= $loc['id'] ?>" <?= (isset($old['last_seen_location_id']) && $old['last_seen_location_id'] == $loc['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($loc['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- When & Where Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h6 class="text-uppercase text-muted fw-semibold mb-3 small">
                                <i class="bi bi-clock me-2"></i>When Lost
                            </h6>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="last_seen_date" class="form-label">Date Lost <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="last_seen_date" name="last_seen_date"
                                        max="<?= date('Y-m-d') ?>" required
                                        value="<?= htmlspecialchars($old['last_seen_date'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="last_seen_time" class="form-label">Approximate Time</label>
                                    <input type="time" class="form-control" id="last_seen_time" name="last_seen_time"
                                        value="<?= htmlspecialchars($old['last_seen_time'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Distinctive Features Card -->
                    <div class="card border-0 shadow-sm mb-4 mb-lg-0">
                        <div class="card-body p-4">
                            <h6 class="text-uppercase text-muted fw-semibold mb-3 small">
                                <i class="bi bi-fingerprint me-2"></i>Identifying Features
                            </h6>

                            <div class="mb-0">
                                <label for="unique_identifiers" class="form-label">Unique Identifiers</label>
                                <textarea class="form-control" id="unique_identifiers" name="unique_identifiers"
                                    rows="2"
                                    placeholder="Any unique marks, scratches, stickers, engravings, serial numbers, etc."><?= htmlspecialchars($old['unique_identifiers'] ?? '') ?></textarea>
                                <div class="form-text"><i class="bi bi-shield-check me-1"></i>This helps verify
                                    ownership when someone finds your item</div>
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

                    <!-- Contact Preferences Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h6 class="text-uppercase text-muted fw-semibold mb-3 small">
                                <i class="bi bi-envelope me-2"></i>Contact Preferences
                            </h6>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="contact_via_email"
                                    name="contact_via_email" value="1" <?= (isset($old['contact_via_email']) ? 'checked' : 'checked') ?>>
                                <label class="form-check-label" for="contact_via_email">
                                    <i class="bi bi-envelope me-1"></i>Contact via Email
                                </label>
                            </div>
                            <div id="emailFieldDiv" class="mb-3 ms-4">
                                <input type="email" class="form-control form-control-sm" id="email" name="email"
                                    placeholder="Enter your email address" maxlength="320"
                                    value="<?= htmlspecialchars($old['email'] ?? '') ?>">
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="contact_via_phone"
                                    name="contact_via_phone" value="1" <?= isset($old['contact_via_phone']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="contact_via_phone">
                                    <i class="bi bi-phone me-1"></i>Contact via Phone
                                </label>
                            </div>
                            <div id="phoneFieldDiv" class="mb-0 ms-4" style="display: none;">
                                <input type="tel" class="form-control form-control-sm" id="phone_number"
                                    name="phone_number" placeholder="Enter your phone number" maxlength="15"
                                    value="<?= htmlspecialchars($old['phone_number'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Reward Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h6 class="text-uppercase text-muted fw-semibold mb-3 small">
                                <i class="bi bi-gift me-2"></i>Reward
                            </h6>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="offer_reward" name="offer_reward"
                                    <?= (!empty($old['offer_reward']) || isset($old['reward_offered'])) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="offer_reward">
                                    Offer a reward
                                </label>
                            </div>

                            <div id="rewardAmountDiv"
                                style="display: <?= (!empty($old['offer_reward']) || !empty($old['reward_offered'])) ? 'block' : 'none' ?>;">
                                <label for="reward_offered" class="form-label">Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" class="form-control" id="reward_offered" name="reward_offered"
                                        min="0" step="50" placeholder="0"
                                        value="<?= htmlspecialchars($old['reward_offered'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Card -->
                    <div class="card border-0 shadow-sm" style="border-top: 3px solid #dc3545 !important;">
                        <div class="card-body p-4">
                            <button type="submit" class="btn btn-danger w-100 mb-2">
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

    .image-remove-btn {
        position: absolute;
        top: 6px;
        right: 6px;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        z-index: 9999 !important;
        pointer-events: auto;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
    }

    .image-remove-btn i {
        pointer-events: none;
        font-size: 0.85rem;
    }

    #imagePreview .col-6 {
        padding: 4px;
    }

    #imagePreview .position-relative {
        overflow: visible;
    }
</style>

<script src="<?= APP_URL ?>/assets/js/image-preview.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof initImagePreview === 'function') {
            initImagePreview('images', 'imagePreview', 'uploadZone', { maxFiles: 5, toastTimeoutMs: 8000 });
        }

        // Toggle reward amount field
        const offerReward = document.getElementById('offer_reward');
        const rewardAmountDiv = document.getElementById('rewardAmountDiv');

        offerReward.addEventListener('change', function () {
            rewardAmountDiv.style.display = this.checked ? 'block' : 'none';
        });

        // Toggle email field
        const contactViaEmail = document.getElementById('contact_via_email');
        const emailFieldDiv = document.getElementById('emailFieldDiv');
        const emailInput = document.getElementById('email');

        contactViaEmail.addEventListener('change', function () {
            emailFieldDiv.style.display = this.checked ? 'block' : 'none';
            emailInput.required = this.checked;
            if (!this.checked) emailInput.value = '';
        });
        // Set initial state
        emailInput.required = contactViaEmail.checked;

        // Toggle phone field
        const contactViaPhone = document.getElementById('contact_via_phone');
        const phoneFieldDiv = document.getElementById('phoneFieldDiv');
        const phoneInput = document.getElementById('phone_number');

        contactViaPhone.addEventListener('change', function () {
            phoneFieldDiv.style.display = this.checked ? 'block' : 'none';
            phoneInput.required = this.checked;
            if (!this.checked) phoneInput.value = '';
        });
    });
</script>

<?php include __DIR__ . '/../layouts/footer-dashboard.php'; ?>