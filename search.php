<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$results = [];

if (!empty($search_query)) {
    try {
        if ($filter === 'users' || $filter === 'all') {
            $stmt = $pdo->prepare("
                SELECT 
                    u.id,
                    u.email,
                    COALESCE(s.name, a.name) as name,
                    CASE 
                        WHEN s.id IS NOT NULL THEN 'Student'
                        WHEN a.id IS NOT NULL THEN 'Alumni'
                    END as user_type
                FROM users u
                LEFT JOIN students s ON u.id = s.user_id
                LEFT JOIN alumni a ON u.id = a.user_id
                WHERE COALESCE(s.name, a.name) LIKE :search
                OR u.email LIKE :search
            ");
            $stmt->execute(['search' => "%$search_query%"]);
            $results['users'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        if ($filter === 'events' || $filter === 'all') {
            $stmt = $pdo->prepare("
                SELECT title, description, event_date, location
                FROM events
                WHERE title LIKE :search
                OR description LIKE :search
                ORDER BY event_date ASC
            ");
            $stmt->execute(['search' => "%$search_query%"]);
            $results['events'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        $error = "An error occurred while searching. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="max-w-4xl mx-auto p-4">
        <!-- Search Form -->
        <form method="GET" class="mb-8">
            <div class="space-y-4">
                <input type="text" 
                       name="q" 
                       value="<?php echo htmlspecialchars($search_query); ?>"
                       class="w-full px-4 py-2 rounded-lg border"
                       placeholder="Search for users or events...">

                <div class="flex space-x-4">
                    <a href="?q=<?php echo urlencode($search_query); ?>&filter=all" 
                       class="<?php echo $filter === 'all' ? 'text-blue-600 font-bold' : ''; ?>">
                        All
                    </a>
                    <a href="?q=<?php echo urlencode($search_query); ?>&filter=users" 
                       class="<?php echo $filter === 'users' ? 'text-blue-600 font-bold' : ''; ?>">
                        Users
                    </a>
                    <a href="?q=<?php echo urlencode($search_query); ?>&filter=events" 
                       class="<?php echo $filter === 'events' ? 'text-blue-600 font-bold' : ''; ?>">
                        Events
                    </a>
                </div>
            </div>
        </form>

        <!-- Results -->
        <?php if (!empty($search_query)): ?>
            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 p-4 rounded-lg">
                    <?php echo $error; ?>
                </div>
            <?php else: ?>
                <!-- Users Results -->
                <?php if ($filter !== 'events' && !empty($results['users'])): ?>
                    <div class="mb-8">
                        <h2 class="text-xl font-bold mb-4">Users</h2>
                        <div class="space-y-4">
                            <?php foreach ($results['users'] as $user): ?>
                                <div class="bg-white p-4 rounded-lg shadow-sm">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <h3 class="font-semibold"><?php echo htmlspecialchars($user['name']); ?></h3>
                                            <p class="text-sm text-gray-500"><?php echo htmlspecialchars($user['user_type']); ?></p>
                                        </div>
                                        <a href="profile.php?id=<?php echo $user['id']; ?>" 
                                           class="bg-blue-600 text-white px-4 py-2 rounded-lg">
                                            View Profile
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Events Results -->
                <?php if ($filter !== 'users' && !empty($results['events'])): ?>
                    <div>
                        <h2 class="text-xl font-bold mb-4">Events</h2>
                        <div class="space-y-4">
                            <?php foreach ($results['events'] as $event): ?>
                                <div class="bg-white p-4 rounded-lg shadow-sm">
                                    <h3 class="font-semibold"><?php echo htmlspecialchars($event['title']); ?></h3>
                                    <p class="text-sm text-gray-600 mt-1"><?php echo htmlspecialchars($event['description']); ?></p>
                                    <div class="mt-2 text-sm text-gray-500">
                                        <p>Date: <?php echo date('F j, Y', strtotime($event['event_date'])); ?></p>
                                        <p>Location: <?php echo htmlspecialchars($event['location']); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (empty($results['users']) && empty($results['events'])): ?>
                    <div class="text-center py-8 text-gray-500">
                        No results found. Try different search terms.
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>