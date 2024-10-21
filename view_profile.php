
<?php
// view_profile.php
session_start();
include 'db_connection.php';

// Verify session variables
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=session_expired");
    exit();
}

// Retrieve user profile information
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users"; // Retrieve all users
$result = $conn->query($sql);

?>


<h1>User Profiles</h1>
<?php while ($row = $result->fetch_assoc()): ?>
    <div>
        <h2/User Profile (ID: <?php echo $row['id'];?>)</h2>
        <p>Username: <?php echo $row['username'];?></p>
        <p>Role: <?php echo $row['role'];?></p>
        <p>Email: <?php echo $row['email'];?></p>
        
        <!-- Edit and Delete links for each profile -->
        <a href="edit_profile.php?user_id=<?php echo $row['id'];?>">Edit Profile</a>
        <?php if ($_SESSION['role'] == 'admin'): ?>
            <a href="delete_profile.php?user_id=<?php echo $row['id'];?>">Delete Profile</a>
        <?php endif; ?>
    </div>
<?php endwhile; ?>

<p>Phone Number: <?php echo $row['phone_number'];?></p>
