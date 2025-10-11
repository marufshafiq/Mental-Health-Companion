<?php
// File: public/api/chat.php

// Set headers for JSON response and CORS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include config and dependencies
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../app/models/Message.php';
require_once __DIR__ . '/../../controllers/ChatController.php';

/**
 * Chat API Endpoint
 * Handles incoming chat messages and returns bot responses
 */

try {
    // Initialize controller and handle request
    $controller = new ChatController();
    $response = $controller->handlePost();
    
    // Output JSON response
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    // Handle unexpected errors
    error_log("Chat API error: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'An unexpected error occurred. Please try again later.'
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
