<?php
session_start();
include 'db_connection.php';

// Authentication check for factory managers only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'manager') {
    header("Location: login.php?error=access_denied");
    exit();
}

// SQL query to retrieve factory performance data
$sql = "SELECT machine_name, AVG(temperature) as avg_temp, AVG(power_consumption) as avg_power, 
        COUNT(CASE WHEN operational_status = 'active' THEN 1 END) as active_count,
        COUNT(CASE WHEN operational_status = 'maintenance' THEN 1 END) as maintenance_count,
        COUNT(CASE WHEN operational_status = 'idle' THEN 1 END) as idle_count
        FROM factory_log
        GROUP BY machine_name";
$result = $conn->query($sql);

// SQL query to retrieve all machines
$sql_machines = "SELECT * FROM machines";
$result_machines = $conn->query($sql_machines);

// SQL query to retrieve all jobs
$sql_jobs = "SELECT * FROM jobs";
$result_jobs = $conn->query($sql_jobs);

// SQL query to retrieve all operators
$sql_operators = "SELECT * FROM users WHERE role = 'operator'";
$result_operators = $conn->query($sql_operators);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factory Manager Dashboard</title>
    <link rel="stylesheet" type="text/css" href="styles/manager_dashboard.css">
</head>
<body>
    <h1>Welcome, Factory Manager <?php echo $_SESSION['username']; ?></h1>

    <div class="dashboard-section">
        <h2>Factory Performance</h2>
        <?php
        if ($result->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>Machine</th><th>Avg Temp</th><th>Avg Power</th><th>Active</th><th>Maintenance</th><th>Idle</th></tr>";
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>".$row["machine_name"]."</td>";
                echo "<td>".round($row["avg_temp"], 2)."°C</td>";
                echo "<td>".round($row["avg_power"], 2)." kW</td>";
                echo "<td>".$row["active_count"]."</td>";
                echo "<td>".$row["maintenance_count"]."</td>";
                echo "<td>".$row["idle_count"]."</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No factory performance data available";
        }
        ?>
    </div>

    <div class="dashboard-section">
        <h2>Manage Machines</h2>
        <table>
            <tr>
                <th>Machine ID</th>
                <th>Machine Name</th>
                <th>Actions</th>
            </tr>
            <?php while($row = $result_machines->fetch_assoc()) {?>
            <tr>
                <td><?php echo $row['machine_id'];?></td>
                <td><?php echo $row['machine_name'];?></td>
                <td>
                    <button onclick="location.href='edit_machine.php?machine_id=<?php echo $row['machine_id'];?>'">Edit</button>
                    <button onclick="if (confirm('Are you sure you want to delete this machine?')) { location.href='delete_machine.php?machine_id=<?php echo $row['machine_id'];?>' }">Delete</button>
                </td>
            </tr>
            <?php }?>
        </table>
        <a href="add_machine.php">Add New Machine</a>
    </div>

    <div class="dashboard-section">
        <h2>Manage Jobs</h2>
        <table>
            <tr>
                <th>Job ID</th>
                <th>Job Description</th>
                <th>Machine</th>
                <th>Assigned Operator</th>
                <th>Actions</th>
            </tr>
            <?php while($row = $result_jobs->fetch_assoc()) {?>
            <tr>
                <td><?php echo $row['job_id'];?></td>
                <td><?php echo $row['job_description'];?></td>
                <td><?php echo $row['machine_name'];?></td>
                <td><?php echo $row['assigned_operator'];?></td>
                <td>
                    <button onclick="location.href='edit_job.php?job_id=<?php echo $row['job_id'];?>'">Edit</button>
                    <button onclick="if (confirm('Are you sure you want to delete this job?')) { location.href='delete_job.php?job_id=<?php echo $row['job_id'];?>' }">Delete</button>
                </td>
            </tr>
            <?php }?>
        </table>
        <a href="add_job.php">Add New Job</a>
    </div>

    <div class="dashboard-section">
        <h2>Assign Roles/Machines/Jobs</h2>
        <form action="assign_role.php" method="post">
            <label for="operator">Operator:</label>
            <select name="operator" required>
                <option value="">Select Operator</option>
                <?php while($row = $result_operators->fetch_assoc()) {?>
                <option value="<?php echo $row['id'];?>"><?php echo $row['username'];?></option>
                <?php }?>
            </select>
            <br>
            <label for="machine">Machine:</label>
            <select name="machine" required>
                <option value="">Select Machine</option>
                <?php $result_machines->data_seek(0); // Reset the pointer ?>
                <?php while($row = $result_machines->fetch_assoc()) {?>
                <option value="<?php echo $row['machine_id'];?>"><?php echo $row['machine_name'];?></option>
                <?php }?>
            </select>
            <br>
            <label for="job">Job:</label>
            <select name="job" required>
                <option value="">Select Job</option>
                <?php $result_jobs->data_seek(0); // Reset the pointer ?>
                <?php while($row = $result_jobs->fetch_assoc()) {?>
                <option value="<?php echo $row['job_id'];?>"><?php echo $row['job_description'];?></option>
                <?php }?>
            </select>
            <br>
            <button type="submit">Assign</button>
        </form>
    </div>

    <a href="logout.php">Logout</a>
    <a href="assign_role.php">Assign Roles/Machines/Jobs to Operators</a>
    <a href="jobs/add_job.php">Add New Job</a>

    <script>
        //  JavaScript here for any client-side functionality
    </script>
</body>
</html>
