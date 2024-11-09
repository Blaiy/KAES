<?php
require_once 'db.php';

if (isset($_GET['school_id'])) {
    $school_id = $_GET['school_id'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM courses WHERE school_id = ? ORDER BY name");
        $stmt->execute([$school_id]);
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode($courses);
    } catch (PDOException $e) {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['error' => 'Failed to fetch courses']);
    }
} 