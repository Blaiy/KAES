<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get category ID from URL
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;

// Pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$topics_per_page = 10;
$offset = ($page - 1) * $topics_per_page;

// Fetch category details
$category_stmt = $pdo->prepare("SELECT * FROM forum_categories WHERE id = ?");
$category_stmt->execute([$category_id]);
$category = $category_stmt->fetch();

if (!$category) {
    header("Location: forum.php");
    exit();
}

// Fetch topics with user and tag information
$topics_stmt = $pdo->prepare("
    SELECT 
        ft.id, 
        ft.title, 
        ft.content, 
        ft.created_at, 
        u.username,
        (SELECT COUNT(*) FROM forum_replies WHERE topic_id = ft.id) as reply_count,
        (
            SELECT GROUP_CONCAT(tag_name SEPARATOR ', ') 
            FROM forum_topic_tags 
            WHERE topic_id = ft.id
        ) as tags
    FROM 
        forum_topics ft
    JOIN 
        users u ON ft.user_id = u.id
    WHERE 
        ft.category_id = ?
    ORDER BY 
        ft.created_at DESC
    LIMIT ? OFFSET ?
");
$topics_stmt->execute([$category_id, $topics_per_page, $offset]);
$topics = $topics_stmt->fetchAll();

// Count total topics for pagination
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM forum_topics WHERE category_id = ?");
$count_stmt->execute([$category_id]);
$total_topics = $count_stmt->fetchColumn();
$total_pages = ceil($total_topics / $topics_per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category['name']); ?> - KAES Forum</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>

<body class="bg-gray-50">
    <?php include 'partials/sidebar.php'; ?>

    <main class="lg:ml-64 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-white rounded-xl shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h1 class="text-2xl font-bold text-kabarak-maroon">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </h1>
                        <a href="create_topic.php?category_id=<?php echo $category_id; ?>" 
                           class="px-4 py-2 bg-kabarak-maroon text-white rounded-lg hover:bg-kabarak-maroon-dark">
                            Create New Topic
                        </a>
                    </div>
                    <p class="text-gray-600 mt-2">
                        <?php echo htmlspecialchars($category['description']); ?>
                    </p>
                </div>

                <?php if (empty($topics)): ?>
                    <div class="p-6 text-center text-gray-500">
                        No topics have been created in this category yet.
                    </div>
                <?php else: ?>
                    <div class="divide-y divide-gray-200">
                        <?php foreach ($topics as $topic): ?>
                            <div class="p-6 hover:bg-gray-50 transition-colors">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1 min-w-0">
                                        <a href="forum_topic_detail.php?id=<?php echo $topic['id']; ?>" 
                                           class="block">
                                            <h2 class="text-lg font-semibold text-gray-900 truncate">
                                                <?php echo htmlspecialchars($topic['title']); ?>
                                            </h2>
                                        </a>
                                        <div class="mt-2 flex items-center text-sm text-gray-500">
                                            <span class="mr-2">
                                                <i class="ri-user-line mr-1"></i>
                                                <?php echo htmlspecialchars($topic['username']); ?>
                                            </span>
                                            <span class="mr-2">
                                                <i class="ri-calendar-line mr-1"></i>
                                                <?php echo date('M d, Y', strtotime($topic['created_at'])); ?>
                                            </span>
                                            <span>
                                                <i class="ri-message-2-line mr-1"></i>
                                                <?php echo $topic['reply_count']; ?> replies
                                            </span>
                                        </div>
                                        <?php if (!empty($topic['tags'])): ?>
                                            <div class="mt-2 flex flex-wrap gap-2">
                                                <?php foreach (explode(', ', $topic['tags']) as $tag): ?>
                                                    <span class="px-2 py-1 bg-gray-100 text-xs rounded-full">
                                                        <?php echo htmlspecialchars($tag); ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="px-6 py-4 flex justify-center">
                        <nav class="flex space-x-2">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <a href="?category_id=<?php echo $category_id; ?>&page=<?php echo $i; ?>"
                                   class="px-3 py-2 <?php echo ($page == $i ? 'bg-kabarak-maroon text-white' : 'bg-white text-gray-700 border'); ?> rounded-lg">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>
                        </nav>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>