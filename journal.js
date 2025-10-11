// File: journal.js
// Journal Page JavaScript

let currentJournalType = 'daily';
let isEditMode = false;
let sentimentChart = null; // Store chart instance

// Journal prompts for each type
const journalPrompts = {
    daily: [
        'How was your day today?',
        'What made you smile today?',
        'What challenged you today?',
        'What did you learn today?',
        'What are you grateful for today?'
    ],
    gratitude: [
        'What are you grateful for today?',
        'Who made a positive impact on you today?',
        'What small joy did you experience?',
        'What do you appreciate about yourself?',
        'What comfort or luxury are you thankful for?'
    ],
    goal: [
        'What goals do you want to achieve?',
        'What steps can you take toward your goals?',
        'What obstacles might you face?',
        'How will you overcome these obstacles?',
        'What progress have you made?'
    ]
};

// Show new entry modal
function showNewEntryModal() {
    isEditMode = false;
    document.getElementById('modalTitleText').textContent = 'New Journal Entry';
    document.getElementById('entryId').value = '';
    document.getElementById('journalForm').reset();
    document.getElementById('sentimentResult').style.display = 'none';
    selectJournalType('daily');
    document.getElementById('entryModal').style.display = 'flex';
}

// Select journal type
function selectJournalType(type) {
    currentJournalType = type;
    document.getElementById('journalType').value = type;
    
    // Update active button
    document.querySelectorAll('.type-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.dataset.type === type) {
            btn.classList.add('active');
        }
    });
    
    // Update prompts
    const promptsSection = document.getElementById('promptsSection');
    promptsSection.innerHTML = `
        <div class="prompts-header">
            <i class="fas fa-lightbulb"></i> Writing Prompts
        </div>
        <ul class="prompts-list">
            ${journalPrompts[type].map(prompt => `<li>${prompt}</li>`).join('')}
        </ul>
    `;
    
    // Update title placeholder
    const titles = {
        daily: 'Daily Journal - ' + new Date().toLocaleDateString(),
        gratitude: 'Gratitude Journal - ' + new Date().toLocaleDateString(),
        goal: 'Goal Journal - ' + new Date().toLocaleDateString()
    };
    document.getElementById('entryTitle').placeholder = titles[type];
}

// Close modal
function closeModal() {
    document.getElementById('entryModal').style.display = 'none';
    document.getElementById('journalForm').reset();
}

// Close view modal
function closeViewModal() {
    document.getElementById('viewModal').style.display = 'none';
}

// Word count update
document.addEventListener('DOMContentLoaded', function() {
    const contentTextarea = document.getElementById('entryContent');
    if (contentTextarea) {
        contentTextarea.addEventListener('input', function() {
            const words = this.value.trim().split(/\s+/).filter(word => word.length > 0);
            document.getElementById('wordCount').textContent = words.length + ' words';
        });
    }
});

// Analyze sentiment
async function analyzeSentiment() {
    const content = document.getElementById('entryContent').value;
    
    if (!content.trim()) {
        alert('Please write some content first');
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('content', content);
        
        const response = await fetch('api/journal_sentiment.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            displaySentimentResult(result.sentiment);
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to analyze sentiment');
    }
}

// Display sentiment result
function displaySentimentResult(sentiment) {
    const resultDiv = document.getElementById('sentimentResult');
    resultDiv.innerHTML = `
        <div class="sentiment-header">
            <h4><i class="fas fa-brain"></i> Sentiment Analysis</h4>
        </div>
        <div class="sentiment-grid">
            <div class="sentiment-item">
                <span class="sentiment-label">Overall Mood</span>
                <span class="sentiment-value">
                    <span style="font-size: 2rem;">${sentiment.mood_emoji}</span>
                    <span style="color: ${sentiment.mood_color}; font-weight: 600;">${sentiment.mood_label}</span>
                </span>
            </div>
            <div class="sentiment-item">
                <span class="sentiment-label">Sentiment Score</span>
                <span class="sentiment-value">${sentiment.sentiment_score}</span>
            </div>
            <div class="sentiment-item">
                <span class="sentiment-label">Positive Words</span>
                <span class="sentiment-value" style="color: #4CAF50;">${sentiment.positive_count}</span>
            </div>
            <div class="sentiment-item">
                <span class="sentiment-label">Negative Words</span>
                <span class="sentiment-value" style="color: #F44336;">${sentiment.negative_count}</span>
            </div>
        </div>
        ${sentiment.positive_words.length > 0 ? `
            <div class="sentiment-words">
                <strong style="color: #4CAF50;">Positive:</strong> 
                ${sentiment.positive_words.join(', ')}
            </div>
        ` : ''}
        ${sentiment.negative_words.length > 0 ? `
            <div class="sentiment-words">
                <strong style="color: #F44336;">Negative:</strong> 
                ${sentiment.negative_words.join(', ')}
            </div>
        ` : ''}
    `;
    resultDiv.style.display = 'block';
}

// Submit journal form
document.getElementById('journalForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const endpoint = isEditMode ? 'api/journal_update.php' : 'api/journal_create.php';
    
    try {
        const response = await fetch(endpoint, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert(result.message);
            closeModal();
            // Refresh stats and chart without full page reload
            await refreshStatsAndChart();
            // Reload to update entries list
            setTimeout(() => location.reload(), 500);
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to save entry');
    }
});

// Edit entry
function editEntry(entryId) {
    isEditMode = true;
    
    // Fetch entry data
    fetch(`controllers/JournalController.php?action=get&id=${entryId}`)
        .then(response => response.json())
        .then(entry => {
            document.getElementById('modalTitleText').textContent = 'Edit Journal Entry';
            document.getElementById('entryId').value = entry.id;
            document.getElementById('entryTitle').value = entry.title;
            document.getElementById('entryContent').value = entry.content;
            
            // Determine journal type from tags
            const tags = entry.tags ? entry.tags.split(',') : [];
            const type = tags.find(t => ['daily', 'gratitude', 'goal'].includes(t.trim())) || 'daily';
            selectJournalType(type);
            
            document.getElementById('entryModal').style.display = 'flex';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load entry');
        });
}

// Delete entry
async function deleteEntry(entryId) {
    if (!confirm('Are you sure you want to delete this entry? This action cannot be undone.')) {
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('entry_id', entryId);
        
        const response = await fetch('api/journal_delete.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert(result.message);
            // Refresh stats and chart without full page reload
            await refreshStatsAndChart();
            // Reload to update entries list
            setTimeout(() => location.reload(), 500);
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to delete entry');
    }
}

// Toggle favorite
async function toggleFavorite(entryId) {
    try {
        const formData = new FormData();
        formData.append('entry_id', entryId);
        
        const response = await fetch('api/journal_favorite.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Refresh stats and chart without full page reload
            await refreshStatsAndChart();
            // Reload to update entries list
            setTimeout(() => location.reload(), 500);
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to update favorite');
    }
}

// View full entry
function viewEntry(entryId) {
    // Implementation for viewing full entry in modal
    window.location.href = `journal.php?action=view&id=${entryId}`;
}

// Filter by type
function filterByType(type) {
    if (type) {
        window.location.href = `journal.php?type=${type}`;
    } else {
        window.location.href = 'journal.php';
    }
}

// Show stats
function showStats() {
    alert('Stats feature coming soon!');
}

// Close modals on outside click
window.onclick = function(event) {
    const entryModal = document.getElementById('entryModal');
    const viewModal = document.getElementById('viewModal');
    
    if (event.target == entryModal) {
        closeModal();
    }
    if (event.target == viewModal) {
        closeViewModal();
    }
}

// Refresh stats and chart dynamically
async function refreshStatsAndChart() {
    try {
        // Get current filter type from URL
        const urlParams = new URLSearchParams(window.location.search);
        const type = urlParams.get('type') || '';
        
        const response = await fetch(`api/journal_stats.php?type=${type}`);
        const result = await response.json();
        
        if (result.success) {
            // Update stats cards
            updateStatsCards(result.stats);
            
            // Update sentiment chart
            if (result.sentiment_trend && result.sentiment_trend.length > 0) {
                updateSentimentChart(result.sentiment_trend);
            } else {
                // Hide chart if no data
                const chartContainer = document.querySelector('.chart-container');
                if (chartContainer) {
                    chartContainer.style.display = 'none';
                }
            }
        }
    } catch (error) {
        console.error('Error refreshing stats:', error);
    }
}

// Update stats cards with new data
function updateStatsCards(stats) {
    // Update total entries
    const totalEntriesElement = document.querySelector('.stat-card:nth-child(1) .stat-value');
    if (totalEntriesElement) {
        totalEntriesElement.textContent = stats.total_entries;
    }
    
    // Update total words
    const totalWordsElement = document.querySelector('.stat-card:nth-child(2) .stat-value');
    if (totalWordsElement) {
        totalWordsElement.textContent = stats.total_words.toLocaleString();
    }
    
    // Update favorites
    const favoritesElement = document.querySelector('.stat-card:nth-child(3) .stat-value');
    if (favoritesElement) {
        favoritesElement.textContent = stats.favorites;
    }
    
    // Update mood trend emoji
    const moodTrendElement = document.querySelector('.stat-card:nth-child(4) .stat-value');
    if (moodTrendElement) {
        moodTrendElement.textContent = stats.mood_trend;
    }
}

// Update sentiment chart with new data
function updateSentimentChart(sentimentData) {
    const canvas = document.getElementById('sentimentChart');
    if (!canvas) return;
    
    // Show chart container if hidden
    const chartContainer = document.querySelector('.chart-container');
    if (chartContainer) {
        chartContainer.style.display = 'block';
    }
    
    const ctx = canvas.getContext('2d');
    
    // Destroy existing chart if it exists
    if (sentimentChart) {
        sentimentChart.destroy();
    }
    
    // Create new chart
    sentimentChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: sentimentData.map(d => new Date(d.date).toLocaleDateString()),
            datasets: [{
                label: 'Sentiment Score',
                data: sentimentData.map(d => d.sentiment),
                borderColor: '#4CAF50',
                backgroundColor: 'rgba(76, 175, 80, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    min: -1,
                    max: 1,
                    ticks: {
                        callback: function(value) {
                            if (value >= 0.6) return '😊 Very Positive';
                            if (value >= 0.2) return '🙂 Positive';
                            if (value >= -0.2) return '😐 Neutral';
                            if (value >= -0.6) return '😕 Negative';
                            return '😢 Very Negative';
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}
