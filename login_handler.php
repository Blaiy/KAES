<?php
session_start();
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        die("Email and password are required");
    }

    try {
        // First get the user from users table
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // User found and password matches
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = $user['user_type'];

            // Get additional user details based on user type
            if ($user['user_type'] === 'student') {
                $stmt = $pdo->prepare("SELECT * FROM students WHERE user_id = ?");
            } else {
                $stmt = $pdo->prepare("SELECT * FROM alumni WHERE user_id = ?");
            }

            $stmt->execute([$user['id']]);
            $userDetails = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($userDetails) {
                $_SESSION['username'] = $userDetails['name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['avatar'] = $userDetails['avatar'] ?? null;

                // Redirect to home page
                header("Location: home.php");
                exit();
            }
        } else {
            // Invalid credentials
            $_SESSION['login_error'] = "Invalid email or password";
            header("Location: login.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['login_error'] = "An error occurred. Please try again.";
        header("Location: login.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>
