<?php
session_start();
include 'db_connection.php';
require_once 'Notification.php';

use App\Notification\Notification;

// Authentication check for admins only
if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php?error=access_denied");
    exit();
}

// Handle machine status update
if (isset($_GET['action']) && $_GET['action'] == 'update_status' && isset($_GET['log_id'])) {
    $logId = $_GET['log_id'];
    $sql = "UPDATE machines SET operational_status = 'non-operational' WHERE log_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $logId);
    $stmt->execute();

    $notification = new Notification($conn);
    $notificationId = $notification->createNotification('machine_downtime', "Machine $logId is non-operational");
    $notification->addRecipient($notificationId, $_SESSION['user_id']);

    // Redirect to remove the action from the URL
    header("Location: machine_management.php");
    exit();
}

// Set the number of machines to display per page
$limit = 15;

// Determine the current page
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $limit;

// Search query
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// SQL query to retrieve machines with pagination and search
$sql = "SELECT * FROM machines WHERE machine_name LIKE '%$search%' LIMIT $offset, $limit";
$result = $conn->query($sql);

// Get the total number of machines for pagination
$total_sql = "SELECT COUNT(*) as total FROM machines WHERE machine_name LIKE '%$search%'";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_machines = $total_row['total'];
$total_pages = ceil($total_machines / $limit);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Machine Management</title>
    <link rel="stylesheet" type="text/css" href="styles/global.css">
    <link rel="stylesheet" type="text/css" href="styles/machine_management.css">
</head>
<body>
    
    <h1>Machine Management</h1>
    <div class="container">
        <a href="add_machine.php" class="button">Add Machine</a>
        <form action="machine_management.php" method="get">
            <label for="search">Search:</label>
            <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <table>
        <tr>
            <th>Machine ID</th>
            <th>Machine Name</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['log_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($row['machine_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($row['operational_status'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td>
                    <a href="edit_machine.php?log_id=<?php echo htmlspecialchars($row['log_id'], ENT_QUOTES, 'UTF-8'); ?>" class="button">Edit</a>
                    <a href="delete_machine.php?log_id=<?php echo htmlspecialchars($row['log_id'], ENT_QUOTES, 'UTF-8'); ?>" onclick="return confirm('Are you sure?');" class="button">Delete</a>
                    <?php if ($row['operational_status'] != 'non-operational') { ?>
                        <a href="machine_management.php?action=update_status&log_id=<?php echo htmlspecialchars($row['log_id'], ENT_QUOTES, 'UTF-8'); ?>" onclick="return confirm('Mark non-operational?');" class="button">Mark Non-Operational</a>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </table>
    <div class="pagination">
    <?php
    $page_range = 5; // Number of pages to show before and after the current page
    $start_page = max(1, $current_page - $page_range);
    $end_page = min($total_pages, $current_page + $page_range);

    // Previous button
    if ($current_page > 1) {
        echo "<button onclick=\"location.href='machine_management.php?page=".($current_page-1)."&search=".urlencode($search)."'\">&laquo; Previous</button> ";
    }

    // First page
    if ($start_page > 1) {
        echo "<button onclick=\"location.href='machine_management.php?page=1&search=".urlencode($search)."'\">1</button> ";
        if ($start_page > 2) {
            echo "<span>...</span> ";
        }
    }

    // Page numbers
    for ($i = $start_page; $i <= $end_page; $i++) {
        if ($i == $current_page) {
            echo "<span class='active'>$i</span> ";
        } else {
            echo "<button onclick=\"location.href='machine_management.php?page=$i&search=".urlencode($search)."'\">$i</button> ";
        }
    }

    // Last page
    if ($end_page < $total_pages) {
        if ($end_page < $total_pages - 1) {
            echo "<span>...</span> ";
        }
        echo "<button onclick=\"location.href='machine_management.php?page=$total_pages&search=".urlencode($search)."'\">$total_pages</button> ";
    }

    // Next button
    if ($current_page < $total_pages) {
        echo "<button onclick=\"location.href='machine_management.php?page=".($current_page+1)."&search=".urlencode($search)."'\">Next &raquo;</button>";
    }
    ?>
</div>

</body>
</html>
