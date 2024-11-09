<?php
session_start();
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $location = $_POST['location'] ?? '';
    $school_id = $_POST['school'] ?? '';
    $course_id = $_POST['course'] ?? '';
    $year_of_graduation = $_POST['year_of_graduation'] ?? '';
    $employment_status = $_POST['employment_status'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate required fields
    if (empty($name) || empty($email) || empty($phone) || empty($location) || 
        empty($school_id) || empty($course_id) || empty($year_of_graduation) || 
        empty($employment_status) || empty($password) || empty($confirm_password)) {
        die("All fields are required");
    }

    // Validate password match
    if ($password !== $confirm_password) {
        die("Passwords do not match");
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Begin transaction
        $pdo->beginTransaction();

        // Insert into users table first
        $stmt = $pdo->prepare("
            INSERT INTO users (email, password, user_type) 
            VALUES (:email, :password, 'alumni')
        ");

        $stmt->execute([
            ':email' => $email,
            ':password' => $hashed_password
        ]);

        $user_id = $pdo->lastInsertId();

        // Insert into alumni table
        $stmt = $pdo->prepare("
            INSERT INTO alumni (
                user_id, name, phone, location,
                school_id, course_id, year_of_graduation,
                employment_status
            ) VALUES (
                :user_id, :name, :phone, :location,
                :school_id, :course_id, :year_of_graduation,
                :employment_status
            )
        ");

        $stmt->execute([
            ':user_id' => $user_id,
            ':name' => $name,
            ':phone' => $phone,
            ':location' => $location,
            ':school_id' => $school_id,
            ':course_id' => $course_id,
            ':year_of_graduation' => $year_of_graduation,
            ':employment_status' => $employment_status
        ]);

        // Commit transaction
        $pdo->commit();

        // Set session variables
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $name;
        $_SESSION['user_type'] = 'alumni';

        // Redirect to home page
        header("Location: home.php");
        exit();

    } catch (PDOException $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        
        if ($e->getCode() == '23000') { // Duplicate entry error
            die("Email already exists");
        } else {
            die("Registration failed: " . $e->getMessage());
        }
    }
}
?>
