<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feature 6 - Profile Implementation Test</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem;
            margin: 0;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        h1 {
            color: #667eea;
            text-align: center;
            margin-bottom: 2rem;
        }
        .test-section {
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        .test-section h2 {
            color: #333;
            margin-top: 0;
            font-size: 1.2rem;
        }
        .status {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: bold;
            margin-left: 1rem;
        }
        .status.success {
            background: #d4edda;
            color: #155724;
        }
        .status.error {
            background: #f8d7da;
            color: #721c24;
        }
        .file-list {
            list-style: none;
            padding: 0;
        }
        .file-list li {
            padding: 0.5rem;
            margin: 0.5rem 0;
            background: white;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .file-path {
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            color: #495057;
        }
        .check {
            color: #28a745;
            font-weight: bold;
        }
        .table-info {
            margin-top: 1rem;
        }
        .table-info table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }
        .table-info th,
        .table-info td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        .table-info th {
            background: #667eea;
            color: white;
            font-weight: 600;
        }
        .table-info tr:last-child td {
            border-bottom: none;
        }
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 0.5rem;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #5568d3;
        }
        .action-buttons {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 2px solid #e9ecef;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>✅ Feature 6: User Profile - Implementation Test</h1>

        <!-- File Verification -->
        <div class="test-section">
            <h2>📁 Required Files Verification</h2>
            <ul class="file-list">
                <?php
                $requiredFiles = [
                    'controllers/ProfileController.php' => 'MVC Controller',
                    'views/profile/profile.php' => 'Profile View (Router)',
                    'public/profile_update.php' => 'Profile Update Handler',
                    'public/demo_request.php' => 'Demo Request Handler',
                    'database/sql/05_create_consultants_and_demo_requests.sql' => 'Database Migration'
                ];

                $allFilesExist = true;
                foreach ($requiredFiles as $file => $description) {
                    $exists = file_exists(__DIR__ . '/' . $file);
                    $allFilesExist = $allFilesExist && $exists;
                    echo "<li>";
                    echo "<span><span class='check'>" . ($exists ? "✓" : "✗") . "</span> $description</span>";
                    echo "<span class='file-path'>$file</span>";
                    echo "</li>";
                }
                ?>
            </ul>
            <?php if ($allFilesExist): ?>
                <span class="status success">ALL FILES PRESENT ✅</span>
            <?php else: ?>
                <span class="status error">MISSING FILES ❌</span>
            <?php endif; ?>
        </div>

        <!-- Database Connection Test -->
        <div class="test-section">
            <h2>🗄️ Database Connection Test</h2>
            <?php
            try {
                require_once __DIR__ . '/config.php';
                $db = getDb();
                echo "<span class='status success'>Database Connected ✅</span>";
                echo "<p style='margin-top:1rem; color:#666;'>Connection to MySQL database 'isd' on port 3307 successful.</p>";
            } catch (Exception $e) {
                echo "<span class='status error'>Connection Failed ❌</span>";
                echo "<p style='margin-top:1rem; color:#dc3545;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
            ?>
        </div>

        <!-- Tables Verification -->
        <div class="test-section">
            <h2>📊 Database Tables Verification</h2>
            <?php
            try {
                $db = getDb();
                
                // Check users table columns
                $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'bio'");
                $bioExists = $stmt->fetch() !== false;
                
                $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'profile_image'");
                $imageExists = $stmt->fetch() !== false;
                
                // Check consultants table
                $stmt = $db->query("SHOW TABLES LIKE 'consultants'");
                $consultantsExists = $stmt->fetch() !== false;
                
                // Check demo_requests table
                $stmt = $db->query("SHOW TABLES LIKE 'demo_requests'");
                $demoRequestsExists = $stmt->fetch() !== false;
                
                echo "<ul class='file-list'>";
                echo "<li><span><span class='check'>" . ($bioExists ? "✓" : "✗") . "</span> users.bio column</span></li>";
                echo "<li><span><span class='check'>" . ($imageExists ? "✓" : "✗") . "</span> users.profile_image column</span></li>";
                echo "<li><span><span class='check'>" . ($consultantsExists ? "✓" : "✗") . "</span> consultants table</span></li>";
                echo "<li><span><span class='check'>" . ($demoRequestsExists ? "✓" : "✗") . "</span> demo_requests table</span></li>";
                echo "</ul>";
                
                if ($bioExists && $imageExists && $consultantsExists && $demoRequestsExists) {
                    echo "<span class='status success'>ALL TABLES READY ✅</span>";
                } else {
                    echo "<span class='status error'>TABLES INCOMPLETE ❌</span>";
                }
                
            } catch (Exception $e) {
                echo "<span class='status error'>Verification Failed ❌</span>";
                echo "<p style='margin-top:1rem; color:#dc3545;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
            ?>
        </div>

        <!-- Consultants Data -->
        <div class="test-section">
            <h2>👨‍⚕️ Mental Health Consultants</h2>
            <?php
            try {
                $db = getDb();
                $stmt = $db->query("SELECT id, name, title FROM consultants ORDER BY id");
                $consultants = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if (count($consultants) > 0) {
                    echo "<span class='status success'>" . count($consultants) . " Consultants Available ✅</span>";
                    echo "<div class='table-info'>";
                    echo "<table>";
                    echo "<thead><tr><th>ID</th><th>Name</th><th>Title</th></tr></thead>";
                    echo "<tbody>";
                    foreach ($consultants as $consultant) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($consultant['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($consultant['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($consultant['title']) . "</td>";
                        echo "</tr>";
                    }
                    echo "</tbody></table>";
                    echo "</div>";
                } else {
                    echo "<span class='status error'>No Consultants Found ❌</span>";
                    echo "<p style='margin-top:1rem; color:#dc3545;'>Please run the SQL migration to insert consultant data.</p>";
                }
                
            } catch (Exception $e) {
                echo "<span class='status error'>Query Failed ❌</span>";
                echo "<p style='margin-top:1rem; color:#dc3545;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
            ?>
        </div>

        <!-- Upload Directory Check -->
        <div class="test-section">
            <h2>📂 Upload Directory Verification</h2>
            <?php
            $uploadDir = __DIR__ . '/assets/uploads/profiles';
            $htaccessFile = $uploadDir . '/.htaccess';
            
            $dirExists = is_dir($uploadDir);
            $dirWritable = $dirExists && is_writable($uploadDir);
            $htaccessExists = file_exists($htaccessFile);
            
            echo "<ul class='file-list'>";
            echo "<li><span><span class='check'>" . ($dirExists ? "✓" : "✗") . "</span> Directory exists</span><span class='file-path'>$uploadDir</span></li>";
            echo "<li><span><span class='check'>" . ($dirWritable ? "✓" : "✗") . "</span> Directory writable</span></li>";
            echo "<li><span><span class='check'>" . ($htaccessExists ? "✓" : "✗") . "</span> .htaccess protection</span></li>";
            echo "</ul>";
            
            if ($dirExists && $dirWritable && $htaccessExists) {
                echo "<span class='status success'>Upload Directory Ready ✅</span>";
            } else {
                echo "<span class='status error'>Configuration Needed ❌</span>";
            }
            ?>
        </div>

        <!-- Controller Check -->
        <div class="test-section">
            <h2>🎮 ProfileController Class Check</h2>
            <?php
            try {
                require_once __DIR__ . '/controllers/ProfileController.php';
                
                if (class_exists('ProfileController')) {
                    echo "<span class='status success'>ProfileController Class Loaded ✅</span>";
                    
                    $reflection = new ReflectionClass('ProfileController');
                    $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
                    
                    echo "<p style='margin-top:1rem; color:#666;'><strong>Public Methods:</strong></p>";
                    echo "<ul class='file-list'>";
                    foreach ($methods as $method) {
                        if ($method->class === 'ProfileController') {
                            echo "<li><span class='check'>✓</span> " . $method->getName() . "()</li>";
                        }
                    }
                    echo "</ul>";
                } else {
                    echo "<span class='status error'>Class Not Found ❌</span>";
                }
                
            } catch (Exception $e) {
                echo "<span class='status error'>Load Failed ❌</span>";
                echo "<p style='margin-top:1rem; color:#dc3545;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
            ?>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <h2 style="color:#333; margin-bottom:1rem;">🚀 Next Steps</h2>
            <a href="login.php" class="btn">Go to Login</a>
            <a href="dashboard.php" class="btn">Go to Dashboard</a>
            <a href="views/profile/profile.php" class="btn">View Profile (Requires Login)</a>
        </div>

        <div style="text-align:center; margin-top:2rem; padding-top:1rem; border-top:1px solid #dee2e6; color:#6c757d; font-size:0.9rem;">
            <p><strong>Feature 6: User Profile</strong> - Implementation Complete ✅</p>
            <p>Developed by: Iftiaq Hossen | Date: October 11, 2025</p>
        </div>
    </div>
</body>
</html>
