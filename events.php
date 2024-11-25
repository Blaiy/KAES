<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
$user_avatar = isset($_SESSION['avatar']) ? $_SESSION['avatar'] : null;

// Function to get events based on filters
function getEvents($pdo, $filter = 'upcoming') {
    $today = date('Y-m-d');
    
    $query = "SELECT * FROM events ";
    
    switch($filter) {
        case 'past':
            $query .= "WHERE event_date < :today ";
            break;
        case 'ongoing':
            $query .= "WHERE event_date = :today ";
            break;
        default: // upcoming
            $query .= "WHERE event_date > :today ";
            break;
    }
    
    $query .= "ORDER BY event_date ASC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute(['today' => $today]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'upcoming';
$events = getEvents($pdo, $filter);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KAES Events</title>
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
<body class="bg-gray-50">
    <?php include 'partials/sidebar.php'; ?>

    <main class="lg:ml-64 min-h-screen">
        <nav class="fixed top-0 right-0 left-64 bg-white border-b border-gray-200 z-30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold text-kabarak-maroon">Events</h1>
                    </div>

                    <div class="flex items-center">
                        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                        <a href="create_event.php" class="bg-kabarak-maroon text-white px-4 py-2 rounded-lg hover:bg-kabarak-maroon/90">
                            Create Event
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>

        <div class="pt-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <div class="mb-6">
                <div class="flex space-x-4">
                    <a href="?filter=upcoming" 
                       class="px-4 py-2 rounded-lg <?php echo $filter === 'upcoming' ? 'bg-kabarak-maroon text-white' : 'bg-white text-gray-600 hover:bg-gray-50'; ?>">
                        Upcoming Events
                    </a>
                    <a href="?filter=ongoing" 
                       class="px-4 py-2 rounded-lg <?php echo $filter === 'ongoing' ? 'bg-kabarak-maroon text-white' : 'bg-white text-gray-600 hover:bg-gray-50'; ?>">
                        Ongoing Events
                    </a>
                    <a href="?filter=past" 
                       class="px-4 py-2 rounded-lg <?php echo $filter === 'past' ? 'bg-kabarak-maroon text-white' : 'bg-white text-gray-600 hover:bg-gray-50'; ?>">
                        Past Events
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($events as $event): ?>
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <?php if ($event['image_url']): ?>
                            <img src="<?php echo htmlspecialchars($event['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($event['name']); ?>"
                                 class="w-full h-48 object-cover">
                        <?php endif; ?>
                        
                        <div class="p-4">
                            <h3 class="font-semibold text-lg mb-2"><?php echo htmlspecialchars($event['name']); ?></h3>
                            
                            <div class="space-y-2 mb-4">
                                <div class="flex items-center text-gray-600">
                                    <i class="ri-calendar-line w-5"></i>
                                    <span><?php echo date('F j, Y', strtotime($event['event_date'])); ?></span>
                                </div>
                                <?php if ($event['location']): ?>
                                    <div class="flex items-center text-gray-600">
                                        <i class="ri-map-pin-line w-5"></i>
                                        <span><?php echo htmlspecialchars($event['location']); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($event['category']): ?>
                                    <div class="flex items-center text-gray-600">
                                        <i class="ri-tag-line w-5"></i>
                                        <span><?php echo htmlspecialchars($event['category']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <p class="text-gray-600 mb-4">
                                <?php echo nl2br(htmlspecialchars(substr($event['description'], 0, 150))); ?>...
                            </p>

                            <div class="mt-4">
                                <a href="event_details.php?id=<?php echo $event['id']; ?>" 
                                   class="block text-center py-2 border border-kabarak-maroon text-kabarak-maroon rounded-lg hover:bg-kabarak-maroon/10">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
</body>
</html>