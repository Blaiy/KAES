<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type']; // "student" or "alumni"

// Fetch user details from database
$table = ($user_type == 'student') ? 'students' : 'alumni';
$query = "SELECT * FROM $table WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $location = $_POST['location'];
    $avatar = $_FILES['avatar'];

    // Handle avatar upload if provided
    if ($avatar && $avatar['tmp_name']) {
        $avatar_path = 'uploads/' . $avatar['name'];
        move_uploaded_file($avatar['tmp_name'], $avatar_path);
    } else {
        $avatar_path = $user['avatar']; // Retain current avatar if not changed
    }

    // Update user details
    $update_query = "UPDATE $table SET name = ?, email = ?, phone = ?, location = ?, avatar = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("sssssi", $name, $email, $phone, $location, $avatar_path, $user_id);

    if ($update_stmt->execute()) {
        echo "<p>Profile updated successfully.</p>";
        header("Refresh:1");
    } else {
        echo "<p>Error updating profile: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2>Edit Profile</h2>
    <form action="edit_profile.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>
        <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>" required>
        </div>
        <div class="form-group">
            <label>Location</label>
            <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($user['location']) ?>">
        </div>
        <div class="form-group">
            <label>Avatar</label><br>
            <img src="<?= $user['avatar'] ?>" width="100" alt="Avatar">
            <input type="file" name="avatar" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>
</body>
</html>
