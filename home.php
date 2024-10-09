<?php
// Start session at the very beginning of the file
session_start();

// Check if user is authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Store user details from session
$user_name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
$user_avatar = isset($_SESSION['avatar']) ? $_SESSION['avatar'] : 'default_avatar.png'; // Set a default avatar if not set
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KAES Home</title>

    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            background-color: #b23b3b;
            padding-top: 20px;
            color: white;
        }
        .sidebar a {
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            display: block;
        }
        .sidebar a:hover {
            background-color: #941f1f;
        }
        .content {
            margin-left: 260px;
            padding: 20px;
        }
        .navbar {
            background-color: #f4a261;
            position: fixed;
            top: 0;
            right: 0;
            left: 260px;
            z-index: 1;
        }
        .navbar .nav-link {
            color: black;
        }
        .post {
            background-color: white;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 10px;
        }
        .job-ad {
            background-color: white;
            padding: 15px;
            margin-top: 20px;
            border-radius: 10px;
        }
        .profile-section {
            display: flex;
            align-items: center;
            padding: 10px 15px;
        }
        .profile-section img {
            border-radius: 50%;
            width: 50px;
            height: 50px;
        }
        .profile-section .username {
            margin-left: 10px;
            color: white;
            font-size: 18px;
        }
        .profile-section .edit-profile {
            margin-left: auto;
            color: white;
            font-size: 14px;
            text-decoration: underline;
        }
        .nav-container {
            background-color: #f4a261;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="text-center mb-3">
            <img src="path_to_logo.png" alt="KAES Logo" class="img-fluid" style="width: 100px;">
        </div>

        <!-- Profile Section -->
        <div class="profile-section">
            <img src="<?php echo $user_avatar; ?>" alt="User Avatar">
            <span class="username"><?php echo $user_name; ?></span>
            <a href="edit_profile.php" class="edit-profile">Edit</a>
        </div>

        <!-- Sidebar Links -->
        <a href="home.php"><i class="bi bi-house"></i> Home</a>
        <a href="search.php"><i class="bi bi-search"></i> Search</a>
        <a href="connect.php"><i class="bi bi-people"></i> Connect</a>
        <a href="messages.php"><i class="bi bi-envelope"></i> Messages</a>
        <a href="system.php"><i class="bi bi-gear"></i> System</a>
        <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light nav-container">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">KAES</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-bell"></i> Notifications</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="forum.php"><i class="bi bi-card-text"></i> Forum</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="create_post.php"><i class="bi bi-plus-circle"></i> Post</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-three-dots"></i> More</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="content">
        <!-- Dynamic Posts Section -->
        <div class="post">
            <div class="d-flex align-items-center mb-2">
                <img src="path_to_user_profile_image.jpg" alt="User Image" class="rounded-circle" width="50" height="50">
                <div class="ms-3">
                    <strong><?php echo $user_name; ?></strong>
                    <small class="text-muted">10 minutes ago</small>
                </div>
            </div>
            <div class="post-content">
                <img src="path_to_post_image.jpg" class="img-fluid" alt="Post Image">
                <p>Sample post caption here...</p>
            </div>
            <div class="post-actions d-flex justify-content-between">
                <a href="#"><i class="bi bi-heart"></i> Like</a>
                <a href="#"><i class="bi bi-chat"></i> Comment</a>
                <a href="#"><i class="bi bi-arrow-repeat"></i> Share</a>
            </div>
        </div>

        <!-- Job Advertisement Section -->
        <div class="job-ad">
            <h5>Job Ad</h5>
            <p>Job description with image, if any...</p>
            <a href="#" class="btn btn-success">More...</a>
            <div class="mt-3">
                <ul>
                    <li><a href="#">Job Listing 1</a></li>
                    <li><a href="#">Job Listing 2</a></li>
                    <li><a href="#">Job Listing 3</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>
</html>
