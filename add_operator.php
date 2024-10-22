<?php
// add_operator.php
session_start();
include 'db_connection.php';

// Authentication check for admins only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?error=access_denied");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $operator_name = $conn->real_escape_string($_POST['operator_name']);
    $sql = "INSERT INTO operators (operator_name) VALUES ('$operator_name')";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: operator_management.php?success=Operator added successfully");
    } else {
        header("Location: operator_management.php?error=Error adding operator: " . $conn->error);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Operator</title>
    <link rel="stylesheet" type="text/css" href="styles/global.css">
    <link rel="stylesheet" type="text/css" href="styles/add_operator.css">
</head>
<body>
    <h1>Add Operator</h1>
    <div class="container">
        <form action="add_operator.php" method="post">
            <label for="operator_name">Operator Name:</label>
            <input type="text" id="operator_name" name="operator_name" required>
            <button type="submit">Add Operator</button>
        </form>
    </div>
    <a href="operator_management.php">Back to Operator Management</a>
</body>
</html>
