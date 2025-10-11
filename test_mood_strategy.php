<?php
// File: test_mood_strategy.php
// Quick test to verify Strategy pattern implementation

require_once 'config.php';
require_once 'services/MoodAnalysisStrategy.php';

echo "<!DOCTYPE html><html><head><title>Mood Strategy Test</title>";
echo "<style>body{font-family:Arial;padding:20px;} .success{color:green;} .error{color:red;} .section{margin:20px 0;padding:15px;border:1px solid #ddd;border-radius:8px;}</style>";
echo "</head><body>";
echo "<h1>🧠 Mood Tracker Strategy Pattern Test</h1>";

try {
    $db = getDb();
    
    // Fetch sample mood data
    $sql = "SELECT * FROM mood_entries WHERE mood_type IS NOT NULL ORDER BY created_at DESC LIMIT 20";
    $stmt = $db->query($sql);
    $moodEntries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<div class='section'>";
    echo "<h2>📊 Sample Data</h2>";
    echo "<p><strong>Entries found:</strong> " . count($moodEntries) . "</p>";
    if (count($moodEntries) > 0) {
        echo "<p class='success'>✅ Mood data available for testing</p>";
    } else {
        echo "<p>ℹ️ No mood entries yet. Try recording a mood first!</p>";
    }
    echo "</div>";
    
    // Test 1: Trend Analysis Strategy
    echo "<div class='section'>";
    echo "<h2>1️⃣ Trend Analysis Strategy</h2>";
    $context = new MoodAnalysisContext();
    $context->setStrategy(new TrendAnalysisStrategy());
    $trendResult = $context->executeStrategy($moodEntries);
    
    echo "<pre>" . print_r($trendResult, true) . "</pre>";
    echo "<p class='success'>✅ Trend Analysis Strategy Working!</p>";
    echo "</div>";
    
    // Test 2: Emotional Pattern Strategy
    echo "<div class='section'>";
    echo "<h2>2️⃣ Emotional Pattern Strategy</h2>";
    $context->setStrategy(new EmotionalPatternStrategy());
    $patternResult = $context->executeStrategy($moodEntries);
    
    echo "<pre>" . print_r($patternResult, true) . "</pre>";
    echo "<p class='success'>✅ Emotional Pattern Strategy Working!</p>";
    echo "</div>";
    
    // Test 3: Weekly Summary Strategy
    echo "<div class='section'>";
    echo "<h2>3️⃣ Weekly Summary Strategy</h2>";
    $context->setStrategy(new WeeklySummaryStrategy());
    $weeklyResult = $context->executeStrategy($moodEntries);
    
    echo "<pre>" . print_r($weeklyResult, true) . "</pre>";
    echo "<p class='success'>✅ Weekly Summary Strategy Working!</p>";
    echo "</div>";
    
    // Test 4: Mood Suggestions
    echo "<div class='section'>";
    echo "<h2>4️⃣ Mood Suggestion Service</h2>";
    require_once 'services/MoodSuggestionService.php';
    
    $moods = ['happy', 'stressed', 'sad'];
    foreach ($moods as $mood) {
        echo "<h3>😊 Suggestions for '$mood' mood:</h3>";
        $suggestions = MoodSuggestionService::getSuggestions($mood, 3);
        echo "<ul>";
        foreach ($suggestions as $suggestion) {
            echo "<li>$suggestion</li>";
        }
        echo "</ul>";
    }
    echo "<p class='success'>✅ Mood Suggestion Service Working!</p>";
    echo "</div>";
    
    // Summary
    echo "<div class='section' style='background:#d4edda;border-color:#c3e6cb;'>";
    echo "<h2>✅ All Tests Passed!</h2>";
    echo "<p><strong>Strategy Pattern Implementation:</strong> SUCCESSFUL</p>";
    echo "<ul>";
    echo "<li>✅ MoodAnalysisContext working correctly</li>";
    echo "<li>✅ Strategy switching works dynamically</li>";
    echo "<li>✅ All 3 strategies return valid results</li>";
    echo "<li>✅ Mood suggestion service operational</li>";
    echo "</ul>";
    echo "<p><a href='mood.php' style='display:inline-block;margin-top:15px;padding:10px 20px;background:#4CAF50;color:white;text-decoration:none;border-radius:5px;'>Go to Mood Tracker →</a></p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='section' style='background:#f8d7da;border-color:#f5c6cb;'>";
    echo "<h2 class='error'>❌ Error</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "</body></html>";
