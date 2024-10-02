<?php
// register.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - KAES</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .register-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
            overflow-y: auto;
            max-height: 100vh;
        }

        h2 {
            color: maroon;
            margin-bottom: 20px;
        }

        .button-container {
            display: flex;
            justify-content: space-between;
        }

        .student-form-btn, .alumni-form-btn {
            background-color: maroon;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 25px; /* Rounded corners */
            cursor: pointer;
            width: 48%; /* Make them side by side */
            margin: 10px 0;
            transition: background-color 0.3s ease;
        }

        .student-form-btn:hover, .alumni-form-btn:hover {
            background-color: darkred;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .hidden {
            display: none;
        }

        .back-link {
            color: maroon;
            text-decoration: none;
            display: block;
            margin-top: 15px;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        label {
            align-self: flex-start;
            font-weight: bold;
        }
    </style>

    <script>
        function showForm(userType) {
            document.getElementById('button-options').style.display = 'none';
            document.getElementById('student-form').style.display = 'none';
            document.getElementById('alumni-form').style.display = 'none';

            if (userType === 'student') {
                document.getElementById('student-form').style.display = 'block';
            } else if (userType === 'alumni') {
                document.getElementById('alumni-form').style.display = 'block';
            }
        }
    </script>
</head>
<body>

<div class="register-container">
    <h2>Register</h2>

    <div id="button-options" class="button-container">
        <button class="student-form-btn" onclick="showForm('student')">Student</button>
        <button class="alumni-form-btn" onclick="showForm('alumni')">Alumni</button>
    </div>

    <!-- Student Registration Form -->
    <div id="student-form" class="hidden">
        <form action="student_registration.php" method="POST">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" required>
            
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
            
            <label for="phone">Phone</label>
            <input type="text" id="phone" name="phone" required>
            
            <label for="year_of_birth">Year of Birth</label>
            <select id="year_of_birth" name="year_of_birth" required>
                <option value="">-- Select Year  --</option>
                <?php
                for ($year = 1974; $year <= date("Y"); $year++) {
                    echo "<option value='$year'>$year</option>";
                }
                ?>
            </select>

            <label for="school">School</label>
            <select id="school" name="school" required onchange="populateCourses('student')">
                <option value="">-- Select School --</option>
                <option value="science">School of Science, Engineering & Technology</option>
                <option value="pharmacy">School of Pharmacy</option>
                <option value="law">School of Law</option>
                <option value="business">School of Business</option>
                <option value="education">School of Education</option>
                <option value="media">School of Media & Music</option>
                <option value="health">School of Health & Sciences</option>
            </select>

            <label for="course">Course</label>
            <select id="student-course" name="course" required>
                <option value="">-- Select Course --</option>
                <!-- Courses will be dynamically populated based on selected school -->
            </select>

            <label for="reg_number">Registration Number</label>
            <input type="text" id="reg_number" name="reg_number" required>
            
            <label for="year_of_study">Year of Study</label>
            <select id="year_of_study" name="year_of_study" required>
                <option value="">-- Select Year and Semester --</option>
                <option value="Y1 S1">Y1 S1</option>
                <option value="Y1 S2">Y1 S2</option>
                <option value="Y1 S3">Y1 S3</option>
                <option value="Y2 S1">Y2 S1</option>
                <option value="Y2 S2">Y2 S2</option>
                <option value="Y2 S3">Y2 S3</option>
                <option value="Y3 S1">Y3 S1</option>
                <option value="Y3 S2">Y3 S2</option>
                <option value="Y3 S3">Y3 S3</option>
                <option value="Y4 S1">Y4 S1</option>
                <option value="Y4 S2">Y4 S2</option>
                <option value="Y4 S3">Y4 S3</option>
                <option value="Y5 S1">Y5 S1</option>
                <option value="Y5 S2">Y5 S2</option>
                <option value="Y5 S3">Y5 S3</option>
            </select>
            
            <input type="submit" value="Register">
        </form>
    </div>

    <!-- Alumni Registration Form -->
    <div id="alumni-form" class="hidden">
        <form action="alumni_registration.php" method="POST">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" required>
            
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
            
            <label for="phone">Phone</label>
            <input type="text" id="phone" name="phone" required>
            
            <label for="year_of_birth">Year of Birth</label>
            <select id="year_of_birth" name="year_of_birth" required>
                <option value="">-- Select Year  --</option>
                <?php
                for ($year = 1974; $year <= date("Y"); $year++) {
                    echo "<option value='$year'>$year</option>";
                }
                ?>
            </select>

            <label for="school">School</label>
            <select id="school" name="school" required onchange="populateCourses('alumni')">
                <option value="">-- Select School --</option>
                <option value="science">School of Science, Engineering & Technology</option>
                <option value="pharmacy">School of Pharmacy</option>
                <option value="law">School of Law</option>
                <option value="business">School of Business</option>
                <option value="education">School of Education</option>
                <option value="media">School of Media & Music</option>
                <option value="health">School of Health & Sciences</option>
            </select>

            <label for="course">Course</label>
            <select id="alumni-course" name="course" required>
                <option value="">-- Select Course --</option>
                <!-- Courses will be dynamically populated based on selected school -->
            </select>

            <label for="year_of_graduation">Year of Graduation</label>
            <select id="year_of_graduation" name="year_of_graduation" required>
                <option value="">-- Select Graduation Year --</option>
                <?php
                for ($year = 2002; $year <= date("Y"); $year++) {
                    echo "<option value='$year'>$year</option>";
                }
                ?>
            </select>

            <label for="employment_status">Employment Status</label>
            <select id="employment_status" name="employment_status" required>
                <option value="employed">Employed</option>
                <option value="self_employed">Self-employed</option>
                <option value="unemployed">Unemployed</option>
            </select>

            <label for="location">Location</label>
            <input type="text" id="location" name="location" required>
            
            <input type="submit" value="Register">
        </form>
    </div>

    <a href="login.php" class="back-link">Back to Login</a>
</div>

<script>
    function populateCourses(formType) {
        const schoolElement = document.getElementById(`${formType}-course`);
        const selectedSchool = document.getElementById('school').value;
        let courses =
