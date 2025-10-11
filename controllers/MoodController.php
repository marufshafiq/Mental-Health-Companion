<?php

class MoodController {
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

    public function getMoodHistory($days = 7) {
        if (!$this->db) {
            return [];
        }

        try {
            $stmt = $this->db->prepare("SELECT * FROM mood_entries WHERE user_id = ? AND DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL ? DAY) ORDER BY created_at DESC");
            $stmt->execute([$this->userId, $days]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error fetching mood history: " . $e->getMessage());
            return [];
        }
    }

    public function getAverageMood($days = 7) {
        if (!$this->db) {
            return null;
        }

        try {
            $stmt = $this->db->prepare("SELECT AVG(mood_value) as average FROM mood_entries WHERE user_id = ? AND DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL ? DAY)");
            $stmt->execute([$this->userId, $days]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['average'] ? round($result['average'], 1) : null;
        } catch(PDOException $e) {
            error_log("Error calculating average mood: " . $e->getMessage());
            return null;
        }
    }

    public function addMoodEntry($moodValue, $notes = '') {
        if (!$this->db) {
            return false;
        }

        try {
            $stmt = $this->db->prepare("INSERT INTO mood_entries (user_id, mood_value, notes, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$this->userId, $moodValue, $notes]);
            return true;
        } catch(PDOException $e) {
            error_log("Error adding mood entry: " . $e->getMessage());
            return false;
        }
    }

    public function deleteMoodEntry($entryId) {
        if (!$this->db) {
            return false;
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM mood_entries WHERE id = ? AND user_id = ?");
            $stmt->execute([$entryId, $this->userId]);
            return true;
        } catch(PDOException $e) {
            error_log("Error deleting mood entry: " . $e->getMessage());
            return false;
        }
    }
}
