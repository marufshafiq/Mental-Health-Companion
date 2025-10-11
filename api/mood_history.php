<?php
// File: api/mood_history.php

/**
 * API Endpoint: Mood History
 * Returns mood entries for a specific time period
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

try {
    $userId = $_SESSION['user_id'];
    $days = isset($_GET['days']) ? (int)$_GET['days'] : 30;
    
    // Limit days to reasonable range
    $days = max(1, min(365, $days));
    
    // Get database connection
    $db = getDb();
    
    // Fetch mood entries for the specified period
    $sql = "SELECT id, mood_type, mood_value, notes, entry_date, entry_time, created_at 
            FROM mood_entries 
            WHERE user_id = ? 
            AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            ORDER BY created_at DESC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([$userId, $days]);
    $moodEntries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate statistics
    if (!empty($moodEntries)) {
        $values = array_column($moodEntries, 'mood_value');
        $average = array_sum($values) / count($values);
        $highest = max($values);
        $lowest = min($values);
        
        // Count mood types
        $moodCounts = [];
        foreach ($moodEntries as $entry) {
            $type = $entry['mood_type'];
            $moodCounts[$type] = ($moodCounts[$type] ?? 0) + 1;
        }
        
        arsort($moodCounts);
        $dominantMood = array_key_first($moodCounts);
    } else {
        $average = 0;
        $highest = 0;
        $lowest = 0;
        $dominantMood = 'neutral';
        $moodCounts = [];
    }
    
    // Return response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'entries' => $moodEntries,
        'statistics' => [
            'total_entries' => count($moodEntries),
            'average_mood' => round($average, 2),
            'highest_mood' => $highest,
            'lowest_mood' => $lowest,
            'dominant_mood' => $dominantMood,
            'mood_distribution' => $moodCounts,
            'period_days' => $days
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Mood history error: " . $e->getMessage());
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
