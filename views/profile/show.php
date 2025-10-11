<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Mental Health Companion</title>
    <link rel="stylesheet" href="../../dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
            transition: margin-left 0.3s ease;
        }

        .main-content.sidebar-collapsed {
            margin-left: 80px;
        }

        .profile-header {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .profile-info {
            display: flex;
            align-items: center;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .profile-image {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #667eea;
        }

        .profile-image-placeholder {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
            border: 4px solid #667eea;
        }

        .profile-details h1 {
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .profile-details p {
            color: #718096;
            margin-bottom: 0.25rem;
        }

        .profile-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #e9ecef;
            color: #495057;
        }

        .btn-secondary:hover {
            background: #dee2e6;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card h3 {
            color: #718096;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: #667eea;
        }

        .stat-icon {
            font-size: 2rem;
            float: right;
            color: #e9ecef;
        }

        /* Content Sections */
        .content-section {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .content-section h2 {
            color: #2d3748;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e9ecef;
        }

        .content-section p {
            color: #718096;
            line-height: 1.8;
            margin-bottom: 1rem;
        }

        .content-section ul {
            list-style-position: inside;
            color: #718096;
            line-height: 2;
        }

        /* Consultants Grid */
        .consultants-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .consultant-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .consultant-card:hover {
            border-color: #667eea;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        }

        .consultant-card h3 {
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .consultant-card .title {
            color: #667eea;
            font-weight: 500;
            margin-bottom: 1rem;
        }

        .consultant-card .bio {
            color: #718096;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            line-height: 1.6;
        }

        .consultant-card .contact {
            color: #495057;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .modal-header h2 {
            color: #2d3748;
            border: none;
            padding: 0;
            margin: 0;
        }

        .close-modal {
            font-size: 2rem;
            cursor: pointer;
            color: #718096;
            border: none;
            background: none;
        }

        .close-modal:hover {
            color: #2d3748;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            color: #2d3748;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .form-group select,
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            transition: border-color 0.3s ease;
        }

        .form-group select:focus,
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        /* Sidebar Collapsed State */
        .sidebar.collapsed {
            width: 80px;
            padding: 2rem 0.5rem;
        }

        .sidebar.collapsed .sidebar-header {
            justify-content: center;
        }

        .sidebar.collapsed h2,
        .sidebar.collapsed .nav-text {
            display: none;
        }

        .sidebar.collapsed nav a {
            justify-content: center;
            padding: 1rem 0.5rem;
        }

        .sidebar.collapsed nav a .nav-icon {
            margin: 0;
        }

        /* Tooltip for collapsed sidebar */
        [data-tooltip] {
            position: relative;
        }

        .sidebar.collapsed [data-tooltip]:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            margin-left: 10px;
            padding: 0.5rem 1rem;
            background: #2d3748;
            color: white;
            border-radius: 6px;
            white-space: nowrap;
            z-index: 1000;
            font-size: 0.9rem;
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
                <a href="../../dashboard.php" data-tooltip="Dashboard">
                    <span class="nav-icon">📊</span>
                    <span class="nav-text">Dashboard</span>
                </a>
                <a href="../../journal.php" data-tooltip="Journal">
                    <span class="nav-icon">📓</span>
                    <span class="nav-text">Journal</span>
                </a>
                <a href="../../mood.php" data-tooltip="Mood Tracker">
                    <span class="nav-icon">😊</span>
                    <span class="nav-text">Mood Tracker</span>
                </a>
                <a href="../chat.php" data-tooltip="AI Chatbot">
                    <span class="nav-icon">💬</span>
                    <span class="nav-text">Chatbot</span>
                </a>
                <a href="../meditation.php" data-tooltip="Meditation & Resources">
                    <span class="nav-icon">🧘‍♀️</span>
                    <span class="nav-text">Meditation & Resources</span>
                </a>
                <a href="profile.php" class="active" data-tooltip="Profile">
                    <span class="nav-icon">👤</span>
                    <span class="nav-text">Profile</span>
                </a>
                <a href="../../logout.php" data-tooltip="Logout">
                    <span class="nav-icon">🚪</span>
                    <span class="nav-text">Logout</span>
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content" id="mainContent">
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-info">
                    <?php if (!empty($user['profile_image'])): ?>
                        <img src="../../assets/uploads/profiles/<?php echo htmlspecialchars($user['profile_image']); ?>" 
                             alt="Profile" class="profile-image">
                    <?php else: ?>
                        <div class="profile-image-placeholder">
                            <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>

                    <div class="profile-details">
                        <h1><?php echo htmlspecialchars($user['name']); ?></h1>
                        <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user['email']); ?></p>
                        <?php if (!empty($user['bio'])): ?>
                            <p><i class="fas fa-info-circle"></i> <?php echo htmlspecialchars($user['bio']); ?></p>
                        <?php endif; ?>
                        
                        <div class="profile-actions">
                            <a href="edit_profile.php" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Edit Profile
                            </a>
                            <a href="#" class="btn btn-secondary">
                                <i class="fas fa-key"></i> Change Password
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <span class="stat-icon">📓</span>
                    <h3>Journal Entries</h3>
                    <div class="stat-value"><?php echo $counts['journal_count']; ?></div>
                </div>
                <div class="stat-card">
                    <span class="stat-icon">😊</span>
                    <h3>Mood Logs</h3>
                    <div class="stat-value"><?php echo $counts['mood_count']; ?></div>
                </div>
                <div class="stat-card">
                    <span class="stat-icon">💬</span>
                    <h3>Messages</h3>
                    <div class="stat-value"><?php echo $counts['message_count']; ?></div>
                </div>
            </div>

            <!-- CBT Information Panel -->
            <div class="content-section">
                <h2>🧠 Cognitive Behavioral Therapy (CBT)</h2>
                <p>
                    Cognitive Behavioral Therapy is a proven, evidence-based approach to mental health treatment 
                    that helps you understand the connection between your thoughts, feelings, and behaviors.
                </p>
                <h3 style="color: #667eea; margin-top: 1.5rem; margin-bottom: 1rem;">Benefits of CBT:</h3>
                <ul>
                    <li>Helps identify and challenge negative thought patterns</li>
                    <li>Provides practical strategies for managing stress and anxiety</li>
                    <li>Improves problem-solving skills and coping mechanisms</li>
                    <li>Reduces symptoms of depression and anxiety disorders</li>
                    <li>Empowers you with lifelong mental wellness tools</li>
                </ul>
                <h3 style="color: #667eea; margin-top: 1.5rem; margin-bottom: 1rem;">Core Principles:</h3>
                <ul>
                    <li><strong>Identify:</strong> Recognize unhelpful thinking patterns</li>
                    <li><strong>Challenge:</strong> Question the validity of negative thoughts</li>
                    <li><strong>Replace:</strong> Develop more balanced, realistic perspectives</li>
                    <li><strong>Practice:</strong> Apply new thinking patterns in daily life</li>
                </ul>
                <p style="margin-top: 1.5rem;">
                    Our mental health consultants are trained in CBT techniques and can guide you through 
                    personalized sessions tailored to your specific needs.
                </p>
            </div>

            <!-- Mental Health Consultants -->
            <div class="content-section">
                <h2>👨‍⚕️ Our Mental Health Consultants</h2>
                <p style="margin-bottom: 2rem;">
                    Connect with our experienced mental health professionals for personalized support and guidance.
                </p>

                <div class="consultants-grid">
                    <?php foreach ($consultants as $consultant): ?>
                    <div class="consultant-card">
                        <h3><?php echo htmlspecialchars($consultant['name'] ?? ''); ?></h3>
                        <div class="title"><?php echo htmlspecialchars($consultant['title'] ?? ''); ?></div>
                        <div class="bio"><?php echo htmlspecialchars($consultant['bio'] ?? 'No bio available'); ?></div>
                        <div class="contact">
                            <i class="fas fa-phone"></i> <?php echo htmlspecialchars($consultant['contact_info'] ?? 'Contact info not available'); ?>
                        </div>
                        <button class="btn btn-primary" style="width: 100%; margin-top: 1rem;" 
                                onclick="openDemoModal(<?php echo $consultant['id']; ?>, '<?php echo htmlspecialchars($consultant['name'] ?? ''); ?>')">
                            <i class="fas fa-calendar-check"></i> Book Demo Session
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Demo Request Modal -->
    <div id="demoModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Book Demo Session</h2>
                <button class="close-modal" onclick="closeDemoModal()">&times;</button>
            </div>
            <form id="demoForm" action="../../public/demo_request.php" method="POST">
                <input type="hidden" id="consultantId" name="consultant_id" value="">
                
                <div class="form-group">
                    <label>Consultant</label>
                    <input type="text" id="consultantName" readonly style="background: #f8f9fa;">
                </div>

                <div class="form-group">
                    <label>Preferred Date & Time</label>
                    <input type="datetime-local" name="preferred_datetime" required>
                </div>

                <div class="form-group">
                    <label>Message / Concerns</label>
                    <textarea name="message" placeholder="Tell us briefly what you'd like to discuss..." required></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-paper-plane"></i> Submit Request
                </button>
            </form>
        </div>
    </div>

    <script>
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

        // Demo Modal Functions
        function openDemoModal(consultantId, consultantName) {
            document.getElementById('consultantId').value = consultantId;
            document.getElementById('consultantName').value = consultantName;
            document.getElementById('demoModal').classList.add('active');
        }

        function closeDemoModal() {
            document.getElementById('demoModal').classList.remove('active');
            document.getElementById('demoForm').reset();
        }

        // Close modal on outside click
        document.getElementById('demoModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDemoModal();
            }
        });
    </script>
</body>
</html>
