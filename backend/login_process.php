<?php
// backend/login_process.php

// Start session (if not already started)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once 'connection.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($email) || empty($password)) {
        die("Please fill in all fields.");
    }

    // Prepare and execute the query
    try {
        $stmt = $conn->prepare("SELECT user_id, password_hash FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        // Check if the user exists
        if ($stmt->num_rows === 1) {
            $stmt->bind_result($user_id, $password_hash);
            $stmt->fetch();

            // Verify the password
            if (password_verify($password, $password_hash)) {
                // Login successful: Set session variables
                $_SESSION['user_id'] = $user_id;
                $_SESSION['email'] = $email;

                // Redirect to the home page
                header("Location: ../index.php");
                exit();
            } else {
                die("Invalid email or password.");
            }
        } else {
            die("Invalid email or password.");
        }
    } catch (mysqli_sql_exception $e) {
        error_log("Database error: " . $e->getMessage());
        die("Oops! Something went wrong. Please try again later.");
    } finally {
        $stmt->close();
    }
} else {
    // Redirect if the form is not submitted
    header("Location: ../login.php");
    exit();
}
?>