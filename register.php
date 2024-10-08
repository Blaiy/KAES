<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - KAES</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f4f4;
            height: 100vh;
        }
        .register-container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            margin: 30px auto;
        }
        h2 {
            color: maroon;
            text-align: center;
            margin-bottom: 20px;
        }
        .rounded-button {
            border-radius: 25px;
            width: 48%;
        }
        .btn-primary {
            background-color: maroon;
            border-color: maroon;
        }
        .btn-primary:hover {
            background-color: darkred;
            border-color: darkred;
        }
        .custom-link {
    color: maroon; /* Maroon color for the text */
    text-decoration: none; /* Remove underline */
    font-weight: bold; /* Make the text bold */
        }

        .custom-link:hover {
    color: #D2B48C; /* Light brown color when hovered */
    text-decoration: underline; /* Optional: Add underline on hover */
        }

    </style>

    <script>
        // Debugging helper function to log when events are triggered
        function logMessage(message) {
            console.log(message);
        }

        function showForm(userType) {
            logMessage(`Showing form: ${userType}`);
            document.getElementById('button-options').style.display = 'none';
            document.getElementById('student-form').style.display = 'none';
            document.getElementById('alumni-form').style.display = 'none';

            if (userType === 'student') {
                document.getElementById('student-form').style.display = 'block';
            } else if (userType === 'alumni') {
                document.getElementById('alumni-form').style.display = 'block';
            }
        }

        // Updated function to include logging and debugging
        function populateCourses(formType) {
            logMessage(`Populating courses for form: ${formType}`);

            const schoolElement = document.getElementById(`${formType}-school`);
            const courseElement = document.getElementById(`${formType}-course`);
            const selectedSchool = schoolElement.value;

            logMessage(`Selected School: ${selectedSchool}`);

            let courses = [];

            courseElement.innerHTML = '<option value="">-- Select Course --</option>';

            switch (selectedSchool) {
                case 'science':
                    courses = ['IT', 'Computer Science', 'Computer Forensics', 'Telecommunication'];
                    break;
                case 'pharmacy':
                    courses = ['Pharmacy'];
                    break;
                case 'law':
                    courses = ['Bachelor of Law (LLB)'];
                    break;
                case 'business':
                    courses = ['Bachelor of Commerce', 'Business IT', 'Economics'];
                    break;
                case 'education':
                    courses = ['Education (Arts)', 'Education (Science)', 'Early Childhood Development'];
                    break;
                case 'media':
                    courses = ['Media and Communication', 'Music', 'Performing Arts'];
                    break;
                case 'health':
                    courses = ['Nursing', 'Clinical Medicine', 'Public Health'];
                    break;
                default:
                    logMessage('No courses available for this school');
            }

            courses.forEach(course => {
                const option = document.createElement('option');
                option.value = course;
                option.textContent = course;
                courseElement.appendChild(option);
            });

            logMessage(`Courses populated for school: ${selectedSchool}`);
        }

        function populateYearOfStudy() {
            const yearOfStudyElement = document.getElementById('year_of_study');
            const maxYears = 5;
            const semesters = ['S1', 'S2', 'S3'];

            yearOfStudyElement.innerHTML = '<option value="">-- Select Year of Study --</option>';
            for (let year = 1; year <= maxYears; year++) {
                semesters.forEach(semester => {
                    const option = document.createElement('option');
                    option.value = `Y${year} ${semester}`;
                    option.textContent = `Y${year} ${semester}`;
                    yearOfStudyElement.appendChild(option);
                });
            }
        }

        function populateGraduationYear() {
            const graduationYearElement = document.getElementById('year_of_graduation');
            const currentYear = new Date().getFullYear();
            const startYear = 2002;

            graduationYearElement.innerHTML = '<option value="">-- Select Year of Graduation --</option>';
            for (let year = startYear; year <= currentYear; year++) {
                const option = document.createElement('option');
                option.value = year;
                option.textContent = year;
                graduationYearElement.appendChild(option);
            }
        }

        window.onload = function() {
            logMessage('Page loaded');
            populateYearOfStudy();
            populateGraduationYear();
        };
    </script>
</head>
<body>

<div class="register-container">
    <h2>KAES Registration</h2>

    <div id="button-options" class="d-flex justify-content-between">
        <button class="btn btn-maroon rounded-button" onclick="showForm('student')">Student</button>
        <button class="btn btn-maroon rounded-button" onclick="showForm('alumni')">Alumni</button>
    </div>

    <!-- Student Registration Form -->
    <div id="student-form" class="hidden" style="display: none;">
        <form action="student_registration.php" method="POST" class="mt-4">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" id="phone" name="phone" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="year_of_birth" class="form-label">Year of Birth</label>
                <select id="year_of_birth" name="year_of_birth" class="form-select" required>
                    <option value="">-- Select Year of Birth --</option>
                    <?php
                    $currentYear = date("Y");
                    for ($year = 1970; $year <= $currentYear; $year++) {
                        echo "<option value='$year'>$year</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="student-school" class="form-label">School</label>
                <select id="student-school" name="school" class="form-select" required onchange="populateCourses('student')">
                    <option value="">-- Select School --</option>
                    <option value="science">School of Science, Engineering & Technology</option>
                    <option value="pharmacy">School of Pharmacy</option>
                    <option value="law">School of Law</option>
                    <option value="business">School of Business</option>
                    <option value="education">School of Education</option>
                    <option value="media">School of Media & Music</option>
                    <option value="health">School of Health & Sciences</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="student-course" class="form-label">Course</label>
                <select id="student-course" name="course" class="form-select" required>
                    <!-- Courses will be dynamically populated -->
                </select>
            </div>
            <div class="mb-3">
                <label for="reg_number" class="form-label">Registration Number</label>
                <input type="text" id="reg_number" name="reg_number" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="year_of_study" class="form-label">Year of Study</label>
                <select id="year_of_study" name="year_of_study" class="form-select" required>
                    <!-- Options for Y1 S1, Y1 S3, up to Y5 S3 will be dynamically populated -->
                </select>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>
    </div>

    <!-- Alumni Registration Form -->
    <div id="alumni-form" class="hidden" style="display: none;">
        <form action="alumni_registration.php" method="POST" class="mt-4">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" id="phone" name="phone" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="year_of_birth" class="form-label">Year of Birth</label>
                <select id="year_of_birth" name="year_of_birth" class="form-select" required>
                    <option value="">-- Select Year of Birth --</option>
                    <?php
                    for ($year = 1970; $year <= $currentYear; $year++) {
                        echo "<option value='$year'>$year</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="alumni-school" class="form-label">School</label>
                <select id="alumni-school" name="school" class="form-select" required onchange="populateCourses('alumni')">
                    <option value="">-- Select School --</option>
                    <option value="science">School of Science, Engineering & Technology</option>
                    <option value="pharmacy">School of Pharmacy</option>
                    <option value="law">School of Law</option>
                    <option value="business">School of Business</option>
                    <option value="education">School of Education</option>
                    <option value="media">School of Media & Music</option>
                    <option value="health">School of Health & Sciences</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="alumni-course" class="form-label">Course</label>
                <select id="alumni-course" name="course" class="form-select" required>
                    <!-- Courses will be dynamically populated -->
                </select>
            </div>
            <div class="mb-3">
                <label for="year_of_graduation" class="form-label">Year of Graduation</label>
                <select id="year_of_graduation" name="year_of_graduation" class="form-select" required>
                    <!-- Options for Year of Graduation will be dynamically populated -->
                </select>
            </div>
            <div class="mb-3">
                <label for="employment_status" class="form-label">Employment Status</label>
                <select id="employment_status" name="employment_status" class="form-select" required>
                    <option value="">-- Select Employment Status --</option>
                    <option value="employed">Employed</option>
                    <option value="unemployed">Unemployed</option>
                    <option value="self_employed">Self-employed</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="location" class="form-label">Location</label>
                <input type="text" id="location" name="location" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>
    </div>
        <div class="text-center mt-4">
            <a href="login.php" class="btn btn-link custom-link">Back to Login</a>
        </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
