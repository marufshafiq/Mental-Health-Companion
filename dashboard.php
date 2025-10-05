<?php

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$name = $_SESSION['name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <link rel="stylesheet" href="dashboard.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
</head>
<body>
  <div class="sidebar">
    <h2>Mental Health Companion</h2>
    <ul>
      <li><a href="journal.php">📓 Journal</a></li>
      <li><a href="mood_tracker.php">📊 Mood Tracker</a></li>
      <li><a href="chatbot.php">💬 Chatbot</a></li>
      <li><a href="profile.php">👤 Profile</a></li>
      <li><a href="logout.php">🚪 Logout</a></li>
    </ul>
  </div>

  <div class="main-content">
    <h1>Welcome, <?php echo htmlspecialchars($name); ?> 👋</h1>
    <p>Select an option from the menu to get started.</p>
  </div>
</body>
</html>
