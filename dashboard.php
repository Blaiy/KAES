<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    // Redirect to login if not logged in
    header('Location: login.php');
    exit;
}

// Further check user type if needed
if ($_SESSION['user_type'] === 'student') {
    // Show student dashboard
} elseif ($_SESSION['user_type'] === 'alumni') {
    // Show alumni dashboard
}
?>
