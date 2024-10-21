<?php
// user_management.php
session_start();
include 'db_connection.php';

// Authentication check for admins only
if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php?error=access_denied");
    exit();
}

// Handle search query
$search_query = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// SQL query to retrieve all users or search results
$sql = "SELECT id, username, role FROM users WHERE username LIKE '%$search_query%'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" type="text/css" href="styles/global.css">
    <link rel="stylesheet" type="text/css" href="styles/user_management.css">
</head>
<body>
    <h1>User Management</h1>
    <form action="user_management.php" method="get">
        <input type="text" name="search" placeholder="Search by username" value="<?php echo htmlspecialchars($search_query); ?>">
        <button type="submit">Search</button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['username']; ?></td>
            <td><?php echo $row['role']; ?></td>
            <td>
                <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="button">Edit</a> | 
                <a href="delete_user.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?');" class="button">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>

    <div class="button-group">
        <a href="add_user.php" class="button">Add New User</a>
    </div>

    <script src="scripts/user_management.js"></script>
</body>
</html>
