<?php

class JournalController {
    private $userId;
    private $db;

    public function __construct($userId) {
        $this->userId = $userId;
        $this->db = $this->getConnection();
    }

    private function getConnection() {
        $host = 'localhost';
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

    public function getEntries($limit = 5) {
        if (!$this->db) {
            return [];
        }

        try {
            $stmt = $this->db->prepare("SELECT * FROM journal_entries WHERE user_id = ? ORDER BY created_at DESC LIMIT ?");
            $stmt->bindValue(1, $this->userId, PDO::PARAM_INT);
            $stmt->bindValue(2, $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error fetching journal entries: " . $e->getMessage());
            return [];
        }
    }

    public function addEntry($title, $content) {
        if (!$this->db) {
            return false;
        }

        try {
            $stmt = $this->db->prepare("INSERT INTO journal_entries (user_id, title, content, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$this->userId, $title, $content]);
            return true;
        } catch(PDOException $e) {
            error_log("Error adding journal entry: " . $e->getMessage());
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
}
