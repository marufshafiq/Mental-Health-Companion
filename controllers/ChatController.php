<?php
// File: controllers/ChatController.php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../app/models/Message.php';
require_once __DIR__ . '/../services/ChatbotService.php';

/**
 * ChatController
 * Handles chat message processing and bot response generation
 */
class ChatController {
    private $userId;
    private $messageModel;

    /**
     * Constructor - initializes user authentication and message model
     */
    public function __construct() {
        $this->userId = $this->getAuthenticatedUserId();
        $this->messageModel = new Message();
    }

    /**
     * Get authenticated user ID using existing helper or session fallback
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
     * Handle POST request for chat messages
     * Processes user message, generates bot response, and stores both in database
     * @return array Response array with success status, response text, and conversation_id
     */
    public function handlePost() {
        // Validate request method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return [
                'success' => false,
                'error' => 'Method not allowed. Use POST.'
            ];
        }

        // Get POST data
        $message = isset($_POST['message']) ? trim($_POST['message']) : '';
        $conversationId = isset($_POST['conversation_id']) ? trim($_POST['conversation_id']) : null;

        // Validate message
        if (empty($message)) {
            http_response_code(400);
            return [
                'success' => false,
                'error' => 'Message cannot be empty.'
            ];
        }

        // Generate conversation ID if not provided
        if (empty($conversationId)) {
            $conversationId = $this->generateConversationId();
        }

        // Store user message in database
        $userMessageId = $this->messageModel->create([
            'user_id' => $this->userId,
            'sender' => 'user',
            'message' => $message,
            'response' => null,
            'conversation_id' => $conversationId
        ]);

        if (!$userMessageId) {
            http_response_code(500);
            return [
                'success' => false,
                'error' => 'Failed to store user message.'
            ];
        }

        // Generate bot response using ChatbotService
        $botResponse = ChatbotService::getResponse($message, $conversationId);

        // Store bot response in database
        $botMessageId = $this->messageModel->create([
            'user_id' => $this->userId,
            'sender' => 'bot',
            'message' => $message,
            'response' => $botResponse,
            'conversation_id' => $conversationId
        ]);

        if (!$botMessageId) {
            error_log("Failed to store bot response for conversation: $conversationId");
        }

        // Return success response
        http_response_code(200);
        return [
            'success' => true,
            'response' => $botResponse,
            'conversation_id' => $conversationId
        ];
    }

    /**
     * Generate unique conversation ID
     * @return string Unique conversation identifier
     */
    private function generateConversationId() {
        return uniqid('conv_', true) . '_' . ($this->userId ?? 'guest');
    }
}
