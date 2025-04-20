<?php
// backend/signup_process.php

// Include database connection
require_once  'connection.php';

// Start session (if not already started)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validate inputs
    if (empty($email) || empty($password) || empty($confirm_password)|| empty($username)) {
        die("Please fill in all fields.");
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        die("Passwords do not match.");
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    // Check if the email is already registered
    try {
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            die("Email is already registered.");
        }
    } catch (mysqli_sql_exception $e) {
        error_log("Database error: " . $e->getMessage());
        die("Oops! Something went wrong. Please try again later.");
    } finally {
        $stmt->close();
    }

    // Hash the password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insert the new user into the database
    try {
        // Insert the new user into the database
        $stmt = $conn->prepare("INSERT INTO users (username ,email, password_hash) VALUES (? ,?, ?)");
        $stmt->bind_param("sss",$username, $email, $password_hash,);
        $stmt->execute();

        // Redirect to the login page after successful registration
        header("Location: ../login.php");
        exit();
    } catch (mysqli_sql_exception $e) {
        error_log("Database error: " . $e->getMessage());
        die("Oops! Something went wrong. Please try again later.");
    } finally {
        $stmt->close();
    }
} else {
    // Redirect if the form is not submitted
    header("Location: ../signup.php");
    exit();
}
?>