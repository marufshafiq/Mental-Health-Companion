<?php
// File: views/profile/edit_profile.php

/**
 * Profile Edit View - Router
 * Routes to ProfileController->edit()
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

// Create controller and show edit form
try {
    $controller = new ProfileController();
    $controller->edit();
} catch (Exception $e) {
    error_log("Profile edit error: " . $e->getMessage());
    die("Error loading profile edit page. Please try again.");
}
