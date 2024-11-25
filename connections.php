<?php
session_start();
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];

// Handle connection actions
if (isset($_POST['action'])) {
    $connection_id = $_POST['connection_id'];
    
    switch ($_POST['action']) {
        case 'accept':
            $update_stmt = $db->prepare("UPDATE connections SET status = 'accepted' WHERE id = ? AND receiver_id = ?");
            $update_stmt->bind_param("ii", $connection_id, $current_user_id);
            $update_stmt->execute();
            break;
        
        case 'reject':
            $update_stmt = $db->prepare("UPDATE connections SET status = 'rejected' WHERE id = ? AND receiver_id = ?");
            $update_stmt->bind_param("ii", $connection_id, $current_user_id);
            $update_stmt->execute();
            break;
        
        case 'remove':
            $delete_stmt = $db->prepare("DELETE FROM connections WHERE id = ? AND (sender_id = ? OR receiver_id = ?)");
            $delete_stmt->bind_param("iii", $connection_id, $current_user_id, $current_user_id);
            $delete_stmt->execute();
            break;
    }
}

// Fetch connection requests
$pending_requests_query = "
    SELECT c.id, c.sender_id, u.name, u.avatar, u.position, u.company 
    FROM connections c
    JOIN users u ON c.sender_id = u.id
    WHERE c.receiver_id = ? AND c.status = 'pending'
";
$pending_stmt = $db->prepare($pending_requests_query);
$pending_stmt->bind_param("i", $current_user_id);
$pending_stmt->execute();
$pending_requests = $pending_stmt->get_result();

// Fetch accepted connections
$connections_query = "
    SELECT 
        c.id, 
        CASE 
            WHEN c.sender_id = ? THEN c2.id 
            ELSE c1.id 
        END as connected_user_id,
        CASE 
            WHEN c.sender_id = ? THEN c2.name 
            ELSE c1.name 
        END as connected_user_name,
        CASE 
            WHEN c.sender_id = ? THEN c2.avatar 
            ELSE c1.avatar 
        END as connected_user_avatar,
        CASE 
            WHEN c.sender_id = ? THEN c2.position 
            ELSE c1.position 
        END as connected_user_position
    FROM connections c
    JOIN users c1 ON c.sender_id = c1.id
    JOIN users c2 ON c.receiver_id = c2.id
    WHERE (c.sender_id = ? OR c.receiver_id = ?) 
    AND c.status = 'accepted'
";
$connections_stmt = $db->prepare($connections_query);
$connections_stmt->bind_param("iiiiii", 
    $current_user_id, $current_user_id, $current_user_id, 
    $current_user_id, $current_user_id, $current_user_id
);
$connections_stmt->execute();
$connections = $connections_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connection Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <main class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold mb-8">Connection Management</h1>

            <!-- Connection Requests Section -->
            <section class="mb-12">
                <h2 class="text-2xl font-semibold mb-4">Connection Requests</h2>
                <?php if ($pending_requests->num_rows > 0): ?>
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php while ($request = $pending_requests->fetch_assoc()): ?>
                            <div class="bg-white rounded-xl shadow-sm p-6">
                                <div class="flex items-center space-x-4 mb-4">
                                    <img src="<?php echo htmlspecialchars($request['avatar']); ?>" 
                                         alt="Profile" class="w-16 h-16 rounded-full object-cover">
                                    <div>
                                        <h3 class="font-semibold"><?php echo htmlspecialchars($request['name']); ?></h3>
                                        <p class="text-gray-600"><?php echo htmlspecialchars($request['position']); ?></p>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <form method="POST" class="w-full">
                                        <input type="hidden" name="connection_id" value="<?php echo $request['id']; ?>">
                                        <div class="flex space-x-2">
                                            <button type="submit" name="action" value="accept" 
                                                    class="flex-1 bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600">
                                                Accept
                                            </button>
                                            <button type="submit" name="action" value="reject" 
                                                    class="flex-1 bg-red-500 text-white py-2 px-4 rounded-lg hover:bg-red-600">
                                                Reject
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500">No pending connection requests.</p>
                <?php endif; ?>
            </section>

            <!-- Connections Section -->
            <section>
                <h2 class="text-2xl font-semibold mb-4">My Connections</h2>
                <?php if ($connections->num_rows > 0): ?>
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php while ($connection = $connections->fetch_assoc()): ?>
                            <div class="bg-white rounded-xl shadow-sm p-6">
                                <div class="flex items-center space-x-4 mb-4">
                                    <img src="<?php echo htmlspecialchars($connection['connected_user_avatar']); ?>" 
                                         alt="Profile" class="w-16 h-16 rounded-full object-cover">
                                    <div>
                                        <h3 class="font-semibold">
                                            <?php echo htmlspecialchars($connection['connected_user_name']); ?>
                                        </h3>
                                        <p class="text-gray-600">
                                            <?php echo htmlspecialchars($connection['connected_user_position']); ?>
                                        </p>
                                    </div>
                                </div>