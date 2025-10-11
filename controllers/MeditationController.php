<?php
// File: controllers/MeditationController.php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../app/models/Resource.php';

/**
 * MeditationController
 * Handles public meditation resources page with Tip-of-the-Day feature
 * 
 * Implements deterministic daily tip selection using day-of-year modulo
 * algorithm to ensure the same tip appears all day long.
 * 
 * @package MentalHealthCompanion
 * @author GitHub Copilot
 */
class MeditationController {
    private $resourceModel;
    private $db;

    /**
     * Constructor - initializes resource model and database connection
     */
    public function __construct() {
        $this->resourceModel = new Resource();
        $this->db = $this->getDatabaseConnection();
    }

    /**
     * Get database connection using Singleton pattern
     * Detects global $pdo or $mysqli from config
     * 
     * @return PDO|mysqli Database connection object
     * @throws Exception If no database connection is available
     */
    private function getDatabaseConnection() {
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
        
        throw new Exception("No database connection available");
    }

    /**
     * Display meditation resources page with Tip-of-the-Day
     * 
     * Algorithm:
     * - Fetches all resources (up to 500)
     * - Calculates deterministic tip based on day of year
     * - Uses modulo operation to cycle through tips: dayOfYear % totalTips
     * - Ensures same tip appears throughout the entire day
     * 
     * @return void Includes view file
     */
    public function index() {
        try {
            // Fetch all resources (up to 500)
            $resources = $this->resourceModel->all(500);
            
            // Get Tip-of-the-Day using deterministic algorithm
            $tip = $this->getTipOfTheDay();
            
            // Include view file with variables
            include __DIR__ . '/../views/meditation.php';
        } catch (Exception $e) {
            error_log("MeditationController index error: " . $e->getMessage());
            die("Error loading meditation page.");
        }
    }

    /**
     * Get deterministic Tip-of-the-Day based on current day of year
     * 
     * Uses modulo arithmetic to select one tip per day:
     * - dayOfYear ranges from 0 to 365 (or 366 in leap years)
     * - index = dayOfYear % totalTips ensures cycling through all tips
     * - Same tip appears all day regardless of how many times page is loaded
     * 
     * @return string Tip text for today, or fallback message if no tips exist
     */
    private function getTipOfTheDay() {
        try {
            // Check if using PDO or mysqli
            if ($this->db instanceof PDO) {
                return $this->getTipOfTheDayPDO();
            } elseif ($this->db instanceof mysqli) {
                return $this->getTipOfTheDayMySQLi();
            } else {
                return "Tip repository is empty.";
            }
        } catch (Exception $e) {
            error_log("getTipOfTheDay error: " . $e->getMessage());
            return "Tip repository is empty.";
        }
    }

    /**
     * Get Tip-of-the-Day using PDO
     * 
     * @return string Tip text
     */
    private function getTipOfTheDayPDO() {
        // Count total tips
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM tips");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = (int)$result['count'];

        // If no tips exist, return fallback message
        if ($count === 0) {
            return "Tip repository is empty.";
        }

        // Calculate deterministic index based on day of year
        // date('z') returns day of year: 0 (Jan 1) to 365 (Dec 31)
        $dayOfYear = (int)date('z');
        $index = $dayOfYear % $count;

        // Fetch the tip at calculated offset, ordered by id ASC
        $stmt = $this->db->prepare("SELECT tip_text FROM tips ORDER BY id ASC LIMIT 1 OFFSET :offset");
        $stmt->bindValue(':offset', $index, PDO::PARAM_INT);
        $stmt->execute();
        $tipRow = $stmt->fetch(PDO::FETCH_ASSOC);

        return $tipRow ? $tipRow['tip_text'] : "Tip repository is empty.";
    }

    /**
     * Get Tip-of-the-Day using MySQLi
     * 
     * @return string Tip text
     */
    private function getTipOfTheDayMySQLi() {
        // Count total tips
        $result = $this->db->query("SELECT COUNT(*) as count FROM tips");
        $row = $result->fetch_assoc();
        $count = (int)$row['count'];

        // If no tips exist, return fallback message
        if ($count === 0) {
            return "Tip repository is empty.";
        }

        // Calculate deterministic index based on day of year
        $dayOfYear = (int)date('z');
        $index = $dayOfYear % $count;

        // Fetch the tip at calculated offset, ordered by id ASC
        $stmt = $this->db->prepare("SELECT tip_text FROM tips ORDER BY id ASC LIMIT 1 OFFSET ?");
        $stmt->bind_param('i', $index);
        $stmt->execute();
        $result = $stmt->get_result();
        $tipRow = $result->fetch_assoc();

        return $tipRow ? $tipRow['tip_text'] : "Tip repository is empty.";
    }
}
