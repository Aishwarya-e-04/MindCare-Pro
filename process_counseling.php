<?php
session_start();
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['content'])) {
    $user_id = $_SESSION['user_id'];
    $content = trim($_POST['content']);

    /** 
     * AI Sentiment Analysis Logic (Rule-based for this simulation)
     * In a production environment, this could call an external API like Gemini or OpenAI.
     */
    function analyze_mental_health($text) {
        $text = strtolower($text);
        
        // Define dictionaries
        $positives = ['happy', 'joy', 'excited', 'good', 'better', 'stable', 'calm', 'hopeful', 'thankful', 'great', 'peaceful', 'optimistic', 'proud', 'accomplished'];
        $negatives = ['sad', 'unhappy', 'tired', 'bad', 'worse', 'anxious', 'worried', 'scared', 'depressed', 'fear', 'lonely', 'miserable', 'numb', 'guilty'];
        $stress_indicators = ['stress', 'overwhelmed', 'pressure', 'hard', 'difficult', 'struggling', 'exhausted', 'cannot cope', 'burned out', 'frustrated', 'panicking'];
        $concerning_indicators = ['suicidal', 'hopeless', 'end', 'hurting', 'pain', 'useless', 'nothing', 'quit', 'worthless', 'death', 'kill myself', 'give up'];

        $score = 5; // Start with neutral score of 5
        $status = 'Stable';

        // Analysis logic with weights
        foreach ($positives as $word) { if (strpos($text, $word) !== false) $score += 0.4; }
        foreach ($negatives as $word) { if (strpos($text, $word) !== false) $score -= 0.6; }
        foreach ($stress_indicators as $word) { if (strpos($text, $word) !== false) $score -= 0.8; }
        foreach ($concerning_indicators as $word) { if (strpos($text, $word) !== false) $score -= 2.0; }

        // Sanitize score to 0-10 range
        $score = max(0, min(10, round($score, 1)));

        // Determine Status based on final score (Clinical classification)
        if ($score >= 7.5) $status = 'Normal';
        elseif ($score >= 5.5) $status = 'Mildly Stressed';
        elseif ($score >= 3.5) $status = 'Moderately Anxious';
        else $status = 'Highly Concerning';

        return ['score' => $score, 'status' => $status];
    }

    $analysis_results = analyze_mental_health($content);
    $score = $analysis_results['score'];
    $status = $analysis_results['status'];

    try {
        $stmt = $pdo->prepare("INSERT INTO counseling_sessions (user_id, content, analysis_score, status) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $content, $score, $status]);
        header("Location: results.php?last_id=" . $pdo->lastInsertId());
    } catch (PDOException $e) {
        die("Error processing session: " . $e->getMessage());
    }
} else {
    header("Location: dashboard.php");
}
?>
