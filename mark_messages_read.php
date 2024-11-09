<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['conversation_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

$conversation_id = $_GET['conversation_id'];
$user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("
        UPDATE messages 
        SET is_read = 1 
        WHERE conversation_id = ? 
        AND receiver_id = ? 
        AND is_read = 0
    ");
    
    $stmt->execute([$conversation_id, $user_id]);

    header('Content-Type: application/json');
    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database error']);
} 