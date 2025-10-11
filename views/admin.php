<?php
/**
 * Admin Panel Entry Point
 * 
 * This file instantiates the AdminController and displays the admin dashboard.
 * Requires admin authentication.
 * 
 * @package MentalHealthCompanion
 * @author Maruf
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required files
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../controllers/AdminController.php';

// Instantiate AdminController and display admin dashboard
$adminController = new AdminController();
$adminController->index();
