<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $q_type = $_POST['q_type'];

    // Calculate Score
    $score = 0;
    $responses = [];
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'q') === 0) {
            $score += (int) $value;
            $responses[$key] = (int) $value;
        }
    }

    /**
     * AI Assessment Logic (Clinical Standards PHQ-9 for Depression)
     * 0-4: Normal / Minimal
     * 5-9: Mild
     * 10-14: Moderate
     * 15-27: Severe
     */
    $classification = 'Normal';
    if ($score >= 15)
        $classification = 'Severe';
    elseif ($score >= 10)
        $classification = 'Moderate';
    elseif ($score >= 5)
        $classification = 'Mild';

    try {
        $stmt = $pdo->prepare("INSERT INTO questionnaires (user_id, q_type, responses, score, classification) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $q_type, json_encode($responses), $score, $classification]);

        // Clear draft after successful completion
        $stmt_draft = $pdo->prepare("DELETE FROM questionnaire_drafts WHERE user_id = ? AND q_type = ?");
        $stmt_draft->execute([$user_id, $q_type]);

        header("Location: results.php?assessment_id=" . $pdo->lastInsertId());
    } catch (PDOException $e) {
        die("Error processing assessment: " . $e->getMessage());
    }
} else {
    header("Location: dashboard.php");
}
?>