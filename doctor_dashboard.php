<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: login.php");
    exit();
}
require_once 'includes/db.php';

$doctor_user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Get doctor special ID
$stmt = $pdo->prepare("SELECT id FROM doctors WHERE user_id = ?");
$stmt->execute([$doctor_user_id]);
$doctor_id = $stmt->fetch()['id'] ?? 0;

// Fetch upcoming appointments
$stmt_app = $pdo->prepare("SELECT appointments.*, users.username as patient_name, users.email as patient_email 
                           FROM appointments 
                           JOIN users ON appointments.user_id = users.id 
                           WHERE appointments.doctor_id = ? AND appointments.status = 'pending' 
                           ORDER BY appointment_date ASC");
$stmt_app->execute([$doctor_id]);
$pending_appointments = $stmt_app->fetchAll();

// Fetch session trends for patients (Include all users with role 'user')
$stmt_patients = $pdo->prepare("SELECT users.id, users.username, IFNULL(AVG(counseling_sessions.analysis_score), 0) as avg_mood 
                                FROM users 
                                LEFT JOIN counseling_sessions ON users.id = counseling_sessions.user_id 
                                WHERE users.role = 'user'
                                GROUP BY users.id");
$stmt_patients->execute();
$patient_insights = $stmt_patients->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard | MindCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fa-solid fa-user-doctor me-2"></i>MindCare Doctor Portal
            </a>
            <div class="ms-auto">
                <span class="text-white me-3">Dr. <?php echo htmlspecialchars($username); ?></span>
                <a href="logout.php" class="btn btn-sm btn-outline-light rounded-pill px-3">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row g-4 mb-4">
            <div class="col-12 fade-in">
                <div class="glass p-4 d-flex justify-content-between align-items-center shadow-sm">
                    <div>
                        <h4 class="fw-bold text-primary mb-0">Professional Dashboard</h4>
                        <p class="text-muted small mb-0">Welcome back, Dr. <?php echo htmlspecialchars($username); ?>
                        </p>
                    </div>
                    <div class="d-flex gap-3">
                        <div class="text-end d-none d-md-block">
                            <div class="fw-bold small text-muted text-uppercase mb-0">Status</div>
                            <span
                                class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 border border-success border-opacity-10 small">Online
                                & Available</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8 fade-in" style="animation-delay: 0.1s;">
                <div class="card p-5 border-0 shadow-lg mb-5 rounded-5">
                    <div class="d-flex justify-content-between align-items-center mb-5">
                        <h5 class="fw-bold mb-0">Pending Appointment Requests</h5>
                        <span class="badge bg-primary rounded-pill px-3 py-2">Active Requests</span>
                    </div>
                    <?php if ($pending_appointments): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="bg-light">
                                    <tr class="text-muted small text-uppercase">
                                        <th class="py-3">Patient Details</th>
                                        <th class="py-3">Requested Session</th>
                                        <th class="py-3">Decision</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pending_appointments as $app): ?>
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-primary">
                                                    <?php echo htmlspecialchars($app['patient_name']); ?>
                                                </div>
                                                <div class="text-muted small">
                                                    <?php echo htmlspecialchars($app['patient_email']); ?>
                                                </div>
                                            </td>
                                            <td class="small fw-semibold">
                                                <?php echo date('M d, Y • H:i', strtotime($app['appointment_date'])); ?>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <button
                                                        class="btn btn-sm btn-primary rounded-pill px-4 py-2 shadow-sm font-monospace">Confirm</button>
                                                    <button
                                                        class="btn btn-sm btn-light rounded-pill px-3 py-2 text-danger">Decline</button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5 bg-light rounded-5 border-dashed border-2">
                            <p class="text-muted mb-0">No appointment requests at this time.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card p-5 border-0 shadow-lg rounded-5">
                    <div class="d-flex justify-content-between align-items-center mb-5">
                        <h5 class="fw-bold mb-0">Patient Clinical Trends</h5>
                        <button class="btn btn-sm btn-link text-primary fw-bold text-decoration-none">Refresh
                            Analytics</button>
                    </div>
                    <div class="row g-4">
                        <?php foreach ($patient_insights as $p):
                            $moodColor = ($p['avg_mood'] >= 7.5) ? 'success' : (($p['avg_mood'] >= 5) ? 'warning' : 'danger');
                            ?>
                            <div class="col-md-6">
                                <div
                                    class="card p-4 border-0 bg-light shadow-sm d-flex flex-row align-items-center justify-content-between transition-all patient-card hover-lift">
                                    <div>
                                        <div class="fw-bold mb-1"><?php echo htmlspecialchars($p['username']); ?></div>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1"
                                                style="width: 80px; height: 6px; border-radius: 10px;">
                                                <div class="progress-bar bg-<?php echo $moodColor; ?>"
                                                    style="width: <?php echo $p['avg_mood'] * 10; ?>%"></div>
                                            </div>
                                            <span
                                                class="small fw-bold text-<?php echo $moodColor; ?>"><?php echo round($p['avg_mood'], 1); ?></span>
                                        </div>
                                    </div>
                                    <a href="video_call.php?room=MindCareSession_<?php echo $p['id']; ?>"
                                        class="btn btn-primary d-flex align-items-center justify-content-center rounded-circle shadow-sm"
                                        style="width: 45px; height: 45px;" title="Launch Private Video Session">
                                        <i class="fa-solid fa-video"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 fade-in" style="animation-delay: 0.2s;">
                <div class="glass p-5 shadow-lg h-100 d-flex flex-column rounded-5">
                    <h5 class="fw-bold mb-5 text-primary">Doctor Controls</h5>
                    <div class="d-grid gap-4 mb-5">
                        <button
                            class="btn btn-primary d-flex align-items-center justify-content-between p-3 rounded-4 shadow fade-in">
                            <span>My Clinical Schedule</span>
                            <i class="fa-solid fa-calendar-check"></i>
                        </button>
                        <button
                            class="btn btn-light d-flex align-items-center justify-content-between p-3 rounded-4 border-0 fade-in"
                            style="animation-delay: 0.1s;">
                            <span>Global Insights</span>
                            <i class="fa-solid fa-earth-americas text-primary"></i>
                        </button>
                    </div>

                    <hr class="my-5 opacity-10">

                    <div class="mt-auto">
                        <div class="p-4 bg-primary bg-opacity-10 rounded-4 border-bottom border-4 border-primary">
                            <div class="d-flex align-items-center mb-4">
                                <i class="fa-solid fa-wand-magic-sparkles text-primary me-2"></i>
                                <h6 class="fw-bold small text-primary text-uppercase mb-0">Clinical Advice Generator
                                </h6>
                            </div>
                            <textarea class="form-control border-0 bg-white p-3 mb-4 shadow-sm" rows="6"
                                placeholder="Enter patient context for AI-driven clinical advice..."></textarea>
                            <button class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-sm">Process with
                                Intelligence</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .patient-card:hover {
            background-color: white !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05) !important;
        }

        .hover-lift:hover {
            transform: translateY(-5px);
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>