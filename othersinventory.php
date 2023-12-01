<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get user information from the session
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Database connection parameters
$host = "localhost";
$dbUsername = "root";
$password = "";
$database = "poke";

// Create a MySQLi connection
$dbConn = new mysqli($host, $dbUsername, $password, $database);

// Check for connection errors
if ($dbConn->connect_error) {
    die("Failed to connect to the database: " . $dbConn->connect_error);
}

// Initialize variables for the search functionality
$searchUsername = '';
$foundInventory = [];

// Check if the search form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    // Get the username entered in the search form
    $searchUsername = trim($_POST['search_username']);

    // TODO: Implement the logic to fetch inventory for the specified user from the database
    // For now, let's assume you have a function to fetch inventory by username
    // Replace 'getInventoryByUsername' with your actual function
    $foundInventory = getInventoryByUsername($searchUsername);
}

// TODO: Implement the function to fetch inventory by username from the database
function getInventoryByUsername($username)
{
    global $dbConn;

    // Use prepared statement to prevent SQL injection
    $sql = "SELECT i.series, i.name, i.version 
            FROM inventory i
            JOIN users u ON i.user_id = u.id
            WHERE u.username = ?";

    $stmt = $dbConn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $foundInventory = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $foundInventory;
    } else {
        echo "Error: Unable to prepare SQL statement.";
    }
}

// Function to check if a user exists
function userExists($username)
{
    global $dbConn;

    // Use prepared statement to prevent SQL injection
    $sql = "SELECT COUNT(*) as userCount FROM users WHERE username = ?";
    
    $stmt = $dbConn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return ($row['userCount'] > 0);
    } else {
        echo "Error: Unable to prepare SQL statement.";
        return false;
    }
}
?>
