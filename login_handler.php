<?php
session_start(); // Start a session

include 'db.php'; // Include your database connection file

// Retrieve form data
$email = $_POST['email'];
$password = $_POST['password'];

// Query to check if the user exists in the alumni table
$sql_alumni = "SELECT * FROM alumni WHERE email = '$email'";
$result_alumni = mysqli_query($conn, $sql_alumni);

// Query to check if the user exists in the students table
$sql_student = "SELECT * FROM students WHERE email = '$email'";
$result_student = mysqli_query($conn, $sql_student);

// First, check if the user is an alumni
if (mysqli_num_rows($result_alumni) > 0) {
    // If the user exists in the alumni table, fetch the row
    $user = mysqli_fetch_assoc($result_alumni);

    // Verify the password
    if (password_verify($password, $user['password'])) {
        // Password matches, set session variables for alumni
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = 'alumni'; // Set the role

        // Redirect to the  home page
        header('Location:home.php');
        exit();
    } else {
        // Password does not match
        echo "Invalid password. Please try again.";
    }
} elseif (mysqli_num_rows($result_student) > 0) {
    // If the user exists in the student table, fetch the row
    $user = mysqli_fetch_assoc($result_student);

    // Verify the password
    if (password_verify($password, $user['password'])) {
        // Password matches, set session variables for student
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = 'student'; // Set the role

        // Redirect to the  home page
        header('Location:home.php');
        exit();
    } else {
        // Password does not match
        echo "Invalid password. Please try again.";
    }
} else {
    // User does not exist in either table
    echo "No user found with this email. Please register first.";
}

// Close the connection
$conn->close();
?>
