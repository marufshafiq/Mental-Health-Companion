<?php
session_start();
header('Content-Type: text/plain');
echo "=== SESSION DEBUG ===\n\n";
echo "Session ID: " . session_id() . "\n\n";
echo "Session Contents:\n";
print_r($_SESSION);
echo "\n\n=== CONFIG CHECK ===\n";
require_once __DIR__ . '/config.php';
echo "APP_ADMIN_EMAIL: " . (defined('APP_ADMIN_EMAIL') ? APP_ADMIN_EMAIL : 'NOT DEFINED') . "\n";
echo "\n=== ADMIN CHECK ===\n";
if (isset($_SESSION['email']) && defined('APP_ADMIN_EMAIL')) {
    echo "Session Email: " . $_SESSION['email'] . "\n";
    echo "Match: " . ($_SESSION['email'] === APP_ADMIN_EMAIL ? 'YES' : 'NO') . "\n";
} else {
    echo "Cannot check - email or constant not set\n";
}
?>
