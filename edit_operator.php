<?php
session_start();
include 'db_connection.php';

// Authentication check for admins only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?error=access_denied");
    exit();
}

$operator_id = isset($_GET['operator_id']) ? (int)$_GET['operator_id'] : 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $operator_name = $conn->real_escape_string($_POST['operator_name']);
    $sql = "UPDATE operators SET operator_name='$operator_name' WHERE operator_id=$operator_id";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: operator_management.php?success=Operator updated successfully");
    } else {
        header("Location: operator_management.php?error=Error updating operator: " . $conn->error);
    }
} else {
    $sql = "SELECT operator_name FROM operators WHERE operator_id=$operator_id";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
    } else {
        header("Location: operator_management.php?error=Operator not found");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Operator</title>
    <link rel="stylesheet" type="text/css" href="styles/edit_operator.css">
</head>
<body>
    <h1>Edit Operator</h1>
    <div class="container">
        <form action="edit_operator.php?operator_id=<?php echo $operator_id;?>" method="post">
            <label for="operator_name">Operator Name:</label>
            <input type="text" id="operator_name" name="operator_name" value="<?php echo $row['operator_name'];?>" required>
            <button type="submit">Update Operator</button>
        </form>
    </div>
    <a href="operator_management.php">Back to Operator Management</a>
</body>
</html>
