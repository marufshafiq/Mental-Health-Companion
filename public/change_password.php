<?php
// File: public/change_password.php

/**
 * Change Password Handler
 * Processes password change requests
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check authentication
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

// Include database configuration
require_once __DIR__ . '/../config.php';

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/profile/change_password.php");
    exit();
}

// Get form data
$currentPassword = $_POST['current_password'] ?? '';
$newPassword = $_POST['new_password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

// Validate input
$errors = [];

if (empty($currentPassword)) {
    $errors[] = "Current password is required.";
}

if (empty($newPassword)) {
    $errors[] = "New password is required.";
}

if (strlen($newPassword) < 6) {
    $errors[] = "New password must be at least 6 characters long.";
}

if ($newPassword !== $confirmPassword) {
    $errors[] = "New password and confirm password do not match.";
}

// If there are validation errors, redirect back with error
if (!empty($errors)) {
    $_SESSION['password_error'] = implode(' ', $errors);
    header("Location: ../views/profile/change_password.php");
    exit();
}

try {
    $db = getDb();
    $userId = $_SESSION['user_id'];
    
    // Get current password from database
    $sql = "SELECT password FROM users WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        $_SESSION['password_error'] = "User not found.";
        header("Location: ../views/profile/change_password.php");
        exit();
    }
    
    // Verify current password (plain text comparison - matching your existing auth system)
    if ($user['password'] !== $currentPassword) {
        $_SESSION['password_error'] = "Current password is incorrect.";
        header("Location: ../views/profile/change_password.php");
        exit();
    }
    
    // Update password (storing as plain text to match existing system)
    $updateSql = "UPDATE users SET password = ? WHERE id = ?";
    $updateStmt = $db->prepare($updateSql);
    $success = $updateStmt->execute([$newPassword, $userId]);
    
    if ($success) {
        $_SESSION['password_success'] = "Password changed successfully!";
        header("Location: ../views/profile/change_password.php");
        exit();
    } else {
        $_SESSION['password_error'] = "Failed to update password. Please try again.";
        header("Location: ../views/profile/change_password.php");
        exit();
    }
    
} catch (PDOException $e) {
    error_log("Password change error: " . $e->getMessage());
    $_SESSION['password_error'] = "An error occurred. Please try again later.";
    header("Location: ../views/profile/change_password.php");
    exit();
}
