<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
$user_avatar = isset($_SESSION['avatar']) ? $_SESSION['avatar'] : 'default_avatar.png';
$user_type = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : 'student'; // 'student' or 'alumni'
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KAES Connect</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Sidebar (Same as home.php) -->
    
    <!-- Main Content -->
    <main class="lg:ml-64">
        <div class="pt-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <!-- Connection Options -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Mentorship Section -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center space-x-3 mb-4">
                        <i class="ri-user-star-line text-2xl text-kabarak-maroon"></i>
                        <h2 class="text-xl font-semibold">Mentorship</h2>
                    </div>
                    <p class="text-gray-600 mb-4">Connect with experienced alumni for career guidance and mentorship.</p>
                    <button class="w-full bg-kabarak-maroon text-white py-2 px-4 rounded-lg hover:bg-kabarak-maroon/90">
                        Find a Mentor
                    </button>
                </div>

                <!-- Networking Section -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center space-x-3 mb-4">
                        <i class="ri-group-line text-2xl text-kabarak-maroon"></i>
                        <h2 class="text-xl font-semibold">Network</h2>
                    </div>
                    <p class="text-gray-600 mb-4">Expand your professional network with fellow alumni and students.</p>
                    <button class="w-full bg-kabarak-maroon text-white py-2 px-4 rounded-lg hover:bg-kabarak-maroon/90">
                        Browse Network
                    </button>
                </div>

                <!-- Industry Insights -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center space-x-3 mb-4">
                        <i class="ri-lightbulb-line text-2xl text-kabarak-maroon"></i>
                        <h2 class="text-xl font-semibold">Industry Insights</h2>
                    </div>
                    <p class="text-gray-600 mb-4">Get valuable insights from professionals in your field of interest.</p>
                    <button class="w-full bg-kabarak-maroon text-white py-2 px-4 rounded-lg hover:bg-kabarak-maroon/90">
                        View Insights
                    </button>
                </div>
            </div>

            <!-- Directory Section -->
            <div class="mt-8">
                <h2 class="text-2xl font-bold mb-6">Alumni Directory</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Directory Filters -->
                    <div class="lg:col-span-3 bg-white rounded-xl shadow-sm p-4">
                        <div class="flex flex-wrap gap-4">
                            <select class="px-4 py-2 border rounded-lg">
                                <option>Field of Study</option>
                                <option>Computer Science</option>
                                <option>Engineering</option>
                                <option>Business</option>
                            </select>
                            <select class="px-4 py-2 border rounded-lg">
                                <option>Graduation Year</option>
                                <option>2023</option>
                                <option>2022</option>
                                <option>2021</option>
                            </select>
                            <select class="px-4 py-2 border rounded-lg">
                                <option>Industry</option>
                                <option>Technology</option>
                                <option>Finance</option>
                                <option>Healthcare</option>
                            </select>
                            <button class="bg-kabarak-maroon text-white px-6 py-2 rounded-lg hover:bg-kabarak-maroon/90">
                                Search
                            </button>
                        </div>
                    </div>

                    <!-- Alumni Cards -->
                    <?php
                    // Sample alumni data - Replace with database query
                    $alumni = [
                        [
                            'name' => 'John Doe',
                            'position' => 'Software Engineer',
                            'company' => 'Tech Corp',
                            'graduation_year' => '2020',
                            'avatar' => 'path_to_avatar.jpg'
                        ],
                        // Add more alumni entries
                    ];

                    foreach ($alumni as $person): ?>
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center space-x-4">
                            <img src="<?php echo $person['avatar']; ?>" alt="Profile" 
                                 class="w-16 h-16 rounded-full">
                            <div>
                                <h3 class="font-semibold"><?php echo $person['name']; ?></h3>
                                <p class="text-gray-600"><?php echo $person['position']; ?></p>
                                <p class="text-sm text-gray-500"><?php echo $person['company']; ?></p>
                                <p class="text-sm text-gray-500">Class of <?php echo $person['graduation_year']; ?></p>
                            </div>
                        </div>
                        <div class="mt-4 flex space-x-2">
                            <button class="flex-1 bg-kabarak-maroon text-white py-2 px-4 rounded-lg hover:bg-kabarak-maroon/90">
                                Connect
                            </button>
                            <button class="flex-1 border border-kabarak-maroon text-kabarak-maroon py-2 px-4 rounded-lg hover:bg-kabarak-maroon/10">
                                Message
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Mobile Menu Button -->
    <button class="lg:hidden fixed bottom-4 right-4 bg-kabarak-maroon text-white p-3 rounded-full shadow-lg" 
            onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')">
        <i class="ri-menu-line text-xl"></i>
    </button>
</body>
</html>
