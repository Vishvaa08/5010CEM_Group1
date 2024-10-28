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

// Fetch user data
$usersRef = $database->getReference('users');
$users = $usersRef->getValue();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userId = $_POST['userId'] ?? null;

    if ($userId) {
        // Verify User
        if (isset($_POST['verifyUser'])) {
            $userSnapshot = $database->getReference('users/' . $userId)->getValue();
            if ($userSnapshot['status'] === 'approved') {
                $database->getReference('users/' . $userId)
                    ->update([
                        'status' => 'verified'
                    ]);
            }
        }

        // Reject User
        if (isset($_POST['rejectUser'])) {
            $database->getReference('users/' . $userId)->remove();
        }

        // Approve Admin
        if (isset($_POST['approveAdmin'])) {
            $userSnapshot = $database->getReference('users/' . $userId)->getValue();
            if ($userSnapshot['role'] === 'adminREQUEST' && $userSnapshot['status'] === 'pending') {
                $database->getReference('users/' . $userId)
                    ->update([
                        'role' => 'admin',
                        'status' => 'approved'
                    ]);
            }
        }

        // Redirect after action
        header('Location: AdminUser.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin User Management</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/User.css">
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
            <li class="active">
                <img src="images/user.jpg" alt="User Icon">
                <a href="AdminUser.php">User Management</a>
            </li>
            <li>
                <img src="images/inventory.png" alt="Inventory Icon">
                <a href="AdminInventory.php">Hotel/Flight Management</a>
            </li>
            <li>
                <img src="images/report.png" alt="Report Icon">
                <a href="AdminReport.php">Report</a>
            </li>
        </ul>
    </div>

<div class="main-content">
    <header>
        <div class="header-left">
            <h2>Dashboard / User Management</h2>
            <p>User Management</p>
        </div>
        <div class="header-right">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Search by name or date" onkeyup="filterUsers()">
            </div>
            <div class="header-right d-flex align-items-center">
            <a href="login.php" class="logout-link">
                <img src="images/logout.png" alt="Logout Icon" class="logout-icon">
                <span>Logout</span> 
            </a>
        </div>
        </div>
    </header>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Profile</th>
                <th>Name</th>
                <th>Role</th>
                <th>Status</th>
                <th>Passport No.</th>
                <th>Phone Number</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (is_array($users) && !empty($users)): ?>
                <?php foreach ($users as $key => $user): ?>
                    <tr>
                        <td>
                            <img src="<?php echo !empty($user['profileImageUrl']) ? htmlspecialchars($user['profileImageUrl']) : 'images/default_profile_picture.jpg'; ?>" 
                            style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                        </td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo isset($user['role']) ? htmlspecialchars($user['role']) : 'N/A'; ?></td>
                        <td><?php echo isset($user['status']) ? htmlspecialchars($user['status']) : 'N/A'; ?></td>
                        <td><?php echo !empty($user['passport']) ? htmlspecialchars($user['passport']) : 'N/A'; ?></td>
                        <td><?php echo htmlspecialchars($user['phone']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                        <form method="POST" action="AdminUser.php" style="display: inline-block;">
                            <input type="hidden" name="userId" value="<?php echo $key; ?>">
                            <button type="submit" name="rejectUser" class="btn btn-danger">Reject</button>
                        </form>

                        <form method="POST" action="AdminUser.php" style="display: inline-block;">
                            <input type="hidden" name="userId" value="<?php echo $key; ?>">
                            <button type="submit" name="verifyUser" class="btn btn-success">Verify</button>
                        </form>

                        <form method="POST" action="AdminUser.php" style="display: inline-block;">
                            <input type="hidden" name="userId" value="<?php echo $key; ?>">
                            <button type="submit" name="approveAdmin" class="btn btn-primary">Admin</button>
                        </form>
                    </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">No users found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    function filterUsers() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            const name = cells[1].textContent.toLowerCase();
            const passport = cells[4].textContent.toLowerCase();
            const phone = cells[5].textContent.toLowerCase();
            const email = cells[6].textContent.toLowerCase();

            if (
                name.includes(searchInput) ||
                passport.includes(searchInput) ||
                phone.includes(searchInput) ||
                email.includes(searchInput)
            ) {
                row.style.display = ''; 
            } else {
                row.style.display = 'none'; 
            }
        });
    }
</script>

</body>
</html>
