<?php
// File: views/profile/profile.php

/**
 * Profile Show View - Router
 * Routes to ProfileController->show()
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug session (optional - remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check authentication BEFORE including anything else
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit();
}

// Include dependencies
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controllers/ProfileController.php';

// Create controller and show profile
try {
    $controller = new ProfileController();
    $controller->show();
} catch (Exception $e) {
    error_log("Profile show error: " . $e->getMessage());
    echo "<h1>Error Loading Profile</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><a href='../../dashboard.php'>Back to Dashboard</a></p>";
}
