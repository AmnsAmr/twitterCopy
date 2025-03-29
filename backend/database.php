<?php
// debug_table.php - Place in your project root
// This script checks your database tables

// Include database connection
require_once 'backend/connection.php';

// Check for table named "users" (case sensitive)
$result = $conn->query("SHOW TABLES LIKE 'users'");
if ($result->num_rows > 0) {
    echo "<p>Table 'users' exists.</p>";
} else {
    // Check for a different case
    $result = $conn->query("SHOW TABLES");
    $tables = [];
    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
    }
    
    echo "<p>Table 'users' not found. Available tables:</p>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>" . htmlspecialchars($table) . "</li>";
    }
    echo "</ul>";
    
    // Look for a similar table name
    foreach ($tables as $table) {
        if (strtolower($table) == 'users') {
            echo "<p>Found table '" . htmlspecialchars($table) . "' which is similar to 'users' but with different case.</p>";
        }
    }
}

// If we find the users table, check its structure
$result = $conn->query("SHOW TABLES LIKE 'users'");
if ($result->num_rows > 0) {
    $result = $conn->query("DESCRIBE users");
    echo "<p>Structure of 'users' table:</p>";
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $key => $value) {
            echo "<td>" . htmlspecialchars($value ?: "NULL") . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}

// Test the session user_id
echo "<p>Current session user_id: " . htmlspecialchars($_SESSION['user_id'] ?? 'Not set') . "</p>";

// If we have a session user_id, verify it exists in the database
if (isset($_SESSION['user_id'])) {
    // Search in all tables that might contain users
    foreach ($tables as $table) {
        // Check if the table has a user_id column
        $result = $conn->query("DESCRIBE `$table`");
        $has_user_id = false;
        $id_column = '';
        
        while ($row = $result->fetch_assoc()) {
            if (strtolower($row['Field']) == 'user_id') {
                $has_user_id = true;
                $id_column = $row['Field'];
                break;
            }
        }
        
        if ($has_user_id) {
            $stmt = $conn->prepare("SELECT * FROM `$table` WHERE `$id_column` = ?");
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows > 0) {
                echo "<p>Found user with ID " . htmlspecialchars($_SESSION['user_id']) . " in table " . htmlspecialchars($table) . ".</p>";
            } else {
                echo "<p>User with ID " . htmlspecialchars($_SESSION['user_id']) . " not found in table " . htmlspecialchars($table) . ".</p>";
            }
            
            $stmt->close();
        }
    }
}

echo "<p>Database charset: " . $conn->character_set_name() . "</p>";
echo "<p>Connection collation: " . $conn->query("SELECT @@session.collation_connection")->fetch_array()[0] . "</p>";
?>