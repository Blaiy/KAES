<?php
require_once 'db.php';

try {
    $stmt = $pdo->query("SELECT * FROM schools ORDER BY name");
    $schools = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($schools);
} catch (PDOException $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Failed to fetch schools']);
} 