<?php
// delete_machine.php
session_start();
include 'db_connection.php';

// Authentication check for admins only
if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php?error=access_denied");
    exit();
}

$machine_id = $_GET['log_id'];

$sql = "DELETE FROM machines WHERE log_id = $machine_id";
if ($conn->query($sql) === TRUE) {
    header("Location: machine_management.php?success=Machine deleted successfully");
    exit();
} else {
    header("Location: machine_management.php?error=Error deleting machine: " . $conn->error);
    exit();
}
?>
