<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_GET['receiver_id'])) {
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

$user_id = $_SESSION['user_id'];
$receiver_id = $_GET['receiver_id'];

try {
    // Get receiver details
    $stmt = $pdo->prepare("
        SELECT 
            u.id,
            COALESCE(s.name, a.name) as name,
            u.user_type
        FROM users u
        LEFT JOIN students s ON u.id = s.user_id
        LEFT JOIN alumni a ON u.id = a.user_id
        WHERE u.id = ?
    ");
    
    $stmt->execute([$receiver_id]);
    $receiver = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$receiver) {
        throw new Exception('Receiver not found');
    }

    // Get messages between users
    $stmt = $pdo->prepare("
        SELECT 
            m.*,
            COALESCE(s.name, a.name) as sender_name,
            COALESCE(s.avatar, a.avatar) as sender_avatar,
            u.user_type
        FROM messages m
        JOIN users u ON m.sender_id = u.id
        LEFT JOIN students s ON u.id = s.user_id
        LEFT JOIN alumni a ON u.id = a.user_id
        WHERE (m.sender_id = ? AND m.receiver_id = ?)
           OR (m.sender_id = ? AND m.receiver_id = ?)
        ORDER BY m.created_at ASC
    ");
    
    $stmt->execute([$user_id, $receiver_id, $receiver_id, $user_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mark messages as read
    $stmt = $pdo->prepare("
        UPDATE messages 
        SET is_read = TRUE 
        WHERE sender_id = ? 
        AND receiver_id = ? 
        AND is_read = FALSE
    ");
    $stmt->execute([$receiver_id, $user_id]);

    echo json_encode([
        'success' => true,
        'receiver' => $receiver,
        'messages' => $messages,
        'debug' => [
            'user_id' => $user_id,
            'receiver_id' => $receiver_id,
            'message_count' => count($messages)
        ]
    ]);

} catch (Exception $e) {
    error_log("Error in get_messages.php: " . $e->getMessage());
    echo json_encode([
        'error' => 'Database error: ' . $e->getMessage(),
        'debug' => [
            'user_id' => $user_id,
            'receiver_id' => $receiver_id
        ]
    ]);
}