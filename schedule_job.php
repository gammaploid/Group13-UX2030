<?php
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $job_id = $_POST['job_id'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    // Validate scheduling conflicts
    $sql = "SELECT * FROM jobs WHERE start_time <= '$end_time' AND end_time >= '$start_time' AND job_id!= '$job_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        header("Location: schedule_job.php?error=Scheduling conflict detected");
        exit();
    }

    // Update job schedule
    $sql = "UPDATE jobs SET start_time = '$start_time', end_time = '$end_time' WHERE job_id = '$job_id'";
    if ($conn->query($sql) === TRUE) {
        header("Location: schedule_job.php?success=Job scheduled successfully");
    } else {
        header("Location: schedule_job.php?error=". $conn->error);
    }
    $conn->close();
}
?>

<!-- Schedule Job Form -->
<form action="schedule_job.php" method="post">
    <label for="job_id">Job ID:</label>
    <input type="number" id="job_id" name="job_id" required>

    <label for="start_time">Start Time:</label>
    <input type="datetime-local" id="start_time" name="start_time" required>

    <label for="end_time">End Time:</label>
    <input type="datetime-local" id="end_time" name="end_time" required>

    <button type="submit">Schedule Job</button>
</form>
