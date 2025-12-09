<?php $pageTitle = 'Edit Item - ' . APP_NAME; ?>
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
                        <li class="breadcrumb-item"><a
                                href="<?= APP_URL ?>/<?= $itemType ?>-items"><?= ucfirst($itemType) ?> Items</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </nav>
                <h4 class="fw-semibold mb-0"><i
                        class="bi bi-pencil me-2 text-<?= $itemType === 'lost' ? 'danger' : 'success' ?>"></i>Edit
                    <?= ucfirst($itemType) ?> Item</h4>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="<?= APP_URL ?>/notifications" class="btn btn-outline-secondary btn-sm position-relative">
                    <i class="bi bi-bell"></i>
                    <?php if (getUnreadNotificationCount() > 0): ?><span class="notification-dot"></span><?php endif; ?>
                </a>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleDarkMode()"
                    data-theme-toggle="true"><i class="bi bi-moon"></i></button>
            </div>
        </div>

        <?php displayFlash(); ?>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border">
                    <div class="card-body p-4">
                        <form action="<?= APP_URL ?>/<?= $itemType ?>-items/<?= $item['id'] ?>/update" method="POST"
                            enctype="multipart/form-data">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label for="title" class="form-label">Item Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title"
                                        value="<?= htmlspecialchars($item['title'] ?? $item['item_name'] ?? '') ?>"
                                        required maxlength="100">
                                </div>
                                <div class="col-md-6">
                                    <label for="category_id" class="form-label">Category <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="category_id" name="category_id" required>
                                        <option value="">Select a category</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= $category['id'] ?>" <?= ($item['category_id'] ?? '') == $category['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($category['name'] ?? '') ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="description" class="form-label">Description <span
                                            class="text-danger">*</span></label>
                                    <textarea class="form-control" id="description" name="description" rows="3" required
                                        maxlength="1000"><?= sanitizeForDisplay($item['description'] ?? '') ?></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label for="location_id" class="form-label">Location <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="location_id" name="location_id" required>
                                        <option value="">Select a location</option>
                                        <?php
                                        $itemLocationId = $item['location_id'] ?? $item['last_seen_location_id'] ?? '';
                                        foreach ($locations as $location): ?>
                                            <option value="<?= $location['id'] ?>" <?= $itemLocationId == $location['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($location['name'] ?? '') ?>    <?php if (!empty($location['building'])): ?>
                                                    (<?= htmlspecialchars($location['building']) ?>)<?php endif; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="last_seen_location" class="form-label">Location Details</label>
                                    <input type="text" class="form-control" id="last_seen_location"
                                        name="last_seen_location"
                                        value="<?= htmlspecialchars($item['last_seen_location'] ?? $item['location_details'] ?? '') ?>"
                                        maxlength="255">
                                </div>

                                <?php if ($itemType === 'lost'): ?>
                                    <div class="col-md-4">
                                        <label for="last_seen_date" class="form-label">Date Lost <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="last_seen_date" name="last_seen_date"
                                            value="<?= htmlspecialchars($item['last_seen_date'] ?? $item['date_lost'] ?? '') ?>"
                                            required max="<?= date('Y-m-d') ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="last_seen_time" class="form-label">Approximate Time</label>
                                        <input type="time" class="form-control" id="last_seen_time" name="last_seen_time"
                                            value="<?= htmlspecialchars($item['last_seen_time'] ?? $item['time_lost'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="reward_offered" class="form-label">Reward (Optional)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">₱</span>
                                            <input type="number" class="form-control" id="reward_offered"
                                                name="reward_offered"
                                                value="<?= htmlspecialchars($item['reward_offered'] ?? $item['reward_amount'] ?? '') ?>"
                                                min="0" step="0.01">
                                        </div>
                                    </div>

                                    <!-- Contact Preferences for Lost Items -->
                                    <div class="col-12">
                                        <label class="form-label">Contact Preferences</label>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" id="contact_via_email"
                                                        name="contact_via_email" value="1" <?= ($item['contact_via_email'] ?? false) ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="contact_via_email">
                                                        <i class="bi bi-envelope me-1"></i>Contact via Email
                                                    </label>
                                                </div>
                                                <div id="emailFieldDiv"
                                                    style="<?= ($item['contact_via_email'] ?? false) ? '' : 'display: none;' ?>">
                                                    <input type="email" class="form-control form-control-sm" id="email"
                                                        name="email" value="<?= htmlspecialchars($item['email'] ?? '') ?>"
                                                        placeholder="Enter your email address" maxlength="320">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" id="contact_via_phone"
                                                        name="contact_via_phone" value="1" <?= ($item['contact_via_phone'] ?? false) ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="contact_via_phone">
                                                        <i class="bi bi-phone me-1"></i>Contact via Phone
                                                    </label>
                                                </div>
                                                <div id="phoneFieldDiv"
                                                    style="<?= ($item['contact_via_phone'] ?? false) ? '' : 'display: none;' ?>">
                                                    <input type="tel" class="form-control form-control-sm" id="phone_number"
                                                        name="phone_number"
                                                        value="<?= htmlspecialchars($item['phone_number'] ?? '') ?>"
                                                        placeholder="Enter your phone number" maxlength="15">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="col-md-6">
                                        <label for="found_date" class="form-label">Date Found <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="found_date" name="found_date"
                                            value="<?= htmlspecialchars($item['found_date'] ?? $item['date_found'] ?? '') ?>"
                                            required max="<?= date('Y-m-d') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="time_found" class="form-label">Approximate Time</label>
                                        <input type="time" class="form-control" id="time_found" name="time_found"
                                            value="<?= htmlspecialchars($item['time_found'] ?? '') ?>">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Current Possession <span
                                                class="text-danger">*</span></label>
                                        <div class="row g-2">
                                            <div class="col-md-4">
                                                <div class="form-check card p-3">
                                                    <input class="form-check-input" type="radio" name="possession"
                                                        id="possession_with_me" value="with_me" <?= ($item['possession'] ?? 'with_me') === 'with_me' ? 'checked' : '' ?>>
                                                    <label class="form-check-label w-100" for="possession_with_me">
                                                        <i class="bi bi-person-check text-primary d-block mb-1"></i><strong>With
                                                            Me</strong>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check card p-3">
                                                    <input class="form-check-input" type="radio" name="possession"
                                                        id="possession_security" value="security" <?= ($item['possession'] ?? '') === 'security' ? 'checked' : '' ?>>
                                                    <label class="form-check-label w-100" for="possession_security">
                                                        <i class="bi bi-shield-check text-success d-block mb-1"></i><strong>Security
                                                            Office</strong>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check card p-3">
                                                    <input class="form-check-input" type="radio" name="possession"
                                                        id="possession_admin" value="admin_office" <?= ($item['possession'] ?? '') === 'admin_office' ? 'checked' : '' ?>>
                                                    <label class="form-check-label w-100" for="possession_admin">
                                                        <i class="bi bi-building text-info d-block mb-1"></i><strong>Admin
                                                            Office</strong>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12" id="turnover_location_group"
                                        style="<?= ($item['possession'] ?? '') !== 'with_me' ? '' : 'display: none;' ?>">
                                        <label for="turnover_location" class="form-label">Turnover Location Details</label>
                                        <input type="text" class="form-control" id="turnover_location"
                                            name="turnover_location"
                                            value="<?= htmlspecialchars($item['turnover_location'] ?? '') ?>"
                                            maxlength="255">
                                    </div>
                                <?php endif; ?>

                                <div class="col-12">
                                    <label for="unique_identifiers" class="form-label">Distinctive Features</label>
                                    <textarea class="form-control" id="unique_identifiers" name="unique_identifiers"
                                        rows="2"
                                        maxlength="500"><?= htmlspecialchars($item['unique_identifiers'] ?? $item['proof_details'] ?? $item['distinctive_features'] ?? '') ?></textarea>
                                </div>

                                <?php if (!empty($item['images'])): ?>
                                    <div class="col-12">
                                        <label class="form-label">Current Images</label>
                                        <div class="row g-2">
                                            <?php foreach ($item['images'] as $image): ?>
                                                <?php
                                                // Get image URL - handle both 'url' and 'image_path' fields
                                                $imageUrl = is_array($image) ? ($image['url'] ?? $image['image_path'] ?? '') : $image;
                                                // Replace backslashes with forward slashes
                                                $imageUrl = str_replace('\\', '/', $imageUrl);
                                                // Remove /api/ prefix if present
                                                $imageUrl = preg_replace('#^(/api/|/|api/)#', '', $imageUrl);
                                                ?>
                                                <?php if (!empty($imageUrl)): ?>
                                                    <div class="col-3 col-md-2">
                                                        <img src="<?= htmlspecialchars(normalizeImageUrl($imageUrl)) ?>"
                                                            class="img-thumbnail"
                                                            style="width: 100%; height: 80px; object-fit: cover;">
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="col-12">
                                    <label for="images" class="form-label">Upload New Images</label>
                                    <input type="file" class="form-control" id="images" name="images[]"
                                        accept="image/jpeg,image/png,image/gif" multiple>
                                    <small class="text-muted">Max 5 images, 5MB each. These will replace existing
                                        images.</small>
                                    <div id="image-preview" class="row g-2 mt-2"></div>
                                </div>
                            </div>

                            <hr class="my-4">
                            <div class="d-flex gap-2">
                                <button type="submit"
                                    class="btn btn-<?= $itemType === 'lost' ? 'danger' : 'success' ?>"><i
                                        class="bi bi-check-lg me-1"></i>Update Item</button>
                                <a href="<?= APP_URL ?>/<?= $itemType ?>-items/<?= $item['id'] ?>"
                                    class="btn btn-outline-secondary">Cancel</a>
                                <button type="button" class="btn btn-outline-danger ms-auto" data-bs-toggle="modal"
                                    data-bs-target="#deleteModal"><i class="bi bi-trash me-1"></i>Delete</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Delete Item</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this item? This action cannot be undone.</p>
                <p class="fw-bold"><?= htmlspecialchars($item['title'] ?? $item['item_name'] ?? 'This item') ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="<?= APP_URL ?>/<?= $itemType ?>-items/<?= $item['id'] ?>/delete" method="POST"
                    class="d-inline">
                    <button type="submit" class="btn btn-danger">Delete Item</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    <?php if ($itemType === 'found'): ?>
        document.querySelectorAll('input[name="possession"]').forEach(radio => {
            radio.addEventListener('change', function () {
                document.getElementById('turnover_location_group').style.display = this.value !== 'with_me' ? 'block' : 'none';
            });
        });
    <?php else: ?>
        // Toggle email field for lost items
        const contactViaEmail = document.getElementById('contact_via_email');
        const emailFieldDiv = document.getElementById('emailFieldDiv');
        const emailInput = document.getElementById('email');

        if (contactViaEmail && emailFieldDiv && emailInput) {
            contactViaEmail.addEventListener('change', function () {
                emailFieldDiv.style.display = this.checked ? 'block' : 'none';
                emailInput.required = this.checked;
                if (!this.checked) emailInput.value = '';
            });
            // Set initial required state
            emailInput.required = contactViaEmail.checked;
        }

        // Toggle phone field for lost items
        const contactViaPhone = document.getElementById('contact_via_phone');
        const phoneFieldDiv = document.getElementById('phoneFieldDiv');
        const phoneInput = document.getElementById('phone_number');

        if (contactViaPhone && phoneFieldDiv && phoneInput) {
            contactViaPhone.addEventListener('change', function () {
                phoneFieldDiv.style.display = this.checked ? 'block' : 'none';
                phoneInput.required = this.checked;
                if (!this.checked) phoneInput.value = '';
            });
            // Set initial required state
            phoneInput.required = contactViaPhone.checked;
        }
    <?php endif; ?>

    document.getElementById('images').addEventListener('change', function (e) {
        const preview = document.getElementById('image-preview');
        preview.innerHTML = '';
        Array.from(e.target.files).slice(0, 5).forEach((file, i) => {
            if (file.size > 5 * 1024 * 1024) { alert(`Image ${i + 1} is too large.`); return; }
            const reader = new FileReader();
            reader.onload = function (e) {
                const col = document.createElement('div');
                col.className = 'col-3 col-md-2';
                col.innerHTML = `<img src="${e.target.result}" class="img-thumbnail" style="width: 100%; height: 60px; object-fit: cover;">`;
                preview.appendChild(col);
            };
            reader.readAsDataURL(file);
        });
    });
</script>

<?php include __DIR__ . '/../layouts/footer-dashboard.php'; ?>