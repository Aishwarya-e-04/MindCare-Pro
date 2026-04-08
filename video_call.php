<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$username = $_SESSION['username'];
$room_name = isset($_GET['room']) ? htmlspecialchars($_GET['room']) : "MindCare_Session_" . rand(100, 999);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Consultation | MindCare Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <script src='https://meet.jit.si/external_api.js'></script>
</head>
<body class="bg-main auth-bg overflow-hidden">

<div class="container-fluid p-0 vh-100 d-flex flex-column">
    <nav class="navbar navbar-expand-lg py-3 px-4 glass border-0 rounded-0 shadow-sm" style="z-index: 1000;">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
                <div class="bg-primary rounded-pill p-2 me-3 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                    <img src="logo.png" style="width: 20px;" alt="Logo">
                </div>
                <span class="fw-bold text-dark fs-5">MindCare Pro <span class="text-primary ms-2 small fw-bold">LIVE CONSULT</span></span>
            </a>
            <div class="ms-auto d-flex align-items-center gap-3">
                <div class="d-none d-md-block text-end me-3">
                    <p class="small text-muted mb-0 uppercase fw-bold" style="letter-spacing: 1px;">Session Active</p>
                    <p class="small text-dark fw-bold mb-0">ID: <?php echo $room_name; ?></p>
                </div>
                <a href="dashboard.php" class="btn btn-danger rounded-pill px-4 py-2 small fw-bold shadow-sm">End Connection</a>
            </div>
        </div>
    </nav>

    <div class="flex-grow-1 position-relative">
        <div id="meet" style="height: 100%; width: 100%;"></div>
        
        <!-- Animated Overlay for Loading -->
        <div id="loading-overlay" class="position-absolute top-50 start-50 translate-middle text-center">
            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
            <h5 class="fw-bold text-primary">Establishing Secure Tunnel...</h5>
            <p class="text-muted small uppercase fw-bold">HIPAA ENCRYPTED CHANNEL</p>
        </div>
    </div>
</div>

<script>
    window.onload = () => {
        const domain = 'meet.jit.si';
        const options = {
            roomName: '<?php echo $room_name; ?>',
            width: '100%',
            height: '100%',
            parentNode: document.querySelector('#meet'),
            userInfo: {
                displayName: '<?php echo htmlspecialchars($username); ?>'
            },
            interfaceConfigOverwrite: {
                TOOLBAR_BUTTONS: [
                    'microphone', 'camera', 'closedcaptions', 'desktop', 'fullscreen',
                    'fodeviceselection', 'hangup', 'profile', 'chat', 'recording',
                    'livestreaming', 'etherpad', 'sharedvideo', 'settings', 'raisehand',
                    'videoquality', 'filmstrip', 'invite', 'feedback', 'stats', 'shortcuts',
                    'tileview', 'videobackgroundblur', 'download', 'help', 'mute-everyone',
                    'security'
                ],
            }
        };
        const api = new JitsiMeetExternalAPI(domain, options);
        
        // Hide overlay once video loads
        api.addEventListener('videoConferenceJoined', () => {
            document.getElementById('loading-overlay').style.display = 'none';
        });
    };
</script>

</body>
</html>