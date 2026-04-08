<?php
require_once 'includes/db.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role']; 

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $email, $password, $role]);
        $user_id = $pdo->lastInsertId();

        if ($role == 'doctor') {
            $stmt_doc = $pdo->prepare("INSERT INTO doctors (user_id, specialty, availability) VALUES (?, 'Professional Counselor', 'Mon-Fri 9:00 AM - 5:00 PM')");
            $stmt_doc->execute([$user_id]);
        }

        $pdo->commit();
        $message = '<div class="alert alert-success mt-3 p-2 rounded-pill text-center bg-success bg-opacity-10 border-0 text-success x-small fw-bold">Success! <a href="login.php" class="text-success">Sign in</a></div>';
    } catch (PDOException $e) {
        $pdo->rollBack();
        $message = '<div class="alert alert-danger mt-3 p-2 rounded-pill text-center bg-danger bg-opacity-10 border-0 text-danger x-small">Error: ' . $e->getMessage() . '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | MindCare Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #dbeafe, #e0e7ff, #f0fdf4);
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container-box {
            width: 420px;
            padding: 30px;
            border-radius: 20px;
            backdrop-filter: blur(15px);
            background: rgba(255, 255, 255, 0.6);
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }

        .title {
            text-align: center;
            font-size: 26px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .subtitle {
            text-align: center;
            font-size: 14px;
            color: gray;
            margin-bottom: 20px;
        }

        .input-box {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 30px;
            border: none;
            outline: none;
            background: #f1f5f9;
            transition: 0.3s;
        }

        .input-box:focus {
            background: #e0f2fe;
            box-shadow: 0 0 8px rgba(59,130,246,0.3);
        }

        .select-box {
            border-radius: 30px;
            padding: 12px;
            width: 100%;
            border: none;
            background: #f1f5f9;
            margin-bottom: 5px;
            outline: none;
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            border-radius: 30px;
            border: none;
            background: linear-gradient(135deg, #6366f1, #3b82f6);
            color: white;
            font-weight: 600;
            margin-top: 15px;
            transition: 0.3s;
        }

        .btn-submit:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(59,130,246,0.4);
        }

        .x-small { font-size: 0.75rem; }
    </style>
</head>
<body>

<div class="container-box">
    <div class="title">Join Your Sanctuary 🌿</div>
    <div class="subtitle">Create your clinical profile</div>

    <form action="register.php" method="POST">
        <input type="text" name="username" class="input-box" placeholder="Full Name" required>
        <input type="email" name="email" class="input-box" placeholder="Email Address" required>
        <input type="password" name="password" class="input-box" placeholder="Password" required>

        <select name="role" class="select-box" required>
            <option value="user">Patient</option>
            <option value="doctor">Doctor</option>
        </select>

        <button type="submit" name="register" class="btn-submit">Get Started</button>
        
        <?php echo $message; ?>
    </form>

    <div class="mt-4 text-center">
        <p class="x-small text-muted mb-0">Already a member? <a href="login.php" class="text-primary fw-bold text-decoration-none">Log in</a></p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>