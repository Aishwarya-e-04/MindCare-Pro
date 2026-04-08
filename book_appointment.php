<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

// Fetch all available doctors
$stmt = $pdo->prepare("SELECT doctors.id, users.username, doctors.specialty, doctors.availability FROM doctors JOIN users ON doctors.user_id = users.id");
$stmt->execute();
$doctors = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book'])) {
    $doctor_id = $_POST['doctor_id'];
    $appointment_date = $_POST['appointment_date'];

    try {
        $stmt = $pdo->prepare("INSERT INTO appointments (user_id, doctor_id, appointment_date, status) VALUES (?, ?, ?, 'pending')");
        $stmt->execute([$user_id, $doctor_id, $appointment_date]);

        // FETCH USER EMAIL FOR NOTIFICATION
        $stmt_user = $pdo->prepare("SELECT email FROM users WHERE id = ?");
        $stmt_user->execute([$user_id]);
        $user_email = $stmt_user->fetch()['email'];

        // EMAIL NOTIFICATION SYSTEM (Virtual Simulation for Demo)
        $to = $user_email;
        $subject = "MindCare Pro | Appointment Requested";
        $message_text = "Your clinical sanctuary session has been requested.\nDate & Time: $appointment_date\n\nPlease wait for doctor confirmation on your portal.";
        $headers = "From: no-reply@mindcarepro.ai";
        
        // Push to Virtual Inbox for Demo
        if(!isset($_SESSION['virtual_inbox'])) $_SESSION['virtual_inbox'] = [];
        $_SESSION['virtual_inbox'][] = [
            'subject' => $subject,
            'body' => $message_text,
            'from' => 'no-reply@mindcarepro.ai',
            'time' => date('H:i')
        ];

        $message = '<div class="alert alert-success border-0 shadow-sm p-4 rounded-4 fade-in">
                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-circle-check fs-2 me-3"></i>
                            <div>
                                <h6 class="fw-bold mb-0">Success! Appointment Requested.</h6>
                                <p class="small mb-0">A confirmation has been sent to your <a href="inbox.php" class="text-success fw-bold">Virtual Inbox</a></p>
                            </div>
                        </div>
                    </div>';
    } catch (PDOException $e) {
        $message = '<div class="alert alert-danger rounded-4">Error: ' . $e->getMessage() . '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment | MindCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="bg-main auth-bg">
    <div class="mesh-bg"></div>

    <nav class="navbar navbar-expand-lg py-3 fade-in">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
                <div class="bg-primary rounded-pill p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <img src="logo.png" style="width: 25px;" alt="Logo">
                </div>
                <span class="fw-bold text-dark fs-4">MindCare Pro</span>
            </a>
            <a href="dashboard.php" class="btn btn-outline-primary rounded-pill px-4 fw-bold shadow-sm">Back to Hub</a>
        </div>
    </nav>

    <div class="container py-5 mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="glass p-5 shadow-lg fade-in rounded-5">
                    <div class="text-center mb-5">
                        <h2 class="fw-bold text-dark" style="letter-spacing: -2px;">Book Clinical Sanctuary</h2>
                        <p class="text-muted small fw-bold uppercase ls-1">Reserve your neural recovery session</p>
                    </div>

                    <?php echo $message; ?>

                    <div class="row g-5 align-items-center">
                        <div class="col-lg-7">
                            <form action="book_appointment.php" method="POST" class="bg-white p-5 rounded-5 shadow-sm">
                                <div class="mb-4">
                                    <label class="form-label text-muted small fw-bold uppercase ps-1">Select a Clinical Specialist</label>
                                    <select name="doctor_id" class="form-select border-0 bg-light p-3 rounded-4" required>
                                        <option value="" disabled selected>Choose your counselor...</option>
                                        <?php foreach ($doctors as $doc): ?>
                                            <option value="<?php echo $doc['id']; ?>">
                                                Dr. <?php echo htmlspecialchars($doc['username']); ?> (<?php echo htmlspecialchars($doc['specialty']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-5">
                                    <label class="form-label text-muted small fw-bold uppercase ps-1">Desired Session Time</label>
                                    <input type="datetime-local" name="appointment_date"
                                        class="form-control border-0 bg-light p-3 rounded-4" required>
                                </div>
                                <button type="submit" name="book"
                                    class="btn btn-primary btn-lg w-100 rounded-pill py-3 shadow-lg fw-bold hover-lift">
                                    <i class="fa-solid fa-paper-plane me-2"></i> Confirm Booking
                                </button>
                            </form>
                        </div>
                        <div class="col-lg-5">
                            <div class="p-5 bg-primary bg-opacity-10 rounded-5 h-100">
                                <h6 class="fw-bold text-primary mb-4 uppercase small"><i class="fa-solid fa-user-doctor me-2"></i>Active Specialists</h6>
                                <div class="d-grid gap-4">
                                    <?php if (empty($doctors)): ?>
                                        <p class="text-muted small">No specialists currently online.</p>
                                    <?php else: ?>
                                        <?php foreach ($doctors as $doc): ?>
                                            <div class="p-3 bg-white rounded-4 shadow-sm border-start border-4 border-primary">
                                                <div class="fw-bold text-dark">Dr. <?php echo htmlspecialchars($doc['username']); ?></div>
                                                <div class="text-muted x-small uppercase fw-bold"><?php echo htmlspecialchars($doc['specialty']); ?></div>
                                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill mt-2 x-small"><?php echo htmlspecialchars($doc['availability']); ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>