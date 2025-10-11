<?php
// File: services/SentimentAnalysisService.php

/**
 * Sentiment Analysis Service
 * Analyzes journal content for positive/negative sentiment
 */

class SentimentAnalysisService {
    
    // Positive words list
    private static $positiveWords = [
        'happy', 'joy', 'love', 'excited', 'great', 'wonderful', 'amazing', 'fantastic',
        'excellent', 'good', 'better', 'best', 'beautiful', 'brilliant', 'calm', 'peaceful',
        'grateful', 'thankful', 'blessed', 'proud', 'accomplished', 'successful', 'win',
        'perfect', 'lovely', 'delightful', 'pleasant', 'positive', 'optimistic', 'hopeful',
        'cheerful', 'joyful', 'content', 'satisfied', 'pleased', 'thrilled', 'enthusiastic',
        'motivated', 'inspired', 'energized', 'confident', 'comfortable', 'relaxed', 'safe',
        'secure', 'warm', 'kind', 'friendly', 'nice', 'sweet', 'caring', 'loving', 'fun',
        'awesome', 'incredible', 'outstanding', 'spectacular', 'superb', 'marvelous',
        'fabulous', 'glorious', 'laugh', 'smile', 'enjoy', 'appreciate', 'admire', 'adore'
    ];
    
    // Negative words list
    private static $negativeWords = [
        'sad', 'depressed', 'unhappy', 'miserable', 'awful', 'terrible', 'horrible',
        'bad', 'worse', 'worst', 'angry', 'frustrated', 'annoyed', 'upset', 'stressed',
        'anxious', 'worried', 'afraid', 'scared', 'fear', 'nervous', 'tense', 'pain',
        'hurt', 'suffering', 'difficult', 'hard', 'struggle', 'problem', 'issue', 'trouble',
        'hate', 'dislike', 'disappoint', 'failure', 'failed', 'lost', 'defeat', 'rejected',
        'alone', 'lonely', 'isolated', 'empty', 'hopeless', 'helpless', 'worthless',
        'useless', 'tired', 'exhausted', 'drained', 'weak', 'sick', 'ill', 'unwell',
        'confused', 'lost', 'uncertain', 'doubt', 'regret', 'guilt', 'shame', 'embarrassed',
        'uncomfortable', 'uneasy', 'disturbed', 'bothered', 'irritated', 'mad', 'furious'
    ];
    
    /**
     * Analyze sentiment of text
     * @param string $text
     * @return array
     */
    public static function analyze($text): array {
        $words = self::extractWords($text);
        
        $positiveCount = 0;
        $negativeCount = 0;
        $foundPositiveWords = [];
        $foundNegativeWords = [];
        
        foreach ($words as $word) {
            $word = strtolower($word);
            
            if (in_array($word, self::$positiveWords)) {
                $positiveCount++;
                $foundPositiveWords[] = $word;
            }
            
            if (in_array($word, self::$negativeWords)) {
                $negativeCount++;
                $foundNegativeWords[] = $word;
            }
        }
        
        $totalSentimentWords = $positiveCount + $negativeCount;
        $sentiment = self::calculateSentiment($positiveCount, $negativeCount);
        $mood = self::determineMood($sentiment);
        
        return [
            'positive_count' => $positiveCount,
            'negative_count' => $negativeCount,
            'total_words' => count($words),
            'sentiment_words' => $totalSentimentWords,
            'sentiment_score' => $sentiment,
            'mood' => $mood['type'],
            'mood_emoji' => $mood['emoji'],
            'mood_color' => $mood['color'],
            'mood_label' => $mood['label'],
            'positive_words' => array_unique($foundPositiveWords),
            'negative_words' => array_unique($foundNegativeWords)
        ];
    }
    
    /**
     * Extract words from text
     * @param string $text
     * @return array
     */
    private static function extractWords($text): array {
        // Remove punctuation and split into words
        $text = preg_replace('/[^\w\s]/', ' ', $text);
        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        return $words;
    }
    
    /**
     * Calculate sentiment score (-1 to 1)
     * @param int $positive
     * @param int $negative
     * @return float
     */
    private static function calculateSentiment($positive, $negative): float {
        $total = $positive + $negative;
        
        if ($total === 0) {
            return 0.0; // Neutral
        }
        
        // Score: -1 (very negative) to 1 (very positive)
        $score = ($positive - $negative) / $total;
        return round($score, 2);
    }
    
    /**
     * Determine mood based on sentiment score
     * @param float $sentiment
     * @return array
     */
    private static function determineMood($sentiment): array {
        if ($sentiment >= 0.6) {
            return [
                'type' => 'very_positive',
                'emoji' => '😊',
                'color' => '#4CAF50',
                'label' => 'Very Positive'
            ];
        } elseif ($sentiment >= 0.2) {
            return [
                'type' => 'positive',
                'emoji' => '🙂',
                'color' => '#8BC34A',
                'label' => 'Positive'
            ];
        } elseif ($sentiment >= -0.2) {
            return [
                'type' => 'neutral',
                'emoji' => '😐',
                'color' => '#FFC107',
                'label' => 'Neutral'
            ];
        } elseif ($sentiment >= -0.6) {
            return [
                'type' => 'negative',
                'emoji' => '😕',
                'color' => '#FF9800',
                'label' => 'Negative'
            ];
        } else {
            return [
                'type' => 'very_negative',
                'emoji' => '😢',
                'color' => '#F44336',
                'label' => 'Very Negative'
            ];
        }
    }
    
    /**
     * Get sentiment trend from multiple entries
     * @param array $entries - Array of journal entries
     * @return array
     */
    public static function getTrend($entries): array {
        $trend = [];
        
        foreach ($entries as $entry) {
            $analysis = self::analyze($entry['content']);
            $trend[] = [
                'date' => $entry['created_at'],
                'sentiment' => $analysis['sentiment_score'],
                'mood' => $analysis['mood'],
                'emoji' => $analysis['mood_emoji']
            ];
        }
        
        return $trend;
    }
    
    /**
     * Get average sentiment from entries
     * @param array $entries
     * @return float
     */
    public static function getAverageSentiment($entries): float {
        if (empty($entries)) {
            return 0.0;
        }
        
        $totalSentiment = 0;
        foreach ($entries as $entry) {
            $analysis = self::analyze($entry['content']);
            $totalSentiment += $analysis['sentiment_score'];
        }
        
        return round($totalSentiment / count($entries), 2);
    }
}
