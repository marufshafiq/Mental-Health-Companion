<?php
/**
 * Setup Admin User
 * Creates a new admin user and updates configuration
 */

require_once __DIR__ . '/config.php';

try {
    // Connect to database
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ":" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "<h2>Setting up Admin User...</h2>";
    
    // Check if admin user exists
    $stmt = $pdo->prepare("SELECT id, username, email FROM users WHERE username = ? OR email = ?");
    $stmt->execute(['admin', 'admin@gmail.com']);
    $existing = $stmt->fetch();
    
    if ($existing) {
        echo "<p style='color: orange;'>⚠️ Admin user already exists:</p>";
        echo "<ul>";
        echo "<li>ID: " . $existing['id'] . "</li>";
        echo "<li>Username: " . $existing['username'] . "</li>";
        echo "<li>Email: " . $existing['email'] . "</li>";
        echo "</ul>";
    } else {
        // Get the next available ID
        $stmt = $pdo->query("SELECT COALESCE(MAX(id), 0) + 1 as next_id FROM users");
        $nextId = $stmt->fetch()['next_id'];
        
        // Insert new admin user (without created_at column)
        $stmt = $pdo->prepare("
            INSERT INTO users (id, name, email, username, password) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$nextId, 'Administrator', 'admin@gmail.com', 'admin', 'admin']);
        
        echo "<p style='color: green;'>✅ Admin user created successfully!</p>";
        echo "<ul>";
        echo "<li>ID: " . $nextId . "</li>";
        echo "<li>Username: admin</li>";
        echo "<li>Email: admin@gmail.com</li>";
        echo "<li>Password: admin</li>";
        echo "</ul>";
    }
    
    // Show current APP_ADMIN_EMAIL setting
    echo "<hr>";
    echo "<h3>Current Configuration:</h3>";
    echo "<p><strong>APP_ADMIN_EMAIL:</strong> " . APP_ADMIN_EMAIL . "</p>";
    
    // Update config.php if needed
    echo "<hr>";
    echo "<h3>Next Steps:</h3>";
    echo "<ol>";
    echo "<li>Update <code>config.php</code> and set: <code>define('APP_ADMIN_EMAIL', 'admin@gmail.com');</code></li>";
    echo "<li>Login with username: <strong>admin</strong> and password: <strong>admin</strong></li>";
    echo "<li>Go to dashboard and click <strong>👑 Admin Panel</strong></li>";
    echo "</ol>";
    
    echo "<hr>";
    echo "<p><a href='login.php' style='padding: 10px 20px; background: #4A90E2; color: white; text-decoration: none; border-radius: 5px;'>Go to Login</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
