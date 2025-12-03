<?php $pageTitle = '404 - Page Not Found'; ?>
<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <i class="bi bi-exclamation-triangle text-warning" style="font-size: 6rem;"></i>
            <h1 class="display-1 fw-bold text-muted">404</h1>
            <h3 class="fw-bold">Page Not Found</h3>
            <p class="text-muted mb-4">The page you're looking for doesn't exist or has been moved.</p>
            <div class="d-flex gap-3 justify-content-center">
                <a href="<?= APP_URL ?>/" class="btn btn-primary">
                    <i class="bi bi-house me-2"></i>Go Home
                </a>
                <a href="<?= APP_URL ?>/items" class="btn btn-outline-secondary">
                    <i class="bi bi-grid me-2"></i>Browse Items
                </a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
