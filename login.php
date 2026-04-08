<?php
session_start();
require_once 'includes/db.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'doctor') {
                header("Location: doctor_dashboard.php");
            } else {
                header("Location: dashboard.php");
            }
            exit();
        } else {
            $message = '<div class="alert alert-danger mt-3 bg-danger bg-opacity-10 border-danger border-opacity-10 text-danger rounded-3 small fw-bold">Invalid credentials!</div>';
        }
    } catch (PDOException $e) {
        $message = '<div class="alert alert-danger mt-3">Error: ' . $e->getMessage() . '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | MindCare Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body class="bg-main auth-bg">

<div class="container d-flex align-items-center justify-content-center min-vh-100 py-5">
    <div class="glass p-5 fade-in" style="width: 420px; border-radius: 40px;">
        <div class="text-center mb-5">
            <div class="bg-primary rounded-4 p-2 me-auto ms-auto mb-4 d-flex align-items-center justify-content-center shadow-md" style="width: 60px; height: 60px;">
                <img src="logo.png" style="width: 35px;" alt="MindCare Pro">
            </div>
            <h2 class="fw-bold mb-1 text-dark" style="letter-spacing: -1.5px;">Welcome Back</h2>
            <p class="text-muted small fw-bold uppercase" style="letter-spacing: 1px;">Clinical Sanctuary Access</p>
        </div>

        <form action="login.php" method="POST">
            <div class="mb-4">
                <label class="form-label text-muted small fw-bold uppercase ps-1">Email Identification</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-0 ps-3 pe-1"><i class="fa-solid fa-envelope text-muted opacity-50 small"></i></span>
                    <input type="email" name="email" class="form-control border-0 ps-2" placeholder="your@email.com" required>
                </div>
            </div>
            
            <div class="mb-5">
                <div class="d-flex justify-content-between mb-2 ps-1">
                    <label class="form-label text-muted small fw-bold uppercase mb-0">Private Key</label>
                    <a href="#" class="text-primary small text-decoration-none fw-bold">Forgot?</a>
                </div>
                <div class="input-group">
                    <span class="input-group-text bg-white border-0 ps-3 pe-1"><i class="fa-solid fa-lock text-muted opacity-50 small"></i></span>
                    <input type="password" name="password" class="form-control border-0 ps-2" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" name="login" class="btn btn-primary w-100 py-3 mb-4 shadow-lg transition-all fw-bold fs-5">
                Sign In <i class="fa-solid fa-chevron-right ms-2 fs-6"></i>
            </button>

            <?php if ($message) echo $message; ?>
        </form>

        <div class="mt-4 text-center border-top border-light pt-4">
            <p class="small text-muted mb-0">Unregistered? 
                <a href="register.php" class="text-primary fw-bold text-decoration-none">Create Sanctuary</a>
            </p>
        </div>
        
        <div class="mt-5 text-center">
            <a href="index.php" class="text-muted small text-decoration-none transition-all hover-teal"><i class="fa-solid fa-arrow-left me-2"></i>Back to Home</a>
        </div>
    </div>
</div>

<style>
    .input-group {
        background: #fff;
        border: 1.5px solid #F1F5F9;
        border-radius: 14px;
        transition: 0.3s;
        overflow: hidden;
    }
    .input-group:focus-within {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.1);
    }
    .hover-teal:hover { color: var(--primary) !important; }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>