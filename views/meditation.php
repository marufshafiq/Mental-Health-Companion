<?php
// File: views/meditation.php

// Initialize controller if variables not set
if (!isset($resources) || !isset($tip)) {
    require_once __DIR__ . '/../controllers/MeditationController.php';
    $controller = new MeditationController();
    $controller->index();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meditation Resources - Mental Health Companion</title>
    <link rel="stylesheet" href="../dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        
        /* Tip of the Day Section - Enhanced Therapeutic Design */
        .tip-of-day {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 0;
            border-radius: 20px;
            margin-bottom: 2rem;
            box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3), 
                        0 0 0 1px rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
            animation: gentleGlow 3s ease-in-out infinite;
        }

        @keyframes gentleGlow {
            0%, 100% {
                box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3), 
                            0 0 0 1px rgba(255, 255, 255, 0.1);
            }
            50% {
                box-shadow: 0 10px 50px rgba(102, 126, 234, 0.5), 
                            0 0 0 1px rgba(255, 255, 255, 0.2);
            }
        }

        .tip-of-day::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: shimmer 8s linear infinite;
        }

        @keyframes shimmer {
            0% {
                transform: translate(-50%, -50%) rotate(0deg);
            }
            100% {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

        .tip-content {
            position: relative;
            z-index: 1;
            padding: 2.5rem;
        }

        .tip-of-day h2 {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            font-size: 1.5rem;
            color: var(--tip-text);
            margin-bottom: 1.5rem;
            font-weight: 600;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            letter-spacing: 0.5px;
        }

        .tip-of-day h2 i {
            font-size: 2rem;
            animation: pulse 2s ease-in-out infinite;
            filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.5));
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }
        }

        .tip-of-day p {
            font-size: 1.25rem;
            color: var(--tip-text);
            line-height: 2;
            text-align: center;
            font-weight: 400;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            padding: 1rem 2rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
        }

        .tip-of-day p::before {
            content: '"';
            position: absolute;
            top: -10px;
            left: 20px;
            font-size: 4rem;
            color: rgba(255, 255, 255, 0.3);
            font-family: Georgia, serif;
            line-height: 1;
        }

        .tip-of-day p::after {
            content: '"';
            position: absolute;
            bottom: -30px;
            right: 20px;
            font-size: 4rem;
            color: rgba(255, 255, 255, 0.3);
            font-family: Georgia, serif;
            line-height: 1;
        }

        /* Decorative elements */
        .tip-decorations {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
            z-index: 0;
        }

        .tip-decorations::before,
        .tip-decorations::after {
            content: '✨';
            position: absolute;
            font-size: 1.5rem;
            opacity: 0.6;
            animation: float 3s ease-in-out infinite;
        }

        .tip-decorations::before {
            top: 20px;
            left: 30px;
            animation-delay: 0s;
        }

        .tip-decorations::after {
            bottom: 20px;
            right: 30px;
            animation-delay: 1.5s;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        /* Resources Section */
        .resources-section {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .resources-section h2 {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.5rem;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        .resources-section h2 i {
            color: var(--primary-color);
        }

        .resource-list {
            display: grid;
            gap: 1.5rem;
        }

        .resource-card {
            background: var(--background-color);
            border-radius: 8px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
        }

        .resource-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-color: var(--primary-color);
        }

        .resource-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
        }

        .resource-title a {
            color: var(--primary-color);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .resource-title a:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }

        .resource-title a i {
            font-size: 0.875rem;
        }

        .resource-meta {
            display: flex;
            gap: 1rem;
            margin-bottom: 0.75rem;
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .resource-meta span {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .resource-meta i {
            color: var(--primary-color);
        }

        .resource-description {
            color: var(--text-secondary);
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--text-secondary);
        }

        .empty-state i {
            font-size: 3rem;
            color: var(--text-secondary);
            opacity: 0.5;
            margin-bottom: 1rem;
        }

        .empty-state p {
            font-size: 1.1rem;
        }

        @media (max-width: 768px) {
            .tip-of-day h2 {
                font-size: 1.25rem;
            }

            .tip-of-day p {
                font-size: 1.1rem;
                padding: 1rem;
                line-height: 1.8;
            }

            .tip-content {
                padding: 1.5rem;
            }

            .tip-of-day p::before,
            .tip-of-day p::after {
                font-size: 3rem;
            }

            .tip-decorations::before,
            .tip-decorations::after {
                font-size: 1rem;
            }
        }

        /* Additional calming elements */
        @keyframes breathe {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.02);
            }
        }

        .tip-of-day:hover {
            animation: breathe 4s ease-in-out infinite;
        }

        /* Accessibility improvements */
        .tip-of-day:focus-within {
            outline: 3px solid rgba(255, 255, 255, 0.5);
            outline-offset: 3px;
        }

        /* Add subtle pattern overlay */
        .tip-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }
    </style>
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
                <a href="../dashboard.php" data-tooltip="Dashboard">
                    <span class="nav-icon">📊</span>
                    <span class="nav-text">Dashboard</span>
                </a>
                <a href="../journal.php" data-tooltip="Journal">
                    <span class="nav-icon">📓</span>
                    <span class="nav-text">Journal</span>
                </a>
                <a href="../mood.php" data-tooltip="Mood Tracker">
                    <span class="nav-icon">😊</span>
                    <span class="nav-text">Mood Tracker</span>
                </a>
                <a href="chat.php" data-tooltip="AI Chatbot">
                    <span class="nav-icon">💬</span>
                    <span class="nav-text">AI Chatbot</span>
                </a>
                <a href="meditation.php" class="active" data-tooltip="Meditation & Resources">
                    <span class="nav-icon">🧘‍♀️</span>
                    <span class="nav-text">Meditation & Resources</span>
                </a>
                <a href="../profile.php" data-tooltip="Profile">
                    <span class="nav-icon">👤</span>
                    <span class="nav-text">Profile</span>
                </a>
                <a href="../logout.php" data-tooltip="Logout">
                    <span class="nav-icon">🚪</span>
                    <span class="nav-text">Logout</span>
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content" id="mainContent">
            <div class="welcome-section">
                <h1><i class="fas fa-spa"></i> Meditation & Wellness Resources</h1>
                <p>Discover curated meditation guides and mental health resources</p>
            </div>

            <!-- Tip of the Day Section -->
            <aside class="tip-of-day">
            <div class="tip-pattern"></div>
            <div class="tip-decorations"></div>
            <div class="tip-content">
                <h2>
                    <i class="fas fa-heart"></i>
                    💫 Daily Wellness Tip 💫
                </h2>
                <p><?php echo htmlspecialchars($tip); ?></p>
            </div>
        </aside>

        <!-- Resources Section -->
        <section class="resources-section">
            <h2>
                <i class="fas fa-book-open"></i>
                Available Resources
            </h2>

            <?php if (empty($resources)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>No resources available at the moment.</p>
                </div>
            <?php else: ?>
                <div class="resource-list">
                    <?php foreach ($resources as $resource): ?>
                        <div class="resource-card">
                            <div class="resource-title">
                                <a href="<?php echo htmlspecialchars($resource['url']); ?>" target="_blank" rel="noopener noreferrer">
                                    <?php echo htmlspecialchars($resource['title']); ?>
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                            
                            <div class="resource-meta">
                                <?php if (!empty($resource['source'])): ?>
                                    <span>
                                        <i class="fas fa-tag"></i>
                                        <?php echo htmlspecialchars($resource['source']); ?>
                                    </span>
                                <?php endif; ?>
                                
                                <?php if (!empty($resource['created_at'])): ?>
                                    <span>
                                        <i class="fas fa-calendar"></i>
                                        <?php echo date('M d, Y', strtotime($resource['created_at'])); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <?php if (!empty($resource['description'])): ?>
                                <div class="resource-description">
                                    <?php echo htmlspecialchars($resource['description']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
        </div><!-- End main-content -->
    </div><!-- End container -->

    <!-- Sidebar Toggle Functionality -->
    <script>
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
        window.addEventListener('DOMContentLoaded', () => {
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
