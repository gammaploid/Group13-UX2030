<?php
session_start();
include 'db_connection.php';
require_once 'Notification.php';

// Authentication check for admins only
if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php?error=access_denied");
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

// Update machine status and create notification
$machineId = isset($_GET['log_id']) ? (int)$_GET['log_id'] : 0;
$newStatus = isset($_GET['new_status']) ? $_GET['new_status'] : 'non-operational';
if ($machineId > 0) {
    $sql = "UPDATE machines SET operational_status = ? WHERE log_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $newStatus, $machineId);
    $stmt->execute();

    if (class_exists('App\Notification\Notification')) {
        $notification = new App\Notification\Notification($conn);
        $notificationId = $notification->createNotification('machine_downtime', "Machine $machineId is $newStatus");
        $notification->addRecipient($notificationId, $_SESSION['user_id']);
    } else {
        echo "Error: Notification class not found.";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Machine Management - SMD</title>
    <link rel="stylesheet" type="text/css" href="styles/global.css">
    <link rel="stylesheet" type="text/css" href="styles/machine_management.css">
</head>
<body>
    <header>
        <h1>Machine Management - Admin <?php echo $_SESSION['username']; ?></h1>
        <nav>
            <ul>
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="user_management.php">Manage Users</a></li>
                <li><a href="machine_management.php">Manage Machines</a></li>
                <li><a href="profile.php">Profile</a></li>
            </ul>
        </nav>
        <div class="notifications">
            <a href="notifications.php" class="notification-badge">
                <span class="badge"><?php echo $unreadCount; ?></span>
                <span class="text">Notifications</span>
            </a>
        </div>
    </header>

    <main>
        <div class="search-bar">
            <form method="GET" action="">
                <input type="text" name="search" placeholder="Search by machine name" value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit">Search</button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Machine ID</th>
                    <th>Machine Name</th>
                    <th>Operational Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['log_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['machine_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['operational_status']); ?></td>
                        <td>
                            <a href="machine_management.php?log_id=<?php echo $row['log_id']; ?>&new_status=non-operational" class="button <?php echo $row['operational_status'] === 'non-operational' ? 'disabled' : ''; ?>">Mark as Non-Operational</a>
                            <a href="machine_management.php?log_id=<?php echo $row['log_id']; ?>&new_status=operational" class="button <?php echo $row['operational_status'] === 'operational' ? 'disabled' : ''; ?>">Mark as Operational</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="pagination">
            <?php
            $start_page = max(1, $current_page - 5);
            $end_page = min($total_pages, $current_page + 5);

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
    </main>

    <footer>
        <p>&copy; 2023 SMD. All rights reserved.</p>
    </footer>
</body>
</html>
