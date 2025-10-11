<?php
// File: api/journal_sentiment.php

/**
 * API Endpoint: Analyze Sentiment
 * Returns sentiment analysis for journal content
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

require_once __DIR__ . '/../services/SentimentAnalysisService.php';

try {
    $content = isset($_POST['content']) ? trim($_POST['content']) : (isset($_GET['content']) ? trim($_GET['content']) : '');
    
    if (empty($content)) {
        throw new Exception('Content is required for sentiment analysis');
    }
    
    // Perform sentiment analysis
    $sentiment = SentimentAnalysisService::analyze($content);
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'sentiment' => $sentiment
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
