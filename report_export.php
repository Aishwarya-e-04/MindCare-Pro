<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'includes/db.php';

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Fetch all journaling sessions
$stmt_sessions = $pdo->prepare("SELECT * FROM counseling_sessions WHERE user_id = ? ORDER BY recorded_at DESC");
$stmt_sessions->execute([$user_id]);
$sessions = $stmt_sessions->fetchAll();

// Fetch all questionnaires
$stmt_q = $pdo->prepare("SELECT * FROM questionnaires WHERE user_id = ? ORDER BY recorded_at ASC");
$stmt_q->execute([$user_id]);
$questionnaires = $stmt_q->fetchAll();

// Fetch latest questionnaire
$latest_q = end($questionnaires);

// Fetch latest journaling session
$latest_session = !empty($sessions) ? $sessions[0] : null;

// Average mood score from journal
$stmt_avg = $pdo->prepare("SELECT AVG(analysis_score) as avg_score FROM counseling_sessions WHERE user_id = ?");
$stmt_avg->execute([$user_id]);
$avg_score_val = $stmt_avg->fetch()['avg_score'];
$avg_score = round($avg_score_val ?? 0, 1);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mental Health Report - <?php echo htmlspecialchars($username); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background: var(--bg-main); color: var(--text-main); }
        .report-paper { 
            background: rgba(255, 255, 255, 0.03); 
            backdrop-filter: blur(15px); 
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 50px;
            margin-top: 30px;
        }
        .report-header { border-bottom: 2px solid var(--primary); padding-bottom: 20px; margin-bottom: 40px; }
        .score-box { background: rgba(255, 255, 255, 0.05); padding: 30px; border-radius: 15px; border: 1px solid rgba(255, 255, 255, 0.1); }
        .score-value { font-size: 48px; font-weight: bold; color: var(--accent); letter-spacing: -2px; }
        
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; }
            .report-paper { background: white !important; color: black !important; border: none; padding: 0; box-shadow: none; border-radius: 0; }
            .report-header { border-bottom: 2px solid #333; }
            .score-box { background: #f8f9fa !important; border: 1px solid #ddd !important; }
            .score-value { color: #000 !important; }
            .text-muted { color: #666 !important; }
            .normal { color: #22c55e !important; }
            .mild { color: #eab308 !important; }
            .moderate { color: #f97316 !important; }
            .severe { color: #ef4444 !important; }
        }
    </style>
</head>
<body class="bg-main">

<div class="no-print container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="results.php" class="btn btn-outline-light px-4 py-2 rounded-pill"><i class="fa-solid fa-chevron-left me-2"></i>Back to Hub</a>
        <button class="btn btn-primary-modern px-5 py-3 rounded-pill fw-bold" onclick="window.print()"><i class="fa-solid fa-print me-2"></i>Download Clinical Report</button>
    </div>
</div>

<div class="container report-paper shadow-lg mb-5 fade-in">
    <div class="report-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="fw-bold mb-0 display-5" style="letter-spacing: -3px;">MindCare Clinical Assessment</h1>
            <p class="text-muted small uppercase fw-bold ls-2" style="letter-spacing: 2px;">Generated on: <?php echo date('Y-m-d H:i:s'); ?></p>
        </div>
        <div class="text-end">
            <h5 class="fw-bold mb-0 text-primary">Report ID: #MC-<?php echo time(); ?></h5>
            <p class="text-muted small uppercase">Official intelligence Assessment</p>
        </div>
    </div>

    <div class="row g-5 mb-5">
        <div class="col-md-6">
            <h4 class="fw-bold text-accent mb-4"><i class="fa-solid fa-user-doctor me-2"></i>1. Patient Information</h4>
            <div class="p-4 glass rounded-3">
                <p class="mb-2"><span class="text-muted small uppercase fw-bold d-block">Legal Name</span> <span class="fs-5 fw-bold"><?php echo htmlspecialchars($username); ?></span></p>
                <hr class="opacity-10 my-3">
                <div class="row">
                    <div class="col-6">
                        <p class="mb-0 small text-muted uppercase fw-bold">Journal Sessions</p>
                        <p class="fs-4 fw-bold mb-0"><?php echo count($sessions); ?></p>
                    </div>
                    <div class="col-6">
                        <p class="mb-0 small text-muted uppercase fw-bold">Clinical Baseline</p>
                        <p class="fs-4 fw-bold mb-0"><?php echo $avg_score; ?>/10</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <h4 class="fw-bold text-accent mb-4"><i class="fa-solid fa-brain-circuit me-2"></i>2. Latest Diagnostic</h4>
            <div class="score-box">
                <?php 
                    $last_score = $latest_session['analysis_score'] ?? 0;
                    $mClass = 'normal';
                    if($last_score < 4) $mClass = 'severe';
                    else if($last_score < 6) $mClass = 'moderate';
                    else if($last_score < 8) $mClass = 'mild';
                ?>
                <p class="text-muted small uppercase fw-bold mb-3">Live Emotional Frequency</p>
                <div class="score-value <?php echo $mClass; ?>"><?php echo $last_score; ?>.0</div>
                <div class="fw-bold fs-5 mt-2 <?php echo $mClass; ?> uppercase"><?php echo $latest_session['status'] ?? 'Stable'; ?></div>
            </div>
        </div>
    </div>

    <div class="mb-5">
        <h4 class="fw-bold text-accent mb-4"><i class="fa-solid fa-wand-magic-sparkles me-2"></i>3. Behavioral Insights</h4>
        <div class="p-4 glass rounded-3 border-start border-4 border-primary">
            <p class="lead mb-0 fw-medium">
                <?php 
                if (($latest_session['analysis_score'] ?? 0) >= 7) echo "Linguistic patterns suggest a stable biological baseline. Continue standard maintenance and high-frequency self-care routines.";
                elseif (($latest_session['analysis_score'] ?? 0) >= 4) echo "Biometrical indicators show mild deviations from base baseline. Clinical recommendation issued: Integrated behavioral therapy or increased session frequency.";
                else echo "CRITICAL BRAIN ADVISORY: Diagnostic scores indicate severe emotional deviation. Immediate high-priority consultation with a clinical specialist is mandatory.";
                ?>
            </p>
        </div>
    </div>

    <div class="report-section mb-5">
        <h4 class="fw-bold text-accent mb-4"><i class="fa-solid fa-list-check me-2"></i>4. Clinical Assessment Log</h4>
        <div class="table-responsive glass rounded-4 overflow-hidden">
            <table class="table table-dark table-hover align-middle mb-0">
                <thead class="bg-primary bg-opacity-10 text-primary">
                    <tr>
                        <th class="py-4 ps-4 border-0 small uppercase fw-bold">Timestamp</th>
                        <th class="py-4 border-0 small uppercase fw-bold">Assessment Model</th>
                        <th class="py-4 border-0 small uppercase fw-bold">Classification</th>
                        <th class="py-4 pe-4 text-end border-0 small uppercase fw-bold">Score</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($questionnaires)): ?>
                        <tr><td colspan="4" class="text-center py-5 text-muted">No diagnostic screenings on file.</td></tr>
                    <?php else: ?>
                        <?php foreach ($questionnaires as $q): ?>
                            <tr>
                                <td class="ps-4 border-0 small opacity-75"><?php echo date('Y-m-d H:i', strtotime($q['recorded_at'])); ?></td>
                                <td class="border-0 fw-bold"><?php echo ucfirst($q['q_type']); ?></td>
                                <td class="border-0">
                                    <?php 
                                        $cClass = 'normal';
                                        if(strpos($q['classification'], 'Severe') !== false) $cClass = 'severe';
                                        else if(strpos($q['classification'], 'Moderate') !== false) $cClass = 'moderate';
                                        else if(strpos($q['classification'], 'Mild') !== false) $cClass = 'mild';
                                    ?>
                                    <span class="fw-bold <?php echo $cClass; ?>"><?php echo $q['classification']; ?></span>
                                </td>
                                <td class="pe-4 text-end border-0 fw-bold fs-5 text-primary"><?php echo $q['score']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5 pt-5 text-center text-muted small border-top border-white border-opacity-10">
        <p class="fw-bold">MindCare Clinical Portal | Sovereign Rights Reserved | Protected by End-to-End Medical Encryption</p>
        <p class="opacity-50">This diagnostic summary is generated via algorithmic linguistic analysis. It is designed to assist clinical evaluation but does not constitute a replacement for a personal medical diagnosis.</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
