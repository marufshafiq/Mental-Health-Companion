<?php
session_start();

// Load configuration
require_once __DIR__ . '/config.php';

// Database connection
$conn = new mysqli("localhost", "root", "", "isd","3307");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Login logic
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Sanitize inputs
    $username = mysqli_real_escape_string($conn, $username);
    $password = mysqli_real_escape_string($conn, $password);

    // Query to check user
    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['name'] = $row['name']; // store the name for homepage
        $_SESSION['email'] = $row['email']; // store email for admin role check
        
        /**
         * Admin Role Assignment
         * Automatically assigns admin role if user email matches APP_ADMIN_EMAIL from config
         */
        if (isset($_SESSION['email']) && defined('APP_ADMIN_EMAIL') && $_SESSION['email'] === APP_ADMIN_EMAIL) {
            $_SESSION['role'] = 'admin';
            // Redirect admin users to admin panel
            header("Location: views/admin.php");
            exit();
        } else {
            $_SESSION['role'] = 'user'; // Default role for regular users
            header("Location: homepage.php");
            exit();
        }
    } else {
        echo "<script>alert('Invalid username or password');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link rel="stylesheet" href="login.css">
</head>
<body>
  <div class="container">
    <h2>Login</h2>
    <form action="" method="POST">
      <div class="input-group">
        <input type="text" name="username" placeholder="Username" required>
      </div>
      <div class="input-group">
        <input type="password" name="password" placeholder="Password" required>
      </div>
      <button type="submit" name="login">Login</button>
    </form>
    <p class="signup-link">Don't have an account? <a href="signup.php">Sign up</a></p>
  </div>
</body>
</html>
