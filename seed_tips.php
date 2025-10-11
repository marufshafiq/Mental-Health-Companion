<?php
require 'config.php';

// Sample tips to insert
$tips = [
    "Take 5 deep breaths when feeling stressed. Inhale for 4 counts, hold for 4, exhale for 6.",
    "Practice gratitude daily. Write down three things you're thankful for each morning.",
    "Stay hydrated! Drink at least 8 glasses of water throughout the day for better mental clarity.",
    "Take regular breaks from screens. Follow the 20-20-20 rule: every 20 minutes, look 20 feet away for 20 seconds.",
    "Get moving! Even a 10-minute walk can boost your mood and reduce anxiety.",
    "Connect with nature. Spending time outdoors can significantly improve mental well-being.",
    "Practice mindfulness meditation for just 5 minutes daily to reduce stress and increase focus.",
    "Maintain a consistent sleep schedule. Aim for 7-9 hours of quality sleep each night.",
    "Limit caffeine intake, especially in the afternoon and evening, for better sleep quality.",
    "Reach out to friends or family. Social connections are vital for mental health.",
    "Set small, achievable goals each day to build momentum and confidence.",
    "Practice self-compassion. Treat yourself with the same kindness you'd offer a good friend.",
];

try {
    // Clear existing tips
    $pdo->exec("TRUNCATE TABLE tips");
    
    // Insert new tips
    $stmt = $pdo->prepare("INSERT INTO tips (tip_text) VALUES (?)");
    
    $count = 0;
    foreach ($tips as $tip) {
        $stmt->execute([$tip]);
        $count++;
    }
    
    echo "Successfully inserted $count tips into the database!\n\n";
    
    // Test the tip-of-day algorithm
    echo "Testing Tip-of-the-Day algorithm:\n";
    echo "Today's day of year: " . date('z') . "\n";
    echo "Total tips: $count\n";
    echo "Selected index: " . (date('z') % $count) . "\n\n";
    
    // Fetch today's tip
    $dayOfYear = (int)date('z');
    $index = $dayOfYear % $count;
    $stmt = $pdo->prepare("SELECT tip_text FROM tips ORDER BY id ASC LIMIT 1 OFFSET ?");
    $stmt->execute([$index]);
    $tip = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Today's Tip:\n";
    echo "\"" . $tip['tip_text'] . "\"\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
