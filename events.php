<?php
session_start();
include 'db.php'; // Include your database connection file

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST['action'];

    if ($action === "create") {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $date = $_POST['date'];

        $query = "INSERT INTO events (name, description, date) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $name, $description, $date);
        if ($stmt->execute()) {
            $message = "Event created successfully!";
        }
    } elseif ($action === "delete") {
        $id = $_POST['id'];

        $query = "DELETE FROM events WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $message = "Event deleted successfully!";
        }
    }
}

$query = "SELECT * FROM events";
$result = $conn->query($query);
$events = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Events</h1>
    <p><?php echo htmlspecialchars($message); ?></p>

    <h2>Create Event</h2>
    <form method="POST" action="events.php">
        <input type="hidden" name="action" value="create">
        <input type="text" name="name" placeholder="Event Name" required>
        <textarea name="description" placeholder="Event Description" required></textarea>
        <input type="date" name="date" required>
        <button type="submit">Create</button>
    </form>

    <h2>Existing Events</h2>
    <ul>
        <?php foreach ($events as $event): ?>
            <li>
                <?php echo htmlspecialchars($event['name'] . " - " . $event['description'] . " (" . $event['date'] . ")"); ?>
                <form method="POST" action="events.php" style="display:inline;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?php echo $event['id']; ?>">
                    <button type="submit">Delete</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
