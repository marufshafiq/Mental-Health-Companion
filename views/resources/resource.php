<?php
// File: views/resources/resource.php

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check admin authentication
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

// Initialize controller if not already done
if (!isset($resources)) {
    require_once __DIR__ . '/../../controllers/ResourceController.php';
    $controller = new ResourceController();
    $controller->index();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Resources - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
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
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 1.75rem;
            font-weight: 600;
        }

        .header-actions {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: white;
            color: var(--primary-color);
        }

        .btn-primary:hover {
            background: var(--background-color);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: rgba(255,255,255,0.2);
            color: white;
        }

        .btn-secondary:hover {
            background: rgba(255,255,255,0.3);
        }

        .btn-danger {
            background: var(--danger-color);
            color: white;
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
        }

        .btn-danger:hover {
            background: #f56565;
        }

        .btn-edit {
            background: var(--warning-color);
            color: white;
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
        }

        .btn-edit:hover {
            background: #f59e0b;
        }

        .alert {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .table-container {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: var(--background-color);
        }

        th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--text-primary);
            border-bottom: 2px solid #e2e8f0;
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }

        tr:hover {
            background: var(--background-color);
        }

        .resource-title {
            font-weight: 600;
            color: var(--primary-color);
        }

        .resource-url {
            color: var(--text-secondary);
            font-size: 0.875rem;
            word-break: break-all;
        }

        .resource-url a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .resource-url a:hover {
            text-decoration: underline;
        }

        .actions {
            display: flex;
            gap: 0.5rem;
        }

        .no-resources {
            text-align: center;
            padding: 3rem;
            color: var(--text-secondary);
        }

        .no-resources i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--text-secondary);
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1><i class="fas fa-link"></i> Manage Resources</h1>
                <p style="opacity: 0.9; margin-top: 0.5rem;">Meditation links and mental health resources</p>
            </div>
            <div class="header-actions">
                <a href="create.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Resource
                </a>
                <a href="../admin.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($_SESSION['success']); ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($_SESSION['error']); ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="table-container">
            <?php if (empty($resources)): ?>
                <div class="no-resources">
                    <i class="fas fa-inbox"></i>
                    <h3>No Resources Yet</h3>
                    <p>Click "Add Resource" to create your first resource.</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>URL</th>
                            <th>Source</th>
                            <th>Created</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resources as $resource): ?>
                            <tr>
                                <td>
                                    <div class="resource-title">
                                        <?php echo htmlspecialchars($resource['title']); ?>
                                    </div>
                                    <?php if (!empty($resource['description'])): ?>
                                        <div style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 0.25rem;">
                                            <?php echo htmlspecialchars(substr($resource['description'], 0, 100)); ?>
                                            <?php if (strlen($resource['description']) > 100): ?>...<?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="resource-url">
                                    <a href="<?php echo htmlspecialchars($resource['url']); ?>" target="_blank" rel="noopener">
                                        <?php echo htmlspecialchars(substr($resource['url'], 0, 50)); ?>
                                        <?php if (strlen($resource['url']) > 50): ?>...<?php endif; ?>
                                        <i class="fas fa-external-link-alt" style="font-size: 0.75rem;"></i>
                                    </a>
                                </td>
                                <td>
                                    <?php echo !empty($resource['source']) ? htmlspecialchars($resource['source']) : '<span style="color: var(--text-secondary);">—</span>'; ?>
                                </td>
                                <td style="font-size: 0.875rem; color: var(--text-secondary);">
                                    <?php echo date('M j, Y', strtotime($resource['created_at'])); ?>
                                </td>
                                <td>
                                    <div class="actions">
                                        <a href="edit.php?id=<?php echo $resource['id']; ?>" class="btn btn-edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form method="POST" action="delete.php" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this resource?');">
                                            <input type="hidden" name="id" value="<?php echo $resource['id']; ?>">
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
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
