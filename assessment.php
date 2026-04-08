<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'includes/db.php';

$user_id = $_SESSION['user_id'];
$q_type = $_GET['type'] ?? 'depression'; // Default to depression

// Mock PHQ-9 Questions (standard for depression assessment)
$questions = [
    ["id" => 1, "text" => "Little interest or pleasure in doing things?"],
    ["id" => 2, "text" => "Feeling down, depressed, or hopeless?"],
    ["id" => 3, "text" => "Trouble falling or staying asleep, or sleeping too much?"],
    ["id" => 4, "text" => "Feeling tired or having little energy?"],
    ["id" => 5, "text" => "Poor appetite or overeating?"],
    ["id" => 6, "text" => "Feeling bad about yourself — or that you are a failure or have let yourself or your family down?"],
    ["id" => 7, "text" => "Trouble concentrating on things, such as reading the newspaper or watching television?"],
    ["id" => 8, "text" => "Moving or speaking so slowly that other people could have noticed? Or the opposite — being so fidgety or restless that you have been moving around a lot more than usual?"],
    ["id" => 9, "text" => "Thoughts that you would be better off dead or of hurting yourself in some way?"]
];

$options = [
    ["value" => 0, "label" => "Not at all"],
    ["value" => 1, "label" => "Several days"],
    ["value" => 2, "label" => "More than half the days"],
    ["value" => 3, "label" => "Nearly every day"]
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mental Health Assessment | MindCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">
                <i class="fa-solid fa-brain-circuit me-2"></i>MindCare Assessment
            </a>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="glass p-5 shadow-lg fade-in">
                    <div
                        class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
                        <div>
                            <h2 class="fw-bold mb-1 text-primary">Mind Assessment</h2>
                            <p class="text-muted small mb-0">Topic: <span
                                    class="fw-bold text-accent"><?php echo ucfirst($q_type); ?> Analysis</span></p>
                        </div>
                        <div id="save-status"
                            class="badge bg-success bg-opacity-10 text-success fw-normal p-3 rounded-pill border border-success border-opacity-10">
                            <i class="fa-solid fa-cloud-check me-2"></i>Securely saved to cloud
                        </div>
                    </div>

                    <div class="alert bg-primary bg-opacity-10 border-0 rounded-4 text-primary p-4 mb-5">
                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-circle-info fa-lg me-3"></i>
                            <p class="mb-0 small fw-medium">Over the <b>last 2 weeks</b>, how often have you been
                                bothered by any of the following problems? Select one option for each question.</p>
                        </div>
                    </div>

                    <form id="assessment-form" action="process_assessment.php" method="POST">
                        <input type="hidden" name="q_type" value="<?php echo $q_type; ?>">

                        <?php foreach ($questions as $q): ?>
                            <div class="card p-4 border-0 mb-5 shadow-sm rounded-4 question-card">
                                <div class="d-flex align-items-start gap-3 mb-4">
                                    <span
                                        class="badge bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 28px; height: 28px; font-size: 0.8rem;"><?php echo $q['id']; ?></span>
                                    <h6 class="fw-bold mb-0 pt-1" style="line-height: 1.5;"><?php echo $q['text']; ?></h6>
                                </div>
                                <div class="row g-3">
                                    <?php foreach ($options as $opt): ?>
                                        <div class="col-md-6">
                                            <input type="radio" class="btn-check" name="q<?php echo $q['id']; ?>"
                                                id="q<?php echo $q['id']; ?>-v<?php echo $opt['value']; ?>"
                                                value="<?php echo $opt['value']; ?>" required onchange="autoSave()">
                                            <label
                                                class="btn btn-light w-100 text-start px-4 py-3 rounded-4 border-0 shadow-sm transition-all"
                                                for="q<?php echo $q['id']; ?>-v<?php echo $opt['value']; ?>">
                                                <span class="small fw-semibold"><?php echo $opt['label']; ?></span>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <div
                            class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-5 p-4 bg-light rounded-4 gap-4">
                            <a href="dashboard.php" class="text-primary text-decoration-none fw-bold small">
                                <i class="fa-solid fa-arrow-left me-2"></i>Leave Assessment
                            </a>
                            <button type="submit"
                                class="btn btn-primary btn-lg rounded-pill px-5 py-3 shadow border-0 fw-bold">
                                Submit for Analysis <i class="fa-solid fa-rocket ms-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .question-card {
            transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 0px solid #4A90E2 !important;
        }

        .question-card:focus-within {
            transform: translateX(5px);
            border-left: 8px solid #4A90E2 !important;
        }

        .btn-check:checked+.btn-light {
            background-color: #4A90E2 !important;
            color: white !important;
            transform: translateY(-2px);
        }
    </style>

    <script>
        async function autoSave() {
            const formData = new FormData(document.getElementById('assessment-form'));
            const formObj = {};
            formData.forEach((value, key) => formObj[key] = value);

            const statusLabel = document.getElementById('save-status');
            statusLabel.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin me-2"></i>Syncing responses...';
            statusLabel.className = 'badge bg-warning bg-opacity-10 text-warning fw-normal p-3 rounded-pill border border-warning border-opacity-10';

            try {
                const response = await fetch('auto_save_assessment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        q_type: '<?php echo $q_type; ?>',
                        data: formObj
                    })
                });
                const data = await response.json();

                if (data.status === 'success') {
                    statusLabel.innerHTML = '<i class="fa-solid fa-cloud-check me-2"></i>Securely saved to cloud';
                    statusLabel.className = 'badge bg-success bg-opacity-10 text-success fw-normal p-3 rounded-pill border border-success border-opacity-10';
                }
            } catch (error) {
                statusLabel.innerHTML = '<i class="fa-solid fa-triangle-exclamation me-2"></i>Network issue';
                statusLabel.className = 'badge bg-danger bg-opacity-10 text-danger fw-normal p-3 rounded-pill border border-danger border-opacity-10';
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>