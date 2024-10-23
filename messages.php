<?php

session_start();
include 'firebase_connection.php'; 

$pic = '';

if (isset($_SESSION['userName'])) {
    $pic = $_SESSION['profileImage'];
    $name = $_SESSION['userName'];
} else {
    $pic = 'images/user.png';
    $name = 'Admin';
}

$reference = $db->getReference('adminNotifications');
$snapshot = $reference->getSnapshot();
$messages = $snapshot->getValue();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link rel="stylesheet" href="messages.css"> 
</head>
<body>
<div class="sidebar">
    <div class="sidebar-header">
        <div class="admin-profile">
        <div class="admin-profile">
        <img src="<?php echo htmlspecialchars($pic); ?>" alt="Admin Profile Picture" class="profile-pic">
        <p><?php echo htmlspecialchars($name); ?></p>
        </div>
    </div>
</div>
        <ul>
            <li>
                <img src="images/home.png" alt="Dashboard Icon">
                <a href="AdminDashboard.php">Dashboard</a>
            </li>
            <li>
                <img src="images/package.png" alt="Packages Icon">
                <a href="AdminPackage.php">Travel Packages</a>
            </li>
            <li>
                <img src="images/users.png" alt="User Icon">
                <a href="AdminUser.php">User Management</a>
            </li>
            <li>
                <img src="images/inventory.png" alt="Inventory Icon">
                <a href="AdminInventory.php">Hotel/Flight Management</a>
            </li>
            <li>
                <img src="images/payments.png" alt="Report Icon">
                <a href="AdminReport.php">Bookings</a>
            </li>
        </ul>
    </div>

    <div class="main-content">
    <header>
        <h2>Dashboard / Messages</h2>
    </header>

    <ul class="notification-list">
    <?php if ($messages): ?>
        <?php foreach ($messages as $key => $message): ?>
            <li class="notification-item" onclick="showDetails('<?php echo $key; ?>')">
                <div class="user-info">
                    <h3><?php echo htmlspecialchars($message['userName']); ?></h3>
                    <p><?php echo htmlspecialchars($message['timestamp']); ?></p>
                </div>
                <div class="notification-details" id="details-<?php echo $key; ?>" style="display: none;" onclick="event.stopPropagation();">
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($message['userEmail']); ?></p>
                    <p><strong>Message:</strong> <?php echo htmlspecialchars($message['userMessage']); ?></p>
                    <p><strong>Timestamp:</strong> <?php echo htmlspecialchars($message['timestamp']); ?></p>
                    <form id="replyForm-<?php echo $key; ?>" method="POST" onclick="event.stopPropagation();">
                        <input type="hidden" name="userEmail" value="<?php echo htmlspecialchars($message['userEmail']); ?>">
                        <textarea name="replyMessage" placeholder="Enter your reply" onclick="event.stopPropagation();"></textarea>
                        <button type="button" onclick="event.stopPropagation(); sendReply('<?php echo $key; ?>')">Send Reply</button>
                    </form>
                </div>
            </li>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No notifications available.</p>
    <?php endif; ?>
</ul>

<script>
    function showDetails(notificationKey) {
        const detailsContainer = document.getElementById('details-' + notificationKey);
        if (detailsContainer.style.display === 'block') {
            detailsContainer.style.display = 'none';
        } else {
            const detailsContainers = document.querySelectorAll('.notification-details');
            detailsContainers.forEach(container => {
                container.style.display = 'none';
            });

            detailsContainer.style.display = 'block';
        }
    }

    function sendReply(notificationKey) {
    // Get the reply message and user email from the specific form
    const replyMessage = document.querySelector(`#replyForm-${notificationKey} textarea`).value;
    const userEmail = document.querySelector(`#replyForm-${notificationKey} input[name="userEmail"]`).value;

    // Fetch the user name from the notification item instead of the details section
    const userName = document.querySelector(`#details-${notificationKey}`).previousElementSibling.querySelector('h3').innerText;

    if (replyMessage.trim() === "") {
        alert("Please enter a reply message.");
        return;
    }

    // Data to be saved in Firebase
    const replyData = {
        adminReply: replyMessage,
        timestamp: new Date().toISOString(),
        userName: userName,
        userEmail: userEmail
    };

    // Firebase URL for saving the reply under userNotifications (using userEmail as reference)
    const userNotificationsRef = `userNotifications/${btoa(userEmail)}`; // Encoding the email to avoid special characters in path

    // Save the reply in Firebase (replace this with your actual saving logic)
    saveReplyToFirebase(userNotificationsRef, replyData);

    // Clear the reply message field after sending
    document.querySelector(`#replyForm-${notificationKey} textarea`).value = '';

    alert("Reply sent successfully!");
}

    function saveReplyToFirebase(reference, data) {
    fetch('reply.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            reference: reference,
            data: data
        })
    })
    .then(response => response.json())
    .then(result => {
        console.log('Reply saved:', result);
    })
    .catch(error => {
        console.error('Error saving reply:', error);
    });
}

</script>



</body>
</html>
