<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "isd");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Signup logic
if (isset($_POST['signup'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Sanitize inputs
    $name = mysqli_real_escape_string($conn, $name);
    $email = mysqli_real_escape_string($conn, $email);
    $username = mysqli_real_escape_string($conn, $username);
    $password = mysqli_real_escape_string($conn, $password);

    // Check if username exists
    $check = $conn->query("SELECT * FROM users WHERE username='$username'");
    if ($check->num_rows > 0) {
        echo "<script>alert('Username already taken');</script>";
    } else {
        $sql = "INSERT INTO users (name, email, username, password)
                VALUES ('$name', '$email', '$username', '$password')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Signup successful! Please login.'); window.location='login.php';</script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sign Up</title>
  <link rel="stylesheet" href="signup.css">
</head>
<body>
  <div class="signup-container">
    <h2>Create Account</h2>
    <form action="" method="POST">
      <div class="input-group">
        <input type="text" name="name" placeholder="Full Name" required>
      </div>
      <div class="input-group">
        <input type="email" name="email" placeholder="Email" required>
      </div>
      <div class="input-group">
        <input type="text" name="username" placeholder="Username" required>
      </div>
      <div class="input-group">
        <input type="password" name="password" placeholder="Password" required>
      </div>
      <button type="submit" name="signup">Sign Up</button>
    </form>
    <p class="login-link">Already have an account? <a href="login.php">Login</a></p>
  </div>
</body>
</html>
