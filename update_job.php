<?php
session_start();
include 'db_connection.php';

// Check if the request is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $job_id = $conn->real_escape_string($_POST['job_id']);
    $status = $conn->real_escape_string($_POST['status']);

    // Update the job status
    $sql_update = "UPDATE jobs SET status = '$status' WHERE job_id = $job_id";

    if ($conn->query($sql_update) === TRUE) {
        header("Location: operator.php?success=Job status updated successfully");
    } else {
        header("Location: operator.php?error=Failed to update status: " . $conn->error);
    }
}
?>
