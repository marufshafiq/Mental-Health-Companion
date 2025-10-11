<?php
// File: mood.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userName = $_SESSION['name'] ?? 'User';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mood Tracker - Mental Health Companion</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="mood.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header" onclick="toggleSidebar()" title="Click to collapse/expand">
                <div class="sidebar-logo">🧠</div>
                <h2>Mental Health Companion</h2>
            </div>
            <nav>
                <a href="dashboard.php" data-tooltip="Dashboard">
                    <span class="nav-icon">📊</span>
                    <span class="nav-text">Dashboard</span>
                </a>
                <a href="journal.php" data-tooltip="Journal">
                    <span class="nav-icon">📓</span>
                    <span class="nav-text">Journal</span>
                </a>
                <a href="mood.php" class="active" data-tooltip="Mood Tracker">
                    <span class="nav-icon">😊</span>
                    <span class="nav-text">Mood Tracker</span>
                </a>
                <a href="views/chat.php" data-tooltip="AI Chatbot">
                    <span class="nav-icon">💬</span>
                    <span class="nav-text">Chatbot</span>
                </a>
                <a href="views/meditation.php" data-tooltip="Meditation & Resources">
                    <span class="nav-icon">🧘‍♀️</span>
                    <span class="nav-text">Meditation</span>
                </a>
                <a href="views/profile/profile.php" data-tooltip="Profile">
                    <span class="nav-icon">👤</span>
                    <span class="nav-text">Profile</span>
                </a>
                <a href="logout.php" data-tooltip="Logout">
                    <span class="nav-icon">🚪</span>
                    <span class="nav-text">Logout</span>
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content" id="mainContent">
            <div class="page-header">
                <h1>😊 Mood Tracker</h1>
                <p>Track your emotional wellbeing and discover patterns</p>
            </div>

            <div class="mood-grid">
                <!-- Mood Selection Card -->
                <div class="mood-card mood-input-section">
                    <h3><i class="fas fa-smile"></i> How are you feeling right now?</h3>
                    <div class="mood-selector">
                        <div class="mood-option" data-mood="happy" data-value="5" title="Happy">
                            <span class="mood-emoji">😊</span>
                            <span class="mood-label">Happy</span>
                        </div>
                        <div class="mood-option" data-mood="excited" data-value="5" title="Excited">
                            <span class="mood-emoji">😄</span>
                            <span class="mood-label">Excited</span>
                        </div>
                        <div class="mood-option" data-mood="calm" data-value="4" title="Calm">
                            <span class="mood-emoji">😌</span>
                            <span class="mood-label">Calm</span>
                        </div>
                        <div class="mood-option" data-mood="neutral" data-value="3" title="Neutral">
                            <span class="mood-emoji">😐</span>
                            <span class="mood-label">Neutral</span>
                        </div>
                        <div class="mood-option" data-mood="tired" data-value="2" title="Tired">
                            <span class="mood-emoji">😴</span>
                            <span class="mood-label">Tired</span>
                        </div>
                        <div class="mood-option" data-mood="stressed" data-value="2" title="Stressed">
                            <span class="mood-emoji">😰</span>
                            <span class="mood-label">Stressed</span>
                        </div>
                        <div class="mood-option" data-mood="anxious" data-value="2" title="Anxious">
                            <span class="mood-emoji">😟</span>
                            <span class="mood-label">Anxious</span>
                        </div>
                        <div class="mood-option" data-mood="sad" data-value="1" title="Sad">
                            <span class="mood-emoji">😢</span>
                            <span class="mood-label">Sad</span>
                        </div>
                    </div>

                    <form id="moodForm">
                        <input type="hidden" name="mood_type" id="moodType">
                        <input type="hidden" name="mood_value" id="moodValue">
                        <textarea name="notes" id="moodNotes" placeholder="Add a note about how you're feeling (optional)" rows="3"></textarea>
                        <button type="submit" class="btn-primary" id="recordBtn" disabled>
                            <i class="fas fa-check-circle"></i> Record Mood
                        </button>
                    </form>
                </div>

                <!-- Mood Trends Card -->
                <div class="mood-card mood-trends">
                    <div class="card-header">
                        <h3><i class="fas fa-chart-line"></i> Your Mood Trends</h3>
                        <div class="chart-controls">
                            <button class="btn-control active" onclick="updateChart(7)" data-period="7">Week</button>
                            <button class="btn-control" onclick="updateChart(30)" data-period="30">Month</button>
                            <button class="btn-control" onclick="updateChart(90)" data-period="90">3 Months</button>
                        </div>
                    </div>
                    <div style="height: 200px; position: relative;">
                        <canvas id="moodChart"></canvas>
                    </div>
                    <div id="moodStats" class="mood-stats"></div>
                </div>

                <!-- Analysis Card -->
                <div class="mood-card mood-analysis">
                    <h3><i class="fas fa-brain"></i> Mood Analysis</h3>
                    <div class="analysis-tabs">
                        <button class="tab-btn active" onclick="loadAnalysis('trend')">Trend</button>
                        <button class="tab-btn" onclick="loadAnalysis('pattern')">Pattern</button>
                        <button class="tab-btn" onclick="loadAnalysis('weekly')">Weekly</button>
                    </div>
                    <div id="analysisResults" class="analysis-content">
                        <div class="loading">Loading analysis...</div>
                    </div>
                </div>

                <!-- Suggestions Card -->
                <div class="mood-card mood-suggestions">
                    <h3><i class="fas fa-lightbulb"></i> Personalized Suggestions</h3>
                    <div id="suggestionsList" class="suggestions-list">
                        <p class="text-muted">Select a mood to see personalized suggestions</p>
                    </div>
                    <div id="emergencyResources" class="emergency-resources" style="display: none;">
                        <h4><i class="fas fa-exclamation-triangle"></i> Need Immediate Support?</h4>
                        <div id="emergencyList"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentMoodChart = null;
        let currentPeriod = 30;

        // Mood selection
        document.querySelectorAll('.mood-option').forEach(option => {
            option.addEventListener('click', () => {
                document.querySelectorAll('.mood-option').forEach(opt => opt.classList.remove('selected'));
                option.classList.add('selected');
                document.getElementById('moodType').value = option.dataset.mood;
                document.getElementById('moodValue').value = option.dataset.value;
                document.getElementById('recordBtn').disabled = false;
                loadSuggestions(option.dataset.mood, option.dataset.value);
            });
        });

        // Form submission
        document.getElementById('moodForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            if (!document.getElementById('moodType').value) {
                showNotification('Please select a mood first', 'error');
                return;
            }

            const formData = new FormData(e.target);
            document.getElementById('recordBtn').disabled = true;
            document.getElementById('recordBtn').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Recording...';
            
            try {
                const response = await fetch('api/mood_record.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                
                if (result.success) {
                    showNotification('Mood recorded successfully! 🎉', 'success');
                    e.target.reset();
                    document.querySelectorAll('.mood-option').forEach(opt => opt.classList.remove('selected'));
                    document.getElementById('recordBtn').disabled = true;
                    document.getElementById('recordBtn').innerHTML = '<i class="fas fa-check-circle"></i> Record Mood';
                    
                    // Refresh data
                    updateChart(currentPeriod);
                    loadAnalysis('trend');
                    
                    // Clear suggestions
                    document.getElementById('suggestionsList').innerHTML = '<p class="text-muted">Select a mood to see personalized suggestions</p>';
                } else {
                    showNotification('Error: ' + result.message, 'error');
                    document.getElementById('recordBtn').disabled = false;
                    document.getElementById('recordBtn').innerHTML = '<i class="fas fa-check-circle"></i> Record Mood';
                }
            } catch (error) {
                showNotification('Error recording mood. Please try again.', 'error');
                document.getElementById('recordBtn').disabled = false;
                document.getElementById('recordBtn').innerHTML = '<i class="fas fa-check-circle"></i> Record Mood';
            }
        });

        // Load mood suggestions
        async function loadSuggestions(moodType, moodValue) {
            try {
                const response = await fetch(`api/mood_suggestions.php?mood_type=${moodType}&value=${moodValue}&count=6`);
                const data = await response.json();
                
                if (data.success) {
                    const suggestionsList = document.getElementById('suggestionsList');
                    suggestionsList.innerHTML = data.suggestions.map(suggestion => `
                        <div class="suggestion-item">
                            <div class="suggestion-content">
                                <p>${suggestion}</p>
                            </div>
                        </div>
                    `).join('');
                    
                    // Show emergency resources if needed
                    if (data.emergency_resources) {
                        const emergencyDiv = document.getElementById('emergencyResources');
                        const emergencyList = document.getElementById('emergencyList');
                        
                        emergencyList.innerHTML = data.emergency_resources.map(resource => `
                            <div class="emergency-item">
                                <h5>${resource.name}</h5>
                                <p><strong>Contact:</strong> ${resource.contact}</p>
                                <p><strong>Available:</strong> ${resource.available}</p>
                                <p class="text-muted">${resource.description}</p>
                            </div>
                        `).join('');
                        
                        emergencyDiv.style.display = 'block';
                    } else {
                        document.getElementById('emergencyResources').style.display = 'none';
                    }
                }
            } catch (error) {
                console.error('Error loading suggestions:', error);
            }
        }

        // Update mood chart
        async function updateChart(days) {
            currentPeriod = days;
            
            // Update active button
            document.querySelectorAll('.btn-control').forEach(btn => {
                btn.classList.remove('active');
                if (parseInt(btn.dataset.period) === days) {
                    btn.classList.add('active');
                }
            });
            
            try {
                const response = await fetch(`api/mood_history.php?days=${days}`);
                const data = await response.json();
                
                if (!data.success) {
                    showNotification('Error loading mood history', 'error');
                    return;
                }
                
                const moodData = data.entries;
                const stats = data.statistics;
                
                // Update statistics display
                const statsDiv = document.getElementById('moodStats');
                statsDiv.innerHTML = `
                    <div class="stat-item">
                        <span class="stat-label">Total Entries:</span>
                        <span class="stat-value">${stats.total_entries}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Average Mood:</span>
                        <span class="stat-value">${stats.average_mood.toFixed(1)}/5</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Dominant Mood:</span>
                        <span class="stat-value">${getMoodEmoji(stats.dominant_mood)} ${capitalizeFirst(stats.dominant_mood)}</span>
                    </div>
                `;
                
                // Prepare chart data
                const dates = moodData.map(entry => {
                    const date = new Date(entry.created_at);
                    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                });
                const values = moodData.map(entry => entry.mood_value);
                const moodTypes = moodData.map(entry => entry.mood_type);

                // Destroy existing chart
                if (currentMoodChart) {
                    currentMoodChart.destroy();
                }

                // Create new chart
                const ctx = document.getElementById('moodChart').getContext('2d');
                currentMoodChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: dates.reverse(),
                        datasets: [{
                            label: 'Mood Level',
                            data: values.reverse(),
                            borderColor: '#4CAF50',
                            backgroundColor: 'rgba(76, 175, 80, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#4CAF50',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const index = context.dataIndex;
                                        const mood = moodTypes.reverse()[index];
                                        const value = context.parsed.y;
                                        return `${getMoodEmoji(mood)} ${capitalizeFirst(mood)} (${value}/5)`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                min: 0,
                                max: 5,
                                ticks: {
                                    stepSize: 1,
                                    font: {
                                        size: 10
                                    },
                                    callback: function(value) {
                                        const labels = ['', '😢', '😰', '😐', '😌', '😊'];
                                        return labels[value] || '';
                                    }
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            },
                            x: {
                                ticks: {
                                    font: {
                                        size: 10
                                    },
                                    maxRotation: 45,
                                    minRotation: 45
                                },
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Error updating chart:', error);
                showNotification('Error loading mood data', 'error');
            }
        }

        // Load mood analysis
        async function loadAnalysis(type) {
            // Update active tab
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            const resultsDiv = document.getElementById('analysisResults');
            resultsDiv.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Analyzing...</div>';
            
            try {
                const response = await fetch(`api/mood_analysis.php?days=${currentPeriod}&type=${type}`);
                const data = await response.json();
                
                if (!data.success) {
                    resultsDiv.innerHTML = '<p class="error">Error loading analysis</p>';
                    return;
                }
                
                const analysis = data.analysis;
                let html = '';
                
                switch (type) {
                    case 'trend':
                        html = `
                            <div class="analysis-result">
                                <div class="trend-indicator trend-${analysis.trend}">
                                    ${analysis.message}
                                </div>
                                <div class="analysis-stats">
                                    <div class="stat-box">
                                        <span class="stat-label">Overall Average</span>
                                        <span class="stat-value">${analysis.average}/5</span>
                                    </div>
                                    <div class="stat-box">
                                        <span class="stat-label">First Period</span>
                                        <span class="stat-value">${analysis.first_period_avg}/5</span>
                                    </div>
                                    <div class="stat-box">
                                        <span class="stat-label">Second Period</span>
                                        <span class="stat-value">${analysis.second_period_avg}/5</span>
                                    </div>
                                </div>
                                ${analysis.suggestions ? `
                                    <div class="trend-suggestions">
                                        <h4>Recommendations:</h4>
                                        ${analysis.suggestions.map(s => `<p>• ${s}</p>`).join('')}
                                    </div>
                                ` : ''}
                            </div>
                        `;
                        break;
                    
                    case 'pattern':
                        const distribution = Object.entries(analysis.mood_distribution)
                            .map(([mood, count]) => `
                                <div class="mood-dist-item">
                                    <span class="mood-name">${getMoodEmoji(mood)} ${capitalizeFirst(mood)}</span>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: ${(count / data.total_entries) * 100}%"></div>
                                    </div>
                                    <span class="mood-count">${count}</span>
                                </div>
                            `).join('');
                        
                        html = `
                            <div class="analysis-result">
                                <div class="pattern-message">${analysis.message}</div>
                                <div class="mood-distribution">
                                    <h4>Mood Distribution:</h4>
                                    ${distribution}
                                </div>
                            </div>
                        `;
                        break;
                    
                    case 'weekly':
                        html = `
                            <div class="analysis-result">
                                <div class="weekly-message">${analysis.message}</div>
                                <div class="analysis-stats">
                                    <div class="stat-box">
                                        <span class="stat-label">Entries</span>
                                        <span class="stat-value">${analysis.total_entries}</span>
                                    </div>
                                    <div class="stat-box">
                                        <span class="stat-label">Days Tracked</span>
                                        <span class="stat-value">${analysis.days_tracked}/7</span>
                                    </div>
                                    <div class="stat-box">
                                        <span class="stat-label">Consistency</span>
                                        <span class="stat-value">${analysis.consistency}%</span>
                                    </div>
                                </div>
                            </div>
                        `;
                        break;
                }
                
                resultsDiv.innerHTML = html;
            } catch (error) {
                console.error('Error loading analysis:', error);
                resultsDiv.innerHTML = '<p class="error">Error loading analysis</p>';
            }
        }

        // Helper functions
        function getMoodEmoji(mood) {
            const emojis = {
                'happy': '😊',
                'calm': '😌',
                'neutral': '😐',
                'stressed': '😰',
                'sad': '😢',
                'anxious': '😟',
                'excited': '😄',
                'tired': '😴'
            };
            return emojis[mood] || '😐';
        }

        function capitalizeFirst(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.add('show');
            }, 10);
            
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Sidebar toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('sidebar-collapsed');
            
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        }

        // Restore sidebar state
        window.addEventListener('DOMContentLoaded', function() {
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            
            if (isCollapsed) {
                document.getElementById('sidebar').classList.add('collapsed');
                document.getElementById('mainContent').classList.add('sidebar-collapsed');
            }
            
            // Initialize with 30-day chart and trend analysis
            updateChart(30);
            loadAnalysis('trend');
        });
    </script>
</body>
</html>