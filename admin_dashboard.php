<?php
session_start();
include 'db_connection.php';

// Authentication check for admins only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?error=access_denied");
    exit();
}

// SQL query to retrieve all users
$sql_users = "SELECT id, username, role FROM users";
$result_users = $conn->query($sql_users);

// SQL query to retrieve factory performance data
$sql_performance = "SELECT machine_name, AVG(temperature) as avg_temp, AVG(power_consumption) as avg_power, 
                    COUNT(CASE WHEN operational_status = 'active' THEN 1 END) as active_count,
                    COUNT(CASE WHEN operational_status = 'maintenance' THEN 1 END) as maintenance_count,
                    COUNT(CASE WHEN operational_status = 'idle' THEN 1 END) as idle_count
                    FROM factory_log
                    GROUP BY machine_name";
$result_performance = $conn->query($sql_performance);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SMD</title>
    <link rel="stylesheet" type="text/css" href="styles/admin_dashboard.css">
</head>
<body>
    <h1>Welcome, Admin <?php echo $_SESSION['username']; ?></h1>

    <div class="dashboard-section">
        <h2>Manage User Accounts and Roles</h2>
        <?php
        if ($result_users->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>ID</th><th>Username</th><th>Role</th><th>Action</th></tr>";
            while($row = $result_users->fetch_assoc()) {
                echo "<tr>";
                echo "<td>".$row["id"]."</td>";
                echo "<td>".$row["username"]."</td>";
                echo "<td>".$row["role"]."</td>";
                echo "<td><a href='edit_user.php?id=".$row["id"]."'>Edit</a> | <a href='delete_user.php?id=".$row["id"]."' onclick='return confirm(\"Are you sure?\");'>Delete</a></td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "0 results";
        }
        ?>

        <h3>Add New User</h3>
        <form action="add_user.php" method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="role" required>
                <option value="">Select Role</option>
                <option value="admin">Admin</option>
                <option value="manager">Factory Manager</option>
                <option value="operator">Production Operator</option>
                <option value="auditor">Auditor</option>
            </select>
            <button type="submit">Add User</button>
        </form>
    </div>

    <div class="dashboard-section">
        <h2>Factory Performance</h2>
        <?php
        if ($result_performance->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>Machine</th><th>Avg Temp</th><th>Avg Power</th><th>Active</th><th>Maintenance</th><th>Idle</th></tr>";
            while($row = $result_performance->fetch_assoc()) {
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
        <h2>Assign Jobs</h2>
        <a href="assign_job.php" class="button">Assign New Job</a>
    </div>

    <a href="logout.php">Logout</a>

    <script>
        // You can add JavaScript here for any client-side functionality
    </script>
</body>
</html>
