<?php
// File: app/models/Resource.php

/**
 * Resource Model
 * Handles CRUD operations for resources table (meditation links, mental health resources)
 * Uses Singleton DB connector pattern
 * 
 * @package MentalHealthCompanion
 * @author Maruf
 */
class Resource {
    private $db;

    /**
     * Constructor - initializes database connection using Singleton pattern
     */
    public function __construct() {
        $this->db = $this->getDatabaseConnection();
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
        $configPath = __DIR__ . '/../../config.php';
        if (file_exists($configPath)) {
            require_once $configPath;
            
            if (isset($pdo) && $pdo instanceof PDO) {
                return $pdo;
            }
            
            if (isset($mysqli) && $mysqli instanceof mysqli) {
                return $mysqli;
            }
        }
        
        throw new Exception("Database connection not available. Please check your configuration.");
    }

    /**
     * Create a new resource
     * 
     * @param array $data Resource data (title, url, description, source)
     * @return int Inserted resource ID
     * @throws Exception If required fields are missing or insert fails
     */
    public function create(array $data) {
        // Validate required fields
        if (empty($data['title']) || empty($data['url'])) {
            throw new Exception("Title and URL are required fields.");
        }

        $title = trim($data['title']);
        $url = trim($data['url']);
        $description = isset($data['description']) ? trim($data['description']) : null;
        $source = isset($data['source']) ? trim($data['source']) : null;

        $sql = "INSERT INTO resources (title, url, description, source, created_at, updated_at) 
                VALUES (?, ?, ?, ?, NOW(), NOW())";

        // Handle PDO
        if ($this->db instanceof PDO) {
            try {
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$title, $url, $description, $source]);
                return (int)$this->db->lastInsertId();
            } catch(PDOException $e) {
                error_log("Resource create error (PDO): " . $e->getMessage());
                throw new Exception("Failed to create resource.");
            }
        }

        // Handle mysqli
        if ($this->db instanceof mysqli) {
            try {
                $stmt = $this->db->prepare($sql);
                if (!$stmt) {
                    throw new Exception("Prepare failed: " . $this->db->error);
                }
                
                $stmt->bind_param("ssss", $title, $url, $description, $source);
                
                if ($stmt->execute()) {
                    $insertId = $stmt->insert_id;
                    $stmt->close();
                    return (int)$insertId;
                }
                
                $error = $stmt->error;
                $stmt->close();
                throw new Exception("Execute failed: " . $error);
            } catch(Exception $e) {
                error_log("Resource create error (mysqli): " . $e->getMessage());
                throw new Exception("Failed to create resource.");
            }
        }

        throw new Exception("Invalid database connection type.");
    }

    /**
     * Update an existing resource
     * 
     * @param int $id Resource ID
     * @param array $data Resource data to update
     * @return bool True on success, false on failure
     */
    public function update(int $id, array $data) {
        if ($id <= 0) {
            return false;
        }

        // Build update query dynamically based on provided fields
        $fields = [];
        $values = [];

        if (isset($data['title'])) {
            $fields[] = "title = ?";
            $values[] = trim($data['title']);
        }

        if (isset($data['url'])) {
            $fields[] = "url = ?";
            $values[] = trim($data['url']);
        }

        if (isset($data['description'])) {
            $fields[] = "description = ?";
            $values[] = trim($data['description']);
        }

        if (isset($data['source'])) {
            $fields[] = "source = ?";
            $values[] = trim($data['source']);
        }

        if (empty($fields)) {
            return false; // Nothing to update
        }

        $fields[] = "updated_at = NOW()";
        $values[] = $id;

        $sql = "UPDATE resources SET " . implode(", ", $fields) . " WHERE id = ?";

        // Handle PDO
        if ($this->db instanceof PDO) {
            try {
                $stmt = $this->db->prepare($sql);
                return $stmt->execute($values);
            } catch(PDOException $e) {
                error_log("Resource update error (PDO): " . $e->getMessage());
                return false;
            }
        }

        // Handle mysqli
        if ($this->db instanceof mysqli) {
            try {
                $stmt = $this->db->prepare($sql);
                if (!$stmt) {
                    error_log("Resource prepare error (mysqli): " . $this->db->error);
                    return false;
                }
                
                // Build type string for bind_param
                $types = str_repeat('s', count($values) - 1) . 'i';
                $stmt->bind_param($types, ...$values);
                
                $success = $stmt->execute();
                $stmt->close();
                return $success;
            } catch(Exception $e) {
                error_log("Resource update error (mysqli): " . $e->getMessage());
                return false;
            }
        }

        return false;
    }

    /**
     * Find a resource by ID
     * 
     * @param int $id Resource ID
     * @return array|null Resource data array or null if not found
     */
    public function find(int $id) {
        if ($id <= 0) {
            return null;
        }

        $sql = "SELECT * FROM resources WHERE id = ?";

        // Handle PDO
        if ($this->db instanceof PDO) {
            try {
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$id]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result ?: null;
            } catch(PDOException $e) {
                error_log("Resource find error (PDO): " . $e->getMessage());
                return null;
            }
        }

        // Handle mysqli
        if ($this->db instanceof mysqli) {
            try {
                $stmt = $this->db->prepare($sql);
                if (!$stmt) {
                    error_log("Resource prepare error (mysqli): " . $this->db->error);
                    return null;
                }
                
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $resource = $result->fetch_assoc();
                $stmt->close();
                return $resource ?: null;
            } catch(Exception $e) {
                error_log("Resource find error (mysqli): " . $e->getMessage());
                return null;
            }
        }

        return null;
    }

    /**
     * Get all resources with optional limit
     * 
     * @param int $limit Maximum number of resources to retrieve (default: 100)
     * @return array Array of resource records
     */
    public function all(int $limit = 100) {
        $limit = max(1, min(1000, $limit)); // Limit between 1 and 1000
        $sql = "SELECT * FROM resources ORDER BY created_at DESC LIMIT ?";

        // Handle PDO
        if ($this->db instanceof PDO) {
            try {
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(1, $limit, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch(PDOException $e) {
                error_log("Resource all error (PDO): " . $e->getMessage());
                return [];
            }
        }

        // Handle mysqli
        if ($this->db instanceof mysqli) {
            try {
                $stmt = $this->db->prepare($sql);
                if (!$stmt) {
                    error_log("Resource prepare error (mysqli): " . $this->db->error);
                    return [];
                }
                
                $stmt->bind_param("i", $limit);
                $stmt->execute();
                $result = $stmt->get_result();
                $resources = $result->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
                return $resources;
            } catch(Exception $e) {
                error_log("Resource all error (mysqli): " . $e->getMessage());
                return [];
            }
        }

        return [];
    }

    /**
     * Delete a resource by ID
     * 
     * @param int $id Resource ID
     * @return bool True on success, false on failure
     */
    public function delete(int $id) {
        if ($id <= 0) {
            return false;
        }

        $sql = "DELETE FROM resources WHERE id = ?";

        // Handle PDO
        if ($this->db instanceof PDO) {
            try {
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([$id]);
            } catch(PDOException $e) {
                error_log("Resource delete error (PDO): " . $e->getMessage());
                return false;
            }
        }

        // Handle mysqli
        if ($this->db instanceof mysqli) {
            try {
                $stmt = $this->db->prepare($sql);
                if (!$stmt) {
                    error_log("Resource prepare error (mysqli): " . $this->db->error);
                    return false;
                }
                
                $stmt->bind_param("i", $id);
                $success = $stmt->execute();
                $stmt->close();
                return $success;
            } catch(Exception $e) {
                error_log("Resource delete error (mysqli): " . $e->getMessage());
                return false;
            }
        }

        return false;
    }
}
