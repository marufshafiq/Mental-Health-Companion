<?php
// File: views/chat.php

// Only start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check authentication
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

$name = $_SESSION['name'] ?? $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Chatbot - Mental Health Companion</title>
    <link rel="stylesheet" href="../dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Chat-specific styles */
        .chat-container {
            background: var(--card-bg);
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: calc(100vh - 140px);
            max-height: 700px;
        }

        .chat-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .chat-header h2 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .chat-status {
            font-size: 0.875rem;
            opacity: 0.9;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
            background: #f8f9fa;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .message {
            display: flex;
            gap: 0.75rem;
            max-width: 75%;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message.user {
            align-self: flex-end;
            flex-direction: row-reverse;
        }

        .message.bot {
            align-self: flex-start;
        }

        .message-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.25rem;
        }

        .message.user .message-avatar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }

        .message.bot .message-avatar {
            background: linear-gradient(135deg, #68D391, #4FD1C5);
            color: white;
        }

        .message-content {
            background: white;
            padding: 0.875rem 1.125rem;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
            word-wrap: break-word;
        }

        .message.user .message-content {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }

        .message-time {
            font-size: 0.75rem;
            color: var(--text-secondary);
            margin-top: 0.25rem;
            opacity: 0.7;
        }

        .typing-indicator {
            display: none;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            background: white;
            border-radius: 12px;
            max-width: 100px;
        }

        .typing-indicator.active {
            display: flex;
        }

        .typing-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--text-secondary);
            animation: typing 1.4s infinite;
        }

        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typing {
            0%, 60%, 100% {
                transform: translateY(0);
            }
            30% {
                transform: translateY(-10px);
            }
        }

        .chat-input-area {
            padding: 1.25rem;
            background: white;
            border-top: 1px solid #e2e8f0;
            display: flex;
            gap: 0.75rem;
        }

        .chat-input {
            flex: 1;
            padding: 0.875rem 1.125rem;
            border: 2px solid #e2e8f0;
            border-radius: 25px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.3s ease;
        }

        .chat-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        }

        .send-button {
            padding: 0.875rem 1.75rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            border-radius: 25px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .send-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(74, 144, 226, 0.4);
        }

        .send-button:active {
            transform: translateY(0);
        }

        .send-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .welcome-message {
            text-align: center;
            padding: 2rem;
            color: var(--text-secondary);
        }

        .welcome-message i {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            background: var(--danger-color);
            color: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: none;
            z-index: 1000;
            animation: slideInRight 0.3s ease;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .toast.success {
            background: var(--success-color);
        }

        .toast.show {
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2>Mental Health Companion</h2>
            <nav>
                <a href="../dashboard.php">📊 Dashboard</a>
                <a href="../journal.php">📓 Journal</a>
                <a href="../mood.php">😊 Mood Tracker</a>
                <a href="chat.php" class="active">💬 AI Chatbot</a>
                <a href="../profile.php">👤 Profile</a>
                <a href="../logout.php">🚪 Logout</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="welcome-section">
                <h1>AI Mental Health Chatbot</h1>
                <p>Chat with our AI assistant for mental health support and guidance. Available 24/7.</p>
            </div>

            <div class="chat-container">
                <div class="chat-header">
                    <div>
                        <i class="fas fa-robot"></i>
                    </div>
                    <div>
                        <h2>Mental Health Support Bot</h2>
                        <div class="chat-status">
                            <i class="fas fa-circle" style="color: #68D391; font-size: 0.5rem;"></i> Online
                        </div>
                    </div>
                </div>

                <div class="chat-messages" id="chatMessages">
                    <div class="welcome-message">
                        <i class="fas fa-comments"></i>
                        <h3>Welcome to Mental Health Support</h3>
                        <p>I'm here to listen and support you. Feel free to share what's on your mind.</p>
                    </div>

                    <!-- Typing indicator -->
                    <div class="message bot">
                        <div class="message-avatar">
                            <i class="fas fa-robot"></i>
                        </div>
                        <div class="typing-indicator" id="typingIndicator">
                            <div class="typing-dot"></div>
                            <div class="typing-dot"></div>
                            <div class="typing-dot"></div>
                        </div>
                    </div>
                </div>

                <div class="chat-input-area">
                    <input 
                        type="text" 
                        class="chat-input" 
                        id="messageInput" 
                        placeholder="Type your message here..." 
                        autocomplete="off"
                    >
                    <button class="send-button" id="sendButton">
                        <i class="fas fa-paper-plane"></i>
                        Send
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast notification -->
    <div class="toast" id="toast"></div>

    <!-- Include chat JavaScript -->
    <script src="../assets/js/chat.js"></script>
</body>
</html>
