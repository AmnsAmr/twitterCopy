<?php
// Start session (if not already started)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once 'backend/connection.php';

// Redirect logged-in users to the home page
if (isset($_SESSION['user_id'])) {
    // Verify that the user still exists in the database
    try {
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $stmt->store_result();

        // If the user exists, redirect to the home page
        if ($stmt->num_rows === 1) {
            header("Location: index.php");
            exit();
        } else {
            // If the user doesn't exist, destroy the session
            session_unset();
            session_destroy();
        }
    } catch (mysqli_sql_exception $e) {
        error_log("Database error: " . $e->getMessage());
        die("Oops! Something went wrong. Please try again later.");
    } finally {
        $stmt->close();
    }
}

// Define the main content for the login page
$content = '
  <div class="container">
    <h1>Login</h1>
    <form method="POST" action="backend/login_process.php">
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
      </div>
      <button type="submit" class="auth-button">Login</button>
    </form>
    <p>Don\'t have an account? <a href="signup.php">Sign Up</a></p>
  </div>
';

// Include the layout file
include 'layout.php';
?>