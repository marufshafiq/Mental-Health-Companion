<?php
// File: services/MoodSuggestionService.php

/**
 * Mood Suggestion Service
 * Provides relaxation tips and suggestions based on current mood
 * 
 * Uses Strategy pattern internally for different suggestion algorithms
 * 
 * @package MentalHealthCompanion
 */
class MoodSuggestionService {
    
    private static $suggestions = [
        'happy' => [
            '🎉 Great mood! Share your positivity with others today.',
            '✨ Capture this moment - write in your journal about what made you happy.',
            '🎵 Listen to uplifting music to maintain your positive energy.',
            '🤸‍♀️ Channel this energy into a fun activity or exercise.',
            '📞 Reach out to a friend and spread the joy!',
            '🎨 Try a creative activity while you\'re feeling inspired.',
            '🌟 Set a positive goal for the week ahead.'
        ],
        'calm' => [
            '🧘‍♀️ Perfect time for meditation or mindfulness practice.',
            '📖 Enjoy a good book in this peaceful state.',
            '🌿 Take a gentle walk in nature to maintain your calm.',
            '☕ Savor a warm beverage mindfully.',
            '🎨 Try some light creative activities like drawing or coloring.',
            '🎶 Listen to ambient or classical music.',
            '✍️ Journal about your day and what brings you peace.'
        ],
        'neutral' => [
            '🤔 A good time to check in with yourself - what do you need right now?',
            '📝 Set small, achievable goals for the day.',
            '🚶‍♂️ A short walk might help lift your spirits.',
            '💭 Practice gratitude - list 3 things you\'re thankful for.',
            '📞 Connect with a friend or loved one.',
            '🎵 Create or listen to a playlist that matches your mood.',
            '🧘 Try a 5-minute breathing exercise.'
        ],
        'stressed' => [
            '🫁 Try box breathing: Breathe in (4 sec), hold (4 sec), out (4 sec), hold (4 sec).',
            '🧘‍♀️ Take a 10-minute meditation break.',
            '🚶 Go for a walk to clear your mind.',
            '📝 Write down what\'s stressing you - sometimes putting it on paper helps.',
            '💪 Light exercise can help reduce stress hormones.',
            '☕ Take a short break with a calming tea.',
            '🎵 Listen to calming music or nature sounds.',
            '📞 Talk to someone you trust about what\'s bothering you.',
            '🛁 Consider a relaxing bath or shower.'
        ],
        'sad' => [
            '🤗 It\'s okay to feel sad. Be gentle with yourself today.',
            '☎️ Reach out to a friend or family member for support.',
            '📝 Journal about your feelings - sometimes writing helps.',
            '🎵 Listen to music that comforts you.',
            '🐾 Spend time with a pet if you have one.',
            '🌅 Get some sunlight - even 10 minutes can help.',
            '💧 Remember to stay hydrated and eat nourishing food.',
            '🧘 Try a guided meditation for difficult emotions.',
            '📺 Watch something that usually brings you comfort.',
            '❤️ Practice self-compassion - treat yourself like you\'d treat a good friend.'
        ],
        'anxious' => [
            '🫁 Practice 4-7-8 breathing: Inhale (4), hold (7), exhale (8).',
            '✋ Use the 5-4-3-2-1 grounding technique: Name 5 things you see, 4 you touch, 3 you hear, 2 you smell, 1 you taste.',
            '📝 Write down your worries and challenge anxious thoughts.',
            '🚶 Physical movement can help - try a walk or gentle stretching.',
            '🎵 Listen to calming music or white noise.',
            '💭 Remind yourself: "This feeling is temporary and will pass."',
            '☕ Avoid caffeine, which can increase anxiety.',
            '🧘 Try progressive muscle relaxation.',
            '📞 Talk to someone you trust.',
            '🛏️ If possible, take a short nap in a cool, dark room.'
        ],
        'excited' => [
            '🎉 Wonderful! Channel this energy into something productive.',
            '📝 Write down your ideas while you\'re feeling creative.',
            '🤸 Perfect time for physical activity or exercise.',
            '🎨 Start that project you\'ve been thinking about.',
            '📞 Share your excitement with friends or family.',
            '📸 Document this moment for future reflection.',
            '🎯 Set ambitious but achievable goals.'
        ],
        'tired' => [
            '😴 Listen to your body - rest if you need to.',
            '💧 Make sure you\'re well hydrated.',
            '🥗 Eat something nourishing to boost your energy.',
            '☕ A short break with green tea might help.',
            '🚶 A brief walk outside can provide a natural energy boost.',
            '💤 Consider a 20-minute power nap.',
            '🛁 A cool shower can help wake you up.',
            '📴 Reduce screen time and get adequate sleep tonight.'
        ]
    ];
    
    /**
     * Get suggestions for a specific mood
     * 
     * @param string $moodType The mood type (happy, sad, stressed, etc.)
     * @param int $count Number of suggestions to return (default: 5)
     * @return array Array of suggestion strings
     */
    public static function getSuggestions(string $moodType, int $count = 5): array {
        $moodType = strtolower($moodType);
        
        if (!isset(self::$suggestions[$moodType])) {
            $moodType = 'neutral'; // Default fallback
        }
        
        $allSuggestions = self::$suggestions[$moodType];
        shuffle($allSuggestions); // Randomize for variety
        
        return array_slice($allSuggestions, 0, $count);
    }
    
    /**
     * Get suggestions based on mood trend
     * 
     * @param string $trend The trend (improving, declining, stable)
     * @return array Array of suggestion strings
     */
    public static function getSuggestionsByTrend(string $trend): array {
        switch ($trend) {
            case 'improving':
                return [
                    '🌟 Your mood is trending upward! Keep doing what\'s working for you.',
                    '✨ Maintain your positive habits - consistency is key.',
                    '📝 Journal about what has been helping you feel better.'
                ];
            
            case 'declining':
                return [
                    '💙 Consider reaching out to a mental health professional.',
                    '🤝 Talk to someone you trust about how you\'ve been feeling.',
                    '🧘 Increase self-care activities and stress management.',
                    '📞 Don\'t hesitate to ask for help - it\'s a sign of strength.'
                ];
            
            case 'stable':
            default:
                return [
                    '👍 Consistency is good! Keep up your routine.',
                    '🔄 Consider trying new wellness activities to enhance your wellbeing.',
                    '📊 Keep tracking to identify patterns over time.'
                ];
        }
    }
    
    /**
     * Get emergency resources if mood is critically low
     * 
     * @return array Array of emergency contact information
     */
    public static function getEmergencyResources(): array {
        return [
            [
                'name' => 'National Suicide Prevention Lifeline',
                'contact' => '988',
                'available' => '24/7',
                'description' => 'Free and confidential support'
            ],
            [
                'name' => 'Crisis Text Line',
                'contact' => 'Text HOME to 741741',
                'available' => '24/7',
                'description' => 'Text-based support'
            ],
            [
                'name' => 'SAMHSA National Helpline',
                'contact' => '1-800-662-4357',
                'available' => '24/7',
                'description' => 'Treatment referral and information'
            ]
        ];
    }
}
