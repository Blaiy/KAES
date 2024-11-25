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
    
    // Validate input
    $name = $_POST['event_name'] ?? '';
    $date = $_POST['event_date'] ?? '';
    $description = $_POST['description'] ?? '';
    $location = $_POST['location'] ?? '';
    $category = $_POST['category'] ?? '';

    // Handle image upload (similar to add_event.php)
    $image_url = null;
    if (!empty($_FILES['event_image']['name'])) {
        // Image upload logic from add_event.php
    }

    // Prepare update SQL
    $sql = "UPDATE events SET 
            name = :name, 
            description = :description, 
            event_date = :date, 
            location = :location, 
            category = :category";
    
    // Add image update if new image uploaded
    if ($image_url) {
        $sql .= ", image_url = :image_url";
    }
    
    $sql .= " WHERE id = :event_id";

    $stmt = $pdo->prepare($sql);

    try {
        $params = [
            ':name' => $name,
            ':description' => $description,
            ':date' => $date,
            ':location' => $location,
            ':category' => $category,
            ':event_id' => $event_id
        ];

        // Add image param if new image
        if ($image_url) {
            $params[':image_url'] = $image_url;
        }

        $stmt->execute($params);

        header("Location: admin_dashboard.php?success=Event updated successfully");
        exit();
    } catch (PDOException $e) {
        error_log($e->getMessage());
        header("Location: admin_dashboard.php?error=Failed to update event");
        exit();
    }
}