<?php
// File: public/profile_update.php

/**
 * Profile Update Handler
 * Processes profile edit form submissions
 * 
 * @package MentalHealthCompanion
 * @author GitHub Copilot
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include dependencies
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../controllers/ProfileController.php';

// Create controller instance and handle update
try {
    $controller = new ProfileController();
    $controller->update();
} catch (Exception $e) {
    error_log("Profile update error: " . $e->getMessage());
    $_SESSION['profile_error'] = "An error occurred while updating your profile. Please try again.";
    header("Location: ../views/profile/edit.php");
    exit();
}
