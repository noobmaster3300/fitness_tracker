<?php
// Database configuration
$host = "localhost";
$user = "root";
$password = "";
$database = "fitness_tracker";

// Create connection with error handling
try {
    $conn = new mysqli($host, $user, $password, $database);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to utf8mb4 for proper Unicode support
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    // Log error and display user-friendly message
    error_log("Database connection error: " . $e->getMessage());
    die("Database connection error. Please try again later.");
}

// Helper function to safely close database connection
function closeConnection($conn) {
    if ($conn) {
        $conn->close();
    }
}

// Register shutdown function to close connection
register_shutdown_function('closeConnection', $conn);
?>
