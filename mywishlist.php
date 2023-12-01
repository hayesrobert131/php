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

// Fetch user's wishlist from the database
$wishlist = fetchWishlist($user_id);

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_item'])) {
    // Process the form submission and insert data into the database
    $series = $_POST['series'];
    $name = $_POST['name'];
    $version = $_POST['version'];

    // Validate input if needed

    // Insert data into the database
    insertWishlistItem($user_id, $series, $name, $version);
}

// Check if search is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $searchType = $_POST['searchType'];
    $searchTerm = $_POST['searchTerm'];

    // Fetch filtered wishlist based on search type and term
    $wishlist = fetchFilteredWishlist($user_id, $searchType, $searchTerm);
}

// Function to fetch user's wishlist from the database
function fetchWishlist($user_id)
{
    global $dbConn;

    // Use prepared statement to prevent SQL injection
    $sql = "SELECT series, name, version FROM wishlist WHERE user_id = ?";
    
    $stmt = $dbConn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $wishlist = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $wishlist;
    } else {
        echo "Error: Unable to prepare SQL statement.";
        return [];
    }
}

// Function to insert data into the wishlist
function insertWishlistItem($user_id, $series, $name, $version)
{
    global $dbConn;

    // Use prepared statement to prevent SQL injection
    $sql = "INSERT INTO wishlist (user_id, series, name, version) VALUES (?, ?, ?, ?)";
    $stmt = $dbConn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("isss", $user_id, $series, $name, $version);
        $stmt->execute();

        // Check for successful insertion
        if ($stmt->affected_rows > 0) {
            // Insertion successful
            // Optionally, you can redirect to prevent form resubmission
            // header('Location: mywishlist.php');
            // exit();
        } else {
            // Insertion failed
            echo "Error: Unable to add item to wishlist.";
        }

        $stmt->close();
    } else {
        echo "Error: Unable to prepare SQL statement.";
    }
}

// Function to fetch filtered wishlist based on search type and term
function fetchFilteredWishlist($user_id, $searchType, $searchTerm)
{
    global $dbConn;

    // Use prepared statement to prevent SQL injection
    $sql = "SELECT series, name, version FROM wishlist WHERE user_id = ? AND $searchType LIKE ?";
    $stmt = $dbConn->prepare($sql);

    if ($stmt) {
        $searchTerm = "%$searchTerm%"; // Add wildcard characters for a partial match
        $stmt->bind_param("is", $user_id, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        $wishlist = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $wishlist;
    } else {
        echo "Error: Unable to prepare SQL statement.";
        return [];
    }
}

// Close the database connection
$dbConn->close();
?>
