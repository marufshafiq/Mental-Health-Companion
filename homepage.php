<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Home</title>
  <link rel="stylesheet" href="homepage.css">
</head>
<body>
  <div class="container">
    <h2>Welcome, <?php echo htmlspecialchars($name); ?> 👋</h2>
    <p>You have successfully logged in to your account.</p>
    <a href="logout.php"><button>Logout</button></a>

    <footer>
      <p>Mental Health Companion © 2025</p>
    </footer>
  </div>
</body>
</html>