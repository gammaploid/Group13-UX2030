<?php
session_start();
include 'db_connection.php';

var_dump($_SESSION);

// Verify session variables
if (!isset($_SESSION['user_id']) ||!isset($_SESSION['role'])) {
    header("Location: login.php?error=session_expired");
    exit();
}

// Authentication check for admins and managers
if (!in_array($_SESSION['role'], ['admin', 'manager'])) {
    header("Location: login.php?error=access_denied");
    exit();
}
// SQL query to retrieve all machines
$sql = "SELECT * FROM machines";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Machine Management</title>
    <link rel="stylesheet" type="text/css" href="styles/machine_management.css">
</head>
<body>
    <h1>Machine Management</h1>
    <?php if (isset($_GET['success'])) { ?>
        <div class="alert success"><?php echo $_GET['success']; ?></div>
    <?php } elseif (isset($_GET['error'])) { ?>
        <div class="alert error"><?php echo $_GET['error']; ?></div>
    <?php } ?>

    <table>
        <tr>
            <th>Machine ID</th>
            <th>Machine Name</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['machine_id']; ?></td>
            <td><?php echo $row['machine_name']; ?></td>
            <td class="actions">
                <button onclick="location.href='edit_machine.php?machine_id=<?php echo $row['machine_id']; ?>'">Edit</button>
                <button onclick="if (confirm('Are you sure you want to delete this machine?')) location.href='delete_machine.php?machine_id=<?php echo $row['machine_id']; ?>'">Delete</button>
            </td>
        </tr>
        <?php } ?>
    </table>

    <div class="button-group">
        <a href="add_machine.php" class="button">Add New Machine</a>
    </div>

    <script src="scripts/machine_management.js"></script>
</body>
</html>
