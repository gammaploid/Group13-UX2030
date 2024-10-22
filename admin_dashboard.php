<?php
// admin_dashboard.php
$page = 'admin_dashboard';
$page_title = 'Admin Dashboard';
include 'templates/admin_header.php';
require_once 'message.php';

use App\Message;

$conn = new mysqli("localhost", "root", "root", "smd_database");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = new Message($conn);

if (isset($_POST['send_message'])) {
    $senderId = $_SESSION['user_id'];
    $receiverId = $_POST['receiver_id'];
    $messageText = $_POST['message'];
    $machineId = !empty($_POST['machine_id']) ? $_POST['machine_id'] : null;
    $jobId = !empty($_POST['job_id']) ? $_POST['job_id'] : null;
    $message->sendMessage($senderId, $receiverId, $messageText, $machineId, $jobId);
}

$messages = $message->getMessages($_SESSION['user_id']);

?>

<div class="dashboard-content">
    <h1>Welcome, Admin <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
    <div class="dashboard-grid">
        <div class="dashboard-section">
            <h2>Manage User Accounts</h2>
            <a href="user_management.php" class="button">Manage Users</a>
        </div>
        <div class="dashboard-section">
            <h2>Manage Machines</h2>
            <a href="machine_management.php" class="button">Manage Machines</a>
        </div>
        <div class="dashboard-section">
            <h2>Factory Performance</h2>
            
        </div>
        <div class="dashboard-section">
            <h2>System Statistics</h2>
            <ul>
                <li>Number of Users: <?php echo $conn->query("SELECT COUNT(*) FROM users")->fetch_assoc()['COUNT(*)']; ?></li>
                <li>Number of Machines: <?php echo $conn->query("SELECT COUNT(*) FROM machines")->fetch_assoc()['COUNT(*)']; ?></li>
                <li>Number of Jobs: <?php echo $conn->query("SELECT COUNT(*) FROM jobs")->fetch_assoc()['COUNT(*)']; ?></li>
            </ul>
        </div>
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
                            $sql = "SELECT id, username FROM users WHERE role IN ('manager', 'operator', 'auditor')";
                            $result = $conn->query($sql);
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value=\"" . $row['id'] . "\">" . htmlspecialchars($row['username']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="machine_id">Regarding Machine (optional):</label>
                        <select name="machine_id" id="machine_id">
                            <option value="">Select Machine</option>
                            <?php
                            $sql = "SELECT id, machine_name FROM machines";
                            $result = $conn->query($sql);
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value=\"" . $row['id'] . "\">" . htmlspecialchars($row['machine_name']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="job_id">Regarding Job (optional):</label>
                        <select name="job_id" id="job_id">
                            <option value="">Select Job</option>
                            <?php
                            $sql = "SELECT job_id, job_name FROM jobs";
                            $result = $conn->query($sql);
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value=\"" . $row['job_id'] . "\">" . htmlspecialchars($row['job_name']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="message">Message:</label>
                        <textarea name="message" id="message" required placeholder="Type your message here..."></textarea>
                    </div>
                    <button type="submit" name="send_message" class="button">Send Message</button>
                </form>
            </div>

            <div class="inbox">
                <h3>Inbox</h3>
                <?php
                if (empty($messages)) {
                    echo "<p>No messages in your inbox.</p>";
                } else {
                    foreach ($messages as $msg) {
                        ?>
                        <div class="message-container">
                            <div class="message-header">
                                <span class="message-sender">From: <?php echo htmlspecialchars($msg['sender_name']); ?></span>
                                <span class="message-timestamp"><?php echo htmlspecialchars($msg['sent_at']); ?></span>
                            </div>
                            <div class="message-body">
                                <?php echo htmlspecialchars($msg['message']); ?>
                            </div>
                            <?php if (isset($msg['machine_name'])): ?>
                                <div class="message-details">
                                    Regarding Machine: <?php echo htmlspecialchars($msg['machine_name']); ?>
                                </div>
                            <?php endif; ?>
                            <?php if (isset($msg['job_name'])): ?>
                                <div class="message-details">
                                    Regarding Job: <?php echo htmlspecialchars($msg['job_name']); ?>
                                </div>
                            <?php endif; ?>
                            <div class="message-footer">
                                <?php if ($msg['read_at'] === null): ?>
                                    <button class="button mark-as-read-button" data-message-id="<?php echo $msg['id']; ?>">Mark as Read</button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/admin_footer.php'; ?>
