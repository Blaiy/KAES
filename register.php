<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KAES Registration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'kabarak-maroon': '#800000',
                        'kabarak-gold': '#FFD700',
                    }
                }
            }
        }
    </script>
</head>

<body class="min-h-screen bg-[url('../images/kabarak-bg.jpg')] bg-cover bg-center bg-no-repeat">
    <div class="min-h-screen bg-gradient-to-br from-kabarak-maroon/95 via-kabarak-maroon/90 to-black/95 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-xl p-8">
                <!-- Logo -->
                <div class="text-center mb-8">
                    <img src="images/kabarak logo.png" alt="KAES Logo" class="mx-auto h-24">
                    <h2 class="mt-6 text-3xl font-bold text-kabarak-maroon">
                        Join KAES Community
                    </h2>
                    <p class="mt-2 text-gray-600">Connect with Kabarak Alumni and Students</p>
                </div>

                <!-- Registration Type Selection -->
                <div id="registration-type" class="mb-8">
                    <div class="grid grid-cols-2 gap-4">
                        <button onclick="showForm('student')"
                            class="flex flex-col items-center p-6 bg-white rounded-xl shadow-sm hover:shadow-md transition-all border-2 border-transparent hover:border-kabarak-maroon">
                            <i class="ri-user-follow-line text-3xl text-kabarak-maroon mb-2"></i>
                            <h3 class="font-semibold text-lg">Student</h3>
                            <p class="text-sm text-gray-500 text-center mt-2">
                                Current Kabarak University student
                            </p>
                        </button>

                        <button onclick="showForm('alumni')"
                            class="flex flex-col items-center p-6 bg-white rounded-xl shadow-sm hover:shadow-md transition-all border-2 border-transparent hover:border-kabarak-maroon">
                            <i class="ri-graduation-cap-line text-3xl text-kabarak-maroon mb-2"></i>
                            <h3 class="font-semibold text-lg">Alumni</h3>
                            <p class="text-sm text-gray-500 text-center mt-2">
                                Graduated from Kabarak University
                            </p>
                        </button>
                    </div>
                </div>

                <!-- Student Registration Form -->
                <div id="student-form" class="hidden bg-white rounded-xl shadow-sm p-8" style="display: none;">
                    <div class="flex items-center mb-6">
                        <button onclick="showRegistrationTypes()" class="text-kabarak-maroon hover:text-kabarak-maroon/80">
                            <i class="ri-arrow-left-line text-xl"></i>
                        </button>
                        <h3 class="text-xl font-semibold ml-4">Student Registration</h3>
                    </div>

                    <form action="student_registration.php" method="POST" class="space-y-6">
                        <!-- Personal Information -->
                        <div class="space-y-6 p-6 bg-gray-50 rounded-lg">
                            <h4 class="font-semibold text-lg text-kabarak-maroon">Personal Information</h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                    <input type="text" name="name" required
                                        class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-kabarak-maroon focus:border-transparent">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Registration Number</label>
                                    <input type="text" name="reg_number" required
                                        class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-kabarak-maroon focus:border-transparent">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input type="email" name="email" required
                                        class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-kabarak-maroon focus:border-transparent">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                    <input type="tel" name="phone" required
                                        class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-kabarak-maroon focus:border-transparent">
                                </div>
                            </div>
                        </div>

                        <!-- Academic Information -->
                        <div class="space-y-6 p-6 bg-gray-50 rounded-lg">
                            <h4 class="font-semibold text-lg text-kabarak-maroon">Academic Information</h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">School</label>
                                    <select name="school" required onchange="populateCourses('student')"
                                        class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-kabarak-maroon focus:border-transparent">
                                        <option value="">Select School</option>
                                        <option value="science">School of Science & Technology</option>
                                        <option value="business">School of Business</option>
                                        <!-- Add other schools -->
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Course</label>
                                    <select name="course" required id="student-course"
                                        class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-kabarak-maroon focus:border-transparent">
                                        <option value="">Select Course</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Year of Study</label>
                                    <select name="year_of_study" required
                                        class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-kabarak-maroon focus:border-transparent">
                                        <!-- Will be populated by JavaScript -->
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Account Security -->
                        <div class="space-y-6 p-6 bg-gray-50 rounded-lg">
                            <h4 class="font-semibold text-lg text-kabarak-maroon">Account Security</h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                    <input type="password" name="password" required
                                        class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-kabarak-maroon focus:border-transparent">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                                    <input type="password" name="confirm_password" required
                                        class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-kabarak-maroon focus:border-transparent">
                                </div>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full bg-kabarak-maroon text-white py-3 px-4 rounded-lg hover:bg-kabarak-maroon/90 transition-colors">
                            Create Account
                        </button>
                    </form>
                </div>

                <!-- Alumni Registration Form -->
                <div id="alumni-form" class="hidden bg-white rounded-xl shadow-sm p-8" style="display: none;">
                    <div class="flex items-center mb-6">
                        <button onclick="showRegistrationTypes()" class="text-kabarak-maroon hover:text-kabarak-maroon/80">
                            <i class="ri-arrow-left-line text-xl"></i>
                        </button>
                        <h3 class="text-xl font-semibold ml-4">Alumni Registration</h3>
                    </div>

                    <form action="alumni_registration.php" method="POST" class="space-y-6">
                        <!-- Personal Information -->
                        <div class="space-y-6 p-6 bg-gray-50 rounded-lg">
                            <h4 class="font-semibold text-lg text-kabarak-maroon">Personal Information</h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                    <input type="text" name="name" required
                                        class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-kabarak-maroon focus:border-transparent">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input type="email" name="email" required
                                        class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-kabarak-maroon focus:border-transparent">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                    <input type="tel" name="phone" required
                                        class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-kabarak-maroon focus:border-transparent">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Location</label>
                                    <input type="text" name="location" required
                                        class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-kabarak-maroon focus:border-transparent">
                                </div>
                            </div>
                        </div>

                        <!-- Academic Information -->
                        <div class="space-y-6 p-6 bg-gray-50 rounded-lg">
                            <h4 class="font-semibold text-lg text-kabarak-maroon">Academic Information</h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">School</label>
                                    <select name="school" id="alumni-school" required onchange="populateCourses('alumni')"
                                        class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-kabarak-maroon focus:border-transparent">
                                        <option value="">Select School</option>
                                        <?php
                                        require_once 'db.php';
                                        try {
                                            $stmt = $pdo->query("SELECT id, name FROM schools ORDER BY name");
                                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['name']) . "</option>";
                                            }
                                        } catch (PDOException $e) {
                                            echo "<option value=''>Error loading schools</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Course</label>
                                    <select name="course" required id="alumni-course"
                                        class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-kabarak-maroon focus:border-transparent">
                                        <option value="">Select Course</option>
                                        <?php
                                        if (isset($_GET['school_id'])) {
                                            try {
                                                $stmt = $pdo->prepare("SELECT id, name FROM courses WHERE school_id = ?");
                                                $stmt->execute([$_GET['school_id']]);
                                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                    echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['name']) . "</option>";
                                                }
                                            } catch (PDOException $e) {
                                                echo "<option value=''>Error loading courses</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Year of Graduation</label>
                                    <select name="year_of_graduation" required
                                        class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-kabarak-maroon focus:border-transparent">
                                        <!-- Will be populated by JavaScript -->
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Employment Status</label>
                                    <select name="employment_status" required
                                        class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-kabarak-maroon focus:border-transparent">
                                        <option value="">Select Status</option>
                                        <option value="employed">Employed</option>
                                        <option value="self_employed">Self Employed</option>
                                        <option value="unemployed">Unemployed</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Account Security -->
                        <div class="space-y-6 p-6 bg-gray-50 rounded-lg">
                            <h4 class="font-semibold text-lg text-kabarak-maroon">Account Security</h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                    <input type="password" name="password" required
                                        class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-kabarak-maroon focus:border-transparent">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                                    <input type="password" name="confirm_password" required
                                        class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-kabarak-maroon focus:border-transparent">
                                </div>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full bg-kabarak-maroon text-white py-3 px-4 rounded-lg hover:bg-kabarak-maroon/90 transition-colors">
                            Create Account
                        </button>
                    </form>
                </div>

                <!-- Login Link -->
                <div class="text-center mt-8">
                    <p class="text-gray-600">
                        Already have an account?
                        <a href="login.php" class="text-kabarak-maroon hover:text-kabarak-maroon/80 font-semibold">
                            Sign in
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function loadSchools() {
            try {
                const response = await fetch('get_schools.php');
                if (!response.ok) throw new Error('Failed to fetch schools');

                const schools = await response.json();
                const studentSchool = document.querySelector('select[name="school"]');
                const alumniSchool = document.querySelector('select[name="school"]');

                const defaultOption = '<option value="">Select School</option>';
                studentSchool.innerHTML = defaultOption;
                alumniSchool.innerHTML = defaultOption;

                schools.forEach(school => {
                    const option = `<option value="${school.id}">${school.name}</option>`;
                    studentSchool.insertAdjacentHTML('beforeend', option);
                    alumniSchool.insertAdjacentHTML('beforeend', option);
                });
            } catch (error) {
                console.error('Error loading schools:', error);
                alert('Failed to load schools. Please refresh the page.');
            }
        }

        async function populateCourses(formType) {
            const schoolSelect = document.querySelector(`#${formType}-form select[name="school"]`);
            const courseSelect = document.querySelector(`#${formType}-form select[name="course"]`);
            const schoolId = schoolSelect.value;

            try {
                courseSelect.innerHTML = '<option value="">Loading courses...</option>';
                courseSelect.disabled = true;

                const response = await fetch(`get_courses.php?school_id=${schoolId}`);
                if (!response.ok) throw new Error('Failed to fetch courses');

                const courses = await response.json();

                courseSelect.innerHTML = '<option value="">Select Course</option>';
                courses.forEach(course => {
                    courseSelect.insertAdjacentHTML('beforeend',
                        `<option value="${course.id}">${course.name}</option>`
                    );
                });
            } catch (error) {
                console.error('Error loading courses:', error);
                courseSelect.innerHTML = '<option value="">Failed to load courses</option>';
            } finally {
                courseSelect.disabled = false;
            }
        }

        // Update form visibility functions
        function showRegistrationTypes() {
            document.getElementById('registration-type').style.display = 'block';
            document.getElementById('student-form').style.display = 'none';
            document.getElementById('alumni-form').style.display = 'none';
        }

        function showForm(type) {
            document.getElementById('registration-type').style.display = 'none';
            document.getElementById('student-form').style.display = type === 'student' ? 'block' : 'none';
            document.getElementById('alumni-form').style.display = type === 'alumni' ? 'block' : 'none';
        }

        // Initialize the page
        window.onload = function() {
            loadSchools();

            // Populate year of study for students
            const yearOfStudySelect = document.querySelector('select[name="year_of_study"]');
            yearOfStudySelect.innerHTML = '<option value="">Select Year of Study</option>';
            for (let year = 1; year <= 5; year++) {
                ['S1', 'S2', 'S3'].forEach(semester => {
                    const option = document.createElement('option');
                    option.value = `Y${year}${semester}`;
                    option.textContent = `Year ${year} - ${semester}`;
                    yearOfStudySelect.appendChild(option);
                });
            }

            // Populate graduation years for alumni
            const graduationYearSelect = document.querySelector('select[name="year_of_graduation"]');
            graduationYearSelect.innerHTML = '<option value="">Select Graduation Year</option>';
            const currentYear = new Date().getFullYear();
            for (let year = currentYear; year >= 2002; year--) {
                const option = document.createElement('option');
                option.value = year;
                option.textContent = year;
                graduationYearSelect.appendChild(option);
            }

            // Add event listeners for school selection
            document.querySelectorAll('select[name="school"]').forEach(select => {
                select.addEventListener('change', (e) => {
                    const formType = e.target.closest('form').id.split('-')[0];
                    populateCourses(formType);
                });
            });
        };
    </script>
</body>

</html>