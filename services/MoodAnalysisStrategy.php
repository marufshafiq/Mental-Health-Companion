<?php
// File: services/MoodAnalysisStrategy.php

/**
 * MoodAnalysisStrategy Interface
 * 
 * Design Pattern: Strategy
 * Allows different algorithms for analyzing mood data without changing client code
 * 
 * @package MentalHealthCompanion
 */
interface MoodAnalysisStrategy {
    /**
     * Analyze mood data and return insights
     * 
     * @param array $moodEntries Array of mood entry records
     * @return array Analysis results
     */
    public function analyze(array $moodEntries): array;
}

/**
 * Trend Analysis Strategy
 * Analyzes overall mood trends over time
 */
class TrendAnalysisStrategy implements MoodAnalysisStrategy {
    public function analyze(array $moodEntries): array {
        if (empty($moodEntries)) {
            return [
                'trend' => 'neutral',
                'message' => 'Not enough data to analyze trends yet.',
                'average' => 0
            ];
        }

        $values = array_column($moodEntries, 'mood_value');
        $average = array_sum($values) / count($values);
        
        // Calculate trend (improving, declining, stable)
        $firstHalf = array_slice($values, 0, ceil(count($values) / 2));
        $secondHalf = array_slice($values, ceil(count($values) / 2));
        
        $firstAvg = array_sum($firstHalf) / count($firstHalf);
        $secondAvg = array_sum($secondHalf) / count($secondHalf);
        
        $difference = $secondAvg - $firstAvg;
        
        if ($difference > 0.5) {
            $trend = 'improving';
            $message = '📈 Your mood is improving! Keep up the positive momentum.';
        } elseif ($difference < -0.5) {
            $trend = 'declining';
            $message = '📉 Your mood has been declining. Consider reaching out for support.';
        } else {
            $trend = 'stable';
            $message = '➡️ Your mood has been stable recently.';
        }
        
        return [
            'trend' => $trend,
            'message' => $message,
            'average' => round($average, 1),
            'first_period_avg' => round($firstAvg, 1),
            'second_period_avg' => round($secondAvg, 1)
        ];
    }
}

/**
 * Emotional Pattern Strategy
 * Identifies patterns in emotional states
 */
class EmotionalPatternStrategy implements MoodAnalysisStrategy {
    public function analyze(array $moodEntries): array {
        if (empty($moodEntries)) {
            return [
                'dominant_mood' => 'unknown',
                'message' => 'Start tracking to see your emotional patterns.'
            ];
        }

        // Count mood types
        $moodCounts = [];
        foreach ($moodEntries as $entry) {
            $type = $entry['mood_type'] ?? 'neutral';
            $moodCounts[$type] = ($moodCounts[$type] ?? 0) + 1;
        }
        
        arsort($moodCounts);
        $dominantMood = array_key_first($moodCounts);
        $percentage = round(($moodCounts[$dominantMood] / count($moodEntries)) * 100);
        
        $moodEmojis = [
            'happy' => '😊',
            'calm' => '😌',
            'neutral' => '😐',
            'stressed' => '😰',
            'sad' => '😢',
            'anxious' => '😟',
            'excited' => '😄',
            'tired' => '😴'
        ];
        
        $emoji = $moodEmojis[$dominantMood] ?? '😐';
        
        return [
            'dominant_mood' => $dominantMood,
            'percentage' => $percentage,
            'emoji' => $emoji,
            'message' => "{$emoji} Your most common mood is '{$dominantMood}' ({$percentage}% of the time).",
            'mood_distribution' => $moodCounts
        ];
    }
}

/**
 * Weekly Summary Strategy
 * Provides weekly mood summaries
 */
class WeeklySummaryStrategy implements MoodAnalysisStrategy {
    public function analyze(array $moodEntries): array {
        if (empty($moodEntries)) {
            return [
                'total_entries' => 0,
                'message' => 'No mood entries this week yet.'
            ];
        }

        $values = array_column($moodEntries, 'mood_value');
        $average = array_sum($values) / count($values);
        $highest = max($values);
        $lowest = min($values);
        
        // Find days with entries
        $daysTracked = [];
        foreach ($moodEntries as $entry) {
            $day = date('l', strtotime($entry['created_at']));
            $daysTracked[$day] = true;
        }
        
        $consistency = round((count($daysTracked) / 7) * 100);
        
        return [
            'total_entries' => count($moodEntries),
            'average_mood' => round($average, 1),
            'highest_mood' => $highest,
            'lowest_mood' => $lowest,
            'days_tracked' => count($daysTracked),
            'consistency' => $consistency,
            'message' => "This week: {$consistency}% tracking consistency with an average mood of " . round($average, 1) . "/5"
        ];
    }
}

/**
 * Mood Analysis Context
 * Uses different strategies to analyze mood data
 */
class MoodAnalysisContext {
    private $strategy;
    
    /**
     * Set the analysis strategy
     * 
     * @param MoodAnalysisStrategy $strategy
     */
    public function setStrategy(MoodAnalysisStrategy $strategy): void {
        $this->strategy = $strategy;
    }
    
    /**
     * Execute the current strategy
     * 
     * @param array $moodEntries
     * @return array
     */
    public function executeStrategy(array $moodEntries): array {
        if (!$this->strategy) {
            throw new Exception('No analysis strategy set');
        }
        
        return $this->strategy->analyze($moodEntries);
    }
}
