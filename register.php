<?php
// Include the database connection script
include('dbconn.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user input
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Validate input
    $errors = [];

    if (empty($username)) {
        $errors[] = 'Username is required';
    }

    if (empty($password)) {
        $errors[] = 'Password is required';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match';
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        // Check if the username is already taken
        $checkUsernameQuery = "SELECT * FROM users WHERE username = ?";
        $checkUsernameStmt = $dbConn->prepare($checkUsernameQuery);
        $checkUsernameStmt->bind_param("s", $username);
        $checkUsernameStmt->execute();
        $checkUsernameResult = $checkUsernameStmt->get_result();

        if ($checkUsernameResult->num_rows > 0) {
            $errors[] = 'Username is already taken. Please choose another.';
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert user data into the database
            $insertUserQuery = "INSERT INTO users (username, password) VALUES (?, ?)";
            $insertUserStmt = $dbConn->prepare($insertUserQuery);
            $insertUserStmt->bind_param("ss", $username, $hashedPassword);

            if ($insertUserStmt->execute()) {
                $registrationMessage = 'Registration successful! You can now login.';

                // Redirect to the login page after successful registration
                echo '<script>window.location.href = "login.php";</script>';
                exit();
            } else {
                $errors[] = 'Error while registering. Please try again.';
            }
        }
    }
}

// Close the database connection
$dbConn->close();
?>
