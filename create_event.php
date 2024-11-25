<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $event_date = $_POST['event_date'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $image_url = ''; // Default empty

    // Handle file upload for event image
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = 'uploads/events/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_name = uniqid() . '_' . basename($_FILES['image']['name']);
        $target_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $image_url = $target_path;
        }
    }

    // Insert event into database
    $stmt = $pdo->prepare("INSERT INTO events (name, event_date, location, description, category, image_url) VALUES (?, ?, ?, ?, ?, ?)");
    
    try {
        $stmt->execute([$name, $event_date, $location, $description, $category, $image_url]);
        $_SESSION['success_message'] = "Event created successfully!";
        header("Location: events.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Failed to create event: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event - KAES</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'kabarak-maroon': '#800000',
                        'kabarak-gold': '#FFD700',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <?php include 'partials/sidebar.php'; ?>

    <main class="lg:ml-64 min-h-screen">
        <nav class="fixed top-0 right-0 left-64 bg-white border-b border-gray-200 z-30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold text-kabarak-maroon">Create Event</h1>
                    </div>
                </div>
            </div>
        </nav>

        <div class="pt-20 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
            <form method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm p-6">
                <div class="space-y-6">
                    <div>
                        <label class="block text-gray-700 mb-2">Event Name</label>
                        <input type="text" name="name" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-kabarak-maroon">
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">Event Date</label>
                        <input type="date" name="event_date" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-kabarak-maroon">
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">Location</label>
                        <input type="text" name="location" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-kabarak-maroon">
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">Category</label>
                        <select name="category" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-kabarak-maroon">
                            <option value="">Select Category</option>
                            <option value="Academic">Academic</option>
                            <option value="Social">Social</option>
                            <option value="Workshop">Workshop</option>
                            <option value="Seminar">Seminar</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">Description</label>
                        <textarea name="description" rows="4" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-kabarak-maroon"></textarea>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">Event Image</label>
                        <input type="file" name="image" accept="image/*" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-kabarak-maroon">
                    </div>

                    <button type="submit" 
                            class="w-full bg-kabarak-maroon text-white py-2 rounded-lg hover:bg-kabarak-maroon/90 transition-colors duration-300">
                        Create Event
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>