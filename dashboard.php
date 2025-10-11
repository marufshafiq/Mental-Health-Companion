<?php
// Only start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug session
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/controllers/JournalController.php';
require_once __DIR__ . '/config.php';

$journalController = new JournalController($_SESSION['user_id']);

// Get mood data directly from database (like API does)
function getMoodHistory($userId, $days = 7) {
    try {
        $db = getDb();
        $sql = "SELECT id, mood_type, mood_value, notes, entry_date, entry_time, created_at 
                FROM mood_entries 
                WHERE user_id = ? 
                AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                ORDER BY created_at DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$userId, $days]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching mood history: " . $e->getMessage());
        return [];
    }
}

function getAverageMood($userId, $days = 7) {
    try {
        $db = getDb();
        $sql = "SELECT AVG(mood_value) as average 
                FROM mood_entries 
                WHERE user_id = ? 
                AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$userId, $days]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['average'] ? round($result['average'], 1) : 3.0;
    } catch (PDOException $e) {
        error_log("Error calculating average mood: " . $e->getMessage());
        return 3.0;
    }
}

$recentEntries = $journalController->getEntries();
$recentMoods = getMoodHistory($_SESSION['user_id'], 7); // Last 7 days
$averageMood = getAverageMood($_SESSION['user_id'], 7);

$name = $_SESSION['name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Mental Health Companion</title>
    <link rel="stylesheet" href="dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Apply sidebar state IMMEDIATELY to prevent flash -->
    <script>
        (function() {
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (isCollapsed) {
                document.documentElement.classList.add('sidebar-collapsed-on-load');
            }
        })();
    </script>
    <style>
        /* Apply collapsed state immediately on page load */
        .sidebar-collapsed-on-load .sidebar {
            width: 80px;
            padding: 2rem 0.5rem;
        }
        .sidebar-collapsed-on-load .sidebar h2,
        .sidebar-collapsed-on-load .sidebar .nav-text {
            opacity: 0;
            width: 0;
        }
        .sidebar-collapsed-on-load .sidebar nav a {
            padding: 1rem 0.5rem;
            justify-content: center;
        }
        .sidebar-collapsed-on-load .main-content {
            margin-left: 80px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header" onclick="toggleSidebar()" title="Click to collapse/expand">
                <div class="sidebar-logo">🧠</div>
                <h2>Mental Health Companion</h2>
            </div>
            <nav>
                <a href="dashboard.php" class="active" data-tooltip="Dashboard">
                    <span class="nav-icon">📊</span>
                    <span class="nav-text">Dashboard</span>
                </a>
                <a href="journal.php" data-tooltip="Journal">
                    <span class="nav-icon">📓</span>
                    <span class="nav-text">Journal</span>
                </a>
                <a href="mood.php" data-tooltip="Mood Tracker">
                    <span class="nav-icon">😊</span>
                    <span class="nav-text">Mood Tracker</span>
                </a>
                <a href="views/chat.php" data-tooltip="AI Chatbot">
                    <span class="nav-icon">💬</span>
                    <span class="nav-text">Chatbot</span>
                </a>
                <a href="views/meditation.php" data-tooltip="Meditation & Resources">
                    <span class="nav-icon">🧘‍♀️</span>
                    <span class="nav-text">Meditation & Resources</span>
                </a>
                <a href="views/profile/profile.php" data-tooltip="Profile">
                    <span class="nav-icon">👤</span>
                    <span class="nav-text">Profile</span>
                </a>
                <a href="logout.php" data-tooltip="Logout">
                    <span class="nav-icon">🚪</span>
                    <span class="nav-text">Logout</span>
                </a>
                <!-- Admin Panel Link (visible only to admin users) -->
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="views/admin.php" data-tooltip="Admin Panel">
                    <span class="nav-icon">👑</span>
                    <span class="nav-text">Admin Panel</span>
                </a>
                <?php endif; ?>
            </nav>
        </div>

        <div class="main-content" id="mainContent">
            <div class="welcome-section">
                <h1>Welcome, <?php echo htmlspecialchars($name); ?> 👋</h1>
                <p class="date"><?php echo date('l, F j, Y'); ?></p>
            </div>

            <div class="dashboard-grid">
                <!-- Quick Mood Entry -->
                <div class="dashboard-card mood-entry">
                    <h3>How are you feeling today?</h3>
                    <div class="mood-quick-select">
                        <div class="mood-option" data-mood="happy" data-value="5">😊</div>
                        <div class="mood-option" data-mood="calm" data-value="4">�</div>
                        <div class="mood-option" data-mood="neutral" data-value="3">😐</div>
                        <div class="mood-option" data-mood="stressed" data-value="2">😰</div>
                        <div class="mood-option" data-mood="sad" data-value="1">😢</div>
                    </div>
                    <button id="recordMoodBtn" disabled>Record Mood</button>
                </div>

                <!-- Mood Overview -->
                <div class="dashboard-card mood-overview">
                    <h3>Your Week in Moods</h3>
                    <canvas id="weeklyMoodChart"></canvas>
                    <p class="mood-average">Weekly Average: 
                        <?php 
                        $moodEmoji = '😐';
                        if ($averageMood >= 4) $moodEmoji = '😊';
                        elseif ($averageMood >= 3) $moodEmoji = '😌';
                        elseif ($averageMood >= 2) $moodEmoji = '😰';
                        elseif ($averageMood >= 1) $moodEmoji = '😢';
                        echo $moodEmoji;
                        ?>
                    </p>
                </div>

                <!-- Recent Journal Entries -->
                <div class="dashboard-card recent-journals">
                    <h3>Recent Journal Entries</h3>
                    <div class="journal-list">
                        <?php foreach(array_slice($recentEntries, 0, 3) as $entry): ?>
                        <div class="journal-item">
                            <h4><?php echo htmlspecialchars($entry['title']); ?></h4>
                            <p><?php echo htmlspecialchars(substr($entry['content'], 0, 100)) . '...'; ?></p>
                            <span class="journal-date"><?php echo date('M j, Y', strtotime($entry['created_at'])); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <a href="journal.php" class="view-all">View All Entries →</a>
                </div>

                <!-- Quick Actions -->
                <div class="dashboard-card quick-actions">
                    <h3>Quick Actions</h3>
                    <div class="action-buttons">
                        <a href="journal.php?type=daily" class="action-btn">
                            � New Journal Entry
                        </a>
                        <a href="mood.php" class="action-btn">
                            📊 View Mood History
                        </a>
                        <a href="chatbot.php" class="action-btn">
                            💬 Talk to Companion
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mood Selection
        const moodOptions = document.querySelectorAll('.mood-option');
        const recordMoodBtn = document.getElementById('recordMoodBtn');
        let selectedMood = null;

        moodOptions.forEach(option => {
            option.addEventListener('click', () => {
                moodOptions.forEach(opt => opt.classList.remove('selected'));
                option.classList.add('selected');
                selectedMood = {
                    type: option.dataset.mood,
                    value: option.dataset.value
                };
                recordMoodBtn.disabled = false;
            });
        });

        // Record Mood
        recordMoodBtn.addEventListener('click', async () => {
            if (!selectedMood) return;

            try {
                const formData = new FormData();
                formData.append('mood_type', selectedMood.type);
                formData.append('mood_value', selectedMood.value);

                const response = await fetch('api/mood_record.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                if (result.success) {
                    alert('Mood recorded successfully!');
                    location.reload(); // Refresh to update charts
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Error recording mood');
            }
        });

        // Weekly Mood Chart
        const ctx = document.getElementById('weeklyMoodChart').getContext('2d');
        const moodData = <?php echo json_encode($recentMoods); ?>;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: moodData.map(entry => new Date(entry.created_at).toLocaleDateString()),
                datasets: [{
                    label: 'Mood Level',
                    data: moodData.map(entry => entry.mood_value),
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        min: 1,
                        max: 5,
                        ticks: {
                            stepSize: 1,
                            callback: function(value) {
                                return ['', 'Sad', 'Stressed', 'Neutral', 'Calm', 'Happy'][value];
                            }
                        }
                    }
                }
            }
        });

        // Sidebar Toggle Functionality
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('sidebar-collapsed');
            
            // Save state to localStorage
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        }

        // Restore sidebar state on page load
        window.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            
            // Remove the temporary class
            document.documentElement.classList.remove('sidebar-collapsed-on-load');
            
            if (isCollapsed) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('sidebar-collapsed');
            }
        });
    </script>
</body>
</html>
