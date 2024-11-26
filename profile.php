<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
require_once 'db.php';

// Check if user_id is provided
if (!isset($_GET['user_id'])) {
    die("No user specified");
}

$profile_user_id = intval($_GET['user_id']);

// Attempt to fetch alumni profile first
$alumni_query = "SELECT a.*, s.name as school_name, c.name as course_name 
                 FROM alumni a
                 JOIN schools s ON a.school_id = s.id
                 JOIN courses c ON a.course_id = c.id
                 WHERE a.user_id = :user_id";
$alumni_stmt = $pdo->prepare($alumni_query);
$alumni_stmt->execute([':user_id' => $profile_user_id]);
$alumni_profile = $alumni_stmt->fetch(PDO::FETCH_ASSOC);

// If no alumni profile, try students
if (!$alumni_profile) {
    $student_query = "SELECT s.*, sc.name as school_name, c.name as course_name 
                      FROM students s
                      JOIN schools sc ON s.school_id = sc.id
                      JOIN courses c ON s.course_id = c.id
                      WHERE s.user_id = :user_id";
    $student_stmt = $pdo->prepare($student_query);
    $student_stmt->execute([':user_id' => $profile_user_id]);
    $student_profile = $student_stmt->fetch(PDO::FETCH_ASSOC);
}

// Determine which profile to use
$profile = $alumni_profile ? $alumni_profile : $student_profile;

// If no profile found
if (!$profile) {
    die("User not found");
}

// Determine profile type
$is_alumni = isset($alumni_profile);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - <?php echo htmlspecialchars($profile['name']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <?php include 'partials/sidebar.php'; ?>

    <main class="lg:ml-64 pt-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="bg-white shadow-md rounded-lg p-8">
            <div class="flex items-center space-x-6 mb-6">
                <img src="<?php echo htmlspecialchars($profile['avatar'] ?? 'default_avatar.png'); ?>" 
                     alt="Profile Picture" class="w-24 h-24 rounded-full object-cover">
                <div>
                    <h1 class="text-3xl font-bold"><?php echo htmlspecialchars($profile['name']); ?></h1>
                    <p class="text-gray-600">
                        <?php if ($is_alumni): ?>
                            <?php echo htmlspecialchars($profile['job_title'] ?? 'Alumni'); ?> 
                            <?php if ($profile['company']): ?>
                                at <?php echo htmlspecialchars($profile['company']); ?>
                            <?php endif; ?>
                        <?php else: ?>
                            <?php echo htmlspecialchars($profile['year_of_study']); ?> Student
                        <?php endif; ?>
                    </p>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-xl font-semibold mb-4">Academic Information</h2>
                    <p><strong>School:</strong> <?php echo htmlspecialchars($profile['school_name']); ?></p>
                    <p><strong>Course:</strong> <?php echo htmlspecialchars($profile['course_name']); ?></p>
                    
                    <?php if ($is_alumni): ?>
                        <p><strong>Year of Graduation:</strong> <?php echo htmlspecialchars($profile['year_of_graduation']); ?></p>
                        <p><strong>Employment Status:</strong> <?php echo htmlspecialchars($profile['employment_status']); ?></p>
                    <?php else: ?>
                        <p><strong>Registration Number:</strong> <?php echo htmlspecialchars($profile['reg_number']); ?></p>
                    <?php endif; ?>
                </div>
                
                <div>
                    <h2 class="text-xl font-semibold mb-4">Contact Information</h2>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($profile['phone']); ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($profile['location'] ?? 'Not specified'); ?></p>
                    
                    <?php if ($is_alumni && $profile['linkedin_url']): ?>
                        <p>
                            <strong>LinkedIn:</strong> 
                            <a href="<?php echo htmlspecialchars($profile['linkedin_url']); ?>" 
                               target="_blank" class="text-blue-600 hover:underline">
                                View LinkedIn Profile
                            </a>
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($is_alumni && $profile['bio']): ?>
                <div class="mt-6">
                    <h2 class="text-xl font-semibold mb-4">Professional Bio</h2>
                    <p><?php echo htmlspecialchars($profile['bio']); ?></p>
                </div>
            <?php endif; ?>

            <div class="mt-6 flex space-x-4">
                <?php if ($_SESSION['user_id'] != $profile_user_id): ?>
                    <a href="?action=connect&user_id=<?php echo $profile_user_id; ?>" 
                       class="bg-kabarak-maroon text-white py-2 px-4 rounded-lg hover:bg-kabarak-maroon/90">
                        Connect
                    </a>
                <?php endif; ?>
                
                <?php if ($is_alumni && $profile['linkedin_url']): ?>
                    <a href="<?php echo htmlspecialchars($profile['linkedin_url']); ?>" 
                       target="_blank" 
                       class="border border-kabarak-maroon text-kabarak-maroon py-2 px-4 rounded-lg hover:bg-kabarak-maroon/10">
                        View LinkedIn
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>