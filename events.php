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
    $today = date('Y-m-d H:i:s');
    
    $query = "SELECT 
                e.*,
                u.email,
                COALESCE(s.name, a.name) as organizer_name,
                COALESCE(s.avatar, a.avatar) as organizer_avatar,
                COUNT(DISTINCT er.user_id) as registered_count
              FROM events e
              JOIN users u ON e.organizer_id = u.id
              LEFT JOIN students s ON u.id = s.user_id
              LEFT JOIN alumni a ON u.id = a.user_id
              LEFT JOIN event_registrations er ON e.id = er.event_id
              ";
    
    switch($filter) {
        case 'past':
            $query .= "WHERE e.end_datetime < :today ";
            break;
        case 'ongoing':
            $query .= "WHERE e.start_datetime <= :today AND e.end_datetime >= :today ";
            break;
        default: // upcoming
            $query .= "WHERE e.start_datetime > :today ";
            break;
    }
    
    $query .= "GROUP BY e.id ORDER BY e.start_datetime ASC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute(['today' => $today]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Check if user is registered for an event
function isUserRegistered($pdo, $event_id, $user_id) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM event_registrations WHERE event_id = ? AND user_id = ?");
    $stmt->execute([$event_id, $user_id]);
    return $stmt->fetchColumn() > 0;
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

    <!-- Main Content -->
    <main class="lg:ml-64 min-h-screen">
        <!-- Top Navigation -->
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

        <!-- Content Area -->
        <div class="pt-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <!-- Filters -->
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

            <!-- Events Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($events as $event): ?>
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <?php if ($event['image_url']): ?>
                            <img src="<?php echo htmlspecialchars($event['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($event['title']); ?>"
                                 class="w-full h-48 object-cover">
                        <?php endif; ?>
                        
                        <div class="p-4">
                            <h3 class="font-semibold text-lg mb-2"><?php echo htmlspecialchars($event['title']); ?></h3>
                            
                            <div class="space-y-2 mb-4">
                                <div class="flex items-center text-gray-600">
                                    <i class="ri-calendar-line w-5"></i>
                                    <span><?php echo date('F j, Y', strtotime($event['start_datetime'])); ?></span>
                                </div>
                                <div class="flex items-center text-gray-600">
                                    <i class="ri-time-line w-5"></i>
                                    <span><?php echo date('g:i A', strtotime($event['start_datetime'])); ?></span>
                                </div>
                                <?php if ($event['location']): ?>
                                    <div class="flex items-center text-gray-600">
                                        <i class="ri-map-pin-line w-5"></i>
                                        <span><?php echo htmlspecialchars($event['location']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <p class="text-gray-600 mb-4">
                                <?php echo nl2br(htmlspecialchars(substr($event['description'], 0, 150))); ?>...
                            </p>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <?php if ($event['organizer_avatar']): ?>
                                        <img src="<?php echo htmlspecialchars($event['organizer_avatar']); ?>" 
                                             alt="Organizer" 
                                             class="w-8 h-8 rounded-full">
                                    <?php else: ?>
                                        <div class="w-8 h-8 rounded-full bg-kabarak-maroon text-white flex items-center justify-center font-bold">
                                            <?php 
                                            $initials = explode(' ', $event['organizer_name']);
                                            echo strtoupper(substr($initials[0], 0, 1) . (isset($initials[1]) ? substr($initials[1], 0, 1) : ''));
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                    <span class="text-sm text-gray-600">
                                        by <?php echo htmlspecialchars($event['organizer_name']); ?>
                                    </span>
                                </div>
                                
                                <span class="text-sm text-gray-600">
                                    <?php echo $event['registered_count']; ?> registered
                                </span>
                            </div>

                            <div class="mt-4 flex space-x-4">
                                <a href="event.php?id=<?php echo $event['id']; ?>" 
                                   class="flex-1 text-center py-2 border border-kabarak-maroon text-kabarak-maroon rounded-lg hover:bg-kabarak-maroon/10">
                                    View Details
                                </a>
                                <?php if ($filter !== 'past'): ?>
                                    <?php if (isUserRegistered($pdo, $event['id'], $_SESSION['user_id'])): ?>
                                        <button onclick="unregisterFromEvent(<?php echo $event['id']; ?>)"
                                                class="flex-1 bg-gray-100 text-gray-600 py-2 rounded-lg hover:bg-gray-200">
                                            Unregister
                                        </button>
                                    <?php else: ?>
                                        <button onclick="registerForEvent(<?php echo $event['id']; ?>)"
                                                class="flex-1 bg-kabarak-maroon text-white py-2 rounded-lg hover:bg-kabarak-maroon/90">
                                            Register
                                        </button>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <script>
        async function registerForEvent(eventId) {
            try {
                const response = await fetch('register_event.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ event_id: eventId })
                });

                const data = await response.json();
                
                if (data.success) {
                    // Reload page to update UI
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to register for event');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while registering for the event');
            }
        }

        async function unregisterFromEvent(eventId) {
            if (!confirm('Are you sure you want to unregister from this event?')) return;
            
            try {
                const response = await fetch('unregister_event.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ event_id: eventId })
                });

                const data = await response.json();
                
                if (data.success) {
                    // Reload page to update UI
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to unregister from event');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while unregistering from the event');
            }
        }
    </script>
</body>

</html>