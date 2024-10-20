<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'manager') {
    header("Location: login.php?error=access_denied");
    exit();
}

$machine_id = isset($_GET['machine_id']) ? (int)$_GET['machine_id'] : 0;

$sql = "DELETE FROM machines WHERE machine_id=$machine_id";

if ($conn->query($sql) === TRUE) {
    header("Location: manager_dashboard.php?message=Machine deleted successfully");
} else {
    header("Location: manager_dashboard.php?error=Error deleting machine: " . $conn->error);
}

$conn->close();
?>
