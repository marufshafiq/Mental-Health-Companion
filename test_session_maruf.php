<?php
session_start();

echo "<h2>Session Debug for Profile Access</h2>";
echo "<hr>";

echo "<h3>Current Session Data:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>Session Status:</h3>";
echo "<ul>";
echo "<li>Session ID: " . session_id() . "</li>";
echo "<li>Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? 'ACTIVE' : 'INACTIVE') . "</li>";
echo "</ul>";

echo "<h3>Authentication Check:</h3>";
echo "<ul>";
echo "<li>user_id isset: " . (isset($_SESSION['user_id']) ? 'YES' : 'NO') . "</li>";
echo "<li>user_id value: " . ($_SESSION['user_id'] ?? 'NOT SET') . "</li>";
echo "<li>username isset: " . (isset($_SESSION['username']) ? 'YES' : 'NO') . "</li>";
echo "<li>username value: " . ($_SESSION['username'] ?? 'NOT SET') . "</li>";
echo "<li>name isset: " . (isset($_SESSION['name']) ? 'YES' : 'NO') . "</li>";
echo "<li>name value: " . ($_SESSION['name'] ?? 'NOT SET') . "</li>";
echo "<li>email isset: " . (isset($_SESSION['email']) ? 'YES' : 'NO') . "</li>";
echo "<li>email value: " . ($_SESSION['email'] ?? 'NOT SET') . "</li>";
echo "</ul>";

echo "<h3>Profile Access Check:</h3>";
$canAccessProfile = isset($_SESSION['user_id']) && isset($_SESSION['username']);
echo "<p style='color: " . ($canAccessProfile ? 'green' : 'red') . "; font-weight: bold;'>";
echo $canAccessProfile ? "✅ CAN ACCESS PROFILE" : "❌ CANNOT ACCESS PROFILE - Will redirect to login";
echo "</p>";

if ($canAccessProfile) {
    echo "<p><a href='views/profile/profile.php' style='padding: 10px 20px; background: #4A90E2; color: white; text-decoration: none; border-radius: 5px;'>Go to Profile</a></p>";
}

echo "<hr>";
echo "<p><a href='logout.php'>Logout</a> | <a href='login.php'>Login</a> | <a href='dashboard.php'>Dashboard</a></p>";
?>
