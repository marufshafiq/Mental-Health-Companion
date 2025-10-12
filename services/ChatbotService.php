<?php
// File: services/ChatbotService.php

require_once __DIR__ . '/../config.php';

/**
 * ChatbotService
 * Handles bot response generation using external API or local fallback
 * Implements Strategy pattern for flexible response generation
 */
class ChatbotService {
    
    /**
     * Get bot response for user message
     * @param string $message User's message
     * @param string|null $conversationId Optional conversation identifier
     * @return string Bot response text
     */
    public static function getResponse($message, $conversationId = null) {
        // Try external API first if configured
        $apiResponse = self::getExternalApiResponse($message, $conversationId);
        
        if ($apiResponse !== null) {
            return $apiResponse;
        }

        // Fallback to local rule-based response
        return self::getFallbackResponse($message);
    }

    /**
     * Get response from external API (OpenAI or custom endpoint)
     * @param string $message User's message
     * @param string|null $conversationId Optional conversation identifier
     * @return string|null API response or null on failure
     */
    private static function getExternalApiResponse($message, $conversationId = null) {
        // Check if API is configured
        $apiUrl = defined('CHATBOT_API_URL') ? CHATBOT_API_URL : null;
        $apiKey = defined('CHATBOT_API_KEY') ? CHATBOT_API_KEY : null;

        if (empty($apiUrl) || empty($apiKey)) {
            return null; // API not configured, use fallback
        }

        try {
            // Prepare request payload
            $payload = [
                'model' => 'deepseek/deepseek-chat-v3.1',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a compassionate mental health support assistant. Provide supportive, empathetic responses.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $message
                    ]
                ],
                'max_tokens' => 1024,
                'temperature' => 0.6
            ];

            if ($conversationId) {
                $payload['conversation_id'] = $conversationId;
            }

            // Initialize cURL
            $ch = curl_init($apiUrl);
            
            // Set cURL options
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $apiKey
                ],
                CURLOPT_TIMEOUT => 30,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_SSL_VERIFYPEER => true
            ]);

            // Execute request
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            // Handle errors
            if ($curlError) {
                error_log("ChatbotService cURL error: $curlError");
                return null;
            }

            if ($httpCode !== 200) {
                error_log("ChatbotService API error: HTTP $httpCode - $response");
                return null;
            }

            // Parse response
            $data = json_decode($response, true);
            
            // Handle OpenAI format
            if (isset($data['choices'][0]['message']['content'])) {
                return trim($data['choices'][0]['message']['content']);
            }

            // Handle custom format with 'reply' field
            if (isset($data['reply'])) {
                return trim($data['reply']);
            }

            // Handle custom format with 'response' field
            if (isset($data['response'])) {
                return trim($data['response']);
            }

            error_log("ChatbotService: Unexpected API response format");
            return null;

        } catch (Exception $e) {
            error_log("ChatbotService exception: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get fallback rule-based response when API is unavailable
     * Implements simple pattern matching for mental health support
     * @param string $message User's message
     * @return string Fallback response text
     */
    private static function getFallbackResponse($message) {
        $messageLower = strtolower(trim($message));

        // Greeting patterns
        if (preg_match('/^(hi|hello|hey|greetings)/i', $messageLower)) {
            return "Hello! I'm here to support you. How are you feeling today?";
        }

        // Anxiety patterns
        if (preg_match('/(anxious|anxiety|worried|nervous|panic)/i', $messageLower)) {
            return "I understand you're feeling anxious. Try taking slow, deep breaths. Remember, it's okay to feel this way. Would you like to talk about what's troubling you?";
        }

        // Depression patterns
        if (preg_match('/(depressed|depression|sad|hopeless|down)/i', $messageLower)) {
            return "I hear that you're going through a difficult time. Your feelings are valid. Remember that you're not alone, and things can get better. Have you considered reaching out to a professional counselor?";
        }

        // Stress patterns
        if (preg_match('/(stressed|stress|overwhelmed|pressure)/i', $messageLower)) {
            return "Feeling stressed is completely normal. Try breaking tasks into smaller steps and take regular breaks. What specific situations are causing you stress?";
        }

        // Sleep issues
        if (preg_match('/(sleep|insomnia|tired|exhausted|can\'t sleep)/i', $messageLower)) {
            return "Sleep is crucial for mental health. Try maintaining a regular sleep schedule, avoiding screens before bed, and creating a relaxing bedtime routine. How long have you been experiencing sleep difficulties?";
        }

        // Loneliness patterns
        if (preg_match('/(lonely|alone|isolated|no friends)/i', $messageLower)) {
            return "Feeling lonely can be really hard. Remember that reaching out, even in small ways, can help. Consider joining groups or activities that interest you. I'm here to listen.";
        }

        // Crisis/emergency patterns
        if (preg_match('/(suicide|suicidal|kill myself|end it all|want to die)/i', $messageLower)) {
            return "I'm very concerned about what you're sharing. Please reach out to a crisis helpline immediately: National Suicide Prevention Lifeline: 988 or 1-800-273-8255. You don't have to face this alone.";
        }

        // Thank you patterns
        if (preg_match('/(thank|thanks|appreciate)/i', $messageLower)) {
            return "You're welcome! I'm here whenever you need support. Take care of yourself!";
        }

        // Goodbye patterns
        if (preg_match('/(bye|goodbye|see you|got to go)/i', $messageLower)) {
            return "Take care! Remember, I'm here whenever you need to talk. Wishing you well!";
        }

        // How are you patterns
        if (preg_match('/(how are you|how do you feel)/i', $messageLower)) {
            return "Thank you for asking! I'm here and ready to support you. More importantly, how are YOU feeling today?";
        }

        // Default supportive response
        $defaultResponses = [
            "I'm here to listen. Could you tell me more about what you're experiencing?",
            "Thank you for sharing. How does this situation make you feel?",
            "I understand this is important to you. What would help you feel better right now?",
            "Your feelings matter. Would you like to explore this further?",
            "I'm here to support you. What's been on your mind lately?"
        ];

        return $defaultResponses[array_rand($defaultResponses)];
    }
}
