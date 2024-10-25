document.addEventListener('DOMContentLoaded', function () {
    // Elements Selection
    const notificationIcon = document.getElementById('notificationIcon');
    const notificationList = document.getElementById('notificationList');
    const userProfile = document.querySelector('.user-profile');
    const profileDropdown = document.querySelector('.profile-dropdown');
    const markAsCompleteButtons = document.querySelectorAll('.complete-job-button');
    const markAsReadButtons = document.querySelectorAll('.mark-as-read-button');

    // Toggle Notification List
    notificationIcon.addEventListener('click', function (event) {
        event.stopPropagation();
        notificationList.style.display = 
            notificationList.style.display === 'block' ? 'none' : 'block';
        profileDropdown.style.display = 'none'; // Close profile if open
    });

    // Toggle Profile Dropdown
    userProfile.addEventListener('click', function (event) {
        event.stopPropagation();
        profileDropdown.style.display = 
            profileDropdown.style.display === 'block' ? 'none' : 'block';
        notificationList.style.display = 'none'; // Close notifications if open
    });

    // Close Dropdowns on Outside Click
    document.addEventListener('click', function (event) {
        if (!notificationIcon.contains(event.target) && 
            !notificationList.contains(event.target)) {
            notificationList.style.display = 'none';
        }
        if (!userProfile.contains(event.target) && 
            !profileDropdown.contains(event.target)) {
            profileDropdown.style.display = 'none';
        }
    });

    // Prevent Dropdown Close on Inner Click
    profileDropdown.addEventListener('click', function (event) {
        event.stopPropagation();
    });

    // Mark Jobs as Complete
    markAsCompleteButtons.forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault(); // Prevent form submission
            const jobId = this.getAttribute('data-job-id');

            // Confirm before marking job as complete
            if (confirm('Are you sure you want to mark this job as complete?')) {
                const formData = new FormData();
                formData.append('job_id', jobId);
                formData.append('complete_job', '1');

                fetch('', { // Send to the same page
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    if (data.includes('success')) { // Check if PHP response indicates success
                        alert('Job marked as complete.');
                        this.closest('tr').remove(); // Remove job row from the table
                    } else {
                        alert('Error marking job as complete. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        });
    });

    // Mark Notifications as Read
    markAsReadButtons.forEach(button => {
        button.addEventListener('click', function () {
            const messageId = this.getAttribute('data-message-id');

            fetch('mark_message_as_read.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'message_id=' + encodeURIComponent(messageId)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.style.display = 'none'; // Hide button after marking as read
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });

    // Optional: Refresh Notification List Periodically (every 60 seconds)
    setInterval(fetchNotifications, 60000);

    // Simulate Fetching Notifications (Replace with actual API if needed)
    function fetchNotifications() {
        const notifications = [
            { message: 'Job #102 completed', time: '2 mins ago' },
            { message: 'Machine #3 needs maintenance', time: '15 mins ago' },
            { message: 'New job added to queue', time: '30 mins ago' }
        ];

        notificationList.innerHTML = ''; // Clear previous notifications

        notifications.forEach(notification => {
            const li = document.createElement('li');
            li.innerHTML = `
                <a href="#">${notification.message}</a>
                <span class="notification-time">${notification.time}</span>
                <button class="mark-as-read-button" data-message-id="${notification.message}">
                    Mark as Read
                </button>
            `;
            notificationList.appendChild(li);
        });
    }
});
