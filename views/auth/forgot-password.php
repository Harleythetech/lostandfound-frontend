<?php $pageTitle = 'Forgot Password - ' . APP_NAME; ?>
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
            <div class="text-center mb-4">
                <i class="bi bi-key text-primary" style="font-size: 2.5rem;"></i>
                <h4 class="mt-2 fw-bold mb-1">Forgot Password?</h4>
                <p class="text-muted small mb-0">Enter your email to reset your password</p>
            </div>
            
            <form action="<?= APP_URL ?>/forgot-password" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label small mb-1">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="your.email@example.com" required>
                    </div>
                    <small class="text-muted">We'll send a password reset link to this email</small>
                </div>
                
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-envelope me-2"></i>Send Reset Link
                </button>
            </form>
            
            <p class="text-center small mt-4 mb-0">
                Remember your password? 
                <a href="<?= APP_URL ?>/login" class="text-primary fw-semibold">Sign in</a>
            </p>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/blank_footer.php'; ?>
