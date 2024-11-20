<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get categories for dropdown
$categories = $pdo->query("SELECT * FROM forum_categories ORDER BY name")->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_id = $_POST['category_id'];
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $tags = isset($_POST['tags']) ? explode(',', $_POST['tags']) : [];

    if (empty($title) || empty($content) || empty($category_id)) {
        $error = "All fields are required.";
    } else {
        try {
            $pdo->beginTransaction();

            // Insert topic
            $stmt = $pdo->prepare("
                INSERT INTO forum_topics (category_id, user_id, title, content)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$category_id, $_SESSION['user_id'], $title, $content]);
            $topic_id = $pdo->lastInsertId();

            // Insert tags
            if (!empty($tags)) {
                $tag_stmt = $pdo->prepare("
                    INSERT INTO forum_topic_tags (topic_id, tag_name)
                    VALUES (?, ?)
                ");
                foreach ($tags as $tag) {
                    $tag = trim($tag);
                    if (!empty($tag)) {
                        $tag_stmt->execute([$topic_id, $tag]);
                    }
                }
            }

            $pdo->commit();
            header("Location: forum_topic.php?id=" . $topic_id);
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "An error occurred while creating the topic.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Topic - KAES Forum</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>

<body class="bg-gray-50">
    <?php include 'partials/sidebar.php'; ?>

    <main class="lg:ml-64 min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-white rounded-xl shadow-sm">
                <div class="p-6">
                    <h1 class="text-2xl font-bold text-kabarak-maroon mb-6">Create New Topic</h1>

                    <?php if (isset($error)): ?>
                        <div class="bg-red-50 text-red-500 p-4 rounded-lg mb-6">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="space-y-6">
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Category
                            </label>
                            <select name="category_id" id="category_id" required
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-kabarak-maroon focus:ring-kabarak-maroon">
                                <option value="">Select a category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>">
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                                Title
                            </label>
                            <input type="text" name="title" id="title" required
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-kabarak-maroon focus:ring-kabarak-maroon"
                                   placeholder="Enter your topic title">
                        </div>

                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-700 mb-1">
                                Content
                            </label>
                            <textarea name="content" id="content" rows="10" required
                                      class="w-full rounded-lg border-gray-300 shadow-sm focus:border-kabarak-maroon focus:ring-kabarak-maroon"
                                      placeholder="Enter your topic content"></textarea>
                        </div>

                        <div>
                            <label for="tags" class="block text-sm font-medium text-gray-700 mb-1">
                                Tags (comma-separated)
                            </label>
                            <input type="text" name="tags" id="tags"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-kabarak-maroon focus:ring-kabarak-maroon"
                                   placeholder="e.g., engineering, computer science, research">
                        </div>

                        <div class="flex justify-end space-x-4">
                            <a href="forum.php" 
                               class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-kabarak-maroon">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="px-4 py-2 bg-kabarak-maroon text-white rounded-lg hover:bg-kabarak-maroon-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-kabarak-maroon">
                                Create Topic
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Optional: Add client-side validation or enhanced functionality here
        document.addEventListener('DOMContentLoaded', function() {
            // You could add tag input enhancement, rich text editor, etc.
        });
    </script>
</body>
</html>