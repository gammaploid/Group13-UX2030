<?php
// Define page variables
$page = 'edit_user';
$page_title = 'Edit User';
$back_url = 'user_management.php';

include 'templates/admin_header.php';
include 'db_connection.php';

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($user_id === 0) {
    header("Location: user_management.php?error=Invalid user ID");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST["username"]);
    $email = $conn->real_escape_string($_POST["email"]);
    $phone_number = $conn->real_escape_string($_POST["phone_number"]);
    $role = $conn->real_escape_string($_POST["user_role"]);

    $sql = "UPDATE users SET username = '$username', email = '$email', phone_number = '$phone_number', role = '$role' WHERE id = $user_id";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: user_management.php?success=User updated successfully");
        exit();
    } else {
        $error = "Error updating user: " . $conn->error;
    }
}

$sql = "SELECT * FROM users WHERE id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    header("Location: user_management.php?error=User not found");
    exit();
}

$user = $result->fetch_assoc();
?>

<div class="dashboard-content">
    <h1 class="page-title"><?php echo $page_title; ?></h1>

    <?php if (isset($error)) { ?>
        <p class="error"><?php echo $error; ?></p>
    <?php } ?>

    <div class="form-container">
        <form action="edit_user.php?id=<?php echo $user_id; ?>" method="post" class="admin-form">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="phone_number">Phone:</label>
                <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>">
            </div>
            <div class="form-group">
                <label for="user_role">Role:</label>
                <select name="user_role" id="user_role" required>
                    <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="manager" <?php echo $user['role'] === 'manager' ? 'selected' : ''; ?>>Factory Manager</option>
                    <option value="operator" <?php echo $user['role'] === 'operator' ? 'selected' : ''; ?>>Production Operator</option>
                    <option value="auditor" <?php echo $user['role'] === 'auditor' ? 'selected' : ''; ?>>Auditor</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="button">Update User</button>
            </div>
        </form>
        <form action="delete_user.php" method="post" onsubmit="return confirm('Are you sure you want to delete this user?');" class="delete-form">
            <input type="hidden" name="id" value="<?php echo $user_id; ?>">
            <button type="submit" class="button delete-button">Delete User</button>
        </form>
    </div>
</div>

<?php include 'templates/admin_footer.php'; ?>