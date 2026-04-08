<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch virtual emails from a session-based log (simulating a real mail server for the demo)
$mock_emails = isset($_SESSION['virtual_inbox']) ? $_SESSION['virtual_inbox'] : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neural Inbox | MindCare Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body class="bg-main auth-bg">
    <div class="mesh-bg"></div>

    <nav class="navbar navbar-expand-lg py-3 fade-in">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
                <div class="bg-primary rounded-pill p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <img src="logo.png" style="width: 25px;" alt="Logo">
                </div>
                <span class="fw-bold text-dark fs-4">MindCare Pro <span class="text-primary ms-2 small">MAIL SIMULATOR</span></span>
            </a>
            <a href="dashboard.php" class="btn btn-outline-primary rounded-pill px-4 fw-bold shadow-sm">Back to Hub</a>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="glass p-5 shadow-lg rounded-5 fade-in">
                    <div class="d-flex justify-content-between align-items-center mb-5">
                        <h3 class="fw-bold mb-0">Virtual Clinical Inbox</h3>
                        <span class="badge bg-primary rounded-pill px-3 py-2"><?php echo count($mock_emails); ?> Messages</span>
                    </div>

                    <?php if (empty($mock_emails)): ?>
                        <div class="text-center py-5">
                            <i class="fa-solid fa-envelope-open text-muted fs-1 mb-4 opacity-25"></i>
                            <p class="text-muted">No clinical notifications at this time. Book an appointment to trigger a simulation.</p>
                        </div>
                    <?php else: ?>
                        <div class="d-grid gap-3">
                            <?php foreach (array_reverse($mock_emails) as $email): ?>
                                <div class="p-4 bg-white rounded-4 shadow-sm border-start border-4 border-primary fade-in">
                                    <div class="d-flex justify-content-between mb-3">
                                        <h6 class="fw-bold text-primary mb-0"><?php echo htmlspecialchars($email['subject']); ?></h6>
                                        <small class="text-muted fw-bold"><?php echo $email['time']; ?></small>
                                    </div>
                                    <div class="text-muted small" style="white-space: pre-wrap;"><?php echo htmlspecialchars($email['body']); ?></div>
                                    <div class="mt-3 pt-3 border-top border-light">
                                        <p class="x-small text-muted mb-0">Dispatched from: <span class="text-dark fw-bold"><?php echo $email['from']; ?></span></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
