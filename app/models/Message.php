<?php
// File: app/Message.php

/**
 * Message Model
 * Handles CRUD operations for chat messages table
 */
class Message {
    private $db;

    /**
     * Constructor - initializes database connection
     */
    public function __construct() {
        $this->db = $this->getConnection();
    }

    /**
     * Get database connection (PDO or mysqli based on config)
     * @return PDO|mysqli|null Database connection object
     */
    private function getConnection() {
        // Check if config.php exists and try to use it
        $configPath = __DIR__ . '/../../config.php';
        if (file_exists($configPath)) {
            require_once $configPath;
            // If config.php sets $pdo or $conn, use it
            if (isset($pdo)) {
                return $pdo;
            }
            if (isset($conn)) {
                return $conn;
            }
        }

        // Fallback: create connection directly
        $host = 'localhost:3307';
        $dbname = 'isd';
        $username = 'root';
        $password = '';

        try {
            // Try PDO first
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $pdo;
        } catch(PDOException $e) {
            error_log("PDO Connection failed: " . $e->getMessage());
            
            // Fallback to mysqli
            try {
                $mysqli = new mysqli('localhost', $username, $password, $dbname, 3307);
                if ($mysqli->connect_error) {
                    throw new Exception("mysqli Connection failed: " . $mysqli->connect_error);
                }
                $mysqli->set_charset("utf8mb4");
                return $mysqli;
            } catch(Exception $e) {
                error_log("mysqli Connection failed: " . $e->getMessage());
                return null;
            }
        }
    }

    /**
     * Create a new message record
     * @param array $data Message data (user_id, sender, message, response, conversation_id)
     * @return int|bool Message ID on success, false on failure
     */
    public function create(array $data) {
        if (!$this->db) {
            return false;
        }

        // Validate required fields
        if (empty($data['message'])) {
            return false;
        }

        // Set defaults
        $userId = isset($data['user_id']) ? (int)$data['user_id'] : null;
        $sender = isset($data['sender']) ? $data['sender'] : 'user';
        $message = trim($data['message']);
        $response = isset($data['response']) ? trim($data['response']) : null;
        $conversationId = isset($data['conversation_id']) ? trim($data['conversation_id']) : null;

        // Validate sender enum
        if (!in_array($sender, ['user', 'bot', 'counselor'])) {
            $sender = 'user';
        }

        $sql = "INSERT INTO messages (user_id, sender, message, response, conversation_id, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())";

        // Handle PDO
        if ($this->db instanceof PDO) {
            try {
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(1, $userId, PDO::PARAM_INT);
                $stmt->bindValue(2, $sender, PDO::PARAM_STR);
                $stmt->bindValue(3, $message, PDO::PARAM_STR);
                $stmt->bindValue(4, $response, PDO::PARAM_STR);
                $stmt->bindValue(5, $conversationId, PDO::PARAM_STR);
                
                if ($stmt->execute()) {
                    return (int)$this->db->lastInsertId();
                }
                return false;
            } catch(PDOException $e) {
                error_log("Message create error (PDO): " . $e->getMessage());
                return false;
            }
        }

        // Handle mysqli
        if ($this->db instanceof mysqli) {
            try {
                $stmt = $this->db->prepare($sql);
                if (!$stmt) {
                    error_log("Message prepare error (mysqli): " . $this->db->error);
                    return false;
                }
                
                $stmt->bind_param("issss", $userId, $sender, $message, $response, $conversationId);
                
                if ($stmt->execute()) {
                    $insertId = $stmt->insert_id;
                    $stmt->close();
                    return (int)$insertId;
                }
                $stmt->close();
                return false;
            } catch(Exception $e) {
                error_log("Message create error (mysqli): " . $e->getMessage());
                return false;
            }
        }

        return false;
    }

    /**
     * Get messages by conversation ID
     * @param string $conversationId Conversation identifier
     * @return array Array of message records
     */
    public function getByConversationId($conversationId) {
        if (!$this->db || empty($conversationId)) {
            return [];
        }

        $conversationId = trim($conversationId);
        $sql = "SELECT * FROM messages WHERE conversation_id = ? ORDER BY created_at ASC";

        // Handle PDO
        if ($this->db instanceof PDO) {
            try {
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(1, $conversationId, PDO::PARAM_STR);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch(PDOException $e) {
                error_log("Message getByConversationId error (PDO): " . $e->getMessage());
                return [];
            }
        }

        // Handle mysqli
        if ($this->db instanceof mysqli) {
            try {
                $stmt = $this->db->prepare($sql);
                if (!$stmt) {
                    error_log("Message prepare error (mysqli): " . $this->db->error);
                    return [];
                }
                
                $stmt->bind_param("s", $conversationId);
                $stmt->execute();
                $result = $stmt->get_result();
                $messages = $result->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
                return $messages;
            } catch(Exception $e) {
                error_log("Message getByConversationId error (mysqli): " . $e->getMessage());
                return [];
            }
        }

        return [];
    }

    /**
     * Get messages by user ID
     * @param int $userId User identifier
     * @return array Array of message records
     */
    public function getByUserId($userId) {
        if (!$this->db || empty($userId)) {
            return [];
        }

        $userId = (int)$userId;
        $sql = "SELECT * FROM messages WHERE user_id = ? ORDER BY created_at DESC";

        // Handle PDO
        if ($this->db instanceof PDO) {
            try {
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(1, $userId, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch(PDOException $e) {
                error_log("Message getByUserId error (PDO): " . $e->getMessage());
                return [];
            }
        }

        // Handle mysqli
        if ($this->db instanceof mysqli) {
            try {
                $stmt = $this->db->prepare($sql);
                if (!$stmt) {
                    error_log("Message prepare error (mysqli): " . $this->db->error);
                    return [];
                }
                
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $result = $stmt->get_result();
                $messages = $result->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
                return $messages;
            } catch(Exception $e) {
                error_log("Message getByUserId error (mysqli): " . $e->getMessage());
                return [];
            }
        }

        return [];
    }

    /**
     * Get latest messages (all users)
     * @param int $limit Number of messages to retrieve (default: 50)
     * @return array Array of message records
     */
    public function latest($limit = 50) {
        if (!$this->db) {
            return [];
        }

        $limit = max(1, min(1000, (int)$limit)); // Limit between 1 and 1000
        $sql = "SELECT * FROM messages ORDER BY created_at DESC LIMIT ?";

        // Handle PDO
        if ($this->db instanceof PDO) {
            try {
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(1, $limit, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch(PDOException $e) {
                error_log("Message latest error (PDO): " . $e->getMessage());
                return [];
            }
        }

        // Handle mysqli
        if ($this->db instanceof mysqli) {
            try {
                $stmt = $this->db->prepare($sql);
                if (!$stmt) {
                    error_log("Message prepare error (mysqli): " . $this->db->error);
                    return [];
                }
                
                $stmt->bind_param("i", $limit);
                $stmt->execute();
                $result = $stmt->get_result();
                $messages = $result->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
                return $messages;
            } catch(Exception $e) {
                error_log("Message latest error (mysqli): " . $e->getMessage());
                return [];
            }
        }

        return [];
    }

    /**
     * Close database connection
     */
    public function __destruct() {
        if ($this->db instanceof mysqli) {
            $this->db->close();
        }
        // PDO connections are closed automatically
    }
}
