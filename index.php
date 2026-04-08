<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MindCare | Professional Mental Health Sanctuary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body class="bg-main auth-bg">

<nav class="navbar navbar-expand-lg py-4 fade-in">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <div class="bg-primary rounded-4 p-2 me-3 d-flex align-items-center justify-content-center shadow-md" style="width: 45px; height: 45px;">
                <img src="logo.png" style="width: 25px;" alt="MindCare">
            </div>
            <span class="fw-bold text-dark fs-3" style="letter-spacing: -2px;">MindCare</span>
        </a>
        <div class="ms-auto d-flex gap-3 align-items-center">
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php" class="btn btn-outline-primary px-4 py-2 rounded-pill fw-bold">Enter Hub</a>
            <?php else: ?>
                <a href="login.php" class="text-dark fw-bold text-decoration-none px-3 d-flex align-items-center"><i class="fa-solid fa-right-to-bracket me-2 small"></i> Login</a>
                <a href="register.php" class="btn btn-primary rounded-pill px-4 py-2 d-flex align-items-center"><i class="fa-solid fa-user-plus me-2 small"></i> Get Started</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<section class="py-5 overflow-hidden">
    <div class="container py-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 fade-in">
                <div class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-4 uppercase fw-bold" style="letter-spacing: 1.5px;">⭐ TRUSTED BY 10,000+ USERS</div>
                <h1 class="display-2 fw-bold mb-4 text-dark" style="letter-spacing: -3px; line-height: 1;">
                    Your Mental Wellness <br><span class="text-primary">Our Priority.</span>
                </h1>
                <p class="lead text-muted mb-5 fs-4" style="line-height: 1.6;">
                    Bridge the gap between technology and therapy. A clinical sanctuary designed for secure mood tracking and professional guidance.
                </p>
                <div class="d-flex gap-4">
                    <a href="register.php" class="btn btn-primary py-4 px-5 fs-5 rounded-pill shadow-lg">Start Free Trial <i class="fa-solid fa-arrow-right ms-2 transition-all"></i></a>
                    <div class="d-inline-flex align-items-center">
                        <div class="p-2 rounded-circle bg-white shadow-sm me-3"><i class="fa-solid fa-play text-primary ms-1"></i></div>
                        <span class="fw-bold text-muted small">See How it Works</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 text-center fade-in delay-2">
                <div class="position-relative">
                    <div class="bg-primary-light rounded-circle position-absolute top-50 start-50 translate-middle scale-in" style="width: 120%; height: 120%; z-index: -1;"></div>
                    <img src="doctors.png" class="img-fluid float" style="max-height: 500px;" alt="Medical Professionals">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-white border-top border-bottom border-light">
    <div class="container py-5">
        <div class="row g-4 text-center">
            <div class="col-md-4 fade-in delay-1">
                <div class="card h-100 p-5 border-0 shadow-sm rounded-5 hover-lift">
                    <div class="bg-primary bg-opacity-10 rounded-4 p-4 d-inline-flex mb-4" style="width: 70px; height: 70px;">
                        <i class="fa-solid fa-feather-pointed text-primary fs-2"></i>
                    </div>
                    <h3 class="fw-bold mb-3">Secure Journal</h3>
                    <p class="text-muted mb-0 fw-medium">An encrypted space for your daily reflections and private thoughts.</p>
                </div>
            </div>
            <div class="col-md-4 fade-in delay-2">
                <div class="card h-100 p-5 border-0 shadow-sm rounded-5 hover-lift">
                    <div class="bg-primary bg-opacity-10 rounded-4 p-4 d-inline-flex mb-4" style="width: 70px; height: 70px;">
                        <i class="fa-solid fa-chart-pie text-primary fs-2"></i>
                    </div>
                    <h3 class="fw-bold mb-3">AI Analytics</h3>
                    <p class="text-muted mb-0 fw-medium">Instant visualization of emotional trends using clinical-grade tracking.</p>
                </div>
            </div>
            <div class="col-md-4 fade-in delay-3">
                <div class="card h-100 p-5 border-0 shadow-sm rounded-5 hover-lift">
                    <div class="bg-primary bg-opacity-10 rounded-4 p-4 d-inline-flex mb-4" style="width: 70px; height: 70px;">
                        <i class="fa-solid fa-user-doctor text-primary fs-2"></i>
                    </div>
                    <h3 class="fw-bold mb-3">Clinical Link</h3>
                    <p class="text-muted mb-0 fw-medium">Direct connection to licensed professionals for professional guidance.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="py-5 text-center mt-5 border-top border-light">
    <div class="container">
        <div class="d-flex align-items-center justify-content-center mb-4">
            <img src="logo.png" style="width: 35px;" class="me-2" alt="Logo">
            <span class="fw-bold text-dark fs-5">MindCare Pro</span>
        </div>
        <p class="text-muted small uppercase fw-bold" style="letter-spacing: 3px;">© 2026 Sovereign Behavioral Health | All Rights Reserved</p>
    </div>
</footer>

<style>
    .hover-lift:hover { transform: translateY(-10px); }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>