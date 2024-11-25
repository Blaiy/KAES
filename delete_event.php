<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

// Verify admin access
if (!isAdmin($_SESSION['user_id'])) {
    http_response_code(403);
    exit('Unauthorized access');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $_POST['event_id'] ?? null;

    if (!$event_id) {
        header("Location: admin_dashboard.php?error=Invalid event ID");
        exit();
    }

    try {
        // First, get the image URL to delete file if exists
        $stmt = $pdo->prepare("SELECT image_url FROM events WHERE id = ?");
        $stmt->execute([$event_id]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        // Delete event from database
        $delete_stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
        $delete_stmt->execute([$event_id]);

        // Remove image file if exists
        if ($event['image_url'] && file_exists($event['image_url'])) {
            unlink($event['image_url']);
        }

        header("Location: admin_dashboard.php?success=Event deleted successfully");
        exit();
    } catch (PDOException $e) {
        error_log($e->getMessage());
        header("Location: admin_dashboard.php?error=Failed to delete event");
        exit();
    }
}