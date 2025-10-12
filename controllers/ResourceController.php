<?php
// File: controllers/ResourceController.php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../app/models/Resource.php';

/**
 * ResourceController
 * Handles resource CRUD operations for meditation links and mental health resources
 * Admin-only access control via session-based authentication
 * 
 * @package MentalHealthCompanion
 * @author Iftiaq Hossen
 */
class ResourceController {
    private $resourceModel;

    /**
     * Constructor - initializes resource model
     */
    public function __construct() {
        $this->resourceModel = new Resource();
    }

    /**
     * Check if current user is admin
     * 
     * @return void Exits with 403 if not admin
     */
    private function requireAdmin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('HTTP/1.1 403 Forbidden');
            die('Access denied. Admin privileges required.');
        }
    }

    /**
     * Display list of all resources (admin view)
     * 
     * @return void Includes view file
     */
    public function index() {
        $this->requireAdmin();

        try {
            $resources = $this->resourceModel->all(100);
            include __DIR__ . '/../views/resources/resource.php';
        } catch (Exception $e) {
            error_log("ResourceController index error: " . $e->getMessage());
            die("Error loading resources.");
        }
    }

    /**
     * Show resource creation form (admin only)
     * 
     * @return void Includes create view file
     */
    public function create() {
        $this->requireAdmin();
        include __DIR__ . '/../views/resources/create.php';
    }

    /**
     * Handle POST request to create new resource (admin only)
     * 
     * @return void Redirects to index on success or back to create on error
     */
    public function store() {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: create.php');
            exit();
        }

        // Get and validate input
        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $url = isset($_POST['url']) ? trim($_POST['url']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $source = isset($_POST['source']) ? trim($_POST['source']) : '';

        // Validate required fields
        if (empty($title)) {
            $_SESSION['error'] = 'Title is required.';
            header('Location: create.php');
            exit();
        }

        if (empty($url)) {
            $_SESSION['error'] = 'URL is required.';
            header('Location: create.php');
            exit();
        }

        // Validate URL format
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $_SESSION['error'] = 'Invalid URL format.';
            header('Location: create.php');
            exit();
        }

        try {
            // Create resource
            $resourceId = $this->resourceModel->create([
                'title' => $title,
                'url' => $url,
                'description' => $description,
                'source' => $source
            ]);

            $_SESSION['success'] = 'Resource created successfully!';
            header('Location: resource.php');
            exit();
        } catch (Exception $e) {
            error_log("ResourceController store error: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to create resource. ' . $e->getMessage();
            header('Location: create.php');
            exit();
        }
    }

    /**
     * Show resource edit form (admin only)
     * 
     * @param int $id Resource ID
     * @return void Includes edit view file
     */
    public function edit($id) {
        $this->requireAdmin();

        $id = (int)$id;
        
        try {
            $resource = $this->resourceModel->find($id);
            
            if (!$resource) {
                $_SESSION['error'] = 'Resource not found.';
                header('Location: resource.php');
                exit();
            }

            include __DIR__ . '/../views/resources/edit.php';
        } catch (Exception $e) {
            error_log("ResourceController edit error: " . $e->getMessage());
            $_SESSION['error'] = 'Error loading resource.';
            header('Location: resource.php');
            exit();
        }
    }

    /**
     * Handle POST request to update existing resource (admin only)
     * 
     * @param int $id Resource ID
     * @return void Redirects to index on success or back to edit on error
     */
    public function update($id) {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: resource.php');
            exit();
        }

        $id = (int)$id;

        // Get and validate input
        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $url = isset($_POST['url']) ? trim($_POST['url']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $source = isset($_POST['source']) ? trim($_POST['source']) : '';

        // Validate required fields
        if (empty($title)) {
            $_SESSION['error'] = 'Title is required.';
            header("Location: edit.php?id=$id");
            exit();
        }

        if (empty($url)) {
            $_SESSION['error'] = 'URL is required.';
            header("Location: edit.php?id=$id");
            exit();
        }

        // Validate URL format
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $_SESSION['error'] = 'Invalid URL format.';
            header("Location: edit.php?id=$id");
            exit();
        }

        try {
            // Update resource
            $success = $this->resourceModel->update($id, [
                'title' => $title,
                'url' => $url,
                'description' => $description,
                'source' => $source
            ]);

            if ($success) {
                $_SESSION['success'] = 'Resource updated successfully!';
            } else {
                $_SESSION['error'] = 'Failed to update resource.';
            }

            header('Location: resource.php');
            exit();
        } catch (Exception $e) {
            error_log("ResourceController update error: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to update resource.';
            header("Location: edit.php?id=$id");
            exit();
        }
    }

    /**
     * Handle resource deletion (admin only)
     * 
     * @param int $id Resource ID
     * @return void Redirects to index with success/error message
     */
    public function destroy($id) {
        $this->requireAdmin();

        $id = (int)$id;

        try {
            $success = $this->resourceModel->delete($id);

            if ($success) {
                $_SESSION['success'] = 'Resource deleted successfully!';
            } else {
                $_SESSION['error'] = 'Failed to delete resource.';
            }
        } catch (Exception $e) {
            error_log("ResourceController destroy error: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to delete resource.';
        }

        header('Location: resource.php');
        exit();
    }
}
