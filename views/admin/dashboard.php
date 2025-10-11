<?php
// File: views/admin/dashboard.php

// Session is already started in AdminController
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

$name = $_SESSION['name'] ?? $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Mental Health Companion</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-color: #4A90E2;
            --secondary-color: #5C6BC0;
            --background-color: #F5F7FA;
            --text-primary: #2C3E50;
            --text-secondary: #606F7B;
            --success-color: #68D391;
            --warning-color: #F6AD55;
            --danger-color: #FC8181;
            --card-bg: #FFFFFF;
            --sidebar-width: 250px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--background-color);
            color: var(--text-primary);
            overflow-x: hidden;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* Main Content Area */
        .main-content {
            flex: 1;
            padding: 2rem 2.5rem;
            margin-left: 0;
            background: var(--background-color);
            max-width: 100%;
            width: 100%;
        }

        .admin-header {
            background: linear-gradient(135deg, #F6AD55, #FC8181);
            color: white;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .admin-header i {
            font-size: 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 1rem;
        }

        .stat-icon.users {
            background: linear-gradient(135deg, #4A90E2, #5C6BC0);
        }

        .stat-icon.active {
            background: linear-gradient(135deg, #68D391, #4FD1C5);
        }

        .stat-icon.journals {
            background: linear-gradient(135deg, #F6AD55, #F59E0B);
        }

        .stat-icon.moods {
            background: linear-gradient(135deg, #FC8181, #F56565);
        }

        .stat-icon.messages {
            background: linear-gradient(135deg, #9F7AEA, #805AD5);
        }

        .stat-icon.resources {
            background: linear-gradient(135deg, #4FD1C5, #38B2AC);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .stat-label {
            font-size: 0.95rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .admin-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .action-btn {
            padding: 1rem 1.5rem;
            background: var(--card-bg);
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            text-decoration: none;
            color: var(--text-primary);
            font-weight: 600;
            text-align: center;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            will-change: transform;
            backface-visibility: hidden;
            -webkit-font-smoothing: antialiased;
        }

        .action-btn:hover {
            border-color: var(--primary-color);
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(74, 144, 226, 0.3);
        }

        .chart-container {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }

        .chart-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Main Content -->
        <div class="main-content">
            <div class="admin-header">
                <i class="fas fa-crown"></i>
                <div style="flex: 1;">
                    <h1>Admin Dashboard</h1>
                    <p>System overview and analytics</p>
                </div>
                <a href="../../logout.php" style="padding: 0.75rem 1.5rem; background: rgba(255,255,255,0.2); color: white; text-decoration: none; border-radius: 8px; font-weight: 500; transition: all 0.3s ease;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>

            <!-- Statistics Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon users">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-value"><?php echo number_format($analytics['total_users']); ?></div>
                    <div class="stat-label">Total Users</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon active">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="stat-value"><?php echo number_format($analytics['active_users_last_30_days']); ?></div>
                    <div class="stat-label">Active Users (30 days)</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon journals">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="stat-value"><?php echo number_format($analytics['total_journals']); ?></div>
                    <div class="stat-label">Journal Entries</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon moods">
                        <i class="fas fa-smile"></i>
                    </div>
                    <div class="stat-value"><?php echo number_format($analytics['total_moods']); ?></div>
                    <div class="stat-label">Mood Logs</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon messages">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="stat-value"><?php echo number_format($analytics['total_messages']); ?></div>
                    <div class="stat-label">Chat Messages</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon resources">
                        <i class="fas fa-link"></i>
                    </div>
                    <div class="stat-value"><?php echo number_format($analytics['total_resources']); ?></div>
                    <div class="stat-label">Resources</div>
                </div>
            </div>

            <!-- Admin Actions -->
            <div class="chart-container">
                <div class="chart-title">
                    <i class="fas fa-tools"></i> Admin Actions
                </div>
                <div class="admin-actions">
                    <a href="../views/resources/resource.php" class="action-btn">
                        <i class="fas fa-link"></i>
                        Manage Resources
                    </a>
                    <a href="../views/resources/create.php" class="action-btn">
                        <i class="fas fa-plus"></i>
                        Add Resource
                    </a>
                    <a href="../../dashboard.php" class="action-btn">
                        <i class="fas fa-chart-line"></i>
                        View User Dashboard
                    </a>
                    <a href="../chat.php" class="action-btn">
                        <i class="fas fa-robot"></i>
                        Test Chatbot
                    </a>
                </div>
            </div>

            <!-- Overview Chart -->
            <div class="chart-container">
                <div class="chart-title">
                    <i class="fas fa-chart-bar"></i> Platform Overview
                </div>
                <canvas id="overviewChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Platform Overview Chart
        const ctx = document.getElementById('overviewChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Users', 'Active Users', 'Journals', 'Moods', 'Messages', 'Resources'],
                datasets: [{
                    label: 'Platform Statistics',
                    data: [
                        <?php echo $analytics['total_users']; ?>,
                        <?php echo $analytics['active_users_last_30_days']; ?>,
                        <?php echo $analytics['total_journals']; ?>,
                        <?php echo $analytics['total_moods']; ?>,
                        <?php echo $analytics['total_messages']; ?>,
                        <?php echo $analytics['total_resources']; ?>
                    ],
                    backgroundColor: [
                        'rgba(74, 144, 226, 0.7)',
                        'rgba(104, 211, 145, 0.7)',
                        'rgba(246, 173, 85, 0.7)',
                        'rgba(252, 129, 129, 0.7)',
                        'rgba(159, 122, 234, 0.7)',
                        'rgba(79, 209, 197, 0.7)'
                    ],
                    borderColor: [
                        'rgba(74, 144, 226, 1)',
                        'rgba(104, 211, 145, 1)',
                        'rgba(246, 173, 85, 1)',
                        'rgba(252, 129, 129, 1)',
                        'rgba(159, 122, 234, 1)',
                        'rgba(79, 209, 197, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
