<?php
// File: test_profile.php
// Quick test to verify profile feature works

session_start();

// Set up test session (simulate logged-in user)
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; // Assuming user ID 1 exists
    $_SESSION['username'] = 'test';
    $_SESSION['name'] = 'Test User';
}

require_once 'config.php';

echo "<!DOCTYPE html><html><head><title>Profile Test</title></head><body>";
echo "<h1>Profile Feature Test</h1>";

// Test 1: Check if tables exist
echo "<h2>Test 1: Database Tables</h2>";
try {
    $db = getDb();
    
    // Check consultants table
    $stmt = $db->query("SHOW TABLES LIKE 'consultants'");
    $consultantsExists = $stmt->rowCount() > 0;
    echo "✅ Consultants table: " . ($consultantsExists ? "EXISTS" : "MISSING") . "<br>";
    
    // Check demo_requests table
    $stmt = $db->query("SHOW TABLES LIKE 'demo_requests'");
    $demoRequestsExists = $stmt->rowCount() > 0;
    echo "✅ Demo_requests table: " . ($demoRequestsExists ? "EXISTS" : "MISSING") . "<br>";
    
    // Check users table has new columns
    $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'bio'");
    $bioExists = $stmt->rowCount() > 0;
    echo "✅ Users.bio column: " . ($bioExists ? "EXISTS" : "MISSING") . "<br>";
    
    $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'profile_image'");
    $profileImageExists = $stmt->rowCount() > 0;
    echo "✅ Users.profile_image column: " . ($profileImageExists ? "EXISTS" : "MISSING") . "<br>";
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// Test 2: Check consultants data
echo "<h2>Test 2: Sample Consultants</h2>";
try {
    $stmt = $db->query("SELECT COUNT(*) as count FROM consultants");
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "✅ Consultants count: $count<br>";
    
    if ($count > 0) {
        $stmt = $db->query("SELECT name, title FROM consultants LIMIT 3");
        echo "<ul>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<li>" . htmlspecialchars($row['name']) . " - " . htmlspecialchars($row['title']) . "</li>";
        }
        echo "</ul>";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

// Test 3: Check ProfileController
echo "<h2>Test 3: ProfileController</h2>";
try {
    require_once 'controllers/ProfileController.php';
    $controller = new ProfileController();
    echo "✅ ProfileController loaded successfully<br>";
} catch (Exception $e) {
    echo "❌ Error loading ProfileController: " . $e->getMessage() . "<br>";
}

// Test 4: Check upload directory
echo "<h2>Test 4: Upload Directory</h2>";
$uploadDir = 'assets/uploads/profiles/';
if (is_dir($uploadDir)) {
    echo "✅ Upload directory exists<br>";
    if (is_writable($uploadDir)) {
        echo "✅ Upload directory is writable<br>";
    } else {
        echo "⚠️ Upload directory is NOT writable<br>";
    }
} else {
    echo "❌ Upload directory does NOT exist<br>";
}

// Test 5: Check required files
echo "<h2>Test 5: Required Files</h2>";
$files = [
    'controllers/ProfileController.php',
    'views/profile/index.php',
    'public/profile_update.php',
    'public/demo_request.php',
    'database/sql/05_create_consultants_and_demo_requests.sql'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ $file<br>";
    } else {
        echo "❌ MISSING: $file<br>";
    }
}

// Test links
echo "<h2>Access Profile Pages</h2>";
echo "<p><a href='views/profile/index.php' style='display:inline-block; padding:10px 20px; background:#4A90E2; color:white; text-decoration:none; border-radius:5px;'>Go to Profile Page</a></p>";

echo "</body></html>";
?>
