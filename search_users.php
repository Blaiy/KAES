<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['q'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

$query = '%' . $_GET['q'] . '%';
$user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("
        SELECT 
            u.id,
            COALESCE(s.name, a.name) as name,
            COALESCE(s.avatar, a.avatar) as avatar,
            u.user_type,
            CASE 
                WHEN u.user_type = 'student' THEN s.reg_number
                ELSE a.year_of_graduation
            END as additional_info
        FROM users u
        LEFT JOIN students s ON u.id = s.user_id
        LEFT JOIN alumni a ON u.id = a.user_id
        WHERE u.id != ? 
        AND (
            s.name LIKE ? 
            OR a.name LIKE ?
            OR s.reg_number LIKE ?
        )
        ORDER BY name
        LIMIT 10
    ");
    
    $stmt->execute([$user_id, $query, $query, $query]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode(['users' => $users]);

} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database error']);
} 