<?php

require_once __DIR__ . '/../services/JournalFactory.php';
require_once __DIR__ . '/../services/SentimentAnalysisService.php';

class JournalController {
    private $userId;
    private $db;

    public function __construct($userId) {
        $this->userId = $userId;
        $this->db = $this->getConnection();
    }

    private function getConnection() {
        $host = 'localhost:3307';
        $dbname = 'isd';
        $username = 'root';
        $password = '';

        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch(PDOException $e) {
            error_log("Connection failed: " . $e->getMessage());
            return null;
        }
    }

    public function getEntries($limit = null, $type = null) {
        if (!$this->db) {
            return [];
        }

        try {
            $sql = "SELECT * FROM journal_entries WHERE user_id = ?";
            $params = [$this->userId];
            
            if ($type) {
                $sql .= " AND tags LIKE ?";
                $params[] = "%{$type}%";
            }
            
            $sql .= " ORDER BY created_at DESC";
            
            if ($limit) {
                $sql .= " LIMIT ?";
                $params[] = $limit;
            }
            
            $stmt = $this->db->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key + 1, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error fetching journal entries: " . $e->getMessage());
            return [];
        }
    }

    public function getEntry($entryId) {
        if (!$this->db) {
            return null;
        }

        try {
            $stmt = $this->db->prepare("SELECT * FROM journal_entries WHERE id = ? AND user_id = ?");
            $stmt->execute([$entryId, $this->userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error fetching journal entry: " . $e->getMessage());
            return null;
        }
    }

    public function addEntry($title, $content, $type = 'daily', $mood = null, $tags = null) {
        if (!$this->db) {
            return false;
        }

        try {
            // Calculate word count
            $wordCount = str_word_count($content);
            
            // Add type to tags if not already there
            if ($tags) {
                $tagsArray = explode(',', $tags);
                if (!in_array($type, $tagsArray)) {
                    $tagsArray[] = $type;
                }
                $tags = implode(',', $tagsArray);
            } else {
                $tags = $type;
            }
            
            $stmt = $this->db->prepare("INSERT INTO journal_entries 
                (user_id, title, content, mood, tags, word_count, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$this->userId, $title, $content, $mood, $tags, $wordCount]);
            return $this->db->lastInsertId();
        } catch(PDOException $e) {
            error_log("Error adding journal entry: " . $e->getMessage());
            return false;
        }
    }

    public function updateEntry($entryId, $title, $content, $mood = null, $tags = null) {
        if (!$this->db) {
            return false;
        }

        try {
            // Calculate word count
            $wordCount = str_word_count($content);
            
            $stmt = $this->db->prepare("UPDATE journal_entries 
                SET title = ?, content = ?, mood = ?, tags = ?, word_count = ?, updated_at = NOW() 
                WHERE id = ? AND user_id = ?");
            $stmt->execute([$title, $content, $mood, $tags, $wordCount, $entryId, $this->userId]);
            return true;
        } catch(PDOException $e) {
            error_log("Error updating journal entry: " . $e->getMessage());
            return false;
        }
    }

    public function deleteEntry($entryId) {
        if (!$this->db) {
            return false;
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM journal_entries WHERE id = ? AND user_id = ?");
            $stmt->execute([$entryId, $this->userId]);
            return true;
        } catch(PDOException $e) {
            error_log("Error deleting journal entry: " . $e->getMessage());
            return false;
        }
    }

    public function toggleFavorite($entryId) {
        if (!$this->db) {
            return false;
        }

        try {
            $stmt = $this->db->prepare("UPDATE journal_entries 
                SET is_favorite = NOT is_favorite 
                WHERE id = ? AND user_id = ?");
            $stmt->execute([$entryId, $this->userId]);
            return true;
        } catch(PDOException $e) {
            error_log("Error toggling favorite: " . $e->getMessage());
            return false;
        }
    }

    public function getStats() {
        if (!$this->db) {
            return [];
        }

        try {
            $stmt = $this->db->prepare("SELECT 
                COUNT(*) as total_entries,
                SUM(word_count) as total_words,
                AVG(word_count) as avg_words,
                COUNT(CASE WHEN is_favorite = 1 THEN 1 END) as favorites
                FROM journal_entries WHERE user_id = ?");
            $stmt->execute([$this->userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error fetching stats: " . $e->getMessage());
            return [];
        }
    }
}

