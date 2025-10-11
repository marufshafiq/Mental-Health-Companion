<?php
// File: services/JournalFactory.php

/**
 * Journal Factory Pattern
 * Creates different types of journal entries: Daily, Gratitude, Goal
 */

// Abstract Journal Class
abstract class Journal {
    protected $title;
    protected $content;
    protected $type;
    protected $prompts = [];
    
    abstract public function getPrompts(): array;
    abstract public function getIcon(): string;
    abstract public function getColor(): string;
    abstract public function getDefaultTitle(): string;
    
    public function setTitle($title) {
        $this->title = $title;
    }
    
    public function setContent($content) {
        $this->content = $content;
    }
    
    public function getType() {
        return $this->type;
    }
    
    public function formatContent($content): string {
        return trim($content);
    }
}

// Daily Journal
class DailyJournal extends Journal {
    public function __construct() {
        $this->type = 'daily';
        $this->prompts = [
            'How was your day today?',
            'What made you smile today?',
            'What challenged you today?',
            'What did you learn today?',
            'What are you grateful for today?'
        ];
    }
    
    public function getPrompts(): array {
        return $this->prompts;
    }
    
    public function getIcon(): string {
        return '📓';
    }
    
    public function getColor(): string {
        return '#4CAF50';
    }
    
    public function getDefaultTitle(): string {
        return 'Daily Journal - ' . date('F j, Y');
    }
}

// Gratitude Journal
class GratitudeJournal extends Journal {
    public function __construct() {
        $this->type = 'gratitude';
        $this->prompts = [
            'What are you grateful for today?',
            'Who made a positive impact on you today?',
            'What small joy did you experience?',
            'What do you appreciate about yourself?',
            'What comfort or luxury are you thankful for?'
        ];
    }
    
    public function getPrompts(): array {
        return $this->prompts;
    }
    
    public function getIcon(): string {
        return '🙏';
    }
    
    public function getColor(): string {
        return '#FF9800';
    }
    
    public function getDefaultTitle(): string {
        return 'Gratitude Journal - ' . date('F j, Y');
    }
}

// Goal Journal
class GoalJournal extends Journal {
    public function __construct() {
        $this->type = 'goal';
        $this->prompts = [
            'What goals do you want to achieve?',
            'What steps can you take toward your goals?',
            'What obstacles might you face?',
            'How will you overcome these obstacles?',
            'What progress have you made?'
        ];
    }
    
    public function getPrompts(): array {
        return $this->prompts;
    }
    
    public function getIcon(): string {
        return '🎯';
    }
    
    public function getColor(): string {
        return '#2196F3';
    }
    
    public function getDefaultTitle(): string {
        return 'Goal Journal - ' . date('F j, Y');
    }
}

// Journal Factory
class JournalFactory {
    /**
     * Create journal instance based on type
     * @param string $type - 'daily', 'gratitude', or 'goal'
     * @return Journal
     * @throws Exception if type is invalid
     */
    public static function createJournal(string $type): Journal {
        switch (strtolower($type)) {
            case 'daily':
                return new DailyJournal();
            case 'gratitude':
                return new GratitudeJournal();
            case 'goal':
                return new GoalJournal();
            default:
                throw new Exception("Unknown journal type: {$type}");
        }
    }
    
    /**
     * Get all available journal types
     * @return array
     */
    public static function getAvailableTypes(): array {
        return [
            'daily' => [
                'name' => 'Daily Journal',
                'description' => 'Reflect on your day',
                'icon' => '📓',
                'color' => '#4CAF50'
            ],
            'gratitude' => [
                'name' => 'Gratitude Journal',
                'description' => 'Count your blessings',
                'icon' => '🙏',
                'color' => '#FF9800'
            ],
            'goal' => [
                'name' => 'Goal Journal',
                'description' => 'Track your progress',
                'icon' => '🎯',
                'color' => '#2196F3'
            ]
        ];
    }
}
