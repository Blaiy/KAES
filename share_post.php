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
$post_id = $data['post_id'] ?? null;
$share_text = $data['share_text'] ?? '';
$user_id = $_SESSION['user_id'];

if (!$post_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Post ID required']);
    exit;
}

try {
    // Insert share record
    $stmt = $pdo->prepare("
        INSERT INTO post_shares (post_id, user_id, share_text)
        VALUES (:post_id, :user_id, :share_text)
    ");

    $stmt->execute([
        ':post_id' => $post_id,
        ':user_id' => $user_id,
        ':share_text' => $share_text
    ]);

    // Get share count
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count 
        FROM post_shares 
        WHERE post_id = ?
    ");
    $stmt->execute([$post_id]);
    $shares_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    echo json_encode([
        'success' => true,
        'shares_count' => $shares_count
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
} 