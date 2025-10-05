<?php
session_start();

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
  <title>Home</title>
  <!-- Load Dashboard CSS -->
  <link rel="stylesheet" href="dashboard.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
</head>
<body>
  <!-- Include the dashboard content -->
  <?php include 'dashboard.php'; ?>
</body>
</html>
