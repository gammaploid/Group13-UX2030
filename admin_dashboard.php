<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?error=access_denied");
    exit();
}
require_once 'Notification.php';
require_once 'db_connection.php';
use App\Notification\Notification;
$notification = new Notification($conn);
$unreadNotifications = $notification->getUnreadNotifications($_SESSION['user_id']);

/*if (!empty($unreadNotifications)) {
    echo "<h2>Unread Notifications</h2>";
    echo "<ul>";
    foreach ($unreadNotifications as $notification) {
        echo "<li>{$notification['message']} ({$notification['created_at']})</li>";
    }
    echo "</ul>";
} else {
    echo "<h2>No Unread Notifications</h2>";
}*/
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SMD</title>
    <link rel="stylesheet" type="text/css" href="styles/global.css">
    <link rel="stylesheet" type="text/css" href="styles/admin_dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <h1>SMD Admin</h1>
            <nav>
                <ul>
                    <li><a href="admin_dashboard.php" class="active">Dashboard</a></li>
                    <li><a href="machine_management.php">Machine Management</a></li>
                    <li><a href="user_management.php">User Management</a></li>
                </ul>
            </nav>
        </aside>
        <main class="main-content">
            <header class="top-bar">
                <div class="user-profile">
                    <img src="profile_pic.png" alt="Profile Picture">
                    <span><?php echo $_SESSION['username']; ?></span>
                    <ul class="profile-dropdown">
                        <li><a href="profile_info.php">Profile Info</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    </ul>
                </div>
                <div class="notification-area">
                    <div class="notification-icon" id="notificationIcon">
                        <span class="notification-count"><?php echo count($unreadNotifications); ?></span>
                        <span>Notifications</span>
                    </div>
                    <ul class="notification-list" id="notificationList">
                        <?php foreach ($unreadNotifications as $notification) { ?>
                            <li>
                                <a href="#">
                                    <?php echo $notification['message']; ?>
                                    <span class="notification-time"><?php echo $notification['created_at']; ?></span>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </header>
            <div class="dashboard-content">
                <h1>Welcome, Admin <?php echo $_SESSION['username']; ?></h1>
                <div class="dashboard-grid">
                    <div class="dashboard-section">
                        <h2>Manage User Accounts and Roles</h2>
                        <a href="user_management.php" class="button">Manage Users</a>
                    </div>
                    <div class="dashboard-section">
                        <h2>Manage Machines</h2>
                        <a href="machine_management.php" class="button">Manage Machines</a>
                    </div>
                    <div class="dashboard-section">
                        <h2>Factory Performance</h2>
                        <div class="table-container">
                            <?php
                            // Your existing PHP code for factory performance table
                            ?>
                        </div>
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
            </div>
        </main>
    </div>
    <script src="scripts/admin_dashboard.js"></script>
</body>
</html>