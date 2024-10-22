
<?php /*
// admin_header_sidebar.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'Notification.php';
require_once 'db_connection.php';
use App\Notification\Notification;
$notification = new Notification($conn);
$unreadNotifications = $notification->getUnreadNotifications($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - SMD Admin' : 'SMD Admin'; ?></title>
    <link rel="stylesheet" type="text/css" href="styles/global.css">
    <link rel="stylesheet" type="text/css" href="styles/admin_dashboard.css">
</head>
<body>
    <div class="dashboard-container">
    <div class="dashboard-container">
    <aside class="sidebar">
        <h1>SMD Admin</h1>
        <nav>
            <ul>
                <li><a href="admin_dashboard.php" <?php echo ($page == 'admin_dashboard') ? 'class="active"' : ''; ?>>Dashboard</a></li>
                <li><a href="machine_management.php" <?php echo ($page == 'machine_management') ? 'class="active"' : ''; ?>>Machine Management</a></li>
                <li><a href="user_management.php" <?php echo ($page == 'user_management') ? 'class="active"' : ''; ?>>User Management</a></li>
            </ul>
        </nav>
    </aside>
        <main class="main-content">
            <header class="top-bar">
                <div class="back-button-container">
                    <?php if (isset($back_url)): ?>
                        <a href="<?php echo $back_url; ?>" class="button back-button">Back</a>
                    <?php endif; ?>
                </div>
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
                        Notifications
                    </div>
                    <ul class="notification-list" id="notificationList">
                        <?php foreach ($unreadNotifications as $notification) { ?>
                            <li><a href="#"><?php echo $notification['message']; ?> (<span class="notification-time"><?php echo $notification['created_at']; ?></span>)</a></li>
                        <?php } ?>
                    </ul>
                </div>
            </header>

            <link rel="stylesheet" type="text/css" href="styles/admin_dashboard.css">