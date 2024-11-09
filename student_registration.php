<?php
session_start();
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $reg_number = $_POST['reg_number'] ?? '';
    $school_id = $_POST['school'] ?? '';
    $course_id = $_POST['course'] ?? '';
    $year_of_study = $_POST['year_of_study'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate required fields
    if (empty($name) || empty($email) || empty($phone) || empty($reg_number) || 
        empty($school_id) || empty($course_id) || empty($year_of_study) || 
        empty($password) || empty($confirm_password)) {
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
            VALUES (:email, :password, 'student')
        ");

        $stmt->execute([
            ':email' => $email,
            ':password' => $hashed_password
        ]);

        $user_id = $pdo->lastInsertId();

        // Insert into students table
        $stmt = $pdo->prepare("
            INSERT INTO students (
                user_id, name, reg_number, phone, 
                school_id, course_id, year_of_study
            ) VALUES (
                :user_id, :name, :reg_number, :phone, 
                :school_id, :course_id, :year_of_study
            )
        ");

        $stmt->execute([
            ':user_id' => $user_id,
            ':name' => $name,
            ':reg_number' => $reg_number,
            ':phone' => $phone,
            ':school_id' => $school_id,
            ':course_id' => $course_id,
            ':year_of_study' => $year_of_study
        ]);

        // Commit transaction
        $pdo->commit();

        // Set session variables
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $name;
        $_SESSION['user_type'] = 'student';

        // Redirect to home page
        header("Location: home.php");
        exit();

    } catch (PDOException $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        
        if ($e->getCode() == '23000') { // Duplicate entry error
            die("Email or Registration number already exists");
        } else {
            die("Registration failed: " . $e->getMessage());
        }
    }
}
?>
