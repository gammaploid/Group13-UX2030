<?php
session_start();
include 'db_connection.php';

// Authentication check for auditors only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'auditor') {
    header("Location: login.php?error=access_denied");
    exit();
}

// Fetch factory performance logs for auditing purposes
$sql_logs = "
    SELECT machine_name, temperature, power_consumption, operational_status, timestamp 
    FROM factory_log 
    ORDER BY timestamp DESC 
    LIMIT 50";
$result_logs = $conn->query($sql_logs);

// Fetch summary of machine statuses
$sql_summary = "
    SELECT machine_name, 
           COUNT(CASE WHEN operational_status = 'active' THEN 1 END) AS active_count,
           COUNT(CASE WHEN operational_status = 'maintenance' THEN 1 END) AS maintenance_count,
           COUNT(CASE WHEN operational_status = 'idle' THEN 1 END) AS idle_count 
    FROM factory_log 
    GROUP BY machine_name";
$result_summary = $conn->query($sql_summary);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auditor Dashboard</title>
    <link rel="stylesheet" type="text/css" href="styles/auditor_dashboard.css">
</head>
<body>
    <h1>Welcome, Auditor <?php echo htmlspecialchars($_SESSION['username']); ?></h1>

    <div class="dashboard-section">
        <h2>Recent Factory Logs</h2>
        <table>
            <tr>
                <th>Machine Name</th>
                <th>Temperature (°C)</th>
                <th>Power (kW)</th>
                <th>Status</th>
                <th>Timestamp</th>
            </tr>
            <?php if ($result_logs->num_rows > 0): ?>
                <?php while ($row = $result_logs->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['machine_name']) ?></td>
                        <td><?= round($row['temperature'], 2) ?></td>
                        <td><?= round($row['power_consumption'], 2) ?></td>
                        <td><?= htmlspecialchars($row['operational_status']) ?></td>
                        <td><?= htmlspecialchars($row['timestamp']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5">No recent logs available</td></tr>
            <?php endif; ?>
        </table>
    </div>

    <div class="dashboard-section">
        <h2>Machine Status Summary</h2>
        <table>
            <tr>
                <th>Machine Name</th>
                <th>Active</th>
                <th>Maintenance</th>
                <th>Idle</th>
            </tr>
            <?php if ($result_summary->num_rows > 0): ?>
                <?php while ($row = $result_summary->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['machine_name']) ?></td>
                        <td><?= $row['active_count'] ?></td>
                        <td><?= $row['maintenance_count'] ?></td>
                        <td><?= $row['idle_count'] ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4">No data available</td></tr>
            <?php endif; ?>
        </table>
    </div>

    <a href="logout.php">Logout</a>

    <script>
        // Optional: Add JavaScript functionality here if needed
    </script>
</body>
</html>
