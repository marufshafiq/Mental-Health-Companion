<?php
// File: api/mood_analysis.php

/**
 * API Endpoint: Mood Analysis
 * Returns mood analysis using Strategy pattern
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

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../services/MoodAnalysisStrategy.php';
require_once __DIR__ . '/../services/MoodSuggestionService.php';

try {
    $userId = $_SESSION['user_id'];
    $days = isset($_GET['days']) ? (int)$_GET['days'] : 30;
    $analysisType = isset($_GET['type']) ? $_GET['type'] : 'trend';
    
    // Limit days to reasonable range
    $days = max(1, min(365, $days));
    
    // Get database connection
    $db = getDb();
    
    // Fetch mood entries
    $sql = "SELECT id, mood_type, mood_value, notes, entry_date, entry_time, created_at 
            FROM mood_entries 
            WHERE user_id = ? 
            AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            ORDER BY created_at ASC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([$userId, $days]);
    $moodEntries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Create analysis context and set strategy
    $context = new MoodAnalysisContext();
    
    switch ($analysisType) {
        case 'trend':
            $context->setStrategy(new TrendAnalysisStrategy());
            break;
        case 'pattern':
            $context->setStrategy(new EmotionalPatternStrategy());
            break;
        case 'weekly':
            $context->setStrategy(new WeeklySummaryStrategy());
            break;
        default:
            $context->setStrategy(new TrendAnalysisStrategy());
    }
    
    // Execute strategy
    $analysis = $context->executeStrategy($moodEntries);
    
    // Get trend-based suggestions if applicable
    if ($analysisType === 'trend' && isset($analysis['trend'])) {
        $trendSuggestions = MoodSuggestionService::getSuggestionsByTrend($analysis['trend']);
        $analysis['suggestions'] = $trendSuggestions;
    }
    
    // Return response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'analysis_type' => $analysisType,
        'period_days' => $days,
        'total_entries' => count($moodEntries),
        'analysis' => $analysis
    ]);
    
} catch (PDOException $e) {
    error_log("Mood analysis error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error. Please try again.'
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
