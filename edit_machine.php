<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) ||!isset($_SESSION['role'])) {
    header("Location: login.php?error=session_expired");
    exit();
}

// Authentication check for admins and managers
if (!in_array($_SESSION['role'], ['admin', 'manager'])) {
    header("Location: login.php?error=access_denied");
    exit();
}


$machine_id = $_GET['machine_id'];

// SQL query to retrieve machine details
$sql = "SELECT * FROM machines WHERE machine_id = '$machine_id'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $machine_name = $conn->real_escape_string($_POST['machine_name']);

    

    // SQL query to update machine details
    $sql = "UPDATE machines SET machine_name = '$machine_name' WHERE machine_id = '$machine_id'";
    if ($conn->query($sql) === TRUE) {
        header("Location: machine_management.php?success=Machine updated successfully");
    } else {
        header("Location: edit_machine.php?machine_id=$machine_id&error=Error updating machine: " . $conn->error);
    }

    exit(); 
    $conn->close();
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Machine</title>
    <link rel="stylesheet" type="text/css" href="styles/edit_machine.css">
</head>
<body>
    <div class="container">
        <?php if (isset($_GET['error'])) { ?>
            <div class="alert error"><?php echo $_GET['error']; ?></div>
        <?php } ?>

        <?php if (isset($_GET['success'])) { ?>
            <div class="alert success"><?php echo $_GET['success']; ?></div>
        <?php } ?>

        <h1>Edit Machine</h1>
        <form action="edit_machine.php?machine_id=<?php echo $machine_id; ?>" method="post">
            <label for="machine_name">Machine Name:</label>
            <input type="text" id="machine_name" name="machine_name" value="<?php echo $row['machine_name']; ?>" required>
            <button type="submit" class="button">Update Machine</button>
        </form>
    </div>

    <script src="scripts/edit_machine.js"></script>
</body>
</html>
