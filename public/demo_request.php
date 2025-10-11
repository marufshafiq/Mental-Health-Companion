<?php
// File: public/demo_request.php

/**
 * Demo Request Handler
 * Handles consultation demo booking requests
 * 
 * @package MentalHealthCompanion
 * @author GitHub Copilot
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check authentication
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Include config
require_once __DIR__ . '/../config.php';

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/profile/profile.php");
    exit();
}

/**
 * Get database connection using Singleton pattern
 * 
 * @return PDO|mysqli Database connection
 */
function getDatabaseConnection() {
    if (function_exists('getDb')) {
        return getDb();
    }
    
    global $pdo;
    if (isset($pdo) && $pdo instanceof PDO) {
        return $pdo;
    }
    
    global $mysqli;
    if (isset($mysqli) && $mysqli instanceof mysqli) {
        return $mysqli;
    }
    
    throw new Exception("No database connection available");
}

/**
 * Get authenticated user ID
 * 
 * @return int User ID
 */
function getAuthUserId() {
    return $_SESSION['user_id'] ?? null;
}

try {
    $db = getDatabaseConnection();
    $userId = getAuthUserId();
    
    if (!$userId) {
        throw new Exception("User not authenticated");
    }
    
    // Get and validate inputs
    $consultantId = filter_input(INPUT_POST, 'consultant_id', FILTER_VALIDATE_INT);
    $preferredDatetime = trim($_POST['preferred_datetime'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    if (!$consultantId) {
        throw new Exception("Please select a consultant");
    }
    
    // Sanitize message
    $message = strip_tags($message);
    
    // Prepare datetime value (NULL if empty)
    $datetimeValue = !empty($preferredDatetime) ? $preferredDatetime : null;
    
    // Insert demo request
    if ($db instanceof PDO) {
        $stmt = $db->prepare("
            INSERT INTO demo_requests (user_id, consultant_id, preferred_datetime, message, status, created_at)
            VALUES (?, ?, ?, ?, 'pending', NOW())
        ");
        $stmt->execute([$userId, $consultantId, $datetimeValue, $message]);
        
    } elseif ($db instanceof mysqli) {
        $stmt = $db->prepare("
            INSERT INTO demo_requests (user_id, consultant_id, preferred_datetime, message, status, created_at)
            VALUES (?, ?, ?, ?, 'pending', NOW())
        ");
        $stmt->bind_param("iiss", $userId, $consultantId, $datetimeValue, $message);
        $stmt->execute();
    }
    
    // Success - set flash message and redirect
    $_SESSION['profile_message'] = "Demo request submitted successfully! The consultant will contact you soon.";
    header("Location: ../views/profile/profile.php");
    exit();
    
} catch (Exception $e) {
    error_log("Demo request error: " . $e->getMessage());
    $_SESSION['profile_error'] = "Failed to submit demo request. Please try again.";
    header("Location: ../views/profile/profile.php");
    exit();
}
