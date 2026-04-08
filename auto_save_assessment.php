<?php
session_start();
require_once 'includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if ($input && isset($input['data'])) {
    $user_id = $_SESSION['user_id'];
    $q_type = $input['q_type'];
    $draft_data = json_encode($input['data']);

    try {
        $stmt = $pdo->prepare("INSERT INTO questionnaire_drafts (user_id, q_type, draft_data) VALUES (?, ?, ?) 
                               ON DUPLICATE KEY UPDATE draft_data = ?, updated_at = CURRENT_TIMESTAMP");
        $stmt->execute([$user_id, $q_type, $draft_data, $draft_data]);
        
        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
}
?>
