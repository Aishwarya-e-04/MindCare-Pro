<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'includes/db.php';

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$stmt = $pdo->prepare("SELECT * FROM counseling_sessions WHERE user_id = ? ORDER BY recorded_at DESC LIMIT 5");
$stmt->execute([$user_id]);
$recent_sessions = $stmt->fetchAll();

// Total sessions
$stmt_count = $pdo->prepare("SELECT COUNT(*) as total FROM counseling_sessions WHERE user_id = ?");
$stmt_count->execute([$user_id]);
$total_sessions = $stmt_count->fetch()['total'];

// Average mood score placeholder
$stmt_avg = $pdo->prepare("SELECT AVG(analysis_score) as avg_score FROM counseling_sessions WHERE user_id = ?");
$stmt_avg->execute([$user_id]);
$avg_score = round($stmt_avg->fetch()['avg_score'], 1) ?? 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | MindCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
</head>

<body class="bg-light">
    <div class="mesh-bg"></div> <!-- Dynamic Mesh Background -->

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fa-solid fa-brain-circuit me-2"></i>MindCare
            </a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <span class="nav-link text-white me-3 d-none d-md-inline">Welcome,
                            <b><?php echo htmlspecialchars($username); ?></b>!</span>
                    </li>
                    <li class="nav-item">
                        <a href="logout.php"
                            class="btn btn-sm btn-outline-light rounded-pill px-4 transition-all logout-btn">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row g-4">
            <!-- Sidebar/Actions -->
            <div class="col-md-3">
                <div class="glass h-100 p-4 fade-in">
                    <h5 class="fw-bold mb-4 text-primary text-uppercase small" style="letter-spacing: 2px;">Wellness Hub
                    </h5>
                    <div class="d-grid gap-3">
                        <a href="counseling_session.php"
                            class="btn btn-primary d-flex align-items-center justify-content-between p-3 rounded-4 shadow-sm scale-in delay-1">
                            <span>New Journal</span>
                            <i class="fa-solid fa-feather-pointed"></i>
                        </a>
                        <a href="assessment.php"
                            class="btn btn-light d-flex align-items-center justify-content-between p-3 rounded-4 border-0 scale-in delay-2">
                            <span>Assessment</span>
                            <i class="fa-solid fa-clipboard-question text-primary"></i>
                        </a>
                        <a href="chatbot.php"
                            class="btn btn-light d-flex align-items-center justify-content-between p-3 rounded-4 border-0 scale-in delay-3">
                            <span>AI Assistant</span>
                            <i class="fa-solid fa-robot text-primary"></i>
                        </a>
                        <a href="book_appointment.php"
                            class="btn btn-light d-flex align-items-center justify-content-between p-3 rounded-4 border-0 scale-in delay-3">
                            <span>Book Session</span>
                            <i class="fa-solid fa-calendar-plus text-primary"></i>
                        </a>
                        <a href="results.php"
                            class="btn btn-light d-flex align-items-center justify-content-between p-3 rounded-4 border-0 scale-in delay-4">
                            <span>Insights</span>
                            <i class="fa-solid fa-chart-line text-primary"></i>
                        </a>
                        <a href="clinic_map.php"
                            class="btn btn-light d-flex align-items-center justify-content-between p-3 rounded-4 border-0 scale-in delay-5">
                            <span>Clinic Locator</span>
                            <i class="fa-solid fa-location-dot text-primary"></i>
                        </a>
                        <a href="video_call.php"
                            class="btn btn-primary d-flex align-items-center justify-content-between p-3 rounded-4 shadow-sm scale-in delay-5">
                            <span>Virtual Consult</span>
                            <i class="fa-solid fa-video"></i>
                        </a>
                    </div>

                    <hr class="my-5 opacity-10">

                    <div class="p-4 bg-primary bg-opacity-10 rounded-4 mt-auto float">
                        <h6 class="fw-bold text-primary mb-2 small"><i class="fa-solid fa-lightbulb me-1"></i> DAILY TIP
                        </h6>
                        <p class="small text-muted mb-0">Record how you feel daily to unlock personalized AI emotional
                            trends.</p>
                    </div>
                </div>
            </div>

            <!-- Main Dashboard Content -->
            <div class="col-md-9">
                <div class="row g-4 mb-5">
                    <div class="col-md-4">
                        <div class="card p-4 h-100 border-0 shadow-lg text-center scale-in delay-1">
                            <div class="p-3 bg-primary bg-opacity-10 rounded-circle d-inline-block mx-auto mb-3">
                                <i class="fa-solid fa-heart-pulse text-primary fa-lg"></i>
                            </div>
                            <h6 class="text-muted text-uppercase small fw-bold mb-2">Mood Score</h6>
                            <h2 class="fw-bold text-primary mb-0"><?php echo $avg_score; ?>/10</h2>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card p-4 h-100 border-0 shadow-lg text-center scale-in delay-2">
                            <div class="p-3 bg-accent bg-opacity-10 rounded-circle d-inline-block mx-auto mb-3">
                                <i class="fa-solid fa-feather text-accent fa-lg"></i>
                            </div>
                            <h6 class="text-muted text-uppercase small fw-bold mb-2">Total Insights</h6>
                            <h2 class="fw-bold text-accent mb-0"><?php echo $total_sessions; ?></h2>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card p-4 h-100 border-0 shadow-lg text-center pulse-soft scale-in delay-3">
                            <div class="p-3 bg-info bg-opacity-10 rounded-circle d-inline-block mx-auto mb-3">
                                <i class="fa-solid fa-bell text-info fa-lg"></i>
                            </div>
                            <h6 class="text-muted text-uppercase small fw-bold mb-2">Notification Center</h6>
                        <div class="text-start small mt-2">
                            <a href="inbox.php" class="d-flex align-items-start gap-2 mb-2 p-2 bg-light rounded-3 text-decoration-none transition-all hover-lift">
                                <i class="fa-solid fa-envelope-circle-check text-success mt-1"></i>
                                <div>
                                    <p class="mb-0 fw-bold text-dark">Email Dispatched</p>
                                    <p class="mb-0 x-small text-muted">Click to read in Virtual Inbox.</p>
                                </div>
                            </a>
                            <div class="d-flex align-items-start gap-2 p-2 bg-light rounded-3">
                                <i class="fa-solid fa-shield-halved text-primary mt-1"></i>
                                <div>
                                    <p class="mb-0 fw-bold">Security Sync</p>
                                    <p class="mb-0 x-small text-muted">Neural tunnel encrypted.</p>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>

                <div class="card p-5 border-0 shadow-lg fade-in delay-4 rounded-5 overflow-hidden position-relative">
                    <div class="d-flex align-items-center justify-content-between mb-5">
                        <h5 class="fw-bold mb-0">Analysis & Emotional History</h5>
                        <a href="results.php"
                            class="btn btn-sm btn-link text-primary fw-bold text-decoration-none">Export All History</a>
                    </div>
                    <?php if ($recent_sessions): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="bg-light">
                                    <tr class="text-muted small text-uppercase">
                                        <th class="py-3">Timestamp</th>
                                        <th class="py-3">Context Snapshot</th>
                                        <th class="py-3 text-center">Status</th>
                                        <th class="py-3 text-end">Metric</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $delay = 1;
                                    foreach ($recent_sessions as $session):
                                        $statusClass = 'status-stable';
                                        if ($session['status'] == 'Stressed')
                                            $statusClass = 'status-stressed';
                                        if ($session['status'] == 'Anxious')
                                            $statusClass = 'status-anxious';
                                        if ($session['status'] == 'Concerning')
                                            $statusClass = 'status-concerning';
                                        ?>
                                        <tr class="fade-in" style="animation-delay: <?php echo 0.5 + ($delay * 0.1); ?>s">
                                            <td class="small fw-bold date-cell">
                                                <?php echo date('M d, Y', strtotime($session['recorded_at'])); ?>
                                            </td>
                                            <td class="text-muted small" style="max-width: 400px;">
                                                <?php echo htmlspecialchars(substr($session['content'], 0, 75)) . '...'; ?>
                                            </td>
                                            <td class="text-center"><span
                                                    class="status-badge <?php echo $statusClass; ?> px-3 shadow-sm"><?php echo htmlspecialchars($session['status']); ?></span>
                                            </td>
                                            <td class="text-end fw-bold text-primary">
                                                <?php echo $session['analysis_score']; ?>.0
                                            </td>
                                        </tr>
                                        <?php $delay++; endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-state-2130362-1800926.png"
                                style="width: 150px; opacity: 0.3;" class="mb-4">
                            <p class="text-muted">No mental health history found. Start your first session now.</p>
                            <a href="counseling_session.php" class="btn btn-primary rounded-pill px-5">Launch New
                                Session</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>