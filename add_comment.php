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
$content = $data['content'] ?? '';
$parent_id = $data['parent_id'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$post_id || empty($content)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO comments (post_id, user_id, parent_id, content)
        VALUES (:post_id, :user_id, :parent_id, :content)
    ");

    $stmt->execute([
        ':post_id' => $post_id,
        ':user_id' => $user_id,
        ':parent_id' => $parent_id,
        ':content' => $content
    ]);

    $comment_id = $pdo->lastInsertId();

    // Fetch the newly created comment with user details
    $stmt = $pdo->prepare("
        SELECT 
            c.*,
            COALESCE(s.name, a.name) as author_name,
            COALESCE(s.avatar, a.avatar) as author_avatar
        FROM comments c
        JOIN users u ON c.user_id = u.id
        LEFT JOIN students s ON u.id = s.user_id
        LEFT JOIN alumni a ON u.id = a.user_id
        WHERE c.id = ?
    ");

    $stmt->execute([$comment_id]);
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'comment' => $comment
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
} 