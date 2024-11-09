<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

if (!isset($_GET['post_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Post ID required']);
    exit;
}

$post_id = $_GET['post_id'];
$user_id = $_SESSION['user_id'];

try {
    // Fetch comments with user details and like counts
    $stmt = $pdo->prepare("
        SELECT 
            c.*,
            COALESCE(s.name, a.name) as author_name,
            COALESCE(s.avatar, a.avatar) as author_avatar,
            COUNT(DISTINCT cl.id) as likes_count,
            EXISTS(SELECT 1 FROM comment_likes WHERE comment_id = c.id AND user_id = ?) as is_liked
        FROM comments c
        JOIN users u ON c.user_id = u.id
        LEFT JOIN students s ON u.id = s.user_id
        LEFT JOIN alumni a ON u.id = a.user_id
        LEFT JOIN comment_likes cl ON c.id = cl.comment_id
        WHERE c.post_id = ? AND c.parent_id IS NULL
        GROUP BY c.id
        ORDER BY c.created_at DESC
    ");

    $stmt->execute([$user_id, $post_id]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch replies for each comment
    foreach ($comments as &$comment) {
        $stmt = $pdo->prepare("
            SELECT 
                c.*,
                COALESCE(s.name, a.name) as author_name,
                COALESCE(s.avatar, a.avatar) as author_avatar,
                COUNT(DISTINCT cl.id) as likes_count,
                EXISTS(SELECT 1 FROM comment_likes WHERE comment_id = c.id AND user_id = ?) as is_liked
            FROM comments c
            JOIN users u ON c.user_id = u.id
            LEFT JOIN students s ON u.id = s.user_id
            LEFT JOIN alumni a ON u.id = a.user_id
            LEFT JOIN comment_likes cl ON c.id = cl.comment_id
            WHERE c.parent_id = ?
            GROUP BY c.id
            ORDER BY c.created_at ASC
        ");
        
        $stmt->execute([$user_id, $comment['id']]);
        $comment['replies'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    header('Content-Type: application/json');
    echo json_encode(['comments' => $comments]);

} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database error']);
} 