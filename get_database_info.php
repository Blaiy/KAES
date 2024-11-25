<?php
// get_database_info.php
//session_start();
require_once 'db.php';
require_once 'functions.php';

// Security check for admin access
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

class DatabaseInfo {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Events Related Methods
     */
    public function getEventById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM events WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching event: " . $e->getMessage());
            return false;
        }
    }

    public function getAllEvents() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM events ORDER BY event_date DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching events: " . $e->getMessage());
            return false;
        }
    }

    /**
     * User Related Methods
     */
    public function getUserById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT id, email, full_name, created_at, is_admin FROM users WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching user: " . $e->getMessage());
            return false;
        }
    }

    public function getAllUsers() {
        try {
            $stmt = $this->pdo->query("SELECT id, email, full_name, created_at, is_admin FROM users ORDER BY created_at DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching users: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Category Related Methods
     */
    public function getAllCategories() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM categories ORDER BY name");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching categories: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Event Registrations Methods
     */
    public function getEventRegistrations($eventId = null) {
        try {
            if ($eventId) {
                $stmt = $this->pdo->prepare("
                    SELECT er.*, u.email, u.full_name, e.name as event_name 
                    FROM event_registrations er
                    JOIN users u ON er.user_id = u.id
                    JOIN events e ON er.event_id = e.id
                    WHERE er.event_id = ?
                    ORDER BY er.created_at DESC
                ");
                $stmt->execute([$eventId]);
            } else {
                $stmt = $this->pdo->query("
                    SELECT er.*, u.email, u.full_name, e.name as event_name 
                    FROM event_registrations er
                    JOIN users u ON er.user_id = u.id
                    JOIN events e ON er.event_id = e.id
                    ORDER BY er.created_at DESC
                ");
            }
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching registrations: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Statistics and Analytics
     */
    public function getDashboardStats() {
        try {
            $stats = [];
            
            // Total users
            $stmt = $this->pdo->query("SELECT COUNT(*) as total_users FROM users");
            $stats['total_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];
            
            // Total events
            $stmt = $this->pdo->query("SELECT COUNT(*) as total_events FROM events");
            $stats['total_events'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_events'];
            
            // Total registrations
            $stmt = $this->pdo->query("SELECT COUNT(*) as total_registrations FROM event_registrations");
            $stats['total_registrations'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_registrations'];
            
            // Events by category
            $stmt = $this->pdo->query("
                SELECT category, COUNT(*) as count 
                FROM events 
                GROUP BY category
            ");
            $stats['events_by_category'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Recent activities
            $stmt = $this->pdo->query("
                SELECT 'registration' as type, er.created_at, u.full_name, e.name as event_name
                FROM event_registrations er
                JOIN users u ON er.user_id = u.id
                JOIN events e ON er.event_id = e.id
                ORDER BY er.created_at DESC
                LIMIT 10
            ");
            $stats['recent_activities'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $stats;
        } catch (PDOException $e) {
            error_log("Error fetching dashboard stats: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Search Functionality
     */
    public function search($query, $type = null) {
        try {
            $results = [];
            $searchTerm = "%$query%";
            
            if (!$type || $type === 'events') {
                $stmt = $this->pdo->prepare("
                    SELECT 'event' as type, id, name as title, description, event_date 
                    FROM events 
                    WHERE name LIKE ? OR description LIKE ?
                ");
                $stmt->execute([$searchTerm, $searchTerm]);
                $results['events'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            
            if (!$type || $type === 'users') {
                $stmt = $this->pdo->prepare("
                    SELECT 'user' as type, id, email, full_name as title 
                    FROM users 
                    WHERE email LIKE ? OR full_name LIKE ?
                ");
                $stmt->execute([$searchTerm, $searchTerm]);
                $results['users'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            
            return $results;
        } catch (PDOException $e) {
            error_log("Error performing search: " . $e->getMessage());
            return false;
        }
    }
}

// Initialize the DatabaseInfo class
$dbInfo = new DatabaseInfo($pdo);

// Handle API requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $response = [];
    $type = $_GET['type'] ?? '';
    
    switch($type) {
        case 'event':
            if (isset($_GET['id'])) {
                $response = $dbInfo->getEventById($_GET['id']);
            } else {
                $response = $dbInfo->getAllEvents();
            }
            break;
            
        case 'user':
            if (isset($_GET['id'])) {
                $response = $dbInfo->getUserById($_GET['id']);
            } else {
                $response = $dbInfo->getAllUsers();
            }
            break;
            
        case 'categories':
            $response = $dbInfo->getAllCategories();
            break;
            
        case 'registrations':
            $eventId = $_GET['event_id'] ?? null;
            $response = $dbInfo->getEventRegistrations($eventId);
            break;
            
        case 'stats':
            $response = $dbInfo->getDashboardStats();
            break;
            
        case 'search':
            $query = $_GET['q'] ?? '';
            $searchType = $_GET['search_type'] ?? null;
            $response = $dbInfo->search($query, $searchType);
            break;
            
        default:
            $response = ['error' => 'Invalid type specified'];
    }
    
    if ($response === false) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error occurred']);
    } else {
        echo json_encode($response);
    }
}
?>