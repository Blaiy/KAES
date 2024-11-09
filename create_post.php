<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
$user_avatar = isset($_SESSION['avatar']) ? $_SESSION['avatar'] : 'default_avatar.png';

// Create uploads directory if it doesn't exist
$upload_dir = 'uploads/posts/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Fetch all users for tagging
$stmt = $pdo->query("
    SELECT u.id, COALESCE(s.name, a.name) as name 
    FROM users u 
    LEFT JOIN students s ON u.id = s.user_id 
    LEFT JOIN alumni a ON u.id = a.user_id 
    WHERE u.id != {$_SESSION['user_id']}
    ORDER BY name
");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['content'] ?? '';
    $location = $_POST['location'] ?? '';
    $tagged_users = $_POST['tagged_users'] ?? [];
    $user_id = $_SESSION['user_id'];
    $image_url = null;

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid() . '.' . $file_extension;
        $target_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $image_url = $target_path;
        }
    }

    try {
        $pdo->beginTransaction();

        // Insert post
        $stmt = $pdo->prepare("
            INSERT INTO posts (user_id, content, image_url, location) 
            VALUES (:user_id, :content, :image_url, :location)
        ");

        $stmt->execute([
            ':user_id' => $user_id,
            ':content' => $content,
            ':image_url' => $image_url,
            ':location' => $location
        ]);

        $post_id = $pdo->lastInsertId();

        // Insert tagged users
        if (!empty($tagged_users)) {
            $stmt = $pdo->prepare("
                INSERT INTO post_tags (post_id, tagged_user_id) 
                VALUES (:post_id, :user_id)
            ");

            foreach ($tagged_users as $tagged_user_id) {
                $stmt->execute([
                    ':post_id' => $post_id,
                    ':user_id' => $tagged_user_id
                ]);
            }
        }

        $pdo->commit();
        header('Location: home.php');
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Error creating post: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post - KAES</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

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
    <div class="max-w-2xl mx-auto mt-10 p-4">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-4">
                    <a href="home.php" class="text-kabarak-maroon hover:text-kabarak-maroon/80">
                        <i class="ri-arrow-left-line text-2xl"></i>
                    </a>
                    <h2 class="text-2xl font-bold text-kabarak-maroon">Create Post</h2>
                </div>
            </div>

            <!-- Post Form -->
            <form action="create_post.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                <!-- User Info -->
                <div class="flex items-center space-x-3">
                    <?php if ($user_avatar && file_exists($user_avatar)): ?>
                        <img src="<?php echo $user_avatar; ?>" alt="Profile" class="w-10 h-10 rounded-full">
                    <?php else: ?>
                        <div class="w-10 h-10 rounded-full bg-kabarak-maroon text-white flex items-center justify-center font-bold">
                            <?php 
                            $initials = explode(' ', $user_name);
                            echo strtoupper(substr($initials[0], 0, 1) . (isset($initials[1]) ? substr($initials[1], 0, 1) : ''));
                            ?>
                        </div>
                    <?php endif; ?>
                    <div>
                        <h3 class="font-semibold"><?php echo htmlspecialchars($user_name); ?></h3>
                    </div>
                </div>

                <!-- Content -->
                <div>
                    <textarea name="content" rows="4" 
                              class="w-full p-4 border rounded-lg focus:ring-2 focus:ring-kabarak-maroon focus:border-transparent"
                              placeholder="What's on your mind?"
                              required></textarea>
                </div>

                <!-- Location -->
                <div class="flex items-center space-x-2">
                    <i class="ri-map-pin-line text-gray-500"></i>
                    <input type="text" name="location" id="location-input"
                           class="flex-1 p-2 border rounded-lg focus:ring-2 focus:ring-kabarak-maroon focus:border-transparent"
                           placeholder="Start typing to search locations..."
                           autocomplete="off">
                    <div id="location-suggestions" class="absolute z-10 w-full bg-white border rounded-lg mt-1 shadow-lg hidden">
                    </div>
                </div>
                <script>
                    const locationInput = document.getElementById('location-input');
                    const suggestionBox = document.getElementById('location-suggestions');
                    let debounceTimer;

                    locationInput.addEventListener('input', function() {
                        clearTimeout(debounceTimer);
                        debounceTimer = setTimeout(() => {
                            const query = this.value;
                            if (query.length >= 3) {
                                fetch(`https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURIComponent(query)}.json?access_token=YOUR_MAPBOX_TOKEN&types=place,locality,neighborhood`)
                                    .then(response => response.json())
                                    .then(data => {
                                        suggestionBox.innerHTML = '';
                                        data.features.forEach(place => {
                                            const div = document.createElement('div');
                                            div.className = 'p-2 hover:bg-gray-100 cursor-pointer';
                                            div.textContent = place.place_name;
                                            div.onclick = () => {
                                                locationInput.value = place.place_name;
                                                suggestionBox.classList.add('hidden');
                                            };
                                            suggestionBox.appendChild(div);
                                        });
                                        suggestionBox.classList.remove('hidden');
                                    });
                            } else {
                                suggestionBox.classList.add('hidden');
                            }
                        }, 300);
                    });

                    document.addEventListener('click', function(e) {
                        if (!locationInput.contains(e.target) && !suggestionBox.contains(e.target)) {
                            suggestionBox.classList.add('hidden');
                        }
                    });
                </script>

                <!-- Tag People -->
                <div class="flex items-center space-x-2">
                    <i class="ri-user-add-line text-gray-500"></i>
                    <select name="tagged_users[]" multiple id="tag-users" 
                            class="flex-1 p-2 border rounded-lg">
                        <?php foreach ($users as $user): ?>
                            <option value="<?php echo $user['id']; ?>">
                                <?php echo htmlspecialchars($user['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Image Upload -->
                <div class="space-y-2">
                    <div class="flex items-center space-x-2">
                        <i class="ri-image-line text-gray-500"></i>
                        <label class="block text-sm font-medium text-gray-700">Add Photos</label>
                    </div>
                    <div class="relative">
                        <input type="file" name="image" accept="image/*" 
                               class="w-full p-2 border rounded-lg"
                               onchange="previewImage(this)">
                        <div id="image-preview" class="mt-2 hidden">
                            <img src="" alt="Preview" class="max-h-48 rounded-lg">
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-4">
                    <a href="home.php" 
                       class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-kabarak-maroon text-white rounded-lg hover:bg-kabarak-maroon/90">
                        Post
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Initialize Tom Select for user tagging
        new TomSelect('#tag-users', {
            plugins: ['remove_button'],
            placeholder: 'Tag people in your post'
        });

        // Image preview
        function previewImage(input) {
            const preview = document.getElementById('image-preview');
            const previewImg = preview.querySelector('img');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
