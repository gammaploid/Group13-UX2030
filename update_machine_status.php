<?php
// Connect to database
include 'db_connection.php';

// Update machine operational status
$machineId = $_POST['machineId'];
$status = $_POST['status'];

$sql = "UPDATE machines SET operational_status = '$status' WHERE log_id = '$machineId'";
if ($conn->query($sql) === TRUE) {
    echo "success";
} else {
    echo "error";
}
