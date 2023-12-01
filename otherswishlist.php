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
$foundWishlist = [];

// Check if the search form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    // Get the username entered in the search form
    $searchUsername = trim($_POST['search_username']);

    // TODO: Implement the logic to fetch wishlist for the specified user from the database
    // For now, let's assume you have a function to fetch wishlist by username
    // Replace 'getWishlistByUsername' with your actual function
    $foundWishlist = getWishlistByUsername($searchUsername);
}

// TODO: Implement the function to fetch wishlist by username from the database
function getWishlistByUsername($username)
{
    global $dbConn;

    // Use prepared statement to prevent SQL injection
    $sql = "SELECT w.series, w.name, w.version 
            FROM wishlist w
            JOIN users u ON w.user_id = u.id
            WHERE u.username = ?";

    $stmt = $dbConn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $foundWishlist = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $foundWishlist;
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
