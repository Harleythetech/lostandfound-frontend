<?php $pageTitle = 'Reset Password - ' . APP_NAME; ?>
<?php include __DIR__ . '/../layouts/header_nonav.php'; ?>

<div class="auth-split-container">
    <!-- Left Side - Brand -->
    <div class="auth-brand-side">
        <div class="auth-brand-top p-3">
            <a href="<?= APP_URL ?>/" class="btn btn-outline-light btn-sm me-2"><i
                    class="bi bi-arrow-left me-1"></i>Back</a>
            <button class="btn btn-outline-light btn-sm" type="button" onclick="toggleDarkMode()" title="Toggle theme">
                <i id="authBrandThemeIcon" class="bi bi-moon"></i>
            </button>
        </div>
        <div class="auth-brand-content">
            <a href="<?= APP_URL ?>/" class="auth-brand-logo">
                <i class="bi bi-search-heart"></i>
                <span><?= APP_NAME ?></span>
            </a>
            <p class="auth-brand-tagline">Helping reunite people with their lost belongings</p>
        </div>
    </div>

    <!-- Right Side - Form -->
    <div class="auth-form-side">
        <div class="auth-form-wrapper">

            <div class="text-center mb-4">
                <i class="bi bi-shield-lock text-primary" style="font-size: 2.5rem;"></i>
                <h4 class="mt-2 fw-bold mb-1">Reset Password</h4>
                <p class="text-muted small mb-0">Enter your new password below</p>
            </div>

            <?php displayFlash(); ?>

            <form action="<?= APP_URL ?>/reset-password" method="POST">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">

                <div class="mb-3">
                    <label for="new_password" class="form-label small mb-1">New Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control" id="new_password" name="new_password"
                            placeholder="Enter new password" required minlength="8">
                        <button class="btn btn-outline-secondary toggle-password" type="button">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <small class="text-muted">Minimum 8 characters</small>
                </div>

                <div class="mb-4">
                    <label for="confirm_password" class="form-label small mb-1">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                            placeholder="Confirm new password" required minlength="8">
                        <button class="btn btn-outline-secondary toggle-password" type="button">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-check-circle me-2"></i>Reset Password
                </button>
            </form>

            <p class="text-center small mt-4 mb-0">
                Remember your password?
                <a href="<?= APP_URL ?>/login" class="text-primary fw-semibold">Sign in</a>
            </p>
        </div>
    </div>
</div>

<script>
    // Client-side password match validation
    document.querySelector('form').addEventListener('submit', function (e) {
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        if (newPassword !== confirmPassword) {
            e.preventDefault();
            alert('Passwords do not match!');
        }
    });
</script>

<?php include __DIR__ . '/../layouts/blank_footer.php'; ?>