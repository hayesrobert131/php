<?php
// Include the database connection script
include('dbconn.php');

// Start or resume a session
session_start();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user input
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query the database for the user
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $dbConn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Check if the password matches
        if (password_verify($password, $row['password'])) {
            // Set user information in the session
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];

            // Redirect to the dashboard
            header('Location: dashboard.php');
            exit();
        } else {
            $errorMessage = 'Invalid username or password';
        }
    } else {
        $errorMessage = 'Invalid username or password';
    }
}

// Close the database connection
$dbConn->close();
?>
