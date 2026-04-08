<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Counseling Session | MindCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">
                <i class="fa-solid fa-brain-circuit me-2"></i>MindCare
            </a>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-7">
                <div class="glass p-5 shadow-lg fade-in">
                    <div class="text-center mb-5">
                        <div class="bg-accent bg-opacity-10 d-inline-block p-4 rounded-circle mb-4">
                            <i class="fa-solid fa-feather-pointed text-accent fa-3x"></i>
                        </div>
                        <h2 class="fw-bold text-primary">Your Safe Space</h2>
                        <p class="text-muted lead">Let your thoughts flow. No judgment, just insight.</p>
                    </div>

                    <form action="process_counseling.php" method="POST">
                        <div class="mb-5 text-start">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-0">Daily Journal /
                                    Counseling Notes</label>
                                <span class="badge bg-light text-muted rounded-pill px-3 py-2 fw-normal">Private &
                                    Encrypted</span>
                            </div>
                            <textarea name="content" class="form-control border-0 bg-light p-4 shadow-inner" rows="10"
                                placeholder="I'm feeling... today because..."
                                style="border-radius: 20px; font-size: 1.1rem; line-height: 1.6;" required></textarea>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="dashboard.php" class="text-primary text-decoration-none fw-bold">
                                <i class="fa-solid fa-arrow-left me-2"></i>Back to Wellness Hub
                            </a>
                            <button type="submit"
                                class="btn btn-primary btn-lg rounded-pill px-5 py-3 shadow-sm fw-bold">
                                Analyze & Save Entry <i class="fa-solid fa-wand-magic-sparkles ms-2"></i>
                            </button>
                        </div>
                    </form>

                    <div class="mt-5 p-4 bg-light rounded-4 border-start border-4 border-accent">
                        <h6 class="fw-bold small text-accent mb-2">WRITING TIP</h6>
                        <p class="small text-muted mb-0">Try to use descriptive words. The more detail you provide, the
                            better our AI can understand your emotional baseline.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>