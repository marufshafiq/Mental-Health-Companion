<?php
// File: api/mood_record.php

/**
 * API Endpoint: Record Mood
 * Handles POST requests to store user mood entries
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

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Use POST.'
    ]);
    exit();
}

require_once __DIR__ . '/../config.php';

try {
    // Get POST data
    $userId = $_SESSION['user_id'];
    $moodType = isset($_POST['mood_type']) ? trim($_POST['mood_type']) : '';
    $moodValue = isset($_POST['mood_value']) ? (int)$_POST['mood_value'] : 0;
    $notes = isset($_POST['notes']) ? trim($_POST['notes']) : null;
    
    // Validate mood type and value
    $validMoods = ['happy', 'calm', 'neutral', 'stressed', 'sad', 'anxious', 'excited', 'tired'];
    if (empty($moodType) || !in_array($moodType, $validMoods)) {
        throw new Exception('Invalid mood type');
    }
    
    if ($moodValue < 1 || $moodValue > 5) {
        throw new Exception('Mood value must be between 1 and 5');
    }
    
    // Get database connection
    $db = getDb();
    
    // Insert mood entry
    $sql = "INSERT INTO mood_entries (user_id, mood_type, mood_value, notes, entry_date, entry_time, created_at) 
            VALUES (?, ?, ?, ?, CURDATE(), CURTIME(), NOW())";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([$userId, $moodType, $moodValue, $notes]);
    
    $moodId = $db->lastInsertId();
    
    // Return success response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Mood recorded successfully!',
        'mood_id' => $moodId,
        'mood_type' => $moodType,
        'mood_value' => $moodValue,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (PDOException $e) {
    error_log("Mood record error: " . $e->getMessage());
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
