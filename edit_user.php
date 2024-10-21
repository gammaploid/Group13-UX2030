<?php
// edit_user.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?error=access_denied");
    exit();
}

include 'db_connection.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $role = $conn->real_escape_string($_POST['role']);

    $sql = "UPDATE users SET username='$username', role='$role' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        header("Location: user_management.php?success=User updated successfully");
    } else {
        header("Location: user_management.php?error=Error updating user: " . $conn->error);
    }
} else {
    $sql = "SELECT username, role FROM users WHERE id=$id";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
    } else {
        header("Location: user_management.php?error=User not found");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - SMD</title>
    <link rel="stylesheet" type="text/css" href="styles/global.css">
    <link rel="stylesheet" type="text/css" href="styles/edit_user.css">
</head>
<body>
    <h1>Edit User</h1>
    <form action="edit_user.php?id=<?php echo $id; ?>" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="<?php echo $row['username']; ?>" required>
        <label for="role">Role:</label>
        <select name="role" id="role" required>
            <option value="admin" <?php if($row['role'] == 'admin') echo 'selected'; ?>>Admin</option>
            <option value="manager" <?php if($row['role'] == 'manager') echo 'selected'; ?>>Factory Manager</option>
            <option value="operator" <?php if($row['role'] == 'operator') echo 'selected'; ?>>Production Operator</option>
            <option value="auditor" <?php if($row['role'] == 'auditor') echo 'selected'; ?>>Auditor</option>
        </select>
        <button type="submit" class="button">Update User</button>
    </form>
    <a href="user_management.php" class="button">Back to User Management</a>

    <script src="scripts/edit_user.js"></script>
</body>
</html>
