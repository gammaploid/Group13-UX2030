<?php
session_start();
require_once 'db_connection.php';
require_once 'Message.php';

use App\Message;

// Authentication check for factory managers only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'manager') {
    header("Location: login.php?error=access_denied");
    exit();
}


$message = new Message($conn);
$managerId = $_SESSION['user_id'];
$messages = $message->getMessages($managerId);

// Pagination for machines
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10; // Number of machines per page
$offset = ($page - 1) * $limit;

// SQL query to retrieve paginated machines
$sql_machines = "SELECT * FROM machines LIMIT $limit OFFSET $offset";
$result_machines = $conn->query($sql_machines);

// Get total number of machines for pagination
$total_machines = $conn->query("SELECT COUNT(*) as count FROM machines")->fetch_assoc()['count'];
$total_pages = ceil($total_machines / $limit);

// Other queries remain the same
$sql = "SELECT machine_name, AVG(temperature) as avg_temp, AVG(power_consumption) as avg_power, 
        COUNT(CASE WHEN operational_status = 'active' THEN 1 END) as active_count,
        COUNT(CASE WHEN operational_status = 'maintenance' THEN 1 END) as maintenance_count,
        COUNT(CASE WHEN operational_status = 'idle' THEN 1 END) as idle_count
        FROM factory_log
        GROUP BY machine_name";
$result = $conn->query($sql);

$sql_jobs = "SELECT * FROM jobs LIMIT 10"; // Limiting to 10 jobs for now
$result_jobs = $conn->query($sql_jobs);

$sql_operators = "SELECT * FROM users WHERE role = 'operator'";
$result_operators = $conn->query($sql_operators);

$page_title = 'Manager Dashboard';
include 'templates/manager_header.php';
?>

<div class="dashboard-content">
    <h1>Welcome, Factory Manager <?php echo htmlspecialchars($_SESSION['username']); ?></h1>

    <!-- Factory Performance Section -->
    <div class="dashboard-section">
        <h2>Factory Performance</h2>
        <?php
        if ($result->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>Machine</th><th>Avg Temp</th><th>Avg Power</th><th>Active</th><th>Maintenance</th><th>Idle</th></tr>";
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>".htmlspecialchars($row["machine_name"])."</td>";
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

    <!-- Manage Machines Section -->
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
                <td><?php echo htmlspecialchars($row['machine_id']);?></td>
                <td><?php echo htmlspecialchars($row['machine_name']);?></td>
                <td>
                    <a href="edit_machine.php?machine_id=<?php echo $row['machine_id'];?>" class="button">Edit</a>
                    <a href="delete_machine.php?machine_id=<?php echo $row['machine_id'];?>" class="button delete-button" onclick="return confirm('Are you sure you want to delete this machine?')">Delete</a>
                </td>
            </tr>
            <?php }?>
        </table>
        
        <!-- Pagination -->
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" <?php echo ($page == $i) ? 'class="active"' : ''; ?>><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
        
        <a href="add_machine.php" class="button">Add New Machine</a>
    </div>

    <!-- Manage Jobs Section -->
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
                <td><?php echo htmlspecialchars($row['job_id']);?></td>
                <td><?php echo htmlspecialchars($row['job_description']);?></td>
                <td><?php echo htmlspecialchars($row['machine_name']);?></td>
                <td><?php echo htmlspecialchars($row['assigned_operator']);?></td>
                <td>
                    <a href="edit_job.php?job_id=<?php echo $row['job_id'];?>" class="button">Edit</a>
                    <a href="delete_job.php?job_id=<?php echo $row['job_id'];?>" class="button delete-button" onclick="return confirm('Are you sure you want to delete this job?')">Delete</a>
                </td>
            </tr>
            <?php }?>
        </table>
        <a href="add_job.php" class="button">Add New Job</a>
    </div>

    <!-- Assign Roles/Machines/Jobs Section -->
    <div class="dashboard-section">
        <h2>Assign Roles/Machines/Jobs</h2>
        <form action="assign_role.php" method="post" class="assign-form">
            <label for="operator">Operator:</label>
            <select name="operator" required>
                <option value="">Select Operator</option>
                <?php while($row = $result_operators->fetch_assoc()) {?>
                <option value="<?php echo $row['id'];?>"><?php echo htmlspecialchars($row['username']);?></option>
                <?php }?>
            </select>

            <label for="machine">Machine:</label>
            <select name="machine" required>
                <option value="">Select Machine</option>
                <?php $result_machines->data_seek(0); // Reset the pointer ?>
                <?php while($row = $result_machines->fetch_assoc()) {?>
                <option value="<?php echo $row['machine_id'];?>"><?php echo htmlspecialchars($row['machine_name']);?></option>
                <?php }?>
            </select>

            <label for="job">Job:</label>
            <select name="job" required>
                <option value="">Select Job</option>
                <?php $result_jobs->data_seek(0); // Reset the pointer ?>
                <?php while($row = $result_jobs->fetch_assoc()) {?>
                <option value="<?php echo $row['job_id'];?>"><?php echo htmlspecialchars($row['job_description']);?></option>
                <?php }?>
            </select>

            <button type="submit" class="button">Assign</button>
        </form>
    </div>

    <!-- Messaging Section -->
    <div class="messaging-pane">
        <h2>Messaging</h2>
        <ul class="message-list">
            <?php foreach ($messages as $msg): ?>
                <li>
                    <span class="message-sender"><?php echo htmlspecialchars($msg['sender_id']); ?></span>
                    <span class="message-text"><?php echo htmlspecialchars($msg['message']); ?></span>
                    <span class="message-timestamp"><?php echo htmlspecialchars($msg['sent_at']); ?></span>
                    <?php if ($msg['read_at'] === null): ?>
                        <button class="mark-as-read-button" data-message-id="<?php echo $msg['id']; ?>">Mark as Read</button>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<?php include 'templates/manager_footer.php'; ?>
