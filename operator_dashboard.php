<?php
session_start();
include 'db_connection.php';
// Authentication check for operators only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'operator') {
    header("Location: login.php?error=access_denied");
    exit();
}

// Fetch jobs assigned to the operator
$user_id = $_SESSION['user_id'];
$sql_jobs = "SELECT job_id, job_name, status, deadline FROM jobs WHERE assigned_operator = $user_id";
$result_jobs = $conn->query($sql_jobs);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operator Dashboard</title>
    <link rel="stylesheet" type="text/css" href="styles/operator_dashboard.css">
</head>
<body>
    <h1>Welcome, Operator <?php echo $_SESSION['username']; ?></h1>

    <div class="dashboard-section">
        <h2>Your Assigned Jobs</h2>
        <?php
        if ($result_jobs->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>Job ID</th><th>Job Name</th><th>Status</th><th>Deadline</th><th>Action</th></tr>";
            while ($row = $result_jobs->fetch_assoc()) {
                echo "<tr>";
                echo "<td>".$row["job_id"]."</td>";
                echo "<td>".$row["job_name"]."</td>";
                echo "<td>".$row["status"]."</td>";
                echo "<td>".$row["deadline"]."</td>";
                echo "<td>";
                echo "<form action='update_job.php' method='post'>";
                echo "<input type='hidden' name='job_id' value='".$row["job_id"]."'>";
                echo "<select name='status' required>";
                echo "<option value='Pending'>Pending</option>";
                echo "<option value='In Progress'>In Progress</option>";
                echo "<option value='Completed'>Completed</option>";
                echo "</select>";
                echo "<button type='submit'>Update Status</button>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No jobs assigned.";
        }
        ?>
    </div>

    <a href="logout.php">Logout</a>

</body>
</html>
