<?php
session_start(); 
require_once 'db.php'; 
require_once 'functions.php';

// Simplified and more reliable admin check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    error_log('Admin access denied. Session data: ' . print_r($_SESSION, true));
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KAES Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Add custom Kabarak colors
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'kabarak-maroon': '#800000'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto p-6">
        <header class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Admin Dashboard</h1>
            <div class="flex items-center gap-4">
                <span class="text-gray-600"><?php echo htmlspecialchars($_SESSION['email']); ?></span>
                <a href="logout.php" class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300">Logout</a>
            </div>
        </header>

        <!-- Events Management -->
        <section class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">Manage Events</h2>
            
            <!-- Add Event Form -->
            <form action="add_event.php" method="POST" enctype="multipart/form-data" class="mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Event Name</label>
                        <input type="text" name="event_name" required
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-kabarak-maroon focus:ring focus:ring-kabarak-maroon focus:ring-opacity-50">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Event Date</label>
                        <input type="date" name="event_date" required
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-kabarak-maroon focus:ring focus:ring-kabarak-maroon focus:ring-opacity-50">
                    </div>
                    <div class="space-y-2 md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" rows="3"
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-kabarak-maroon focus:ring focus:ring-kabarak-maroon focus:ring-opacity-50"></textarea>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Location</label>
                        <input type="text" name="location"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-kabarak-maroon focus:ring focus:ring-kabarak-maroon focus:ring-opacity-50">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Category</label>
                        <select name="category" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-kabarak-maroon focus:ring focus:ring-kabarak-maroon focus:ring-opacity-50">
                            <option value="">Select Category</option>
                            <option value="academic">Academic</option>
                            <option value="social">Social</option>
                            <option value="sports">Sports</option>
                        </select>
                    </div>
                    <div class="space-y-2 md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Event Image</label>
                        <input type="file" name="event_image"
                               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-kabarak-maroon file:text-white hover:file:bg-kabarak-maroon/90">
                    </div>
                </div>
                <button type="submit" 
                        class="mt-4 bg-kabarak-maroon text-white px-4 py-2 rounded hover:bg-kabarak-maroon/90 focus:outline-none focus:ring-2 focus:ring-kabarak-maroon focus:ring-offset-2">
                    Create Event
                </button>
            </form>

            <!-- Events List -->
            <div class="mt-8">
                <h3 class="font-semibold mb-3">Current Events</h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php
                            try {
                                $stmt = $pdo->query("SELECT * FROM events ORDER BY event_date");
                                while ($event = $stmt->fetch(PDO::FETCH_ASSOC)):
                            ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($event['name']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($event['event_date']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($event['location']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap space-x-2">
                                    <button onclick="editEvent(<?php echo $event['id']; ?>)"
                                            class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                                        Edit
                                    </button>
                                    <button onclick="deleteEvent(<?php echo $event['id']; ?>)"
                                            class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                            <?php 
                                endwhile;
                            } catch (PDOException $e) {
                                error_log("Error fetching events: " . $e->getMessage());
                                echo "<tr><td colspan='4' class='px-6 py-4 text-center text-red-500'>Error loading events</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <script>
        function editEvent(eventId) {
            // Implement edit functionality
            window.location.href = `edit_event.php?id=${eventId}`;
        }

        function deleteEvent(eventId) {
            if (confirm('Are you sure you want to delete this event?')) {
                fetch(`delete_event.php?id=${eventId}`, {
                    method: 'DELETE',
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error deleting event');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting event');
                });
            }
        }
    </script>
</body>
</html>