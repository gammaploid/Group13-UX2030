<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'operator') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Welcome, Operator <?php echo $_SESSION['username']; ?></h1>
    <p>You are logged in as an Operator.</p>
    <a href="logout.php">Logout</a>
</body>
</html>
