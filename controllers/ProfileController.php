<?php
// File: controllers/ProfileController.php

/**
 * ProfileController
 * Handles user profile viewing, editing, and consultant demo requests
 * 
 * @package MentalHealthCompanion
 * @author Iftiaq Hossen
 */
class ProfileController {
    private $db;
    private $userId;

    /**
     * Constructor - initializes database connection using Singleton pattern
     */
    public function __construct() {
        $this->db = $this->getDatabaseConnection();
        $this->userId = $this->getAuthenticatedUserId();
    }

    /**
     * Get database connection using Singleton pattern
     * Detects getDb(), $pdo or $mysqli from config
     * 
     * @return PDO|mysqli Database connection object
     * @throws Exception If no database connection is available
     */
    private function getDatabaseConnection() {
        // Check for getDb() helper function (Singleton)
        if (function_exists('getDb')) {
            return getDb();
        }
        
        // Check for global PDO connection
        global $pdo;
        if (isset($pdo) && $pdo instanceof PDO) {
            return $pdo;
        }
        
        // Check for global mysqli connection
        global $mysqli;
        if (isset($mysqli) && $mysqli instanceof mysqli) {
            return $mysqli;
        }
        
        // Include config.php if not already included
        $configPath = __DIR__ . '/../config.php';
        if (file_exists($configPath)) {
            require_once $configPath;
            if (function_exists('getDb')) {
                return getDb();
            }
            if (isset($GLOBALS['pdo'])) {
                return $GLOBALS['pdo'];
            }
        }
        
        throw new Exception("No database connection available");
    }

    /**
     * Get authenticated user ID
     * Uses getAuthenticatedUserId() if available, otherwise $_SESSION['user_id']
     * 
     * @return int|null User ID or null if not authenticated
     */
    private function getAuthenticatedUserId() {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Fallback to session
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Require authentication - redirect to login if not authenticated
     * 
     * @return void
     */
    private function requireAuth() {
        if ($this->userId === null || $this->userId === '') {
            header("Location: ../../login.php");
            exit();
        }
    }

    /**
     * Show user profile page
     * Displays user info, stats, consultants, and CBT information
     * 
     * @return void
     */
    public function show() {
        $this->requireAuth();

        // Load user data
        $user = $this->getUserData($this->userId);
        if (!$user) {
            die("User not found");
        }

        // Get user statistics
        $counts = $this->getUserStats($this->userId);

        // Get consultants list
        $consultants = $this->getConsultants();

        // Include the view
        include __DIR__ . '/../views/profile/show.php';
    }

    /**
     * Show edit profile page
     * 
     * @return void
     */
    public function edit() {
        $this->requireAuth();

        // Load user data
        $user = $this->getUserData($this->userId);
        if (!$user) {
            die("User not found");
        }

        // Get any flash messages
        $message = $_SESSION['profile_message'] ?? null;
        $error = $_SESSION['profile_error'] ?? null;
        unset($_SESSION['profile_message'], $_SESSION['profile_error']);

        // Include the view
        include __DIR__ . '/../views/profile/edit.php';
    }

    /**
     * Update user profile
     * Handles form submission for profile updates including image upload
     * 
     * @return void
     */
    public function update() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: ../views/profile/edit_profile.php");
            exit();
        }

        $errors = [];

        // Validate name
        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            $errors[] = "Name is required";
        } elseif (strlen($name) > 150) {
            $errors[] = "Name must be less than 150 characters";
        }

        // Validate email
        $email = trim($_POST['email'] ?? '');
        if (empty($email)) {
            $errors[] = "Email is required";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        } else {
            // Check email uniqueness
            if (!$this->isEmailUnique($email, $this->userId)) {
                $errors[] = "Email already taken by another user";
            }
        }

        // Validate bio (optional)
        $bio = trim($_POST['bio'] ?? '');
        $bio = strip_tags($bio, '<p><br><strong><em><ul><ol><li>'); // Allow basic HTML tags

        // Handle profile image upload
        $profileImagePath = null;
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = $this->handleImageUpload($_FILES['profile_image']);
            if ($uploadResult['success']) {
                $profileImagePath = $uploadResult['path'];
            } else {
                $errors[] = $uploadResult['error'];
            }
        }

        // If there are errors, redirect back with error messages
        if (!empty($errors)) {
            $_SESSION['profile_error'] = implode('<br>', $errors);
            header("Location: ../views/profile/edit_profile.php");
            exit();
        }

        // Update user data
        if ($this->updateUserData($this->userId, $name, $email, $bio, $profileImagePath)) {
            // Update session data
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
            
            $_SESSION['profile_message'] = "Profile updated successfully!";
            header("Location: ../views/profile/profile.php");
        } else {
            $_SESSION['profile_error'] = "Failed to update profile. Please try again.";
            header("Location: ../views/profile/edit_profile.php");
        }
        exit();
    }

    /**
     * Get user data from database
     * 
     * @param int $userId User ID
     * @return array|null User data or null if not found
     */
    private function getUserData($userId) {
        if ($this->db instanceof PDO) {
            $stmt = $this->db->prepare("SELECT id, name, email, username, bio, profile_image FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } elseif ($this->db instanceof mysqli) {
            $stmt = $this->db->prepare("SELECT id, name, email, username, bio, profile_image FROM users WHERE id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        }
        return null;
    }

    /**
     * Get user statistics (journal entries, mood logs, messages)
     * 
     * @param int $userId User ID
     * @return array Statistics array
     */
    private function getUserStats($userId) {
        $counts = [
            'journal_count' => 0,
            'mood_count' => 0,
            'message_count' => 0
        ];

        if ($this->db instanceof PDO) {
            // Count journals
            try {
                $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM journal_entries WHERE user_id = ?");
                $stmt->execute([$userId]);
                $counts['journal_count'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
            } catch (PDOException $e) {
                // Table might not exist, ignore
            }

            // Count mood logs
            try {
                $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM mood_logs WHERE user_id = ?");
                $stmt->execute([$userId]);
                $counts['mood_count'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
            } catch (PDOException $e) {
                // Table might not exist, ignore
            }

            // Count messages
            try {
                $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM messages WHERE user_id = ?");
                $stmt->execute([$userId]);
                $counts['message_count'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
            } catch (PDOException $e) {
                // Table might not exist, ignore
            }

        } elseif ($this->db instanceof mysqli) {
            // Count journals
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM journal_entries WHERE user_id = ?");
            if ($stmt) {
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $result = $stmt->get_result();
                $counts['journal_count'] = $result->fetch_assoc()['count'] ?? 0;
            }

            // Count mood logs
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM mood_logs WHERE user_id = ?");
            if ($stmt) {
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $result = $stmt->get_result();
                $counts['mood_count'] = $result->fetch_assoc()['count'] ?? 0;
            }

            // Count messages
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM messages WHERE user_id = ?");
            if ($stmt) {
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $result = $stmt->get_result();
                $counts['message_count'] = $result->fetch_assoc()['count'] ?? 0;
            }
        }

        return $counts;
    }

    /**
     * Get list of consultants
     * 
     * @return array Array of consultants
     */
    private function getConsultants() {
        $consultants = [];

        try {
            if ($this->db instanceof PDO) {
                $stmt = $this->db->query("SELECT id, name, title, bio, contact_info FROM consultants ORDER BY name");
                $consultants = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } elseif ($this->db instanceof mysqli) {
                $result = $this->db->query("SELECT id, name, title, bio, contact_info FROM consultants ORDER BY name");
                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        $consultants[] = $row;
                    }
                }
            }
        } catch (Exception $e) {
            // Table might not exist yet, return empty array
        }

        return $consultants;
    }

    /**
     * Check if email is unique (excluding current user)
     * 
     * @param string $email Email to check
     * @param int $userId Current user ID
     * @return bool True if unique, false otherwise
     */
    private function isEmailUnique($email, $userId) {
        if ($this->db instanceof PDO) {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $userId]);
            return $stmt->rowCount() === 0;
        } elseif ($this->db instanceof mysqli) {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->bind_param("si", $email, $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->num_rows === 0;
        }
        return false;
    }

    /**
     * Handle profile image upload
     * 
     * @param array $file $_FILES array element
     * @return array Result array with 'success', 'path', and 'error' keys
     */
    private function handleImageUpload($file) {
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'File upload failed'];
        }

        // Validate file size (2MB max)
        $maxSize = 2 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'File size must be less than 2MB'];
        }

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            return ['success' => false, 'error' => 'Only JPG and PNG images are allowed'];
        }

        // Get file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
            $extension = 'jpg';
        }

        // Create safe filename
        $timestamp = time();
        $filename = $this->userId . '_' . $timestamp . '.' . $extension;

        // Ensure uploads directory exists
        $uploadDir = __DIR__ . '/../assets/uploads/profiles/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        // Move uploaded file
        $uploadPath = $uploadDir . $filename;
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            // Return only the filename (not the full path)
            return [
                'success' => true,
                'path' => $filename
            ];
        } else {
            return ['success' => false, 'error' => 'Failed to save uploaded file'];
        }
    }

    /**
     * Update user data in database
     * 
     * @param int $userId User ID
     * @param string $name Name
     * @param string $email Email
     * @param string $bio Bio
     * @param string|null $profileImage Profile image path
     * @return bool True on success, false on failure
     */
    private function updateUserData($userId, $name, $email, $bio, $profileImage = null) {
        if ($this->db instanceof PDO) {
            if ($profileImage) {
                $stmt = $this->db->prepare("UPDATE users SET name = ?, email = ?, bio = ?, profile_image = ? WHERE id = ?");
                return $stmt->execute([$name, $email, $bio, $profileImage, $userId]);
            } else {
                $stmt = $this->db->prepare("UPDATE users SET name = ?, email = ?, bio = ? WHERE id = ?");
                return $stmt->execute([$name, $email, $bio, $userId]);
            }
        } elseif ($this->db instanceof mysqli) {
            if ($profileImage) {
                $stmt = $this->db->prepare("UPDATE users SET name = ?, email = ?, bio = ?, profile_image = ? WHERE id = ?");
                $stmt->bind_param("ssssi", $name, $email, $bio, $profileImage, $userId);
            } else {
                $stmt = $this->db->prepare("UPDATE users SET name = ?, email = ?, bio = ? WHERE id = ?");
                $stmt->bind_param("sssi", $name, $email, $bio, $userId);
            }
            return $stmt->execute();
        }
        return false;
    }
}
