<?php
session_start();
include 'db.php'; // Include your database connection file

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$searchResults = [];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $searchTerm = trim($_POST['search_term']);
    $category = trim($_POST['category']);

    if (!empty($searchTerm)) {
        $query = "SELECT * FROM $category WHERE name LIKE ? OR description LIKE ?";
        $stmt = $conn->prepare($query);
        $searchTermWildcard = "%" . $searchTerm . "%";
        $stmt->bind_param("ss", $searchTermWildcard, $searchTermWildcard);
        $stmt->execute();
        $result = $stmt->get_result();
        $searchResults = $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Search</h1>
    <form method="POST" action="search.php">
        <input type="text" name="search_term" placeholder="Enter search term" required>
        <select name="category">
            <option value="users">Users</option>
            <option value="events">Events</option>
        </select>
        <button type="submit">Search</button>
    </form>

    <?php if (!empty($searchResults)): ?>
        <h2>Results:</h2>
        <ul>
            <?php foreach ($searchResults as $result): ?>
                <li><?php echo htmlspecialchars($result['name'] . " - " . $result['description']); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php elseif ($_SERVER["REQUEST_METHOD"] === "POST"): ?>
        <p>No results found.</p>
    <?php endif; ?>
</body>
</html>
