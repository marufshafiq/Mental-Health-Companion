<?php
// File: api/mood_suggestions.php

/**
 * API Endpoint: Mood Suggestions
 * Returns personalized suggestions based on mood type and trend
 */

header('Content-Type: application/json');

// Start session if not already started
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

require_once __DIR__ . '/../services/MoodSuggestionService.php';

try {
    $moodType = isset($_GET['mood_type']) ? trim($_GET['mood_type']) : 'neutral';
    $count = isset($_GET['count']) ? (int)$_GET['count'] : 5;
    
    // Get suggestions for the mood type
    $suggestions = MoodSuggestionService::getSuggestions($moodType, $count);
    
    // Check if mood is critically low
    $showEmergency = false;
    if (in_array($moodType, ['sad', 'anxious']) && isset($_GET['value']) && (int)$_GET['value'] <= 2) {
        $showEmergency = true;
        $emergencyResources = MoodSuggestionService::getEmergencyResources();
    }
    
    // Return response
    http_response_code(200);
    $response = [
        'success' => true,
        'mood_type' => $moodType,
        'suggestions' => $suggestions
    ];
    
    if ($showEmergency) {
        $response['emergency_resources'] = $emergencyResources;
        $response['message'] = 'If you\'re in crisis, please reach out for immediate support.';
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
