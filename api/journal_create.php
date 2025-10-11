<?php
// File: api/journal_create.php

/**
 * API Endpoint: Create Journal Entry
 * Handles POST requests to create new journal entries
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
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Use POST.'
    ]);
    exit();
}

require_once __DIR__ . '/../controllers/JournalController.php';
require_once __DIR__ . '/../services/SentimentAnalysisService.php';

try {
    $userId = $_SESSION['user_id'];
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    $type = isset($_POST['type']) ? trim($_POST['type']) : 'daily';
    $tags = isset($_POST['tags']) ? trim($_POST['tags']) : null;
    
    // Validate input
    if (empty($title)) {
        throw new Exception('Title is required');
    }
    
    if (empty($content)) {
        throw new Exception('Content is required');
    }
    
    // Perform sentiment analysis
    $sentiment = SentimentAnalysisService::analyze($content);
    $mood = $sentiment['mood'];
    
    // Create entry
    $controller = new JournalController($userId);
    $entryId = $controller->addEntry($title, $content, $type, $mood, $tags);
    
    if ($entryId) {
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Journal entry created successfully',
            'entry_id' => $entryId,
            'sentiment' => $sentiment
        ]);
    } else {
        throw new Exception('Failed to create journal entry');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
