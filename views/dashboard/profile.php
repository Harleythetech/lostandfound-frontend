<?php $pageTitle = 'My Profile - ' . APP_NAME; ?>
<?php include __DIR__ . '/../layouts/header-dashboard.php'; ?>

<?php
// DEBUG: Uncomment to see user data
// echo '<pre style="background:#fff;padding:20px;margin:20px;">User data: '; print_r($user); echo '</pre>';
?>

<div class="dashboard-wrapper">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    
    <main class="dashboard-main">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <div>
                <h4 class="fw-semibold mb-1">Profile Settings</h4>
                <p class="text-muted mb-0 small">Manage your account information and preferences</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="<?= APP_URL ?>/notifications" class="btn ui-btn-secondary btn-sm position-relative" title="Notifications">
                    <i class="bi bi-bell"></i>
                    <?php if (getUnreadNotificationCount() > 0): ?><span class="notification-dot"></span><?php endif; ?>
                </a>
                <button type="button" class="btn ui-btn-secondary btn-sm" onclick="toggleDarkMode()" data-theme-toggle="true" title="Toggle Dark Mode">
                    <i class="bi bi-moon header-theme-icon" id="headerThemeIcon"></i>
                </button>
            </div>
        </div>
        
        <?php displayFlash(); ?>
        
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <ul class="nav nav-tabs card-header-tabs" id="profileTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="info-tab" data-bs-toggle="tab" href="#info">
                            <i class="bi bi-person me-2"></i>Personal Info
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="security-tab" data-bs-toggle="tab" href="#security">
                            <i class="bi bi-shield-lock me-2"></i>Security
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="notifications-tab" data-bs-toggle="tab" href="#notifications">
                            <i class="bi bi-bell me-2"></i>Notifications
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                    <div class="tab-content" id="profileTabsContent">
                        <!-- Personal Info Tab -->
                        <div class="tab-pane fade show active" id="info" role="tabpanel">
                            <!-- View Mode -->
                            <div id="viewMode">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h6 class="mb-0"><i class="bi bi-person-circle me-2"></i>Account Information</h6>
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editPasswordModal">
                                        <i class="bi bi-pencil me-1"></i>Edit Profile
                                    </button>
                                </div>
                                
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small mb-1">First Name</label>
                                        <p class="mb-0 fw-medium"><?= htmlspecialchars($user['first_name'] ?? 'N/A') ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small mb-1">Last Name</label>
                                        <p class="mb-0 fw-medium"><?= htmlspecialchars($user['last_name'] ?? 'N/A') ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small mb-1">School ID</label>
                                        <p class="mb-0 fw-medium"><?= htmlspecialchars($user['school_id'] ?? 'N/A') ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small mb-1">Email Address</label>
                                        <p class="mb-0 fw-medium"><?= htmlspecialchars($user['email'] ?? 'N/A') ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small mb-1">Contact Number</label>
                                        <p class="mb-0 fw-medium"><?= htmlspecialchars($user['contact_number'] ?? 'N/A') ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small mb-1">Date of Birth</label>
                                        <p class="mb-0 fw-medium"><?= !empty($user['date_of_birth']) ? date('F j, Y', strtotime($user['date_of_birth'])) : 'N/A' ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small mb-1">Gender</label>
                                        <p class="mb-0 fw-medium"><?= ucfirst($user['gender'] ?? 'N/A') ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small mb-1">Department</label>
                                        <p class="mb-0 fw-medium"><?= htmlspecialchars($user['department'] ?? 'N/A') ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small mb-1">Year Level</label>
                                        <p class="mb-0 fw-medium"><?= htmlspecialchars($user['year_level'] ?? 'N/A') ?></p>
                                    </div>
                                </div>
                                
                                <hr class="my-4">
                                <h6 class="mb-3"><i class="bi bi-geo-alt me-2"></i>Address Information</h6>
                                
                                <div class="row g-4">
                                    <div class="col-12">
                                        <label class="form-label text-muted small mb-1">Street Address</label>
                                        <p class="mb-0 fw-medium"><?= htmlspecialchars($user['address_line1'] ?? 'N/A') ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small mb-1">Barangay</label>
                                        <p class="mb-0 fw-medium"><?= htmlspecialchars($user['address_line2'] ?? 'N/A') ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small mb-1">City/Municipality</label>
                                        <p class="mb-0 fw-medium"><?= htmlspecialchars($user['city'] ?? 'N/A') ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small mb-1">Province</label>
                                        <p class="mb-0 fw-medium"><?= htmlspecialchars($user['province'] ?? 'N/A') ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small mb-1">Postal Code</label>
                                        <p class="mb-0 fw-medium"><?= htmlspecialchars($user['postal_code'] ?? 'N/A') ?></p>
                                    </div>
                                </div>
                                
                                <hr class="my-4">
                                <h6 class="mb-3"><i class="bi bi-telephone me-2"></i>Emergency Contact</h6>
                                
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small mb-1">Contact Name</label>
                                        <p class="mb-0 fw-medium"><?= htmlspecialchars($user['emergency_contact_name'] ?? 'N/A') ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small mb-1">Contact Number</label>
                                        <p class="mb-0 fw-medium"><?= htmlspecialchars($user['emergency_contact_number'] ?? 'N/A') ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Edit Mode (hidden by default) -->
                            <div id="editMode" class="d-none">
                                <form id="profileForm" onsubmit="submitProfileForm(event)">
                                    <input type="hidden" name="password" id="editPassword">
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h6 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Profile</h6>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="cancelEdit()">
                                            <i class="bi bi-x me-1"></i>Cancel
                                        </button>
                                    </div>
                                    
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="first_name" name="first_name" 
                                                   value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="last_name" name="last_name" 
                                                   value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="school_id_display" class="form-label">School ID</label>
                                            <input type="text" class="form-control bg-light" id="school_id_display" 
                                                   value="<?= htmlspecialchars($user['school_id'] ?? '') ?>" readonly disabled>
                                            <small class="text-muted">School ID cannot be changed</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="email_display" class="form-label">Email Address</label>
                                            <input type="email" class="form-control bg-light" id="email_display" 
                                                   value="<?= htmlspecialchars($user['email'] ?? '') ?>" readonly disabled>
                                            <small class="text-muted">Email is linked to your School ID</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="contact_number" class="form-label">Contact Number <span class="text-danger">*</span></label>
                                            <input type="tel" class="form-control" id="contact_number" name="contact_number" 
                                                   value="<?= htmlspecialchars($user['contact_number'] ?? '') ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                                                   value="<?= htmlspecialchars($user['date_of_birth'] ?? '') ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="gender" class="form-label">Gender</label>
                                            <select class="form-select" id="gender" name="gender">
                                                <option value="">Select</option>
                                                <option value="male" <?= ($user['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
                                                <option value="female" <?= ($user['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                                                <option value="other" <?= ($user['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="department" class="form-label">Department</label>
                                            <input type="text" class="form-control" id="department" name="department" 
                                                   value="<?= htmlspecialchars($user['department'] ?? '') ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="year_level" class="form-label">Year Level</label>
                                            <input type="text" class="form-control" id="year_level" name="year_level" 
                                                   value="<?= htmlspecialchars($user['year_level'] ?? '') ?>">
                                        </div>
                                    </div>
                                    
                                    <hr class="my-4">
                                    <h6 class="mb-3"><i class="bi bi-geo-alt me-2"></i>Address Information</h6>
                                    
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="address_line1" class="form-label">Street Address</label>
                                            <input type="text" class="form-control" id="address_line1" name="address_line1" 
                                                   value="<?= htmlspecialchars($user['address_line1'] ?? '') ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="address_line2" class="form-label">Barangay</label>
                                            <input type="text" class="form-control" id="address_line2" name="address_line2" 
                                                   value="<?= htmlspecialchars($user['address_line2'] ?? '') ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="city" class="form-label">City/Municipality</label>
                                            <input type="text" class="form-control" id="city" name="city" 
                                                   value="<?= htmlspecialchars($user['city'] ?? '') ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="province" class="form-label">Province</label>
                                            <input type="text" class="form-control" id="province" name="province" 
                                                   value="<?= htmlspecialchars($user['province'] ?? '') ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="postal_code" class="form-label">Postal Code</label>
                                            <input type="text" class="form-control" id="postal_code" name="postal_code" 
                                                   value="<?= htmlspecialchars($user['postal_code'] ?? '') ?>">
                                        </div>
                                    </div>
                                    
                                    <hr class="my-4">
                                    <h6 class="mb-3"><i class="bi bi-telephone me-2"></i>Emergency Contact</h6>
                                    
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="emergency_contact_name" class="form-label">Contact Name</label>
                                            <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name" 
                                                   value="<?= htmlspecialchars($user['emergency_contact_name'] ?? '') ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="emergency_contact_number" class="form-label">Contact Number</label>
                                            <input type="text" class="form-control" id="emergency_contact_number" name="emergency_contact_number" 
                                                   value="<?= htmlspecialchars($user['emergency_contact_number'] ?? '') ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-lg me-2"></i>Save Changes
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary ms-2" onclick="cancelEdit()">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Security Tab -->
                        <div class="tab-pane fade" id="security" role="tabpanel">
                            <h6 class="mb-4"><i class="bi bi-key me-2"></i>Change Password</h6>
                            <form action="<?= APP_URL ?>/dashboard/profile/password" method="POST" autocomplete="off">
                                <!-- Hidden fields to prevent browser password manager popup -->
                                <input type="text" name="prevent_autofill" id="prevent_autofill" style="display:none;" autocomplete="off">
                                <input type="password" name="prevent_autofill_pass" id="prevent_autofill_pass" style="display:none;" autocomplete="off">
                                
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label for="current_password" class="form-label">Current Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" id="current_password" name="current_password" autocomplete="off" data-lpignore="true" data-form-type="other" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="new_password" class="form-label">New Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" 
                                               minlength="8" autocomplete="off" data-lpignore="true" data-form-type="other" required>
                                        <small class="text-muted">Minimum 8 characters</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="confirm_password" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" autocomplete="off" data-lpignore="true" data-form-type="other" required>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="bi bi-key me-2"></i>Change Password
                                    </button>
                                </div>
                            </form>
                            
                            <?php if (!empty($user['firebase_uid'])): ?>
                            <hr class="my-4">
                            <h6 class="mb-3"><i class="bi bi-google me-2"></i>Connected Accounts</h6>
                            <div class="alert alert-success d-flex align-items-center">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                <div>
                                    <strong>Google Account Connected</strong>
                                    <br><small class="text-muted">You can sign in using Google</small>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Notifications Tab -->
                        <div class="tab-pane fade" id="notifications" role="tabpanel">
                            <h6 class="mb-4"><i class="bi bi-bell me-2"></i>Notification Preferences</h6>
                            <form action="<?= APP_URL ?>/notifications/preferences" method="POST">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" 
                                           <?= ($preferences['email_notifications'] ?? true) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="email_notifications">
                                        <strong>Email Notifications</strong>
                                        <br><small class="text-muted">Receive notifications via email</small>
                                    </label>
                                </div>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="match_notifications" name="match_notifications" 
                                           <?= ($preferences['match_notifications'] ?? true) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="match_notifications">
                                        <strong>Match Notifications</strong>
                                        <br><small class="text-muted">Get notified when potential matches are found</small>
                                    </label>
                                </div>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="claim_notifications" name="claim_notifications" 
                                           <?= ($preferences['claim_notifications'] ?? true) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="claim_notifications">
                                        <strong>Claim Notifications</strong>
                                        <br><small class="text-muted">Get notified about claim status updates</small>
                                    </label>
                                </div>
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg me-2"></i>Save Preferences
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
    </main>
</div>

<!-- Password Verification Modal -->
<div class="modal fade" id="editPasswordModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-shield-lock me-2"></i>Verify Your Identity</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Please enter your password to edit your profile.</p>
                <div class="mb-3">
                    <label for="verifyPassword" class="form-label">Current Password</label>
                    <input type="password" class="form-control" id="verifyPassword" placeholder="Enter your password">
                    <div id="passwordError" class="invalid-feedback">Password is required</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="enableEditMode()">
                    <i class="bi bi-unlock me-1"></i>Continue
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function enableEditMode() {
    const password = document.getElementById('verifyPassword').value;
    
    if (!password) {
        document.getElementById('verifyPassword').classList.add('is-invalid');
        return;
    }
    
    // Store password for form submission
    document.getElementById('editPassword').value = password;
    
    // Hide modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('editPasswordModal'));
    modal.hide();
    
    // Show edit mode, hide view mode
    document.getElementById('viewMode').classList.add('d-none');
    document.getElementById('editMode').classList.remove('d-none');
    
    // Clear password field
    document.getElementById('verifyPassword').value = '';
    document.getElementById('verifyPassword').classList.remove('is-invalid');
}

function cancelEdit() {
    // Show view mode, hide edit mode
    document.getElementById('viewMode').classList.remove('d-none');
    document.getElementById('editMode').classList.add('d-none');
    
    // Clear stored password
    document.getElementById('editPassword').value = '';
}

// Clear modal state when hidden
document.getElementById('editPasswordModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('verifyPassword').value = '';
    document.getElementById('verifyPassword').classList.remove('is-invalid');
});

// Submit profile form via PUT request
async function submitProfileForm(event) {
    event.preventDefault();
    
    const form = document.getElementById('profileForm');
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
    
    // Build form data
    const formData = {
        password: document.getElementById('editPassword').value,
        first_name: document.getElementById('first_name').value,
        last_name: document.getElementById('last_name').value,
        contact_number: document.getElementById('contact_number').value,
        date_of_birth: document.getElementById('date_of_birth').value || null,
        gender: document.getElementById('gender').value || null,
        address_line1: document.getElementById('address_line1').value || null,
        address_line2: document.getElementById('address_line2').value || null,
        city: document.getElementById('city').value || null,
        province: document.getElementById('province').value || null,
        postal_code: document.getElementById('postal_code').value || null,
        emergency_contact_name: document.getElementById('emergency_contact_name').value || null,
        emergency_contact_number: document.getElementById('emergency_contact_number').value || null,
        department: document.getElementById('department').value || null,
        year_level: document.getElementById('year_level').value || null
    };
    
    try {
        const response = await fetch('<?= API_BASE_URL ?>/auth/profile', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer <?= $_SESSION['accessToken'] ?? '' ?>'
            },
            body: JSON.stringify(formData)
        });
        
        const result = await response.json();
        
        if (response.ok) {
            // Show success and reload to update view
            showAlert('success', 'Profile updated successfully!');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            // Show error message
            const errorMsg = result.message || result.error || 'Failed to update profile';
            showAlert('danger', errorMsg);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    } catch (error) {
        console.error('Profile update error:', error);
        showAlert('danger', 'An error occurred while updating your profile.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Insert at top of main content
    const mainContent = document.querySelector('.dashboard-main');
    const firstCard = mainContent.querySelector('.card');
    mainContent.insertBefore(alertDiv, firstCard);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => alertDiv.remove(), 5000);
}
</script>

<?php include __DIR__ . '/../layouts/footer-dashboard.php'; ?>
