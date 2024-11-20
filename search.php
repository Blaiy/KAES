<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Search functionality
$search_query = isset($_GET['query']) ? trim($_GET['query']) : '';
$search_type = isset($_GET['type']) ? $_GET['type'] : 'all';

$results = [];
$error = '';

if (!empty($search_query)) {
    try {
        // Search Users
        $user_stmt = $pdo->prepare("
            SELECT id, username, email 
            FROM users 
            WHERE username LIKE ? OR email LIKE ?
            LIMIT 10
        ");
        $user_stmt->execute(["%$search_query%", "%$search_query%"]);
        $user_results = $user_stmt->fetchAll();

        // Search Topics
        $topic_stmt = $pdo->prepare("
            SELECT 
                ft.id, 
                ft.title, 
                ft.content, 
                fc.name as category, 
                u.username 
            FROM forum_topics ft
            JOIN forum_categories fc ON ft.category_id = fc.id
            JOIN users u ON ft.user_id = u.id
            WHERE 
                ft.title LIKE ? OR 
                ft.content LIKE ? 
            LIMIT 10
        ");
        $topic_stmt->execute(["%$search_query%", "%$search_query%"]);
        $topic_results = $topic_stmt->fetchAll();

        // Search Events (assuming you have an events table)
        $event_stmt = $pdo->prepare("
            SELECT 
                id, 
                title, 
                description, 
                start_date, 
                location 
            FROM events 
            WHERE 
                title LIKE ? OR 
                description LIKE ? OR 
                location LIKE ?
            LIMIT 10
        ");
        $event_stmt->execute(["%$search_query%", "%$search_query%", "%$search_query%"]);
        $event_results = $event_stmt->fetchAll();
    } catch (Exception $e) {
        $error = "An error occurred during search.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search - KAES Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>

<body class="bg-gray-50">
    <?php include 'partials/sidebar.php'; ?>

    <main class="lg:ml-64 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-white rounded-xl shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <h1 class="text-2xl font-bold text-kabarak-maroon mb-4">
                         Search
                    </h1>

                    <form method="GET" class="mb-6">
                        <div class="flex space-x-2">
                            <input 
                                type="text" 
                                name="query" 
                                value="<?php echo htmlspecialchars($search_query); ?>"
                                placeholder="Search users, topics, events..."
                                class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-kabarak-maroon focus:ring-kabarak-maroon"
                            >
                            <select 
                                name="type" 
                                class="rounded-lg border-gray-300 shadow-sm focus:border-kabarak-maroon focus:ring-kabarak-maroon"
                            >
                                <option value="all" <?php echo ($search_type == 'all' ? 'selected' : ''); ?>>All</option>
                                <option value="users" <?php echo ($search_type == 'users' ? 'selected' : ''); ?>>Users</option>
                                <option value="topics" <?php echo ($search_type == 'topics' ? 'selected' : ''); ?>>Topics</option>
                                <option value="events" <?php echo ($search_type == 'events' ? 'selected' : ''); ?>>Events</option>
                            </select>
                            <button 
                                type="submit" 
                                class="px-4 py-2 bg-kabarak-maroon text-white rounded-lg hover:bg-kabarak-maroon-dark"
                            >
                                Search
                            </button>
                        </div>
                    </form>

                    <?php if ($error): ?>
                        <div class="bg-red-50 text-red-500 p-4 rounded-lg mb-4">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($search_query)): ?>
                        <div class="space-y-6">
                            <!-- Users Results -->
                            <?php if (!empty($user_results)): ?>
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-900 mb-3">Users</h2>
                                    <div class="space-y-2">
                                        <?php foreach ($user_results as $user): ?>
                                            <div class="bg-gray-100 p-3 rounded-lg flex justify-between items-center">
                                                <div>
                                                    <span class="font-medium"><?php echo htmlspecialchars($user['username']); ?></span>
                                                    <span class="text-gray-500 text-sm ml-2"><?php echo htmlspecialchars($user['email']); ?></span>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Topics Results -->
                            <?php if (!empty($topic_results)): ?>
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-900 mb-3">Topics</h2>
                                    <div class="space-y-2">
                                        <?php foreach ($topic_results as $topic): ?>
                                            <div class="bg-gray-100 p-3 rounded-lg">
                                                <a href="forum_topic_detail.php?id=<?php echo $topic['id']; ?>" class="block">
                                                    <h3 class="font-medium text-kabarak-maroon"><?php echo htmlspecialchars($topic['title']); ?></h3>
                                                    <p class="text-sm text-gray-600 line-clamp-2"><?php echo htmlspecialchars($topic['content']); ?></p>
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        Category: <?php echo htmlspecialchars($topic['category']); ?> 
                                                        | By <?php echo htmlspecialchars($topic['username']); ?>
                                                    </div>
                                                </a>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Events Results -->
                            <?php if (!empty($event_results)): ?>
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-900 mb-3">Events</h2>
                                    <div class="space-y-2">
                                        <?php foreach ($event_results as $event): ?>
                                            <div class="bg-gray-100 p-3 rounded-lg">
                                                <h3 class="font-medium text-kabarak-maroon"><?php echo htmlspecialchars($event['title']); ?></h3>
                                                <p class="text-sm text-gray-600 line-clamp-2"><?php echo htmlspecialchars($event['description']); ?></p>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    Date: <?php echo date('M d, Y', strtotime($event['start_date'])); ?> 
                                                    | Location: <?php echo htmlspecialchars($event['location']); ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- No Results -->
                            <?php if (empty($user_results) && empty($topic_results) && empty($event_results)): ?>
                                <div class="text-center text-gray-500 p-6">
                                    No results found for "<?php echo htmlspecialchars($search_query); ?>"
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
