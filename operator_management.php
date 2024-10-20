<?php
session_start();
include 'db_connection.php';

// Authentication check for admins only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?error=access_denied");
    exit();
}

// SQL query to retrieve all operators
$sql = "SELECT * FROM operators";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operator Management</title>
    <link rel="stylesheet" type="text/css" href="styles/operator_management.css">
</head>
<body>
    <h1>Operator Management</h1>
    <?php if(isset($_GET['error'])) {?>
        <div style="color: red;"><?php echo $_GET['error'];?></div>
    <?php }?>
    <?php if(isset($_GET['success'])) {?>
        <div style="color: green;"><?php echo $_GET['success'];?></div>
    <?php }?>

    <table>
        <tr>
            <th>Operator ID</th>
            <th>Operator Name</th>
            <th>Actions</th>
        </tr>
        <?php while($row = $result->fetch_assoc()) {?>
        <tr>
            <td><?php echo $row['operator_id'];?></td>
            <td><?php echo $row['operator_name'];?></td>
            <td>
                <a href="edit_operator.php?operator_id=<?php echo $row['operator_id'];?>">Edit</a> | 
                <a href="delete_operator.php?operator_id=<?php echo $row['operator_id'];?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php }?>
    </table>

    <div class="container">
        <div class="button-group">
            <a href="add_operator.php" class="button">Add New Operator</a>
        </div>
    </div>

    <script src="scripts/operator_management.js"></script>
</body>
</html>
