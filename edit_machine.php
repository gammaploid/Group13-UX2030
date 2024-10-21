<?php
// edit_machine.php
session_start();
include 'db_connection.php';

// Authentication check for admins only
if ($_SESSION['role']!== 'admin') {
    header("Location: login.php?error=access_denied");
    exit();
}

$log_id = $_GET['log_id'];

// Fetch the machine details
$sql = "SELECT * FROM machines WHERE log_id = '$log_id'";
$result = $conn->query($sql);
$machine = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $machine_name = $conn->real_escape_string($_POST['machine_name']);

    $sql = "UPDATE machines SET machine_name = '$machine_name' WHERE log_id = '$log_id'";
    if ($conn->query($sql) === TRUE) {
        header("Location: machine_management.php?success=Machine updated successfully");
        exit();
    } else {
        header("Location: machine_management.php?error=Error updating machine: ". $conn->error);
        exit();
    }
}
// Machine operational status switcher
if ($row['operational_status'] == 'non-operational') {
    echo "<button class='op-status-switch' data-machine-id='{$row['log_id']}'>Mark as Operational</button>";
} else {
    echo "<button class='op-status-switch dimmed' data-machine-id='{$row['log_id']}'>Mark as Non-Operational</button>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Machine</title>
    <link rel="stylesheet" type="text/css" href="global.css">
    <link rel="stylesheet" type="text/css" href="styles/machine_management.css">
</head>
<body>
    <div class="container" style="background-color: #ffffff; padding: 20px; border: 1px solid #ddd; border-radius: 5px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
        <h2>Edit Machine</h2>
        <form action="edit_machine.php?log_id=<?php echo $log_id;?>" method="post">
            <div class="form-group">
                <label for="machine_name">Machine Name:</label>
                <input type="text" id="machine_name" name="machine_name" value="<?php echo htmlspecialchars($machine['machine_name'], ENT_QUOTES, 'UTF-8');?>" required>
            </div>
            <button type="submit" class="button">Update Machine</button>
        </form>
        <a href="machine_management.php" class="button">Back to Machine Management</a>
    </div>
</body>
</html>
