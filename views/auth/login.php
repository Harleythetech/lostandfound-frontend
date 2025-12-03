<?php $pageTitle = 'Login - ' . APP_NAME; ?>
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
    <div class="auth-form-side">
        <div class="auth-form-wrapper">
            <!-- Loading Overlay -->
            <div id="authLoadingOverlay" class="auth-loading-overlay d-none">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            
            <div class="text-center mb-3">
                <i class="bi bi-box-arrow-in-right text-primary" style="font-size: 2.5rem;"></i>
                <h4 class="mt-2 fw-bold mb-1">Welcome Back</h4>
                <p class="text-muted small mb-0">Sign in to your account</p>
            </div>
            
            <?php if (isset($_GET['registered'])): ?>
            <div class="alert alert-success py-2 small" role="alert">
                <i class="bi bi-check-circle me-1"></i>
                Registration successful! Pending admin approval.
            </div>
            <?php endif; ?>
            
            <!-- Auth Error Alert -->
            <div id="authError" class="alert alert-danger py-2 small d-none" role="alert"></div>
            
            <!-- Firebase Sign-In -->
            <button type="button" class="btn btn-outline-secondary w-100 mb-3 firebase-btn" onclick="signInWithGoogle()">
                <svg class="me-2" width="18" height="18" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Continue with Google
            </button>
            
            <div class="divider-text mb-3"><span>or sign in with School ID</span></div>
            
            <form action="<?= APP_URL ?>/login" method="POST" id="loginForm">
                <div class="mb-2">
                    <label for="school_id" class="form-label small mb-1">School ID</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                        <input type="text" class="form-control" id="school_id" name="school_id" 
                               placeholder="XX-XXXXX" pattern="[A-Za-z0-9]{2}-[A-Za-z0-9]{5}" required>
                    </div>
                </div>
                
                <div class="mb-2">
                    <label for="password" class="form-label small mb-1">Password</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Enter your password" required>
                        <button class="btn btn-outline-secondary toggle-password" type="button">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                
                <div class="mb-3 d-flex justify-content-between align-items-center small">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="rememberMe" name="remember">
                        <label class="form-check-label" for="rememberMe">Remember me</label>
                    </div>
                    <a href="<?= APP_URL ?>/forgot-password" class="text-primary text-decoration-none">
                        Forgot Password?
                    </a>
                </div>
                
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                </button>
            </form>
            
            <p class="text-center small mt-3 mb-0">
                Don't have an account? 
                <a href="<?= APP_URL ?>/register" class="text-primary fw-semibold">Create one</a>
            </p>
        </div>
    </div>
</div>

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

<?php include __DIR__ . '/../layouts/blank_footer.php'; ?>
