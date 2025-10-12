<?php
// File: controllers/AdminController.php

require_once __DIR__ . '/../config.php';

/**
 * AdminController
 * Handles admin panel operations and analytics
 * Implements MVC pattern with admin-only access control
 */
class AdminController {
    private $db;
    private $userId;
    private $isAdmin;

    /**
     * Constructor - initializes database connection and checks authentication
     */
    public function __construct() {
        $this->db = $this->getDatabaseConnection();
        $this->userId = $this->getAuthenticatedUserId();
        $this->isAdmin = $this->checkAdminRole();
    }

    /**
     * Get database connection using Singleton pattern
     * @return PDO|mysqli Database connection object
     * @throws Exception If no database connection is available
     */
    private function getDatabaseConnection() {
        // Check for existing PDO connection from config.php
        global $pdo, $mysqli;
        
        if (isset($pdo) && $pdo instanceof PDO) {
            return $pdo;
        }
        
        if (isset($mysqli) && $mysqli instanceof mysqli) {
            return $mysqli;
        }
        
        throw new Exception("Database connection not available. Please check your configuration.");
    }

    /**
     * Get authenticated user ID
     * @return int|null User ID or null if not authenticated
     */
    private function getAuthenticatedUserId() {
        // Get from session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    }

    /**
     * Check if current user is admin
     * @return bool True if admin, false otherwise
     */
    private function checkAdminRole() {
        // Check session for admin role
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    /**
     * Ensure user is authenticated and has admin privileges
     * Redirects or sends 403 if not authorized
     * @return void
     */
    private function requireAdmin() {
        // Debug output (remove after testing)
        if (APP_DEBUG) {
            error_log("AdminController Debug:");
            error_log("User ID: " . ($this->userId ?? 'NULL'));
            error_log("Is Admin: " . ($this->isAdmin ? 'true' : 'false'));
            error_log("Session Role: " . ($_SESSION['role'] ?? 'NOT SET'));
            error_log("Session Email: " . ($_SESSION['email'] ?? 'NOT SET'));
        }
        
        // Check if user_id is set (use === null to handle user_id of 0)
        if ($this->userId === null) {
            // Redirect to login (relative to the entry point, not controller)
            header("Location: " . ($_SERVER['DOCUMENT_ROOT'] ?? '') . "/Mental-Health-Companion/login.php");
            exit();
        }

        if (!$this->isAdmin) {
            http_response_code(403);
            die("Access denied. Admin privileges required. Your role: " . ($_SESSION['role'] ?? 'not set'));
        }
    }

    /**
     * Admin dashboard index - displays analytics and overview
     * @return void
     */
    public function index() {
        $this->requireAdmin();

        try {
            // Compute analytics
            $analytics = [
                'total_users' => $this->getTotalUsers(),
                'active_users_last_30_days' => $this->getActiveUsers(),
                'total_journals' => $this->getTotalJournals(),
                'total_moods' => $this->getTotalMoods(),
                'total_messages' => $this->getTotalMessages(),
                'total_resources' => $this->getTotalResources()
            ];

            // Include admin view
            include __DIR__ . '/../views/admin/dashboard.php';
        } catch (Exception $e) {
            error_log("AdminController index error: " . $e->getMessage());
            die("An error occurred loading the admin dashboard.");
        }
    }

    /**
     * Get total number of users
     * @return int Total user count
     */
    private function getTotalUsers() {
        $sql = "SELECT COUNT(*) as count FROM users";
        return $this->executeCountQuery($sql);
    }

    /**
     * Get count of active users in last 30 days
     * Users who have logged mood or journal entries
     * @return int Active user count
     */
    private function getActiveUsers() {
        // This query combines users who have journal or mood entries in last 30 days
        $sql = "SELECT COUNT(DISTINCT user_id) as count FROM (
                    SELECT DISTINCT user_id FROM journal_entries WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                    UNION
                    SELECT DISTINCT user_id FROM mood_entries WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                ) as active_users";
        
        try {
            return $this->executeCountQuery($sql);
        } catch (Exception $e) {
            // If tables don't exist, return 0
            error_log("ActiveUsers query error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get total number of journal entries
     * @return int Total journal count
     */
    private function getTotalJournals() {
        $sql = "SELECT COUNT(*) as count FROM journal_entries";
        try {
            return $this->executeCountQuery($sql);
        } catch (Exception $e) {
            error_log("TotalJournals query error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get total number of mood entries
     * @return int Total mood count
     */
    private function getTotalMoods() {
        $sql = "SELECT COUNT(*) as count FROM mood_entries";
        try {
            return $this->executeCountQuery($sql);
        } catch (Exception $e) {
            error_log("TotalMoods query error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get total number of chat messages
     * @return int Total message count
     */
    private function getTotalMessages() {
        $sql = "SELECT COUNT(*) as count FROM messages";
        return $this->executeCountQuery($sql);
    }

    /**
     * Get total number of resources
     * @return int Total resource count
     */
    private function getTotalResources() {
        $sql = "SELECT COUNT(*) as count FROM resources";
        return $this->executeCountQuery($sql);
    }

    /**
     * Execute a COUNT query and return the result
     * @param string $sql SQL query
     * @return int Count result
     */
    private function executeCountQuery($sql) {
        // Handle PDO
        if ($this->db instanceof PDO) {
            try {
                $stmt = $this->db->query($sql);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return (int)($result['count'] ?? 0);
            } catch (PDOException $e) {
                error_log("Count query error (PDO): " . $e->getMessage());
                return 0;
            }
        }

        // Handle mysqli
        if ($this->db instanceof mysqli) {
            try {
                $result = $this->db->query($sql);
                if ($result) {
                    $row = $result->fetch_assoc();
                    return (int)($row['count'] ?? 0);
                }
                return 0;
            } catch (Exception $e) {
                error_log("Count query error (mysqli): " . $e->getMessage());
                return 0;
            }
        }

        return 0;
    }

    /**
     * Resources management page
     * Redirects to existing resources admin page
     * @return void
     */
    public function resources() {
        $this->requireAdmin();
        header("Location: ../views/resources/index.php");
        exit();
    }

    /**
     * Show resource creation form
     * Forwards to Resource model's create view
     * @return void
     */
    public function resourceCreate() {
        $this->requireAdmin();
        header("Location: ../views/resources/create.php");
        exit();
    }

    /**
     * Store a new resource
     * Will forward to Resource model's store method
     * @return void
     */
    public function resourceStore() {
        $this->requireAdmin();
        
        // This will be implemented when Resource model is added
        // For now, redirect to resources store handler
        header("Location: ../views/resources/store.php");
        exit();
    }

    /**
     * Show resource edit form
     * Forwards to Resource model's edit view
     * @param int $id Resource ID
     * @return void
     */
    public function resourceEdit($id) {
        $this->requireAdmin();
        
        $id = (int)$id;
        header("Location: ../views/resources/edit.php?id=" . $id);
        exit();
    }

    /**
     * Update an existing resource
     * Will forward to Resource model's update method
     * @param int $id Resource ID
     * @return void
     */
    public function resourceUpdate($id) {
        $this->requireAdmin();
        
        // This will be implemented when Resource model is added
        // For now, redirect to resources update handler
        header("Location: ../views/resources/update.php");
        exit();
    }

    /**
     * Delete a resource
     * Will forward to Resource model's delete method
     * @param int $id Resource ID
     * @return void
     */
    public function resourceDelete($id) {
        $this->requireAdmin();
        
        $id = (int)$id;
        
        // This will be implemented when Resource model is added
        // For now, set ID in POST and redirect to delete handler
        $_POST['id'] = $id;
        header("Location: ../views/resources/delete.php");
        exit();
    }
}
