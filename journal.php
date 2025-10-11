<?php
// File: journal.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/controllers/JournalController.php';
require_once __DIR__ . '/services/JournalFactory.php';
require_once __DIR__ . '/services/SentimentAnalysisService.php';

$controller = new JournalController($_SESSION['user_id']);

// Get parameters
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$type = isset($_GET['type']) ? $_GET['type'] : null;
$entryId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch data based on action
$entries = [];
$entry = null;
$stats = $controller->getStats();
$journalTypes = JournalFactory::getAvailableTypes();

if ($action === 'list' || $action === 'create') {
    $entries = $controller->getEntries(null, $type);
} elseif ($action === 'edit' && $entryId > 0) {
    $entry = $controller->getEntry($entryId);
    if (!$entry) {
        header("Location: journal.php");
        exit();
    }
}

// Calculate sentiment trend
$sentimentTrend = [];
if (!empty($entries)) {
    $sentimentTrend = SentimentAnalysisService::getTrend(array_slice($entries, 0, 10));
}

$name = $_SESSION['name'] ?? $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journal - Mental Health Companion</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="journal.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header" onclick="toggleSidebar()" title="Click to collapse/expand">
                <div class="sidebar-logo">🧠</div>
                <h2>Mental Health Companion</h2>
            </div>
            <nav>
                <a href="dashboard.php" data-tooltip="Dashboard">
                    <span class="nav-icon">📊</span>
                    <span class="nav-text">Dashboard</span>
                </a>
                <a href="journal.php" class="active" data-tooltip="Journal">
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
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="views/admin.php" data-tooltip="Admin Panel">
                    <span class="nav-icon">👑</span>
                    <span class="nav-text">Admin Panel</span>
                </a>
                <?php endif; ?>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content" id="mainContent">
            <!-- Header -->
            <div class="page-header">
                <div class="header-content">
                    <div>
                        <h1>📓 My Journal</h1>
                        <p>Write your thoughts and track your emotional journey</p>
                    </div>
                    <div class="header-actions">
                        <button class="btn-secondary" onclick="showStats()">
                            <i class="fas fa-chart-bar"></i> Stats
                        </button>
                        <button class="btn-primary" onclick="showNewEntryModal()">
                            <i class="fas fa-plus"></i> New Entry
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #4CAF50;">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo $stats['total_entries'] ?? 0; ?></div>
                        <div class="stat-label">Total Entries</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #2196F3;">
                        <i class="fas fa-file-word"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo number_format($stats['total_words'] ?? 0); ?></div>
                        <div class="stat-label">Words Written</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #FF9800;">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo $stats['favorites'] ?? 0; ?></div>
                        <div class="stat-label">Favorites</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #9C27B0;">
                        <i class="fas fa-smile"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value">
                            <?php 
                            if (!empty($entries)) {
                                $avgSentiment = SentimentAnalysisService::getAverageSentiment($entries);
                                if ($avgSentiment >= 0.5) echo '😊';
                                elseif ($avgSentiment >= 0) echo '🙂';
                                else echo '😕';
                            } else {
                                echo '😐';
                            }
                            ?>
                        </div>
                        <div class="stat-label">Mood Trend</div>
                    </div>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="filter-tabs">
                <button class="tab-btn <?php echo !$type ? 'active' : ''; ?>" onclick="filterByType(null)">
                    <i class="fas fa-list"></i> All Entries
                </button>
                <?php foreach ($journalTypes as $key => $journalType): ?>
                <button class="tab-btn <?php echo $type === $key ? 'active' : ''; ?>" 
                        onclick="filterByType('<?php echo $key; ?>')"
                        style="border-color: <?php echo $journalType['color']; ?>;">
                    <?php echo $journalType['icon']; ?> <?php echo $journalType['name']; ?>
                </button>
                <?php endforeach; ?>
            </div>

            <!-- Sentiment Trend Chart -->
            <?php if (!empty($sentimentTrend)): ?>
            <div class="chart-container">
                <h3><i class="fas fa-chart-line"></i> Emotional Trend</h3>
                <canvas id="sentimentChart"></canvas>
            </div>
            <?php endif; ?>

            <!-- Journal Entries -->
            <div class="journal-grid">
                <?php if (empty($entries)): ?>
                <div class="empty-state">
                    <i class="fas fa-book-open"></i>
                    <h3>No journal entries yet</h3>
                    <p>Start writing your first entry to track your emotional journey</p>
                    <button class="btn-primary" onclick="showNewEntryModal()">
                        <i class="fas fa-plus"></i> Create First Entry
                    </button>
                </div>
                <?php else: ?>
                    <?php foreach ($entries as $journalEntry): 
                        $sentiment = SentimentAnalysisService::analyze($journalEntry['content']);
                    ?>
                    <div class="journal-card" data-entry-id="<?php echo $journalEntry['id']; ?>">
                        <div class="journal-card-header">
                            <div class="journal-title">
                                <h3><?php echo htmlspecialchars($journalEntry['title']); ?></h3>
                                <span class="journal-date">
                                    <i class="far fa-calendar"></i>
                                    <?php echo date('F j, Y', strtotime($journalEntry['created_at'])); ?>
                                </span>
                            </div>
                            <div class="journal-actions">
                                <button class="icon-btn" onclick="toggleFavorite(<?php echo $journalEntry['id']; ?>)" 
                                        title="<?php echo $journalEntry['is_favorite'] ? 'Remove from favorites' : 'Add to favorites'; ?>">
                                    <i class="fa<?php echo $journalEntry['is_favorite'] ? 's' : 'r'; ?> fa-star"></i>
                                </button>
                                <button class="icon-btn" onclick="editEntry(<?php echo $journalEntry['id']; ?>)" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="icon-btn" onclick="deleteEntry(<?php echo $journalEntry['id']; ?>)" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="journal-content">
                            <?php echo nl2br(htmlspecialchars(substr($journalEntry['content'], 0, 200))); ?>
                            <?php if (strlen($journalEntry['content']) > 200): ?>
                                <span class="read-more" onclick="viewEntry(<?php echo $journalEntry['id']; ?>)">... Read more</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="journal-footer">
                            <div class="journal-meta">
                                <span class="word-count">
                                    <i class="fas fa-file-word"></i> <?php echo $journalEntry['word_count']; ?> words
                                </span>
                                <span class="sentiment-badge" style="background: <?php echo $sentiment['mood_color']; ?>;">
                                    <?php echo $sentiment['mood_emoji']; ?> <?php echo $sentiment['mood_label']; ?>
                                </span>
                            </div>
                            <?php if ($journalEntry['tags']): ?>
                            <div class="journal-tags">
                                <?php 
                                $tags = explode(',', $journalEntry['tags']);
                                foreach ($tags as $tag): 
                                    $tag = trim($tag);
                                    if (isset($journalTypes[$tag])):
                                ?>
                                <span class="tag" style="background: <?php echo $journalTypes[$tag]['color']; ?>20; color: <?php echo $journalTypes[$tag]['color']; ?>;">
                                    <?php echo $journalTypes[$tag]['icon']; ?> <?php echo $journalTypes[$tag]['name']; ?>
                                </span>
                                <?php endif; endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- New/Edit Entry Modal -->
    <div id="entryModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">
                    <i class="fas fa-edit"></i> <span id="modalTitleText">New Journal Entry</span>
                </h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            
            <div class="journal-type-selector">
                <p>Choose journal type:</p>
                <div class="type-buttons">
                    <?php foreach ($journalTypes as $key => $journalType): ?>
                    <button class="type-btn" data-type="<?php echo $key; ?>" 
                            style="border-color: <?php echo $journalType['color']; ?>;"
                            onclick="selectJournalType('<?php echo $key; ?>')">
                        <span class="type-icon"><?php echo $journalType['icon']; ?></span>
                        <span class="type-name"><?php echo $journalType['name']; ?></span>
                        <span class="type-desc"><?php echo $journalType['description']; ?></span>
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <form id="journalForm">
                <input type="hidden" id="entryId" name="entry_id">
                <input type="hidden" id="journalType" name="type" value="daily">
                
                <div class="form-group">
                    <label for="entryTitle">
                        <i class="fas fa-heading"></i> Title
                    </label>
                    <input type="text" id="entryTitle" name="title" required 
                           placeholder="Give your entry a title...">
                </div>
                
                <div id="promptsSection" class="prompts-section">
                    <!-- Prompts will be inserted here by JavaScript -->
                </div>
                
                <div class="form-group">
                    <label for="entryContent">
                        <i class="fas fa-pen"></i> Content
                    </label>
                    <textarea id="entryContent" name="content" rows="10" required
                              placeholder="Write your thoughts..."></textarea>
                    <div class="content-info">
                        <span id="wordCount">0 words</span>
                        <button type="button" class="btn-text" onclick="analyzeSentiment()">
                            <i class="fas fa-brain"></i> Analyze Sentiment
                        </button>
                    </div>
                </div>
                
                <div id="sentimentResult" class="sentiment-result" style="display: none;">
                    <!-- Sentiment analysis will be shown here -->
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="closeModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Save Entry
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Entry Modal -->
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="viewTitle"></h2>
                <button class="close-btn" onclick="closeViewModal()">&times;</button>
            </div>
            <div id="viewContent" class="view-content">
                <!-- Full entry content will be displayed here -->
            </div>
        </div>
    </div>

    <script src="journal.js"></script>
    <script>
        // Initialize sentiment chart
        <?php if (!empty($sentimentTrend)): ?>
        const sentimentData = <?php echo json_encode($sentimentTrend); ?>;
        const ctx = document.getElementById('sentimentChart').getContext('2d');
        sentimentChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: sentimentData.map(d => new Date(d.date).toLocaleDateString()),
                datasets: [{
                    label: 'Sentiment Score',
                    data: sentimentData.map(d => d.sentiment),
                    borderColor: '#4CAF50',
                    backgroundColor: 'rgba(76, 175, 80, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        min: -1,
                        max: 1,
                        ticks: {
                            callback: function(value) {
                                if (value >= 0.6) return '😊 Very Positive';
                                if (value >= 0.2) return '🙂 Positive';
                                if (value >= -0.2) return '😐 Neutral';
                                if (value >= -0.6) return '😕 Negative';
                                return '😢 Very Negative';
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
        <?php endif; ?>

        // Sidebar toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        }

        // Restore sidebar state
        window.addEventListener('DOMContentLoaded', function() {
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (isCollapsed) {
                document.getElementById('sidebar').classList.add('collapsed');
                document.getElementById('mainContent').classList.add('sidebar-collapsed');
            }
        });
    </script>
</body>
</html>
