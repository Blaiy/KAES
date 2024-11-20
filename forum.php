<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get categories with topic counts
$categories_query = "
    SELECT 
        fc.*, 
        COUNT(DISTINCT ft.id) as topics_count,
        COUNT(DISTINCT fr.id) as replies_count,
        MAX(ft.updated_at) as last_activity
    FROM forum_categories fc
    LEFT JOIN forum_topics ft ON fc.id = ft.category_id
    LEFT JOIN forum_replies fr ON ft.id = fr.topic_id
    GROUP BY fc.id
    ORDER BY fc.name
";
$categories = $pdo->query($categories_query)->fetchAll();

// Get latest topics
$latest_topics_query = "
    SELECT 
        ft.*,
        fc.name as category_name,
        u.email,
        COALESCE(s.name, a.name) as author_name,
        COALESCE(s.avatar, a.avatar) as author_avatar,
        COUNT(DISTINCT fr.id) as replies_count,
        MAX(fr.created_at) as last_reply_at
    FROM forum_topics ft
    JOIN forum_categories fc ON ft.category_id = fc.id
    JOIN users u ON ft.user_id = u.id
    LEFT JOIN students s ON u.id = s.user_id
    LEFT JOIN alumni a ON u.id = a.user_id
    LEFT JOIN forum_replies fr ON ft.id = fr.topic_id
    GROUP BY ft.id
    ORDER BY ft.is_pinned DESC, ft.created_at DESC
    LIMIT 10
";
$latest_topics = $pdo->query($latest_topics_query)->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KAES Forum</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>

<body class="bg-gray-50">
    <?php include 'partials/sidebar.php'; ?>

    <main class="lg:ml-64 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Forum Header -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-kabarak-maroon">Forum</h1>
                <a href="create_topic.php" class="bg-kabarak-maroon text-white px-4 py-2 rounded-lg hover:bg-kabarak-maroon/90">
                    Create New Topic
                </a>
            </div>

            <!-- Categories Section -->
            <div class="bg-white rounded-xl shadow-sm mb-6">
                <div class="p-4 border-b">
                    <h2 class="text-lg font-semibold">Categories</h2>
                </div>
                <div class="divide-y">
                    <?php foreach ($categories as $category): ?>
                        <div class="p-4 hover:bg-gray-50">
                            <a href="forum_category.php?id=<?php echo $category['id']; ?>" class="flex justify-between items-center">
                                <div>
                                    <h3 class="font-medium text-kabarak-maroon">
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </h3>
                                    <p class="text-sm text-gray-600">
                                        <?php echo htmlspecialchars($category['description']); ?>
                                    </p>
                                </div>
                                <div class="text-right text-sm text-gray-500">
                                    <div><?php echo $category['topics_count']; ?> Topics</div>
                                    <div><?php echo $category['replies_count']; ?> Replies</div>
                                    <?php if ($category['last_activity']): ?>
                                        <div>Last active <?php echo time_elapsed_string($category['last_activity']); ?></div>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Latest Topics Section -->
            <div class="bg-white rounded-xl shadow-sm">
                <div class="p-4 border-b">
                    <h2 class="text-lg font-semibold">Latest Topics</h2>
                </div>
                <div class="divide-y">
                    <?php foreach ($latest_topics as $topic): ?>
                        <div class="p-4 hover:bg-gray-50">
                            <div class="flex items-start space-x-4">
                                <?php if ($topic['author_avatar'] && file_exists($topic['author_avatar'])): ?>
                                    <img src="<?php echo htmlspecialchars($topic['author_avatar']); ?>" 
                                         alt="<?php echo htmlspecialchars($topic['author_name']); ?>" 
                                         class="w-10 h-10 rounded-full">
                                <?php else: ?>
                                    <div class="w-10 h-10 rounded-full bg-kabarak-maroon text-white flex items-center justify-center font-bold">
                                        <?php 
                                        $initials = explode(' ', $topic['author_name']);
                                        echo strtoupper(substr($initials[0], 0, 1) . (isset($initials[1]) ? substr($initials[1], 0, 1) : ''));
                                        ?>
                                    </div>
                                <?php endif; ?>

                                <div class="flex-1">
                                    <div class="flex items-center space-x-2">
                                        <?php if ($topic['is_pinned']): ?>
                                            <i class="ri-pushpin-fill text-kabarak-maroon"></i>
                                        <?php endif; ?>
                                        <a href="forum_topic.php?id=<?php echo $topic['id']; ?>" 
                                           class="font-medium hover:text-kabarak-maroon">
                                            <?php echo htmlspecialchars($topic['title']); ?>
                                        </a>
                                    </div>
                                    <div class="text-sm text-gray-500 mt-1">
                                        Posted by <?php echo htmlspecialchars($topic['author_name']); ?> 
                                        in <?php echo htmlspecialchars($topic['category_name']); ?> •
                                        <?php echo time_elapsed_string($topic['created_at']); ?>
                                    </div>
                                </div>

                                <div class="text-right text-sm text-gray-500">
                                    <div><?php echo $topic['views']; ?> Views</div>
                                    <div><?php echo $topic['replies_count']; ?> Replies</div>
                                    <?php if ($topic['last_reply_at']): ?>
                                        <div>Last reply <?php echo time_elapsed_string($topic['last_reply_at']); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>
</body>
</html>