<?php
session_start();
include 'db_connection.php';

// Authentication check for admins only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?error=access_denied");
    exit();
}

$operator_id = isset($_GET['operator_id']) ? (int)$_GET['operator_id'] : 0;

$sql = "DELETE FROM operators WHERE operator_id=$operator_id";

if ($conn->query($sql) === TRUE) {
    header("Location: operator_management.php?success=Operator deleted successfully");
} else {
    header("Location: operator_management.php?error=Error deleting operator: " . $conn->error);
}

$conn->close();
?>
