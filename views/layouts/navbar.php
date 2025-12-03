<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
    <div class="container py-2">
        <a class="navbar-brand fw-bold" href="<?= APP_URL ?>/">
            <?= APP_NAME ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?= APP_URL ?>/">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= APP_URL ?>/about">About</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <button class="nav-link btn btn-link text-white" onclick="toggleDarkMode()" title="Toggle theme">
                        <i id="navThemeIcon" class="bi bi-moon"></i>
                    </button>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?= APP_URL ?>/login">
                        Sign In
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<?php $flash = getFlash(); ?>
<?php if ($flash): ?>
<div class="container mt-3">
    <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<?php endif; ?>
