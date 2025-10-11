<?php
// File: api/journal_update.php

/**
 * API Endpoint: Update Journal Entry
 * Handles PUT/POST requests to update existing journal entries
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

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Use POST or PUT.'
    ]);
    exit();
}

require_once __DIR__ . '/../controllers/JournalController.php';
require_once __DIR__ . '/../services/SentimentAnalysisService.php';

try {
    $userId = $_SESSION['user_id'];
    $entryId = isset($_POST['entry_id']) ? (int)$_POST['entry_id'] : 0;
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    $tags = isset($_POST['tags']) ? trim($_POST['tags']) : null;
    
    // Validate input
    if ($entryId <= 0) {
        throw new Exception('Invalid entry ID');
    }
    
    if (empty($title)) {
        throw new Exception('Title is required');
    }
    
    if (empty($content)) {
        throw new Exception('Content is required');
    }
    
    // Perform sentiment analysis
    $sentiment = SentimentAnalysisService::analyze($content);
    $mood = $sentiment['mood'];
    
    // Update entry
    $controller = new JournalController($userId);
    
    // Verify ownership
    $entry = $controller->getEntry($entryId);
    if (!$entry) {
        throw new Exception('Journal entry not found');
    }
    
    $success = $controller->updateEntry($entryId, $title, $content, $mood, $tags);
    
    if ($success) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Journal entry updated successfully',
            'sentiment' => $sentiment
        ]);
    } else {
        throw new Exception('Failed to update journal entry');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
