<?php
session_start();
require_once 'includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['reply' => 'Please login to use the chat assistant.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
    $user_id = $_SESSION['user_id'];
    $message = trim($_POST['message']);

    /**
     * AI Chatbot Logic
     * Option 1: Mocked Logic (Improved)
     * Option 2: OpenAI API Integration (Placeholder)
     */
    
    // --- OPENAI INTEGRATION START ---
    /*
    $apiKey = 'YOUR_OPENAI_API_KEY';
    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'model' => 'gpt-3.5-turbo',
        'messages' => [['role' => 'user', 'content' => $message]]
    ]));
    $response = curl_exec($ch);
    $data = json_decode($response, true);
    $ai_reply = $data['choices'][0]['message']['content'];
    */
    // --- OPENAI INTEGRATION END ---

    // For now, use a more sophisticated mock logic
    $msg_lower = strtolower($message);
    $ai_reply = "I hear you. Can you tell me more about that?";

    if (strpos($msg_lower, 'depressed') !== false || strpos($msg_lower, 'sad') !== false) {
        $ai_reply = "I'm sorry you're feeling this way. It's okay to feel sad sometimes. How long have you been feeling this way?";
    } elseif (strpos($msg_lower, 'anxious') !== false || strpos($msg_lower, 'stress') !== false) {
        $ai_reply = "Anxiety can be very taxing. Take a deep breath. What do you think is the main cause of your stress right now?";
    } elseif (strpos($msg_lower, 'thank') !== false || strpos($msg_lower, 'good') !== false) {
        $ai_reply = "I'm glad to hear that! Is there anything else you'd like to share or track today?";
    } elseif (strpos($msg_lower, 'hurt') !== false || strpos($msg_lower, 'kill') !== false) {
        $ai_reply = "I'm really concerned because it sounds like you're going through a very tough time. Please reach out to someone who can help. You can call a crisis helpline or talk to a trusted friend. You are not alone.";
    }

    // Save to logs
    try {
        $stmt = $pdo->prepare("INSERT INTO chatbot_logs (user_id, message, response) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $message, $ai_reply]);
    } catch (PDOException $e) {
        // Log error internally but return AI reply anyway
    }

    echo json_encode(['reply' => $ai_reply]);
}
?>
