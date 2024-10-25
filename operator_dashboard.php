<?php
// operator_dashboard.php
$page = 'operator_dashboard';
$page_title = 'Operator Dashboard';
include 'templates/operator_header.php';
require_once 'message.php';

use App\Message;

$message = new Message($conn);

// Mark job as complete
if (isset($_POST['complete_job'])) {
    $jobId = $_POST['job_id'];
    $sql = "UPDATE jobs SET status = 'completed' WHERE job_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $jobId);
    $stmt->execute();
}

// Send messages
if (isset($_POST['send_message'])) {
    $senderId = $_SESSION['user_id'];
    $receiverId = $_POST['receiver_id'];
    $messageText = $_POST['message'];
    $message->sendMessage($senderId, $receiverId, $messageText);
}

// Fetch assigned jobs for the operator
$sql_jobs = "SELECT * FROM jobs WHERE operator_id = ? AND status != 'completed'";
$stmt = $conn->prepare($sql_jobs);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$jobs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch performance data
$sql_machines = "SELECT * FROM machines";
$result_machines = $conn->query($sql_machines);

$messages = $message->getMessages($_SESSION['user_id']);
?>

<head>
    <link rel="stylesheet" type="text/css" href="styles/operator_dashboard.css">
    <script src="scripts/operator_dashboard.js"></script>
</head>
<body>

<div class="dashboard-content">
    <h1>Welcome, Operator <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
    <div class="dashboard-grid">

        <div class="dashboard-section">
            <h2>Assigned Jobs</h2>
            <?php if (!empty($jobs)): ?>
                <table class="job-table">
                    <thead>
                        <tr>
                            <th>Job ID</th>
                            <th>Job Name</th>
                            <th>Machine</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jobs as $job): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($job['job_id']); ?></td>
                                <td><?php echo htmlspecialchars($job['job_name']); ?></td>
                                <td><?php echo htmlspecialchars($job['machine_id']); ?></td>
                                <td><?php echo htmlspecialchars($job['status']); ?></td>
                                <td>
                                    <form method="post">
                                        <input type="hidden" name="job_id" value="<?php echo $job['job_id']; ?>">
                                        <button type="submit" name="complete_job" class="button">Mark as Complete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No assigned jobs available.</p>
            <?php endif; ?>
        </div>

        <div class="dashboard-section">
            <h2>Machine Performance</h2>
            <table class="machine-table">
                <thead>
                    <tr>
                        <th>Machine ID</th>
                        <th>Machine Name</th>
                        <th>Status</th>
                        <th>Last Maintenance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($machine = $result_machines->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($machine['id']); ?></td>
                            <td><?php echo htmlspecialchars($machine['machine_name']); ?></td>
                            <td><?php echo htmlspecialchars($machine['status']); ?></td>
                            <td><?php echo htmlspecialchars($machine['last_maintenance']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="dashboard-section messaging-container">
            <h2>Messaging</h2>
            <div class="messaging-pane">
                <div class="send-message-form">
                    <h3>Send Message</h3>
                    <form action="" method="post">
                        <div class="form-group">
                            <label for="receiver_id">Recipient:</label>
                            <select name="receiver_id" id="receiver_id" required>
                                <option value="">Select Recipient</option>
                                <?php
                                $sql = "SELECT id, username FROM users WHERE role IN ('admin', 'manager')";
                                $result = $conn->query($sql);
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value=\"" . $row['id'] . "\">" . htmlspecialchars($row['username']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="message">Message:</label>
                            <textarea name="message" id="message" required placeholder="Type your message..."></textarea>
                        </div>
                        <button type="submit" name="send_message" class="button">Send Message</button>
                    </form>
                </div>

                <div class="inbox">
                    <h3>Inbox</h3>
                    <?php if (empty($messages)): ?>
                        <p>No messages in your inbox.</p>
                    <?php else: ?>
                        <?php foreach ($messages as $msg): ?>
                            <div class="message-container">
                                <span class="message-sender">From: <?php echo htmlspecialchars($msg['sender_name']); ?></span>
                                <span class="message-body"><?php echo htmlspecialchars($msg['message']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include 'templates/operator_footer.php'; ?>
