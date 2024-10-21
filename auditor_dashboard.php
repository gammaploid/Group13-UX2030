<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'auditor') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles/global.css">
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Welcome, Auditor <?php echo $_SESSION['username']; ?></h1>
    <p>You are logged in as an Auditor.</p>
    <a href="logout.php">Logout</a>
</body>
</html>
