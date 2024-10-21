// job_management.php (updated)

$sql = "UPDATE jobs SET status = 'completed' WHERE id =?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $jobId);
$stmt->execute();

$notification = new Notification($conn);
$notificationId = $notification->createNotification('job_completion', "Job $jobId completed successfully");
$notification->addRecipient($notificationId, $_SESSION['user_id']);
