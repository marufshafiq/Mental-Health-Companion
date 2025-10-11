<?php
// Session Debug Test Page
// Access this at: http://localhost/Mental-Health-Companion/session_test.php

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Enable error display
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Debug Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .section {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .section h2 {
            margin-top: 0;
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            border-left: 4px solid #667eea;
        }
        .status {
            padding: 10px 15px;
            border-radius: 5px;
            margin: 10px 0;
            font-weight: bold;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 5px;
        }
        .btn:hover {
            background: #5568d3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background: #667eea;
            color: white;
        }
    </style>
</head>
<body>
    <h1 style="text-align: center; color: #667eea;">🔍 Session Debug Test</h1>

    <!-- Session Status -->
    <div class="section">
        <h2>📊 Session Status</h2>
        <?php
        $sessionStatus = session_status();
        $statusText = [
            PHP_SESSION_DISABLED => 'DISABLED',
            PHP_SESSION_NONE => 'NONE (not started)',
            PHP_SESSION_ACTIVE => 'ACTIVE'
        ];
        
        if ($sessionStatus === PHP_SESSION_ACTIVE) {
            echo "<div class='status success'>✅ Session is ACTIVE</div>";
        } else {
            echo "<div class='status error'>❌ Session is NOT ACTIVE</div>";
        }
        ?>
        <table>
            <tr>
                <th>Property</th>
                <th>Value</th>
            </tr>
            <tr>
                <td>Session Status</td>
                <td><?php echo $statusText[$sessionStatus]; ?></td>
            </tr>
            <tr>
                <td>Session ID</td>
                <td><?php echo session_id() ?: 'No session ID'; ?></td>
            </tr>
            <tr>
                <td>Session Name</td>
                <td><?php echo session_name(); ?></td>
            </tr>
            <tr>
                <td>Session Save Path</td>
                <td><?php echo session_save_path(); ?></td>
            </tr>
        </table>
    </div>

    <!-- Session Data -->
    <div class="section">
        <h2>💾 Session Data</h2>
        <?php if (empty($_SESSION)): ?>
            <div class='status warning'>⚠️ Session is EMPTY - You are NOT logged in</div>
        <?php else: ?>
            <div class='status success'>✅ Session contains data</div>
        <?php endif; ?>
        <pre><?php print_r($_SESSION); ?></pre>
    </div>

    <!-- Authentication Check -->
    <div class="section">
        <h2>🔐 Authentication Check</h2>
        <?php
        $hasUserId = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
        $hasUsername = isset($_SESSION['username']) && !empty($_SESSION['username']);
        $isAuthenticated = $hasUserId && $hasUsername;
        ?>
        <table>
            <tr>
                <th>Check</th>
                <th>Status</th>
            </tr>
            <tr>
                <td>$_SESSION['user_id'] exists</td>
                <td><?php echo $hasUserId ? '✅ YES' : '❌ NO'; ?></td>
            </tr>
            <tr>
                <td>$_SESSION['username'] exists</td>
                <td><?php echo $hasUsername ? '✅ YES' : '❌ NO'; ?></td>
            </tr>
            <tr>
                <td><strong>Authenticated</strong></td>
                <td><strong><?php echo $isAuthenticated ? '✅ YES' : '❌ NO'; ?></strong></td>
            </tr>
        </table>
        
        <?php if ($isAuthenticated): ?>
            <div class='status success'>
                ✅ You are authenticated! Profile page should work.
            </div>
        <?php else: ?>
            <div class='status error'>
                ❌ You are NOT authenticated! Profile page will redirect to login.
            </div>
        <?php endif; ?>
    </div>

    <!-- PHP Info -->
    <div class="section">
        <h2>⚙️ PHP Session Configuration</h2>
        <table>
            <tr>
                <th>Setting</th>
                <th>Value</th>
            </tr>
            <tr>
                <td>session.cookie_lifetime</td>
                <td><?php echo ini_get('session.cookie_lifetime'); ?></td>
            </tr>
            <tr>
                <td>session.cookie_path</td>
                <td><?php echo ini_get('session.cookie_path'); ?></td>
            </tr>
            <tr>
                <td>session.cookie_domain</td>
                <td><?php echo ini_get('session.cookie_domain'); ?></td>
            </tr>
            <tr>
                <td>session.cookie_secure</td>
                <td><?php echo ini_get('session.cookie_secure') ? 'Yes' : 'No'; ?></td>
            </tr>
            <tr>
                <td>session.cookie_httponly</td>
                <td><?php echo ini_get('session.cookie_httponly') ? 'Yes' : 'No'; ?></td>
            </tr>
            <tr>
                <td>session.use_cookies</td>
                <td><?php echo ini_get('session.use_cookies') ? 'Yes' : 'No'; ?></td>
            </tr>
        </table>
    </div>

    <!-- Cookie Check -->
    <div class="section">
        <h2>🍪 Cookie Check</h2>
        <?php if (empty($_COOKIE)): ?>
            <div class='status warning'>⚠️ No cookies found</div>
        <?php else: ?>
            <div class='status success'>✅ Cookies are working</div>
        <?php endif; ?>
        <pre><?php print_r($_COOKIE); ?></pre>
    </div>

    <!-- Action Links -->
    <div class="section">
        <h2>🔗 Test Navigation</h2>
        <a href="login.php" class="btn">Go to Login</a>
        <a href="dashboard.php" class="btn">Go to Dashboard</a>
        <a href="views/profile/profile.php" class="btn">Try Profile Page</a>
        <a href="session_test.php" class="btn">Refresh This Page</a>
        <a href="logout.php" class="btn" style="background: #dc3545;">Logout</a>
    </div>

    <!-- Instructions -->
    <div class="section">
        <h2>📝 Troubleshooting Steps</h2>
        <ol>
            <li><strong>If session is empty:</strong>
                <ul>
                    <li>Go to <a href="login.php">login page</a> and log in</li>
                    <li>Then come back to this page to verify session</li>
                </ul>
            </li>
            <li><strong>If session exists but profile still redirects:</strong>
                <ul>
                    <li>Check if user_id and username are both present</li>
                    <li>Verify session cookie is being sent in browser</li>
                    <li>Clear browser cache and try again</li>
                </ul>
            </li>
            <li><strong>If cookies are not working:</strong>
                <ul>
                    <li>Check browser cookie settings</li>
                    <li>Make sure you're using http://localhost (not file://)</li>
                    <li>Check if session save path is writable</li>
                </ul>
            </li>
        </ol>
    </div>
</body>
</html>
