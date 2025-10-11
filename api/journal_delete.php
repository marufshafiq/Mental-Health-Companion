<?php
// File: api/journal_delete.php

/**
 * API Endpoint: Delete Journal Entry
 * Handles DELETE/POST requests to delete journal entries
 */

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check authentication
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized. Please log in.'
    ]);
    exit();
}

require_once __DIR__ . '/../controllers/JournalController.php';

try {
    $userId = $_SESSION['user_id'];
    $entryId = isset($_POST['entry_id']) ? (int)$_POST['entry_id'] : (isset($_GET['entry_id']) ? (int)$_GET['entry_id'] : 0);
    
    // Validate input
    if ($entryId <= 0) {
        throw new Exception('Invalid entry ID');
    }
    
    // Delete entry
    $controller = new JournalController($userId);
    
    // Verify ownership
    $entry = $controller->getEntry($entryId);
    if (!$entry) {
        throw new Exception('Journal entry not found');
    }
    
    $success = $controller->deleteEntry($entryId);
    
    if ($success) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Journal entry deleted successfully'
        ]);
    } else {
        throw new Exception('Failed to delete journal entry');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
