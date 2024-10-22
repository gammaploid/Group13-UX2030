<?php
// Define page variables
$page = 'add_user';
$page_title = 'Add New User';
$back_url = 'user_management.php';

include 'templates/admin_header.php';
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST["username"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $email = $conn->real_escape_string($_POST["email"]);
    $phone_number = $conn->real_escape_string($_POST["phone_number"]);
    $role = $conn->real_escape_string($_POST["user_role"]);

    $sql = "INSERT INTO users (username, password, email, phone_number, role) VALUES ('$username', '$password', '$email', '$phone_number', '$role')";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: user_management.php?success=User added successfully");
        exit();
    } else {
        $error = "Error adding user: " . $conn->error;
    }
}
?>

<div class="dashboard-content">
    <h1 class="page-title"><?php echo $page_title; ?></h1>

    <?php if (isset($error)) { ?>
        <p class="error"><?php echo $error; ?></p>
    <?php } ?>

    <div class="form-container">
        <form action="add_user.php" method="post" class="admin-form">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="phone_number">Phone:</label>
                <input type="text" id="phone_number" name="phone_number">
            </div>
            <div class="form-group">
                <label for="user_role">Role:</label>
                <select name="user_role" id="user_role" required>
                    <option value="admin">Admin</option>
                    <option value="manager">Factory Manager</option>
                    <option value="operator">Production Operator</option>
                    <option value="auditor">Auditor</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="button">Add User</button>
            </div>
        </form>
    </div>
</div>

<?php include 'templates/admin_footer.php'; ?>