<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
require_once 'db.php';

// Handle connection request
if (isset($_GET['action']) && isset($_GET['user_id'])) {
    $action = $_GET['action'];
    $target_user_id = intval($_GET['user_id']);

    switch ($action) {
        case 'connect':
            // Check if connection already exists
            $check_query = "SELECT * FROM connections 
                            WHERE (sender_id = :sender1 AND receiver_id = :receiver1) 
                            OR (sender_id = :sender2 AND receiver_id = :receiver2)";
            $check_stmt = $pdo->prepare($check_query);
            $check_stmt->execute([
                ':sender1' => $_SESSION['user_id'], 
                ':receiver1' => $target_user_id,
                ':sender2' => $target_user_id, 
                ':receiver2' => $_SESSION['user_id']
            ]);
            $existing_connection = $check_stmt->fetch(PDO::FETCH_ASSOC);

            if (!$existing_connection) {
                // Send connection request
                $query = "INSERT INTO connections (sender_id, receiver_id, status) VALUES (:sender, :receiver, 'pending')";
                $stmt = $pdo->prepare($query);
                $stmt->execute([
                    ':sender' => $_SESSION['user_id'], 
                    ':receiver' => $target_user_id
                ]);
            }
            break;
    }
}

// Fetch all alumni except the current user
$users_query = "SELECT a.*, s.name as school_name, c.name as course_name 
                FROM alumni a
                JOIN schools s ON a.school_id = s.id
                JOIN courses c ON a.course_id = c.id
                WHERE a.user_id != :user_id";
$users_stmt = $pdo->prepare($users_query);
$users_stmt->execute([':user_id' => $_SESSION['user_id']]);
$users_result = $users_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connect with Alumni</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Sidebar -->
    <?php include 'partials/sidebar.php'; ?>
    
    <!-- Main Content -->
    <main class="lg:ml-64">
        <div class="pt-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <h1 class="text-3xl font-bold mb-8">Connect with Alumni</h1>

            <!-- Users Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($users_result as $user): ?>
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center space-x-4 mb-4">
                            <img src="<?php echo htmlspecialchars($user['avatar'] ?? 'default_avatar.png'); ?>" 
                                 alt="Profile" class="w-16 h-16 rounded-full object-cover">
                            <div>
                                <h3 class="font-semibold"><?php echo htmlspecialchars($user['name']); ?></h3>
                                <p class="text-gray-600">
                                    <?php echo htmlspecialchars($user['job_title'] ?? 'Alumni'); ?> 
                                    at <?php echo htmlspecialchars($user['company'] ?? $user['school_name']); ?>
                                </p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <a href="?action=connect&user_id=<?php echo $user['user_id']; ?>" 
                               class="flex-1 bg-kabarak-maroon text-white py-2 px-4 rounded-lg text-center hover:bg-kabarak-maroon/90">
                                Connect
                            </a>
                            <a href="profile.php?user_id=<?php echo $user['user_id']; ?>" 
                               class="flex-1 border border-kabarak-maroon text-kabarak-maroon py-2 px-4 rounded-lg text-center hover:bg-kabarak-maroon/10">
                                View Profile
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <!-- Mobile Menu Button -->
    <button class="lg:hidden fixed bottom-4 right-4 bg-kabarak-maroon text-white p-3 rounded-full shadow-lg" 
            onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')">
        <i class="ri-menu-line text-xl"></i>
    </button>
</body>
</html>