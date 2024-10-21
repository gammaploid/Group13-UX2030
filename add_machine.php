<?php
// add_machine.php
session_start();
include 'db_connection.php';

// Authentication check for admins only
if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php?error=access_denied");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $machine_name = $conn->real_escape_string($_POST['machine_name']);

    $sql = "INSERT INTO machines (machine_name) VALUES ('$machine_name')";
    if ($conn->query($sql) === TRUE) {
        header("Location: machine_management.php?success=Machine added successfully");
        exit();
    } else {
        header("Location: machine_management.php?error=Error adding machine: " . $conn->error);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Machine</title>
    <link rel="stylesheet" type="text/css" href="global.css">
    <link rel="stylesheet" type="text/css" href="styles/machine_management.css">
</head>
<body>
    <div class="container" style="background-color: #ffffff; padding: 20px; border: 1px solid #ddd; border-radius: 5px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
        <h2>Add Machine</h2>
        <form action="add_machine.php" method="post">
            <div class="form-group">
                <label for="machine_name">Machine Name:</label>
                <input type="text" id="machine_name" name="machine_name" required>
            </div>
            <button type="submit" class="button">Add Machine</button>
        </form>
        <a href="machine_management.php" class="button">Back to Machine Management</a>
    </div>
</body>
</html>
