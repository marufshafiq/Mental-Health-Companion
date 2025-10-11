<?php
// File: config.php

/**
 * Application Configuration File
 * Contains database connection and external API settings
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_PORT', '3307');
define('DB_NAME', 'isd');
define('DB_USER', 'root');
define('DB_PASS', '');

// Chatbot API Configuration
// Replace these with your actual API credentials
define('CHATBOT_API_URL', 'https://openrouter.ai/api/v1/chat/completions');
define('CHATBOT_API_KEY', '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');

// Application Settings
define('APP_ENV', 'development'); // development or production
define('APP_DEBUG', true);
define('APP_ADMIN_EMAIL', 'admin@gmail.com'); // Admin user email for role assignment
// Database Connection (PDO)
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ":" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch(PDOException $e) {
    error_log("Database Connection Error: " . $e->getMessage());
    if (APP_DEBUG) {
        die("Database connection failed. Please check your configuration.");
    } else {
        die("An error occurred. Please try again later.");
    }
}

/**
 * Get database connection (Singleton pattern)
 * 
 * @return PDO Database connection object
 */
function getDb() {
    global $pdo;
    return $pdo;
}

// Optional: mysqli connection for compatibility
// Uncomment if needed
/*
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
if ($mysqli->connect_error) {
    error_log("mysqli Connection Error: " . $mysqli->connect_error);
    die("Database connection failed.");
}
$mysqli->set_charset("utf8mb4");
*/
