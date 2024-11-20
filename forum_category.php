<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch categories with topic count
$categories_stmt = $pdo->query("
    SELECT 
        fc.id, 
        fc.name, 
        fc.description, 
        COUNT(ft.id) as topic_count
    FROM 
        forum_categories fc
    LEFT JOIN 
        forum_topics ft ON fc.id = ft.category_id
    GROUP BY 
        fc.id, fc.name, fc.description
    ORDER BY 
        topic_count DESC
");
$categories = $categories_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum Categories - KAES Forum</title>
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
                            Forum Categories
                        </h1>
                        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                            <a href="manage_categories.php" 
                               class="px-4 py-2 bg-kabarak-maroon text-white rounded-lg hover:bg-kabarak-maroon-dark">
                                Manage Categories
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (empty($categories)): ?>
                    <div class="p-6 text-center text-gray-500">
                        No categories have been created yet.
                    </div>
                <?php else: ?>
                    <div class="divide-y divide-gray-200">
                        <?php foreach ($categories as $category): ?>
                            <div class="p-6 hover:bg-gray-50 transition-colors group">
                                <a href="forum_topics.php?category_id=<?php echo $category['id']; ?>" class="block">
                                    <div class="flex justify-between items-center">
                                        <div class="flex-1 min-w-0">
                                            <h2 class="text-lg font-semibold text-gray-900 group-hover:text-kabarak-maroon">
                                                <?php echo htmlspecialchars($category['name']); ?>
                                            </h2>
                                            <p class="mt-2 text-sm text-gray-500 line-clamp-2">
                                                <?php echo htmlspecialchars($category['description']); ?>
                                            </p>
                                            <div class="mt-2 text-xs text-gray-500">
                                                <i class="ri-file-text-line mr-1"></i>
                                                <?php echo $category['topic_count']; ?> Topics
                                            </div>
                                        </div>
                                        <div class="ml-4 flex-shrink-0">
                                            <i class="ri-arrow-right-line text-gray-400 group-hover:text-kabarak-maroon"></i>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>