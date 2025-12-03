<?php $pageTitle = 'Register - ' . APP_NAME; ?>
<?php include __DIR__ . '/../layouts/header_nonav.php'; ?>

<div class="auth-split-container">
    <!-- Left Side - Brand -->
    <div class="auth-brand-side">
        <div class="auth-brand-content">
            <a href="<?= APP_URL ?>/" class="auth-brand-logo">
                <i class="bi bi-search-heart"></i>
                <span><?= APP_NAME ?></span>
            </a>
            <p class="auth-brand-tagline">Helping reunite people with their lost belongings</p>
        </div>
        <button class="btn btn-link auth-theme-toggle" onclick="toggleDarkMode()" title="Toggle theme">
            <i id="navThemeIcon" class="bi bi-moon"></i>
        </button>
    </div>
    
    <!-- Right Side - Form -->
    <div class="auth-form-side auth-form-side-wide">
        <div class="auth-form-wrapper auth-form-wrapper-wide position-relative">
            <!-- Loading Overlay -->
            <div id="authLoadingOverlay" class="auth-loading-overlay d-none">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            
            <div class="text-center mb-2">
                <i class="bi bi-person-plus text-primary" style="font-size: 2rem;"></i>
                <h4 class="mt-1 fw-bold mb-0">Create Account</h4>
                <p class="text-muted small mb-2">Join our Lost and Found community</p>
            </div>
            
            <!-- Auth Error Alert -->
            <div id="authError" class="alert alert-danger py-2 small d-none" role="alert"></div>
            
            <!-- Firebase Quick Sign Up -->
            <div class="text-center mb-2">
                <small class="text-muted">Quick sign up with</small>
            </div>
            <button type="button" class="btn btn-outline-secondary w-100 mb-2 firebase-btn" onclick="signUpWithGoogleForRegistration()">
                <svg class="me-2" width="16" height="16" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Google
            </button>
            <small class="text-muted d-block text-center mb-2">We'll pre-fill your info from your Google account</small>
            
            <div class="divider-text mb-2"><span>or fill out the form manually</span></div>
            
            <!-- Hidden field for Firebase token -->
            <input type="hidden" id="firebase_token" name="firebase_token" value="">
            
            <form action="<?= APP_URL ?>/register" method="POST" id="registerForm">
                <!-- Progress Steps -->
                <div class="step-progress-compact mb-3">
                    <div class="d-flex justify-content-between">
                        <div class="step-compact active" data-step="1">
                            <div class="step-circle-compact">1</div>
                            <small>Account</small>
                        </div>
                        <div class="step-compact" data-step="2">
                            <div class="step-circle-compact">2</div>
                            <small>Personal</small>
                        </div>
                        <div class="step-compact" data-step="3">
                            <div class="step-circle-compact">3</div>
                            <small>Address</small>
                        </div>
                        <div class="step-compact" data-step="4">
                            <div class="step-circle-compact">4</div>
                            <small>Emergency</small>
                        </div>
                    </div>
                </div>

                <!-- Step 1: Account Information -->
                <div class="step-content" data-step="1">
                    <h6 class="mb-2"><i class="bi bi-person-badge me-1"></i>Account Information</h6>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label for="school_id" class="form-label small mb-1">School ID <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                                <input type="text" class="form-control" id="school_id" name="school_id" 
                                       placeholder="XX-XXXXX" pattern="[A-Za-z0-9]{2}-[A-Za-z0-9]{5}" required>
                            </div>
                            <small class="text-muted" style="font-size: 0.7rem;">Format: XX-XXXXX (e.g., 20-12345)</small>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label small mb-1">Email Address <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" 
                                       placeholder="Enter your email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label small mb-1">Password <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="Create a password" minlength="8" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <small class="text-muted" style="font-size: 0.7rem;">Minimum 8 characters, include uppercase, lowercase, number</small>
                        </div>
                        <div class="col-md-6">
                            <label for="confirm_password" class="form-label small mb-1">Confirm Password <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                       placeholder="Confirm your password" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Personal Information -->
                <div class="step-content d-none" data-step="2">
                    <h6 class="mb-2"><i class="bi bi-person me-1"></i>Personal Information</h6>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label small mb-1">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="first_name" name="first_name" 
                                   placeholder="Enter first name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label small mb-1">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="last_name" name="last_name" 
                                   placeholder="Enter last name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="contact_number" class="form-label small mb-1">Contact Number <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="bi bi-phone"></i></span>
                                <input type="tel" class="form-control" id="contact_number" name="contact_number" 
                                       placeholder="09XXXXXXXXX" pattern="^(09|\+639)[0-9]{9}$" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="date_of_birth" class="form-label small mb-1">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-sm" id="date_of_birth" name="date_of_birth" required>
                        </div>
                        <div class="col-md-4">
                            <label for="gender" class="form-label small mb-1">Gender <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" id="gender" name="gender" required>
                                <option value="">Select</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="department" class="form-label small mb-1">Department</label>
                            <input type="text" class="form-control form-control-sm" id="department" name="department" 
                                   placeholder="e.g., BSIT">
                        </div>
                        <div class="col-md-4">
                            <label for="year_level" class="form-label small mb-1">Year Level</label>
                            <select class="form-select form-select-sm" id="year_level" name="year_level">
                                <option value="">Select</option>
                                <option value="1">1st Year</option>
                                <option value="2">2nd Year</option>
                                <option value="3">3rd Year</option>
                                <option value="4">4th Year</option>
                                <option value="5">5th Year</option>
                                <option value="graduate">Graduate</option>
                                <option value="faculty">Faculty</option>
                                <option value="staff">Staff</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Address Information -->
                <div class="step-content d-none" data-step="3">
                    <h6 class="mb-2"><i class="bi bi-geo-alt me-1"></i>Address Information</h6>
                    <div class="row g-2">
                        <div class="col-12">
                            <label for="street_address" class="form-label small mb-1">Street Address <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="street_address" name="street_address" 
                                   placeholder="House/Unit No., Street Name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="barangay" class="form-label small mb-1">Barangay <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="barangay" name="barangay" 
                                   placeholder="Enter barangay" required>
                        </div>
                        <div class="col-md-6">
                            <label for="city" class="form-label small mb-1">City/Municipality <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="city" name="city" 
                                   placeholder="Enter city" required>
                        </div>
                        <div class="col-md-6">
                            <label for="province" class="form-label small mb-1">Province <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="province" name="province" 
                                   placeholder="Enter province" required>
                        </div>
                        <div class="col-md-6">
                            <label for="postal_code" class="form-label small mb-1">Postal Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="postal_code" name="postal_code" 
                                   placeholder="Enter postal code" required>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Emergency Contact -->
                <div class="step-content d-none" data-step="4">
                    <h6 class="mb-2"><i class="bi bi-telephone me-1"></i>Emergency Contact</h6>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label for="emergency_contact_name" class="form-label small mb-1">Contact Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="emergency_contact_name" name="emergency_contact_name" 
                                   placeholder="Full name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="emergency_contact_number" class="form-label small mb-1">Contact Number <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="bi bi-phone"></i></span>
                                <input type="tel" class="form-control" id="emergency_contact_number" name="emergency_contact_number" 
                                       placeholder="09XXXXXXXXX" pattern="^(09|\+639)[0-9]{9}$" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="emergency_contact_relationship" class="form-label small mb-1">Relationship <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" id="emergency_contact_relationship" name="emergency_contact_relationship" required>
                                <option value="">Select relationship</option>
                                <option value="parent">Parent</option>
                                <option value="guardian">Guardian</option>
                                <option value="sibling">Sibling</option>
                                <option value="spouse">Spouse</option>
                                <option value="relative">Other Relative</option>
                                <option value="friend">Friend</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                <label class="form-check-label small" for="terms">
                                    I agree to the <a href="<?= APP_URL ?>/terms" target="_blank">Terms</a> 
                                    and <a href="<?= APP_URL ?>/privacy" target="_blank">Privacy Policy</a>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="d-flex justify-content-between mt-3">
                    <button type="button" class="btn btn-outline-secondary btn-sm btn-prev d-none" id="prevBtn">
                        <i class="bi bi-arrow-left me-1"></i>Previous
                    </button>
                    <button type="button" class="btn btn-primary btn-sm btn-next ms-auto" id="nextBtn">
                        Next<i class="bi bi-arrow-right ms-1"></i>
                    </button>
                    <button type="submit" class="btn btn-success btn-sm btn-submit d-none ms-auto" id="submitBtn">
                        <i class="bi bi-person-plus me-1"></i>Create Account
                    </button>
                </div>
            </form>
            
            <p class="text-center small mt-2 mb-0">
                Already have an account? 
                <a href="<?= APP_URL ?>/login" class="text-primary fw-semibold">Sign in</a>
            </p>
        </div>
    </div>
</div>

<style>
.step-progress-compact {
    position: relative;
}
.step-progress-compact::before {
    content: '';
    position: absolute;
    top: 12px;
    left: 40px;
    right: 40px;
    height: 2px;
    background: #e0e0e0;
    z-index: 0;
}
.step-compact {
    text-align: center;
    position: relative;
    z-index: 1;
}
.step-compact small {
    font-size: 0.65rem;
}
.step-circle-compact {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #e0e0e0;
    color: #666;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 3px;
    font-weight: bold;
    font-size: 0.75rem;
    transition: all 0.3s;
}
.step-compact.active .step-circle-compact,
.step-compact.completed .step-circle-compact {
    background: var(--bs-primary);
    color: white;
}

/* Dark mode for step progress */
body.dark-mode .step-progress-compact::before {
    background: #374151;
}
body.dark-mode .step-circle-compact {
    background: #374151;
    color: #9ca3af;
}
body.dark-mode .step-compact.active .step-circle-compact,
body.dark-mode .step-compact.completed .step-circle-compact {
    background: #3b82f6;
    color: white;
}
</style>

<!-- Firebase SDK -->
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-auth-compat.js"></script>
<script>
    const API_BASE_URL = '<?= API_BASE_URL ?>';
    const APP_URL = '<?= APP_URL ?>';
    
    // Firebase config from server environment
    const FIREBASE_CONFIG = {
        apiKey: '<?= FIREBASE_API_KEY ?>',
        authDomain: '<?= FIREBASE_AUTH_DOMAIN ?>',
        projectId: '<?= FIREBASE_PROJECT_ID ?>',
        storageBucket: '<?= FIREBASE_STORAGE_BUCKET ?>',
        messagingSenderId: '<?= FIREBASE_MESSAGING_SENDER_ID ?>',
        appId: '<?= FIREBASE_APP_ID ?>'
    };
</script>
<script src="<?= APP_URL ?>/assets/js/firebase-auth.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentStep = 1;
    const totalSteps = 4;
    
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    const submitBtn = document.getElementById('submitBtn');
    
    function showStep(step) {
        document.querySelectorAll('.step-content').forEach(el => el.classList.add('d-none'));
        document.querySelector(`.step-content[data-step="${step}"]`).classList.remove('d-none');
        
        document.querySelectorAll('.step-compact').forEach(el => {
            const stepNum = parseInt(el.dataset.step);
            el.classList.remove('active', 'completed');
            if (stepNum < step) el.classList.add('completed');
            if (stepNum === step) el.classList.add('active');
        });
        
        prevBtn.classList.toggle('d-none', step === 1);
        nextBtn.classList.toggle('d-none', step === totalSteps);
        submitBtn.classList.toggle('d-none', step !== totalSteps);
    }
    
    function validateStep(step) {
        const stepContent = document.querySelector(`.step-content[data-step="${step}"]`);
        const inputs = stepContent.querySelectorAll('input[required], select[required]');
        let valid = true;
        
        inputs.forEach(input => {
            if (!input.checkValidity()) {
                input.classList.add('is-invalid');
                valid = false;
            } else {
                input.classList.remove('is-invalid');
            }
        });
        
        // Check password match on step 1
        if (step === 1) {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('confirm_password').value;
            if (password !== confirm) {
                document.getElementById('confirm_password').classList.add('is-invalid');
                valid = false;
            }
        }
        
        return valid;
    }
    
    nextBtn.addEventListener('click', function() {
        if (validateStep(currentStep) && currentStep < totalSteps) {
            currentStep++;
            showStep(currentStep);
        }
    });
    
    prevBtn.addEventListener('click', function() {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    });
    
    // Handle form submission with Firebase token
    document.getElementById('registerForm').addEventListener('submit', async function(e) {
        if (!validateStep(currentStep)) {
            e.preventDefault();
            return;
        }
        
        const firebaseToken = document.getElementById('firebase_token').value;
        if (firebaseToken) {
            e.preventDefault();
            showLoading();
            
            // Collect form data
            const formData = new FormData(this);
            const data = {};
            formData.forEach((value, key) => {
                if (key !== 'firebase_token' && key !== 'confirm_password' && key !== 'terms') {
                    data[key] = value;
                }
            });
            
            // Map form field names to API field names
            if (data.street_address) {
                data.address_line1 = data.street_address;
                delete data.street_address;
            }
            if (data.barangay !== undefined) {
                data.address_line2 = data.barangay || '';
                delete data.barangay;
            }
            
            // Ensure optional fields have defaults
            data.department = data.department || '';
            data.year_level = data.year_level || '';
            
            // Use Firebase registration
            const success = await firebaseRegister(data, firebaseToken);
            if (!success) {
                hideLoading();
            }
        }
    });
});

// Special function for registration with Google
async function signUpWithGoogleForRegistration() {
    showLoading();
    try {
        const result = await firebase.auth().signInWithPopup(googleProvider);
        const user = result.user;
        const idToken = await user.getIdToken();
        
        // Store the token for form submission
        document.getElementById('firebase_token').value = idToken;
        
        // Pre-fill form from Firebase user
        prefillFormFromFirebase(user);
        
        hideLoading();
        showSuccess('Google account connected! Please complete the remaining fields.');
        
    } catch (error) {
        hideLoading();
        console.error('Google sign-up error:', error);
        showAuthError(getFirebaseErrorMessage(error));
    }
}

function showSuccess(message) {
    const alertDiv = document.getElementById('authError');
    if (alertDiv) {
        alertDiv.classList.remove('d-none', 'alert-danger');
        alertDiv.classList.add('alert-success');
        alertDiv.textContent = message;
    }
}
</script>

<?php include __DIR__ . '/../layouts/blank_footer.php'; ?>
