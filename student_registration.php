<?php
// student_registration.php
include 'db.php'; // Make sure this path is correct

// Retrieve form data
$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$year_of_birth = $_POST['year_of_birth'];
$school = $_POST['school'];
$course = $_POST['course'];
$reg_number = $_POST['reg_number'];
$year_of_study = $_POST['year_of_study'];
$hashed_password = password_hash($password, PASSWORD_DEFAULT);


// SQL query to insert data
$sql = "INSERT INTO students (name, email, phone, year_of_birth, school, course, reg_number, year_of_study, password) 
        VALUES ('$name', '$email', '$phone', '$year_of_birth', '$school', '$course', '$reg_number', '$year_of_study', '$hashed_password')";

// Execute the query
if (mysqli_query($conn, $sql)) {
    echo "Registration successful!";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

// Close the connection
$conn->close();
?>
