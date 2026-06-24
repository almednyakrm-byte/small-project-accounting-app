<?php

// Start the session to handle user authentication
session_start();

// Import the database connection script
require_once 'db.php';

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    // If the user is logged in, return a JSON response indicating their status
    echo json_encode(array('status' => 'logged_in', 'user_id' => $_SESSION['user_id']));
    exit;
}

// Check if the user is attempting to register or login
if (isset($_POST['action'])) {
    // Check if the action is register
    if ($_POST['action'] == 'register') {
        // Check if all required fields are present
        if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])) {
            // Check if the username and email are not empty
            if (!empty($_POST['username']) && !empty($_POST['email'])) {
                // Check if the password is at least 8 characters long
                if (strlen($_POST['password']) >= 8) {
                    // Prepare the SQL statement to insert the user into the database
                    $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
                    $stmt->bindParam(':username', $_POST['username']);
                    $stmt->bindParam(':email', $_POST['email']);
                    $stmt->bindParam(':password', password_hash($_POST['password'], PASSWORD_DEFAULT));
                    // Execute the SQL statement
                    $stmt->execute();
                    // Return a JSON response indicating that the user has been registered
                    echo json_encode(array('status' => 'registered'));
                    exit;
                } else {
                    // Return a JSON response indicating that the password is too short
                    echo json_encode(array('status' => 'error', 'message' => 'Password must be at least 8 characters long'));
                    exit;
                }
            } else {
                // Return a JSON response indicating that the username and email are required
                echo json_encode(array('status' => 'error', 'message' => 'Username and email are required'));
                exit;
            }
        } else {
            // Return a JSON response indicating that all fields are required
            echo json_encode(array('status' => 'error', 'message' => 'All fields are required'));
            exit;
        }
    }
    // Check if the action is login
    elseif ($_POST['action'] == 'login') {
        // Check if all required fields are present
        if (isset($_POST['username']) && isset($_POST['password'])) {
            // Check if the username and password are not empty
            if (!empty($_POST['username']) && !empty($_POST['password'])) {
                // Prepare the SQL statement to select the user from the database
                $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
                $stmt->bindParam(':username', $_POST['username']);
                // Execute the SQL statement
                $stmt->execute();
                // Fetch the user data from the database
                $user = $stmt->fetch();
                // Check if the user exists and the password is correct
                if ($user && password_verify($_POST['password'], $user['password'])) {
                    // Start a new session for the user
                    session_start();
                    // Store the user ID in the session
                    $_SESSION['user_id'] = $user['id'];
                    // Return a JSON response indicating that the user has been logged in
                    echo json_encode(array('status' => 'logged_in', 'user_id' => $_SESSION['user_id']));
                    exit;
                } else {
                    // Return a JSON response indicating that the username or password is incorrect
                    echo json_encode(array('status' => 'error', 'message' => 'Invalid username or password'));
                    exit;
                }
            } else {
                // Return a JSON response indicating that the username and password are required
                echo json_encode(array('status' => 'error', 'message' => 'Username and password are required'));
                exit;
            }
        } else {
            // Return a JSON response indicating that all fields are required
            echo json_encode(array('status' => 'error', 'message' => 'All fields are required'));
            exit;
        }
    }
}

// Check if the user is attempting to logout
if (isset($_POST['action']) && $_POST['action'] == 'logout') {
    // Destroy the session to log the user out
    session_destroy();
    // Return a JSON response indicating that the user has been logged out
    echo json_encode(array('status' => 'logged_out'));
    exit;
}

// If the user is not logged in, return a JSON response indicating their status
echo json_encode(array('status' => 'logged_out'));
exit;

?>