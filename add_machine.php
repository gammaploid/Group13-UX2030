<?php
session_start();
include 'db_connection.php';

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


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $machine_name = $conn->real_escape_string($_POST['machine_name']);


    // SQL query to insert the new machine
    $sql = "INSERT INTO machines (machine_name) VALUES ('$machine_name')";
    if ($conn->query($sql) === TRUE) {
        header("Location: machine_management.php?success=Machine added successfully");
    } else {
        header("Location: add_machine.php?error=Error adding machine: " . $conn->error);
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
    <title>Add New Machine</title>
    <link rel="stylesheet" type="text/css" href="styles/add_machine.css">
</head>
<body>
    <div class="container">
        <?php if (isset($_GET['error'])) { ?>
            <div class="alert error"><?php echo $_GET['error']; ?></div>
        <?php } ?>

        <h1>Add New Machine</h1>
        <form action="add_machine.php" method="post">
            <label for="machine_name">Machine Name:</label>
            <input type="text" id="machine_name" name="machine_name" required>
            <button type="submit" class="button">Add Machine</button>
        </form>
    </div>

    <script src="scripts/add_machine.js"></script>
</body>
</html>
