<?php

require 'vendor/autoload.php';

use Kreait\Firebase\Factory;

$serviceAccount = __DIR__ . '/prvkey.json';

$factory = (new Factory)
    ->withServiceAccount($serviceAccount)
    ->withDatabaseUri('https://traveltrail-39e23-default-rtdb.firebaseio.com/');

$database = $factory->createDatabase();
$storage = $factory->createStorage(); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];
    $action = $_POST['action'];

    if ($action === 'verify') {
        $database->getReference('users/' . $userId)->update(['status' => 'verified']);
    } elseif ($action === 'reject') {
        $database->getReference('users/' . $userId)->update(['status' => 'rejected']);
    }
}

$usersRef = $database->getReference('users');
$users = $usersRef->getValue();
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
        <h2>TravelTrail</h2>
    </div>
    <ul>
        <li>
            <img src="images/home dark.jpg" alt="Dashboard Icon">
            <a href="AdminDashboard.php">Dashboard</a>
        </li>
        <li>
            <img src="images/package.png" alt="Packages Icon">
            <a href="AdminPackage.php">Travel Packages</a>
        </li>
        <li class="active">
            <img src="images/users.png" alt="User Icon">
            <a href="AdminUser.php">User Management</a>
        </li>
        <li>
            <img src="images/inventory.png" alt="Inventory Icon">
            <a href="AdminInventory.php">Inventory Status</a>
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
        <div class="header-right d-flex align-items-center">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Search by Name, Phone, Email, or Passport" onkeyup="filterUsers()">
            </div>

            <div class="user-wrapper">
                <p>Hi, Admin</p>
                <a href="AdminLogin.php"><img src="images/logout.png" alt="Logout Icon" class="logout-icon"></a>
            </div>
        </div>
    </header>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Passport No.</th>
                <th>Phone Number</th>
                <th>Email</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (is_array($users) && !empty($users)): ?>
                <?php foreach ($users as $key => $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo !empty($user['passport']) ? htmlspecialchars($user['passport']) : 'N/A'; ?></td>
                        <td><?php echo htmlspecialchars($user['phone']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <?php 
                                $status = isset($user['status']) ? $user['status'] : 'unverified';
                                if ($status === 'verified'): 
                            ?>
                                <span class="status verified">Verified</span>
                            <?php elseif ($status === 'rejected'): ?>
                                <span class="status rejected">Rejected</span>
                            <?php else: ?>
                                <span class="status unverified">Unverified</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($status !== 'verified' && $status !== 'rejected'): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($key); ?>">
                                    <input type="hidden" name="action" value="verify">
                                    <button type="submit" class="btn btn-success">✔️ Verify</button>
                                </form>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($key); ?>">
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="btn btn-danger">❌ Reject</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No users found</td>
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
            const name = cells[0].textContent.toLowerCase();
            const passport = cells[1].textContent.toLowerCase();
            const phone = cells[2].textContent.toLowerCase();
            const email = cells[3].textContent.toLowerCase();

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
