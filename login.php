<?php
session_start();
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    // Login Logic
$sql = "SELECT id, username, role, password FROM users WHERE username = '$username'";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    if (password_verify($password, $row['password'])) {
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role'];

        switch ($row['role']) {
            case 'admin':
                header("Location: admin_dashboard.php");
                break;
            case 'manager':
                header("Location: manager_dashboard.php");
                break;
            case 'operator':
                header("Location: operator_dashboard.php");
                break;
            case 'auditor':
                header("Location: auditor_dashboard.php");
                break;
            default:
                header("Location: login.php?error=invalid_role");
        }
    } else {
        header("Location: login.php?error=invalid_password");
    }
} else {
    header("Location: login.php?error=user_not_found");
}
$conn->close();

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMD Login</title>
    <link rel="stylesheet" type="text/css" href="styles/login.css">
</head>
<body>
    <form action="login.php" method="post">
        <h2>SMD Login</h2>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </form>
    <script src="scripts/login.js"></script>
</body>
</html>