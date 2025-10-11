// File: assets/js/chat.js

/**
 * Chat Application
 * Handles user interaction with the AI chatbot using Strategy pattern
 */

class ChatApp {
    constructor() {
        this.messageInput = document.getElementById('messageInput');
        this.sendButton = document.getElementById('sendButton');
        this.chatMessages = document.getElementById('chatMessages');
        this.typingIndicator = document.getElementById('typingIndicator');
        this.conversationId = null;
        
        this.init();
    }

    /**
     * Initialize chat application
     */
    init() {
        // Bind event listeners
        this.sendButton.addEventListener('click', () => this.sendMessage());
        this.messageInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });

        // Focus on input
        this.messageInput.focus();
    }

    /**
     * Send message to chatbot API
     */
    async sendMessage() {
        const message = this.messageInput.value.trim();
        
        if (!message) {
            return;
        }

        // Disable input while processing
        this.setInputState(false);

        // Display user message
        this.appendMessage('user', message);

        // Clear input
        this.messageInput.value = '';

        // Show typing indicator
        this.showTyping(true);

        try {
            // Prepare form data
            const formData = new FormData();
            formData.append('message', message);
            
            if (this.conversationId) {
                formData.append('conversation_id', this.conversationId);
            }

            // Send POST request to API
            const response = await fetch('../public/api/chat.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            });

            // Hide typing indicator
            this.showTyping(false);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                // Store conversation ID
                if (data.conversation_id) {
                    this.conversationId = data.conversation_id;
                }

                // Display bot response
                this.appendMessage('bot', data.response);
            } else {
                throw new Error(data.error || 'Failed to get response');
            }

        } catch (error) {
            console.error('Chat error:', error);
            this.showTyping(false);
            this.showToast('Error: ' + error.message, 'error');
            
            // Display error message in chat
            this.appendMessage('bot', 'Sorry, I encountered an error. Please try again.');
        } finally {
            // Re-enable input
            this.setInputState(true);
            this.messageInput.focus();
        }
    }

    /**
     * Append message to chat window
     * @param {string} sender - 'user' or 'bot'
     * @param {string} text - Message text
     */
    appendMessage(sender, text) {
        // Remove welcome message if it exists
        const welcomeMsg = this.chatMessages.querySelector('.welcome-message');
        if (welcomeMsg) {
            welcomeMsg.remove();
        }

        // Create message element
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}`;

        const avatarDiv = document.createElement('div');
        avatarDiv.className = 'message-avatar';
        avatarDiv.innerHTML = sender === 'user' 
            ? '<i class="fas fa-user"></i>' 
            : '<i class="fas fa-robot"></i>';

        const contentDiv = document.createElement('div');
        
        const messageContent = document.createElement('div');
        messageContent.className = 'message-content';
        messageContent.textContent = text;

        const messageTime = document.createElement('div');
        messageTime.className = 'message-time';
        messageTime.textContent = this.getCurrentTime();

        contentDiv.appendChild(messageContent);
        contentDiv.appendChild(messageTime);

        messageDiv.appendChild(avatarDiv);
        messageDiv.appendChild(contentDiv);

        // Insert before typing indicator
        const typingContainer = this.typingIndicator.parentElement;
        this.chatMessages.insertBefore(messageDiv, typingContainer);

        // Scroll to bottom
        this.scrollToBottom();
    }

    /**
     * Show/hide typing indicator
     * @param {boolean} show - Show or hide
     */
    showTyping(show) {
        if (show) {
            this.typingIndicator.classList.add('active');
        } else {
            this.typingIndicator.classList.remove('active');
        }
        this.scrollToBottom();
    }

    /**
     * Enable/disable input and button
     * @param {boolean} enabled - Enable or disable
     */
    setInputState(enabled) {
        this.messageInput.disabled = !enabled;
        this.sendButton.disabled = !enabled;
    }

    /**
     * Scroll chat to bottom
     */
    scrollToBottom() {
        setTimeout(() => {
            this.chatMessages.scrollTop = this.chatMessages.scrollHeight;
        }, 100);
    }

    /**
     * Get current time formatted
     * @returns {string} Formatted time
     */
    getCurrentTime() {
        const now = new Date();
        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = now.getMinutes().toString().padStart(2, '0');
        return `${hours}:${minutes}`;
    }

    /**
     * Show toast notification
     * @param {string} message - Toast message
     * @param {string} type - Toast type ('success' or 'error')
     */
    showToast(message, type = 'error') {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.className = `toast ${type} show`;

        setTimeout(() => {
            toast.classList.remove('show');
        }, 4000);
    }
}

// Initialize chat application when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new ChatApp();
});
