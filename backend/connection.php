<?php
// Database configuration (Store in a separate config file for security)
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // Replace with your username
define('DB_PASSWORD', ''); // Replace with your password
define('DB_NAME', 'twittercopy'); // Replace with your database name

// Create a database connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Set charset to UTF-8
$conn->set_charset("utf8mb4");

// Optional: Error handling for development
if ($conn->connect_error) {
    error_log("Database connection error: " . $conn->connect_error);
    die("Oops! Something went wrong. Please try again later.");
}
?>