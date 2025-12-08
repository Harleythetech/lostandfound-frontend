<nav class="navbar navbar-expand-lg navbar-dark bg-custom-blue sticky-top">
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
                    <button type="button" class="btn ui-btn-secondary btn-sm" onclick="toggleDarkMode()"
                        data-theme-toggle="true" title="Toggle Dark Mode">
                        <i class="bi bi-moon header-theme-icon" id="headerThemeIcon"></i>
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