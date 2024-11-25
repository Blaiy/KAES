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
    // Validate input
    $name = $_POST['event_name'] ?? '';
    $date = $_POST['event_date'] ?? '';
    $description = $_POST['description'] ?? '';
    $location = $_POST['location'] ?? '';
    $category = $_POST['category'] ?? '';

    // Handle image upload
    $image_url = null;
    if (!empty($_FILES['event_image']['name'])) {
        $upload_dir = 'uploads/events/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $image_filename = uniqid() . '_' . basename($_FILES['event_image']['name']);
        $image_path = $upload_dir . $image_filename;
        
        if (move_uploaded_file($_FILES['event_image']['tmp_name'], $image_path)) {
            $image_url = $image_path;
        }
    }

    // Prepare SQL statement
    $stmt = $pdo->prepare("
        INSERT INTO events 
        (name, description, event_date, location, category, image_url) 
        VALUES 
        (:name, :description, :date, :location, :category, :image_url)
    ");

    try {
        $stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':date' => $date,
            ':location' => $location,
            ':category' => $category,
            ':image_url' => $image_url
        ]);

        // Redirect with success message
        header("Location: admin_dashboard.php?success=Event created successfully");
        exit();
    } catch (PDOException $e) {
        // Log error and redirect with error message
        error_log($e->getMessage());
        header("Location: admin_dashboard.php?error=Failed to create event");
        exit();
    }
}