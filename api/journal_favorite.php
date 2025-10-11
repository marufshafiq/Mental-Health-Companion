<?php
// File: api/journal_favorite.php

/**
 * API Endpoint: Toggle Favorite
 * Toggles the favorite status of a journal entry
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
    $entryId = isset($_POST['entry_id']) ? (int)$_POST['entry_id'] : 0;
    
    // Validate input
    if ($entryId <= 0) {
        throw new Exception('Invalid entry ID');
    }
    
    // Toggle favorite
    $controller = new JournalController($userId);
    
    // Verify ownership
    $entry = $controller->getEntry($entryId);
    if (!$entry) {
        throw new Exception('Journal entry not found');
    }
    
    $success = $controller->toggleFavorite($entryId);
    
    if ($success) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Favorite status updated',
            'is_favorite' => !$entry['is_favorite']
        ]);
    } else {
        throw new Exception('Failed to update favorite status');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
