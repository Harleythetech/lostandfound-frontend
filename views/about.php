<?php $pageTitle = 'About Us - ' . APP_NAME; ?>
<?php include __DIR__ . '/layouts/header.php'; ?>

<!-- Hero Section -->
<section class="about-hero py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 mb-3">About Us</span>
                <h1 class="display-5 fw-bold mb-4">Reuniting People With Their Belongings</h1>
                <p class="lead text-muted">
                    <?= APP_NAME ?> is a community-driven platform designed to help students and staff 
                    recover their lost belongings quickly and efficiently.
                </p>
            </div>
            <div class="col-lg-6 text-center d-none d-lg-block">
                <i class="bi bi-search-heart text-primary" style="font-size: 12rem; opacity: 0.2;"></i>
            </div>
        </div>
    </div>
</section>

<div class="container py-5">
    <!-- Mission Section -->
    <div class="row justify-content-center mb-5">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="row g-0">
                    <div class="col-md-4 bg-primary d-flex align-items-center justify-content-center p-4">
                        <i class="bi bi-bullseye text-white" style="font-size: 5rem;"></i>
                    </div>
                    <div class="col-md-8">
                        <div class="card-body p-4">
                            <h3 class="fw-bold mb-3">Our Mission</h3>
                            <p class="mb-3">We understand how stressful it can be to lose something important, whether it's your wallet, keys, phone, or precious documents.</p>
                            <p class="mb-0">Our mission is to create a simple, efficient, and free platform that connects people who have lost items with those who have found them, fostering a helpful and honest community.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- How It Works -->
    <div class="text-center mb-4">
        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 mb-3">Process</span>
        <h2 class="fw-bold">How It Works</h2>
    </div>
    
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="text-center p-4">
                <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-camera" style="font-size: 2rem;"></i>
                </div>
                <h5 class="fw-bold">1. Report</h5>
                <p class="text-muted small">Create a detailed report with photos and description of your lost or found item.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="text-center p-4">
                <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-search" style="font-size: 2rem;"></i>
                </div>
                <h5 class="fw-bold">2. Match</h5>
                <p class="text-muted small">Our system automatically matches lost items with found reports and notifies you.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="text-center p-4">
                <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-hand-thumbs-up" style="font-size: 2rem;"></i>
                </div>
                <h5 class="fw-bold">3. Connect</h5>
                <p class="text-muted small">Verify ownership and coordinate with the finder to retrieve your belongings.</p>
            </div>
        </div>
    </div>
    
    <!-- Values -->
    <div class="row justify-content-center mb-5">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 mb-3">Values</span>
                        <h3 class="fw-bold">What We Stand For</h3>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex gap-3">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="bi bi-people"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">Community First</h6>
                                    <p class="text-muted small mb-0">We believe in the power of community to help each other in times of need.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-3">
                                <div class="flex-shrink-0">
                                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="bi bi-currency-dollar"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">Free Forever</h6>
                                    <p class="text-muted small mb-0">Our platform is completely free to use for everyone, with no hidden charges.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-3">
                                <div class="flex-shrink-0">
                                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="bi bi-shield-lock"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">Privacy Focused</h6>
                                    <p class="text-muted small mb-0">We respect your privacy and protect your personal data at all times.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-3">
                                <div class="flex-shrink-0">
                                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="bi bi-lightning-charge"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">Simple & Fast</h6>
                                    <p class="text-muted small mb-0">We keep things simple so anyone can report and find items quickly.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Developer Section -->
    <div class="row justify-content-center mb-5">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body p-5">
                    <span class="badge bg-dark bg-opacity-10 text-dark px-3 py-2 mb-3">Development</span>
                    <h3 class="fw-bold mb-4">Developed By</h3>
                    <div class="mb-4">
                        <img src="<?= APP_URL ?>/assets/img/logo.png" alt="The South Devs" 
                             class="shadow-sm" 
                             style="width: 150px; height: 150px; object-fit: contain; border-radius: 16px;">
                    </div>
                    <h4 class="fw-bold mb-2">The South Devs</h4>
                    <p class="text-muted mb-0">Building solutions for the campus community</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- CTA -->
    <div class="text-center">
        <h4 class="fw-bold mb-3">Ready to Get Started?</h4>
        <p class="text-muted mb-4">Join our community and help reunite lost items with their owners.</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="<?= APP_URL ?>/register" class="btn btn-primary btn-lg">
                <i class="bi bi-person-plus me-2"></i>Create Account
            </a>
            <a href="<?= APP_URL ?>/login" class="btn btn-outline-primary btn-lg">
                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
            </a>
        </div>
    </div>
</div>

<style>
.about-hero {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
}

body.dark-mode .about-hero {
    background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
}
</style>

<?php include __DIR__ . '/layouts/footer.php'; ?>
