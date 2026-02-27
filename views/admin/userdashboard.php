<?php
/**
 * Admin User Dashboard
 * Displays detailed statistics for all registered users
 * Shows individual user's journal entries and mood logs counts
 * 
 * @package MentalHealthCompanion
 * @author GitHub Copilot
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check admin authentication
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

// Include database configuration
require_once __DIR__ . '/../../config.php';

/**
 * Get all users with their activity statistics
 * @param PDO $db Database connection
 * @return array Array of users with statistics
 */
function getUsersWithStats($db) {
    try {
        // Query to get all users with their journal and mood counts
        $sql = "
            SELECT 
                u.id,
                u.username,
                u.name,
                u.email,
                COUNT(DISTINCT je.id) as journal_count,
                COUNT(DISTINCT me.id) as mood_count,
                MAX(GREATEST(
                    COALESCE(je.created_at, '1970-01-01'),
                    COALESCE(me.created_at, '1970-01-01')
                )) as last_activity
            FROM users u
            LEFT JOIN journal_entries je ON u.id = je.user_id
            LEFT JOIN mood_entries me ON u.id = me.user_id
            GROUP BY u.id, u.username, u.name, u.email
            ORDER BY (COUNT(DISTINCT je.id) + COUNT(DISTINCT me.id)) DESC, u.id ASC
        ";
        
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("getUsersWithStats error: " . $e->getMessage());
        return [];
    }
}

/**
 * Calculate total statistics across all users
 * @param array $users Array of users with statistics
 * @return array Total statistics
 */
function calculateTotalStats($users) {
    $totalJournals = 0;
    $totalMoods = 0;
    $activeUsers = 0;
    
    foreach ($users as $user) {
        $totalJournals += $user['journal_count'];
        $totalMoods += $user['mood_count'];
        if ($user['journal_count'] > 0 || $user['mood_count'] > 0) {
            $activeUsers++;
        }
    }
    
    return [
        'total_users' => count($users),
        'active_users' => $activeUsers,
        'total_journals' => $totalJournals,
        'total_moods' => $totalMoods
    ];
}

// Get database connection
$db = getDb();

// Fetch users with statistics
$users = getUsersWithStats($db);
$totalStats = calculateTotalStats($users);

// Get admin name
$adminName = $_SESSION['name'] ?? $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            padding: 2rem;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .header-icon {
            font-size: 2.5rem;
        }

        .header-text h1 {
            font-size: 1.8rem;
            margin-bottom: 0.25rem;
        }

        .header-text p {
            opacity: 0.9;
            font-size: 0.95rem;
        }

        .header-actions {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--card-bg);
            padding: 1.5rem;
            border-radius: 12px;
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

        .stat-icon.blue {
            background: linear-gradient(135deg, #4A90E2, #5C6BC0);
        }

        .stat-icon.green {
            background: linear-gradient(135deg, #68D391, #4FD1C5);
        }

        .stat-icon.orange {
            background: linear-gradient(135deg, #F6AD55, #F59E0B);
        }

        .stat-icon.pink {
            background: linear-gradient(135deg, #FC8181, #F56565);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .stat-label {
            font-size: 0.9rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .users-section {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .section-title {
            font-size: 1.5rem;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-title i {
            color: var(--primary-color);
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
            overflow: hidden;
        }

        .users-table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .users-table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .users-table td {
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
        }

        .users-table tbody tr {
            transition: all 0.3s ease;
        }

        .users-table tbody tr:hover {
            background: #f8f9fa;
            transform: scale(1.01);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .user-details {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 0.95rem;
        }

        .user-email {
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        .count-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 50px;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .count-badge.journal {
            background: rgba(246, 173, 85, 0.15);
            color: #F6AD55;
        }

        .count-badge.mood {
            background: rgba(252, 129, 129, 0.15);
            color: #FC8181;
        }

        .count-badge.total {
            background: rgba(74, 144, 226, 0.15);
            color: #4A90E2;
        }

        .activity-status {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .activity-status.active {
            background: rgba(104, 211, 145, 0.15);
            color: #68D391;
        }

        .activity-status.inactive {
            background: rgba(160, 174, 192, 0.15);
            color: #A0AEC0;
        }

        .activity-status i {
            font-size: 0.7rem;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--text-secondary);
        }

        .empty-state i {
            font-size: 4rem;
            color: #e9ecef;
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .header {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }

            .stats-overview {
                grid-template-columns: 1fr;
            }

            .users-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <div class="header-icon">📊</div>
                <div class="header-text">
                    <h1>User Activity Dashboard</h1>
                    <p>Comprehensive overview of all registered users and their activities</p>
                </div>
            </div>
            <div class="header-actions">
                <a href="dashboard.php" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Admin
                </a>
            </div>
        </div>

        <!-- Statistics Overview -->
        <div class="stats-overview">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value"><?php echo number_format($totalStats['total_users']); ?></div>
                <div class="stat-label">Total Registered Users</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-value"><?php echo number_format($totalStats['active_users']); ?></div>
                <div class="stat-label">Active Users</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fas fa-book"></i>
                </div>
                <div class="stat-value"><?php echo number_format($totalStats['total_journals']); ?></div>
                <div class="stat-label">Total Journal Entries</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon pink">
                    <i class="fas fa-smile"></i>
                </div>
                <div class="stat-value"><?php echo number_format($totalStats['total_moods']); ?></div>
                <div class="stat-label">Total Mood Logs</div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="users-section">
            <h2 class="section-title">
                <i class="fas fa-users"></i>
                Detailed User Statistics
            </h2>

            <?php if (empty($users)): ?>
                <div class="empty-state">
                    <i class="fas fa-users-slash"></i>
                    <p>No users found in the system.</p>
                </div>
            <?php else: ?>
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Username</th>
                            <th style="text-align: center;">Journal Entries</th>
                            <th style="text-align: center;">Mood Logs</th>
                            <th style="text-align: center;">Total Activity</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <?php
                                $totalActivity = $user['journal_count'] + $user['mood_count'];
                                $isActive = $totalActivity > 0;
                                $initial = strtoupper(substr($user['name'] ?? $user['username'], 0, 1));
                            ?>
                            <tr>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar"><?php echo htmlspecialchars($initial); ?></div>
                                        <div class="user-details">
                                            <span class="user-name"><?php echo htmlspecialchars($user['name'] ?? 'N/A'); ?></span>
                                            <span class="user-email"><?php echo htmlspecialchars($user['email'] ?? 'No email'); ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                                </td>
                                <td style="text-align: center;">
                                    <span class="count-badge journal">
                                        <?php echo number_format($user['journal_count']); ?>
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <span class="count-badge mood">
                                        <?php echo number_format($user['mood_count']); ?>
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <span class="count-badge total">
                                        <?php echo number_format($totalActivity); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="activity-status <?php echo $isActive ? 'active' : 'inactive'; ?>">
                                        <i class="fas fa-circle"></i>
                                        <?php echo $isActive ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
