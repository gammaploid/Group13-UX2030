<?php
// edit_profile.php
session_start();
include 'db_connection.php';

// Verify session variables
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=session_expired");
    exit();
}

// Retrieve user profile information
$user_id = $_GET['user_id']; // Retrieve user ID from query string
$sql = "SELECT * FROM users WHERE id = '$user_id'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $phone_number = $conn->real_escape_string($_POST['phone_number']); 

    $sql = "UPDATE users SET email = '$email', phone_number = '$phone_number' WHERE id = '$user_id'";
    if ($conn->query($sql) === TRUE) {
        header("Location: view_profile.php?success=Profile updated successfully");
    } else {
        header("Location: edit_profile.php?error=". $conn->error);
    }
    $conn->close();
}
?>

<h1>Edit Profile</h1>
<form action="edit_profile.php" method="post">
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" value="<?php echo $row['email'];?>" required>
    <button type="submit">Update Profile</button>
</form>
<label for="phone_number">Phone Number:</label>
<input type="tel" id="phone_number" name="phone_number" value="<?php echo $row['phone_number'];?>">