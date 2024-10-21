<?php
// delete_user.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?error=access_denied");
    exit();
}

include 'db_connection.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql = "DELETE FROM users WHERE id=$id";

if ($conn->query($sql) === TRUE) {
    header("Location: user_management.php?success=User deleted successfully");
} else {
    header("Location: user_management.php?error=Error deleting user: " . $conn->error);
}

$conn->close();
?>
