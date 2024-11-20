<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

try {
    // Fetch user details
    $stmt = $pdo->prepare("
        SELECT 
            u.*,
            COALESCE(s.name, a.name) as name,
            COALESCE(s.phone, a.phone) as phone,
            COALESCE(s.location, a.location) as location,
            COALESCE(s.avatar, a.avatar) as avatar,
            s.reg_number,
            a.employment_status,
            a.year_of_graduation
        FROM users u
        LEFT JOIN students s ON u.id = s.user_id
        LEFT JOIN alumni a ON u.id = a.user_id
        WHERE u.id = ?
    ");
    
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $location = $_POST['location'] ?? '';

        try {
            $pdo->beginTransaction();

            // Update users table
            $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
            $stmt->execute([$email, $user_id]);

            // Update specific table based on user type
            if ($user_type === 'student') {
                $stmt = $pdo->prepare("
                    UPDATE students 
                    SET name = ?, phone = ?, location = ?
                    WHERE user_id = ?
                ");
            } else {
                $stmt = $pdo->prepare("
                    UPDATE alumni 
                    SET name = ?, phone = ?, location = ?
                    WHERE user_id = ?
                ");
            }
            
            $stmt->execute([$name, $phone, $location, $user_id]);

            // Handle avatar upload if provided
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'uploads/avatars/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $file_extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
                $file_name = uniqid() . '.' . $file_extension;
                $target_path = $upload_dir . $file_name;

                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target_path)) {
                    // Update avatar path in database
                    if ($user_type === 'student') {
                        $stmt = $pdo->prepare("UPDATE students SET avatar = ? WHERE user_id = ?");
                    } else {
                        $stmt = $pdo->prepare("UPDATE alumni SET avatar = ? WHERE user_id = ?");
                    }
                    $stmt->execute([$target_path, $user_id]);
                }
            }

            $pdo->commit();
            $_SESSION['success_message'] = "Profile updated successfully!";
            header("Location: edit_profile.php");
            exit();

        } catch (PDOException $e) {
            $pdo->rollBack();
            $_SESSION['error_message'] = "Error updating profile: " . $e->getMessage();
        }
    }

} catch (PDOException $e) {
    $_SESSION['error_message'] = "Error fetching user details: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - KAES</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <?php include 'partials/sidebar.php'; ?>

    <main class="lg:ml-64 min-h-screen p-4">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-2xl font-bold text-kabarak-maroon mb-6">Edit Profile</h2>

                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-6">
                        <?php 
                        echo $_SESSION['success_message'];
                        unset($_SESSION['success_message']);
                        ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6">
                        <?php 
                        echo $_SESSION['error_message'];
                        unset($_SESSION['error_message']);
                        ?>
                    </div>
                <?php endif; ?>

                <form action="edit_profile.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <!-- Profile Picture -->
                    <div class="flex items-center space-x-6">
                        <?php if (!empty($user['avatar']) && file_exists($user['avatar'])): ?>
                            <img src="<?php echo htmlspecialchars($user['avatar']); ?>" 
                                 class="w-24 h-24 rounded-full">
                        <?php else: ?>
                            <div class="w-24 h-24 rounded-full bg-kabarak-maroon text-white flex items-center justify-center text-2xl font-bold">
                                <?php echo getInitials($user['name']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Profile Picture</label>
                            <input type="file" name="avatar" accept="image/*" 
                                   class="block w-full text-sm text-gray-500
                                          file:mr-4 file:py-2 file:px-4
                                          file:rounded-full file:border-0
                                          file:text-sm file:font-semibold
                                          file:bg-kabarak-maroon file:text-white
                                          hover:file:bg-kabarak-maroon/90">
                        </div>
                    </div>

                    <!-- Personal Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required
                                   class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-kabarak-maroon focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required
                                   class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-kabarak-maroon focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required
                                   class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-kabarak-maroon focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                            <input type="text" name="location" value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>"
                                   class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-kabarak-maroon focus:border-transparent">
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <?php if ($user_type === 'student'): ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Registration Number</label>
                            <input type="text" value="<?php echo htmlspecialchars($user['reg_number']); ?>" disabled
                                   class="w-full px-4 py-2 rounded-lg border bg-gray-50">
                        </div>
                    <?php else: ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Graduation Year</label>
                                <input type="text" value="<?php echo htmlspecialchars($user['year_of_graduation']); ?>" disabled
                                       class="w-full px-4 py-2 rounded-lg border bg-gray-50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Employment Status</label>
                                <input type="text" value="<?php echo htmlspecialchars($user['employment_status']); ?>" disabled
                                       class="w-full px-4 py-2 rounded-lg border bg-gray-50">
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="flex justify-end space-x-4">
                        <a href="home.php" 
                           class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-kabarak-maroon text-white rounded-lg hover:bg-kabarak-maroon/90">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
