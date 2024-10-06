<?php
// alumni_registration.php
include 'db.php'; // Include your database connection file

// Retrieve form data
$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$year_of_birth = $_POST['year_of_birth'];
$year_of_graduation = $_POST['year_of_graduation'];
$employment_status = $_POST['employment_status'];
$location = $_POST['location'];
$school = $_POST['school'];
$course = $_POST['course'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

// Check if password and confirm password match
if ($password !== $confirm_password) {
    echo "Error: Passwords do not match.";
    exit();
}

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Check if email already exists in the database
$checkEmailQuery = "SELECT * FROM alumni WHERE email = '$email'";
$result = mysqli_query($conn, $checkEmailQuery);

if (mysqli_num_rows($result) > 0) {
    // Email already exists
    echo "Error: An alumni member with this email already exists!";
} else {
    // Email does not exist, proceed with the registration
    $sql = "INSERT INTO alumni (name, email, phone, year_of_birth, year_of_graduation, employment_status, location, school, course, password) 
            VALUES ('$name', '$email', '$phone', '$year_of_birth', '$year_of_graduation', '$employment_status', '$location', '$school', '$course', '$hashed_password')";

    // Execute the query
    if (mysqli_query($conn, $sql)) {
        echo "Registration successful!";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

// Close the connection
$conn->close();
?>
