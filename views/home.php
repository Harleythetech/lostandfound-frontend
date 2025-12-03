<?php $pageTitle = APP_NAME . ' - Find Your Lost Items'; ?>
<?php include __DIR__ . '/layouts/header.php'; ?>

<!-- Hero Section - Modern Split Design -->
<section class="landing-hero">
    <div class="container">
        <div class="row align-items-center min-vh-75">
            <div class="col-lg-6 py-5">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 mb-3">
                    <i class="bi bi-lightning-charge me-1"></i>Campus Lost & Found Platform
                </span>
                <h1 class="display-4 fw-bold mb-4 landing-title">
                    Lost Something? <br>
                    <span class="text-primary">We'll Help You Find It.</span>
                </h1>
                <p class="lead text-muted mb-4">
                    Join our campus community to report, search, and recover lost items. 
                    Quick, easy, and completely free.
                </p>
                <div class="d-flex gap-3 flex-wrap mb-4">
                    <a href="<?= APP_URL ?>/register" class="btn btn-primary btn-lg px-4">
                        <i class="bi bi-rocket-takeoff me-2"></i>Get Started
                    </a>
                    <a href="<?= APP_URL ?>/login" class="btn btn-outline-secondary btn-lg px-4">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                    </a>
                </div>
                <div class="d-flex gap-4 text-muted small">
                    <span><i class="bi bi-check-circle text-success me-1"></i>100% Free</span>
                    <span><i class="bi bi-check-circle text-success me-1"></i>Secure</span>
                    <span><i class="bi bi-check-circle text-success me-1"></i>Fast Matching</span>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <div class="landing-hero-graphic">
                    <div class="hero-card hero-card-1">
                        <i class="bi bi-wallet2 text-danger"></i>
                        <span>Wallet Found!</span>
                    </div>
                    <div class="hero-card hero-card-2">
                        <i class="bi bi-key text-warning"></i>
                        <span>Keys Matched</span>
                    </div>
                    <div class="hero-card hero-card-3">
                        <i class="bi bi-phone text-info"></i>
                        <span>Phone Returned</span>
                    </div>
                    <div class="hero-main-icon">
                        <i class="bi bi-search-heart"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Counter -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-4">
                <div class="stat-counter">
                    <h2 class="display-5 fw-bold mb-0"><?= $stats['total_lost'] ?? 0 ?></h2>
                    <p class="mb-0 opacity-75 small">Items Lost</p>
                </div>
            </div>
            <div class="col-4">
                <div class="stat-counter border-start border-end border-white-25">
                    <h2 class="display-5 fw-bold mb-0"><?= $stats['total_found'] ?? 0 ?></h2>
                    <p class="mb-0 opacity-75 small">Items Found</p>
                </div>
            </div>
            <div class="col-4">
                <div class="stat-counter">
                    <h2 class="display-5 fw-bold mb-0"><?= $stats['total_resolved'] ?? 0 ?></h2>
                    <p class="mb-0 opacity-75 small">Reunited</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works - Modern Steps -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 mb-3">Simple Process</span>
            <h2 class="fw-bold">How It Works</h2>
            <p class="text-muted">Three simple steps to find or return lost items</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="how-it-works-card text-center p-4 h-100">
                    <div class="step-number">1</div>
                    <div class="step-icon bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-camera"></i>
                    </div>
                    <h5 class="fw-bold mt-3">Report</h5>
                    <p class="text-muted small mb-0">Take a photo and describe the item you lost or found with location details.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="how-it-works-card text-center p-4 h-100">
                    <div class="step-number">2</div>
                    <div class="step-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-cpu"></i>
                    </div>
                    <h5 class="fw-bold mt-3">Smart Match</h5>
                    <p class="text-muted small mb-0">Our system automatically matches lost items with found reports.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="how-it-works-card text-center p-4 h-100">
                    <div class="step-number">3</div>
                    <div class="step-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-hand-thumbs-up"></i>
                    </div>
                    <h5 class="fw-bold mt-3">Reunite</h5>
                    <p class="text-muted small mb-0">Verify ownership and coordinate pickup to get your item back.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Recent Lost Items -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 mb-2">Missing Items</span>
                <h3 class="fw-bold mb-0">Recently Lost</h3>
            </div>
        </div>
        
        <?php if (!empty($recentLost)): ?>
        <div class="row g-3">
            <?php foreach ($recentLost as $item): ?>
            <?php 
                $imageUrl = $item['primary_image'] ?? $item['image'] ?? $item['image_url'] ?? '';
                if ($imageUrl && !str_starts_with($imageUrl, 'http')) {
                    // API_BASE_URL ends with /api, image paths start with /api/uploads - remove duplicate
                    $baseUrl = str_replace('/api', '', API_BASE_URL);
                    $imageUrl = rtrim($baseUrl, '/') . '/' . ltrim($imageUrl, '/');
                }
                $location = $item['location'] ?? $item['location_name'] ?? $item['last_seen_location'] ?? 'Unknown';
            ?>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card h-100 border-0 shadow-sm item-card-modern">
                    <div class="item-card-image">
                        <?php if ($imageUrl): ?>
                        <img src="<?= htmlspecialchars($imageUrl) ?>" alt="<?= htmlspecialchars($item['title'] ?? 'Item') ?>"
                             onerror="this.onerror=null; this.src='<?= APP_URL ?>/assets/img/no-image.svg';">
                        <?php else: ?>
                        <div class="no-image"><i class="bi bi-image"></i></div>
                        <?php endif; ?>
                        <span class="item-badge lost">Lost</span>
                    </div>
                    <div class="card-body p-2">
                        <h6 class="card-title mb-1 text-truncate" title="<?= htmlspecialchars($item['title'] ?? '') ?>"><?= htmlspecialchars($item['title'] ?? 'Unknown') ?></h6>
                        <small class="text-muted d-block text-truncate">
                            <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($location) ?>
                        </small>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="bi bi-inbox text-muted display-4 mb-3 d-block"></i>
            <p class="text-muted">No lost items reported yet.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Recent Found Items -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 mb-2">Found Items</span>
                <h3 class="fw-bold mb-0">Recently Found</h3>
            </div>
        </div>
        
        <?php if (!empty($recentFound)): ?>
        <div class="row g-3">
            <?php foreach ($recentFound as $item): ?>
            <?php 
                $imageUrl = $item['primary_image'] ?? $item['image'] ?? $item['image_url'] ?? '';
                if (!empty($item['images']) && is_array($item['images']) && !$imageUrl) {
                    $imageUrl = $item['images'][0];
                }
                if ($imageUrl && !str_starts_with($imageUrl, 'http')) {
                    // API_BASE_URL ends with /api, image paths start with /api/uploads - remove duplicate
                    $baseUrl = str_replace('/api', '', API_BASE_URL);
                    $imageUrl = rtrim($baseUrl, '/') . '/' . ltrim($imageUrl, '/');
                }
                $location = $item['location'] ?? $item['location_name'] ?? $item['found_location'] ?? 'Unknown';
            ?>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card h-100 border-0 shadow-sm item-card-modern">
                    <div class="item-card-image">
                        <?php if ($imageUrl): ?>
                        <img src="<?= htmlspecialchars($imageUrl) ?>" alt="<?= htmlspecialchars($item['title'] ?? 'Item') ?>"
                             onerror="this.onerror=null; this.src='<?= APP_URL ?>/assets/img/no-image.svg';">
                        <?php else: ?>
                        <div class="no-image"><i class="bi bi-image"></i></div>
                        <?php endif; ?>
                        <span class="item-badge found">Found</span>
                    </div>
                    <div class="card-body p-2">
                        <h6 class="card-title mb-1 text-truncate" title="<?= htmlspecialchars($item['title'] ?? '') ?>"><?= htmlspecialchars($item['title'] ?? 'Unknown') ?></h6>
                        <small class="text-muted d-block text-truncate">
                            <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($location) ?>
                        </small>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="bi bi-inbox text-muted display-4 mb-3 d-block"></i>
            <p class="text-muted">No found items reported yet.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Features -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 mb-3">Features</span>
            <h2 class="fw-bold">Why Choose Us?</h2>
        </div>
        
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="feature-card text-center p-4 h-100">
                    <div class="feature-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-lightning-charge"></i>
                    </div>
                    <h6 class="fw-bold mt-3">Fast & Easy</h6>
                    <p class="text-muted small mb-0">Report items in under 2 minutes with our simple form.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card text-center p-4 h-100">
                    <div class="feature-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h6 class="fw-bold mt-3">Secure</h6>
                    <p class="text-muted small mb-0">Your data is protected and never shared without consent.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card text-center p-4 h-100">
                    <div class="feature-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-bell"></i>
                    </div>
                    <h6 class="fw-bold mt-3">Notifications</h6>
                    <p class="text-muted small mb-0">Get instant alerts when potential matches are found.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card text-center p-4 h-100">
                    <div class="feature-icon bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <h6 class="fw-bold mt-3">100% Free</h6>
                    <p class="text-muted small mb-0">No hidden fees, no premium plans. Free forever.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 cta-section">
    <div class="container text-center py-4">
        <h2 class="fw-bold text-white mb-3">Ready to Find Your Lost Item?</h2>
        <p class="text-white-50 mb-4">Join hundreds of students using our platform every day.</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="<?= APP_URL ?>/register" class="btn btn-light btn-lg px-4">
                <i class="bi bi-person-plus me-2"></i>Create Free Account
            </a>
            <a href="<?= APP_URL ?>/login" class="btn btn-outline-light btn-lg px-4">
                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
            </a>
        </div>
    </div>
</section>

<style>
/* Landing Hero */
.landing-hero {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    position: relative;
    overflow: hidden;
}

.landing-title {
    line-height: 1.2;
}

.min-vh-75 {
    min-height: 75vh;
}

/* Hero Graphics */
.landing-hero-graphic {
    position: relative;
    height: 400px;
}

.hero-main-icon {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 12rem;
    color: var(--bs-primary);
    opacity: 0.1;
}

.hero-card {
    position: absolute;
    background: white;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-weight: 600;
    font-size: 0.9rem;
    animation: float 3s ease-in-out infinite;
}

.hero-card i {
    font-size: 1.5rem;
}

.hero-card-1 {
    top: 15%;
    left: 10%;
    animation-delay: 0s;
}

.hero-card-2 {
    top: 45%;
    right: 5%;
    animation-delay: 1s;
}

.hero-card-3 {
    bottom: 15%;
    left: 20%;
    animation-delay: 2s;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

/* Stats */
.stat-counter {
    padding: 1rem;
}

.border-white-25 {
    border-color: rgba(255,255,255,0.25) !important;
}

/* How It Works Cards */
.how-it-works-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    position: relative;
    transition: transform 0.3s, box-shadow 0.3s;
}

.how-it-works-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
}

.step-number {
    position: absolute;
    top: -15px;
    left: 50%;
    transform: translateX(-50%);
    width: 30px;
    height: 30px;
    background: var(--bs-primary);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.875rem;
}

.step-icon {
    width: 80px;
    height: 80px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
}

/* Item Cards Modern */
.item-card-modern {
    border-radius: 12px;
    overflow: hidden;
    transition: transform 0.3s, box-shadow 0.3s;
}

.item-card-modern:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}

.item-card-image {
    height: 120px;
    position: relative;
    overflow: hidden;
    background: #f1f5f9;
}

.item-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.item-card-image .no-image {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: #cbd5e1;
}

.item-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-size: 0.65rem;
    font-weight: 600;
    text-transform: uppercase;
}

.item-badge.lost {
    background: #fee2e2;
    color: #dc2626;
}

.item-badge.found {
    background: #dcfce7;
    color: #16a34a;
}

/* Feature Cards */
.feature-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    transition: transform 0.3s;
}

.feature-card:hover {
    transform: translateY(-5px);
}

.feature-icon {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

/* CTA Section */
.cta-section {
    background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
}

/* Dark Mode */
body.dark-mode .landing-hero {
    background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
}

body.dark-mode .hero-card {
    background: #374151;
    color: #f9fafb;
}

body.dark-mode .how-it-works-card,
body.dark-mode .feature-card {
    background: #1f2937;
}

body.dark-mode .item-card-modern {
    background: #1f2937;
}

body.dark-mode .item-card-image {
    background: #374151;
}

body.dark-mode .item-badge.lost {
    background: rgba(239, 68, 68, 0.2);
}

body.dark-mode .item-badge.found {
    background: rgba(34, 197, 94, 0.2);
}

body.dark-mode .cta-section {
    background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
}
</style>

<?php include __DIR__ . '/layouts/footer.php'; ?>
