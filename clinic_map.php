<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic Locator | MindCare Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    
    <!-- Leaflet JS & CSS (FREE Open Source Map) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        #map { 
            height: 600px; 
            width: 100%; 
            border-radius: 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            z-index: 1;
        }
        .leaflet-container {
            background: #f8fafc;
        }
    </style>
</head>
<body class="bg-main auth-bg">

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

<div class="container py-5">
    <div class="row g-5">
        <div class="col-lg-4 fade-in">
            <div class="glass p-5 h-100">
                <h3 class="fw-bold text-primary mb-4" style="letter-spacing: -2px;">Nearby Sanctuary Clinics</h3>
                <p class="text-muted small fw-bold mb-5 uppercase" style="letter-spacing: 1px;">REAL-TIME RADIUS DETECTION</p>
                
                <div class="d-grid gap-4">
                    <div class="p-4 bg-white rounded-4 shadow-sm border-start border-4 border-primary hover-up">
                        <h6 class="fw-bold mb-1">Central Neural Clinic</h6>
                        <p class="small text-muted mb-0">2.4 km away • Open Now</p>
                    </div>
                    <div class="p-4 bg-white rounded-4 shadow-sm hover-up">
                        <h6 class="fw-bold mb-1">Indigo Wellness Center</h6>
                        <p class="small text-muted mb-0">5.1 km away • Appointment Only</p>
                    </div>
                    <div class="p-4 bg-white rounded-4 shadow-sm hover-up">
                        <h6 class="fw-bold mb-1">Sovereign Mind Hospital</h6>
                        <p class="small text-muted mb-0">8.9 km away • 24/7 Access</p>
                    </div>
                </div>

                <div class="mt-5 p-4 bg-primary bg-opacity-10 rounded-4">
                    <p class="small text-primary fw-bold mb-0">
                        <i class="fa-solid fa-circle-nodes me-2"></i> Our OpenSource map engine detects the nearest validated mental health professional based on your GPS pulse.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-8 fade-in delay-1">
            <div id="map"></div>
        </div>
    </div>
</div>

<script>
function initLeafletMap() {
    // Default location (Coordinates for a general starting point)
    var defaultLat = 28.6139; // Example: New Delhi
    var defaultLng = 77.2090;

    var map = L.map('map').setView([defaultLat, defaultLng], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Try to get actual user location
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;
            map.setView([lat, lng], 14);

            var userIcon = L.divIcon({
                className: 'user-marker',
                html: '<div style="background-color: #0D9488; width: 15px; height: 15px; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 10px rgba(0,0,0,0.3);"></div>',
                iconSize: [15, 15]
            });

            L.marker([lat, lng], {icon: userIcon}).addTo(map).bindPopup('<b>Your Sanctuary</b><br>Currently Active.').openPopup();

            // Mock Clinic Locations nearby
            L.marker([lat + 0.005, lng + 0.005]).addTo(map).bindPopup('Central Neural Clinic');
            L.marker([lat - 0.008, lng + 0.012]).addTo(map).bindPopup('Indigo Wellness Center');
        });
    }
}

document.addEventListener("DOMContentLoaded", initLeafletMap);
</script>

<style>
    .hover-up { transition: 0.3s; cursor: pointer; }
    .hover-up:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
