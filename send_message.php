<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$receiver_id = $data['receiver_id'] ?? null;
$content = $data['content'] ?? '';
$user_id = $_SESSION['user_id'];

// Debug log
error_log("Sending message: " . json_encode([
    'sender_id' => $user_id,
    'receiver_id' => $receiver_id,
    'content' => $content
]));

if (!$receiver_id || empty($content)) {
    http_response_code(400);
    echo json_encode(['error' => 'Receiver ID and content are required']);
    exit;
}

try {
    // Insert message
    $stmt = $pdo->prepare("
        INSERT INTO messages (sender_id, receiver_id, content)
        VALUES (:sender_id, :receiver_id, :content)
    ");

    $stmt->execute([
        ':sender_id' => $user_id,
        ':receiver_id' => $receiver_id,
        ':content' => $content
    ]);

    $message_id = $pdo->lastInsertId();

    // Get the newly created message with sender details
    $stmt = $pdo->prepare("
        SELECT 
            m.*,
            COALESCE(s.name, a.name) as sender_name,
            COALESCE(s.avatar, a.avatar) as sender_avatar
        FROM messages m
        JOIN users u ON m.sender_id = u.id
        LEFT JOIN students s ON u.id = s.user_id
        LEFT JOIN alumni a ON u.id = a.user_id
        WHERE m.id = :message_id
    ");

    $stmt->execute([':message_id' => $message_id]);
    $message = $stmt->fetch(PDO::FETCH_ASSOC);

    // Debug log
    error_log("Message sent successfully: " . json_encode($message));

    echo json_encode([
        'success' => true,
        'message' => $message
    ]);

} catch (PDOException $e) {
    error_log("Error sending message: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}