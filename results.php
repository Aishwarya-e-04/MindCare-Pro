<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'includes/db.php';

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Fetch all sessions (Journal)
$stmt_journal = $pdo->prepare("SELECT recorded_at, analysis_score FROM counseling_sessions WHERE user_id = ? ORDER BY recorded_at ASC");
$stmt_journal->execute([$user_id]);
$journal_sessions = $stmt_journal->fetchAll();

// Fetch all questionnaires
$stmt_q = $pdo->prepare("SELECT recorded_at, score, classification, q_type FROM questionnaires WHERE user_id = ? ORDER BY recorded_at ASC");
$stmt_q->execute([$user_id]);
$questionnaires = $stmt_q->fetchAll();

$dates = [];
$scores = [];
foreach ($journal_sessions as $s) {
    if (count($dates) < 7) { // limit for visual clarity
        $dates[] = date('M d', strtotime($s['recorded_at']));
        $scores[] = (float) $s['analysis_score'];
    }
}

// Prepare questionnaire chart data (Classification counts)
$class_counts = ['Normal' => 0, 'Mild' => 0, 'Moderate' => 0, 'Severe' => 0];
foreach ($questionnaires as $q) {
    $class_counts[$q['classification']]++;
}
$class_labels = array_keys($class_counts);
$class_values = array_values($class_counts);

// Latest report logic
$latest_q = end($questionnaires);
$latest_status_class = 'status-stable';
if ($latest_q) {
    if ($latest_q['classification'] == 'Severe')
        $latest_status_class = 'status-concerning';
    elseif ($latest_q['classification'] == 'Moderate')
        $latest_status_class = 'status-stressed';
    elseif ($latest_q['classification'] == 'Mild')
        $latest_status_class = 'status-anxious';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analysis Report | MindCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">
                <i class="fa-solid fa-brain-circuit me-2"></i>MindCare
            </a>
            <div class="ms-auto">
                <a href="dashboard.php" class="btn btn-sm btn-outline-light rounded-pill px-3">Back to Dashboard</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row mb-5 fade-in">
            <div
                class="col-12 text-center text-lg-start d-flex flex-column flex-lg-row align-items-center justify-content-between gap-4">
                <div>
                    <h2 class="fw-bold mb-2 text-primary">Wellness Journey Analytics</h2>
                    <p class="text-muted mb-0">Evidence-based insights into your emotional progress and assessment
                        history.</p>
                </div>
                <div class="d-flex gap-3">
                    <a href="dashboard.php" class="btn btn-light rounded-pill px-4 py-2 border-0 shadow-sm small">
                        <i class="fa-solid fa-arrow-left me-2"></i>Back to Hub
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <!-- Latest Status Card -->
            <div class="col-lg-4 fade-in" style="animation-delay: 0.1s;">
                <div class="glass h-100 p-5 shadow-lg border-0 transition-all rounded-5">
                    <div class="d-flex align-items-center mb-5">
                        <div class="p-3 bg-primary bg-opacity-10 rounded-4 me-3">
                            <i class="fa-solid fa-stethoscope text-primary fa-lg"></i>
                        </div>
                        <h5 class="fw-bold mb-0">Clinical Snapshot</h5>
                    </div>

                    <?php if ($latest_q): ?>
                        <div class="mb-5 pb-4 border-bottom border-light">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div>
                                    <div class="text-muted small text-uppercase fw-bold mb-1">
                                        <?php echo ucfirst($latest_q['q_type']); ?> Status
                                    </div>
                                    <span
                                        class="status-badge <?php echo $latest_status_class; ?> fs-6 py-2 px-4 shadow-sm"><?php echo htmlspecialchars($latest_q['classification']); ?></span>
                                </div>
                                <div class="text-end">
                                    <div class="text-muted small text-uppercase fw-bold mb-1">Score</div>
                                    <h3 class="fw-bold mb-0 text-primary"><?php echo $latest_q['score']; ?></h3>
                                </div>
                            </div>
                            <p class="text-muted small"><i class="fa-regular fa-clock me-1"></i> Recorded on
                                <?php echo date('M d, Y', strtotime($latest_q['recorded_at'])); ?>
                            </p>
                        </div>

                        <div class="p-4 bg-primary bg-opacity-10 rounded-4 border-start border-4 border-primary">
                            <h6 class="fw-bold text-primary small mb-3 text-uppercase">Clinical Recommendation</h6>
                            <p class="small text-muted mb-0" style="line-height: 1.6;">
                                <?php
                                if ($latest_q['classification'] == 'Normal')
                                    echo "Your metrics are currently within the stable range. We recommend maintaining your current wellness routine and repeating assessments monthly.";
                                elseif ($latest_q['classification'] == 'Mild')
                                    echo "We detected slight emotional shifts. Consider increasing self-care activities and monitoring your sleep patterns closely.";
                                elseif ($latest_q['classification'] == 'Moderate')
                                    echo "Moderate distress indicators are present. Scheduled a counseling session to discuss these trends further.";
                                else
                                    echo "<b>Urgent:</b> High distress levels detected. Please contact your assigned specialist immediately or use our video session feature.";
                                ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <img src="https://cdni.iconscout.com/illustration/premium/thumb/no-data-found-1965030-1662565.png"
                                style="width: 120px;" class="mb-4 opacity-50">
                            <p class="text-muted mb-4 small">No clinical assessments found. Start your first structured
                                survey now.</p>
                            <a href="assessment.php" class="btn btn-primary rounded-pill px-5 py-3 shadow-sm fw-bold">Begin
                                Assessment</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Trend Chart -->
            <div class="col-lg-5 fade-in" style="animation-delay: 0.2s;">
                <div class="card h-100 p-5 shadow-lg border-0 rounded-5">
                    <div class="d-flex align-items-center justify-content-between mb-5">
                        <h5 class="fw-bold mb-0">Mood Trajectory</h5>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light rounded-pill px-3 border-0" type="button">7
                                Days</button>
                        </div>
                    </div>
                    <div style="height: 300px;">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Distribution Chart -->
            <div class="col-lg-3 fade-in" style="animation-delay: 0.3s;">
                <div class="card h-100 p-5 shadow-lg border-0 rounded-5">
                    <h5 class="fw-bold mb-5">Assessment Mix</h5>
                    <div style="height: 250px;">
                        <canvas id="distChart"></canvas>
                    </div>
                    <div class="mt-4 pt-4 border-top border-light">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted fw-bold small text-uppercase">Total Completed</small>
                            <span class="small fw-bold"><?php echo count($questionnaires); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Report Generation -->
        <div class="row">
            <div class="col-12 fade-in">
                <div
                    class="card p-5 shadow-lg border-0 bg-primary text-white rounded-5 mb-5 position-relative overflow-hidden">
                    <div class="row align-items-center position-relative" style="z-index: 2;">
                        <div class="col-lg-8 text-lg-start text-center mb-4 mb-lg-0">
                            <h3 class="fw-bold mb-3">Download Your Full Patient Report</h3>
                            <p class="opacity-75 mb-0">Generate a professional, clinical-grade summary of your mental
                                health journey. Optimized for printing and sharing with your therapist.</p>
                        </div>
                        <div class="col-lg-4 text-lg-end text-center">
                            <a href="report_export.php"
                                class="btn btn-light btn-lg rounded-pill px-5 py-3 shadow-lg fw-bold text-primary transition-all hover-lift">
                                <i class="fa-solid fa-file-export me-2"></i>Export to PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Custom Chart.js Defaults
        Chart.defaults.font.family = "'Outfit', sans-serif";
        Chart.defaults.color = '#94A3B8';

        const ctx = document.getElementById('trendChart').getContext('2d');
        
        // --- STRATEGIC DEMO ENGINE ---
        let chartLabels = <?php echo json_encode($dates ?: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']); ?>;
        let chartScores = <?php echo json_encode($scores ?: [6.5, 7.8, 5.2, 8.4, 7.1, 9.2, 8.5]); ?>;
        // ----------------------------

        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(79, 70, 229, 0.25)');
        gradient.addColorStop(1, 'rgba(79, 70, 229, 0)');

        const trendChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Emotional Frequency',
                    data: chartScores,
                    borderColor: '#4F46E5',
                    backgroundColor: gradient,
                    borderWidth: 4,
                    fill: true,
                    tension: 0.45,
                    pointRadius: 6,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#4F46E5',
                    pointBorderWidth: 3,
                    pointHoverRadius: 9,
                    pointHoverBackgroundColor: '#4F46E5',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 10,
                        grid: { color: 'rgba(226, 232, 240, 0.4)', drawBorder: false },
                        ticks: { padding: 10 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { padding: 10 }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1E293B',
                        padding: 15,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 14 },
                        cornerRadius: 12,
                        displayColors: false
                    }
                }
            }
        });

        const distCtx = document.getElementById('distChart').getContext('2d');
        
        // --- DEMO DATA FOR PIE ---
        let pieLabels = <?php echo json_encode(!empty($class_values) && array_sum($class_values) > 0 ? $class_labels : ['Stable', 'Stressed', 'Concerning', 'Restoring']); ?>;
        let pieData = <?php echo json_encode(!empty($class_values) && array_sum($class_values) > 0 ? $class_values : [45, 25, 15, 15]); ?>;
        // -------------------------

        const distChart = new Chart(distCtx, {
            type: 'doughnut',
            data: {
                labels: pieLabels,
                datasets: [{
                    data: pieData,
                    backgroundColor: ['#10B981', '#F59E0B', '#EF4444', '#6366F1'],
                    borderWidth: 0,
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, padding: 25, usePointStyle: true, font: { size: 11, weight: '600' } } }
                }
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>