<?php
// File: api/journal_stats.php
// API endpoint to get journal statistics and sentiment trend

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

require_once __DIR__ . '/../controllers/JournalController.php';
require_once __DIR__ . '/../services/SentimentAnalysisService.php';

try {
    $controller = new JournalController($_SESSION['user_id']);
    
    // Get filter type if provided
    $type = isset($_GET['type']) ? $_GET['type'] : null;
    
    // Get statistics
    $stats = $controller->getStats();
    
    // Get entries for trend calculation
    $entries = $controller->getEntries(null, $type);
    
    // Calculate sentiment trend (last 10 entries)
    $sentimentTrend = [];
    if (!empty($entries)) {
        $sentimentTrend = SentimentAnalysisService::getTrend(array_slice($entries, 0, 10));
    }
    
    // Calculate average sentiment for mood trend
    $moodTrendEmoji = '😐';
    if (!empty($entries)) {
        $avgSentiment = SentimentAnalysisService::getAverageSentiment($entries);
        if ($avgSentiment >= 0.5) {
            $moodTrendEmoji = '😊';
        } elseif ($avgSentiment >= 0) {
            $moodTrendEmoji = '🙂';
        } else {
            $moodTrendEmoji = '😕';
        }
    }
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'total_entries' => $stats['total_entries'] ?? 0,
            'total_words' => $stats['total_words'] ?? 0,
            'favorites' => $stats['favorites'] ?? 0,
            'mood_trend' => $moodTrendEmoji
        ],
        'sentiment_trend' => $sentimentTrend
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
