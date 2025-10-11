-- =========================================================
-- Sample Mood Data for Testing
-- Inserts realistic mood entries for testing the analysis
-- =========================================================

USE `isd`;

-- Insert sample mood entries for user_id = 0 (or change to your user ID)
-- These entries span the last 14 days for testing trend analysis

-- Week 1 - Declining trend
INSERT INTO mood_entries (user_id, mood_type, mood_value, notes, entry_date, entry_time) VALUES
(0, 'happy', 5, 'Great start to the week!', DATE_SUB(CURDATE(), INTERVAL 14 DAY), '09:30:00'),
(0, 'calm', 4, 'Feeling peaceful', DATE_SUB(CURDATE(), INTERVAL 13 DAY), '10:15:00'),
(0, 'happy', 5, 'Had a good day', DATE_SUB(CURDATE(), INTERVAL 12 DAY), '14:20:00'),
(0, 'neutral', 3, 'Just okay today', DATE_SUB(CURDATE(), INTERVAL 11 DAY), '11:00:00'),
(0, 'stressed', 2, 'Work pressure mounting', DATE_SUB(CURDATE(), INTERVAL 10 DAY), '16:45:00'),
(0, 'stressed', 2, 'Still stressed', DATE_SUB(CURDATE(), INTERVAL 9 DAY), '12:30:00'),
(0, 'sad', 1, 'Not feeling well', DATE_SUB(CURDATE(), INTERVAL 8 DAY), '18:00:00');

-- Week 2 - Improving trend
INSERT INTO mood_entries (user_id, mood_type, mood_value, notes, entry_date, entry_time) VALUES
(0, 'tired', 2, 'Low energy', DATE_SUB(CURDATE(), INTERVAL 7 DAY), '08:00:00'),
(0, 'neutral', 3, 'Getting better', DATE_SUB(CURDATE(), INTERVAL 6 DAY), '13:15:00'),
(0, 'calm', 4, 'Relaxed today', DATE_SUB(CURDATE(), INTERVAL 5 DAY), '10:30:00'),
(0, 'calm', 4, 'Feeling stable', DATE_SUB(CURDATE(), INTERVAL 4 DAY), '15:00:00'),
(0, 'happy', 5, 'Much better!', DATE_SUB(CURDATE(), INTERVAL 3 DAY), '11:45:00'),
(0, 'excited', 5, 'Great news today', DATE_SUB(CURDATE(), INTERVAL 2 DAY), '14:30:00'),
(0, 'happy', 5, 'Wonderful day', DATE_SUB(CURDATE(), INTERVAL 1 DAY), '12:00:00'),
(0, 'calm', 4, 'Feeling good', CURDATE(), '09:15:00');

-- Add some variety for pattern analysis
INSERT INTO mood_entries (user_id, mood_type, mood_value, notes, entry_date, entry_time) VALUES
(0, 'anxious', 2, 'A bit worried', DATE_SUB(CURDATE(), INTERVAL 10 DAY), '20:00:00'),
(0, 'tired', 2, 'Need rest', DATE_SUB(CURDATE(), INTERVAL 8 DAY), '22:30:00'),
(0, 'excited', 5, 'Looking forward to weekend', DATE_SUB(CURDATE(), INTERVAL 5 DAY), '17:00:00');

-- Success message
SELECT 'Sample mood data inserted successfully!' AS Result;
SELECT COUNT(*) as TotalEntries FROM mood_entries;
SELECT mood_type, COUNT(*) as Count FROM mood_entries GROUP BY mood_type ORDER BY Count DESC;
