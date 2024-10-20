<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Country Management</title>
    <link rel="stylesheet" href="css/package.css">
    <link href="https://fonts.googleapis.com/css2?family=Itim&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

</head>
<body>

<?php
include 'firebase_connection.php'; 
include 'firebase_data.php'; 

session_start();
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    header('Location: AdminLogin.php');
    exit();
}

$userSnapshot = $database->getReference('users/' . $userId)->getValue();
if (!$userSnapshot || $userSnapshot['role'] !== 'admin' || $userSnapshot['status'] !== 'approved') {
    header('Location: index.php');
    exit();
}

$adminName = $userSnapshot['name'] ?? 'Admin';

// Fetch user data
$usersRef = $database->getReference('users');
$users = $usersRef->getValue();
$newUsersCount = 0;
if (is_array($users)) {
    foreach ($users as $user) {
        if (!array_key_exists('status', $user)) {
            $newUsersCount++;
        }
    }
}

if (isset($_POST['submit'])) {
    $countryName = $_POST['countryName'];
    $countryImage = $_FILES['countryImage']['name'];
    $tempImagePath = $_FILES['countryImage']['tmp_name'];

    $bucket = $storage->getBucket(); 
    
    $filePath = $countryImage; 

    $bucket->upload(
        fopen($tempImagePath, 'r'),
        [
            'name' => $filePath 
        ]
    );

    $bucketName = 'traveltrail-39e23.appspot.com'; 
    $imageUrl = sprintf('https://firebasestorage.googleapis.com/v0/b/%s/o/%s?alt=media', 
        $bucketName, 
        urlencode($filePath) 
    );

    $postData = [
        'CountryImage' => $imageUrl 
    ];

    $database->getReference('Packages/' . $countryName)->set($postData);

    echo "<meta http-equiv='refresh' content='0'>";
}
?>

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
            <li class="active">
                <img src="images/package dark.jpg" alt="Packages Icon">
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
                <img src="images/report.png" alt="Report Icon">
                <a href="AdminReport.php">Report</a>
            </li>
        </ul>
    </div>


    <div class="main-content">
        <header>
            <div class="header-left">
                <h2>Dashboard / Travel Package</h2>
                <p>Travel Package</p>
            </div>

            <div class="header-right d-flex align-items-center">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Search" onkeyup="filterCountries()">
                </div>

                <div class="user-wrapper">
                    <p>Hi, <?php echo htmlspecialchars($adminName); ?>!</p> 
                    <a href="AdminLogin.php">
                        <img src="images/logout.png" alt="Logout Icon" class="logout-icon">
                    </a>
                </div>
            </div>
        </header>


        <div class="countries-display" id="countriesDisplay">
            <div class="country-grid">
                <?php
                foreach ($data as $country => $cities) {
                    echo '<div class="country-card">';
                    
                    if (isset($cities['CountryImage'])) {
                        echo '<img src="' . htmlspecialchars($cities['CountryImage']) . '" class="card-img">';
                    } else {
                        echo '<img src="images/error.jpg" class="card-img">';
                    }
                    
                    echo '<h4 class="country-name">' . ucfirst($country) . '</h4>';

                    // Availability checkbox
                    $availability = isset($cities['Availability']) ? $cities['Availability'] : 'Not Available';
                    echo '<div class="form-check">';
                    echo '<input type="checkbox" class="form-check-input" id="availabilityCheckbox_' . htmlspecialchars($country) . '" ' . ($availability === 'Available' ? 'checked' : '') . '>';
                    echo '<label class="form-check-label" for="availabilityCheckbox_' . htmlspecialchars($country) . '">Available</label>';
                    echo '</div>';

                    echo '<a href="PackageDetails.php?country=' . urlencode($country) . '" class="view-btn">View More</a>';
                    echo '</div>';
                        }
                ?>
            </div> 
        </div>

        <div class="add-package-btn text-center mt-4">
    <button type="button" class="btn btn-primary btn-lg rounded-circle" onclick="toggleForm()">
        <i class="fas fa-plus"></i>
    </button>
</div>

<div id="addCountryForm" style="display:none; margin-top: 30px;" class="container">
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title text-center mb-4">Add New Country</h5>
            <form action="AdminPackage.php" method="POST" enctype="multipart/form-data">
                <div class="mb-4">
                    <label for="countryName" class="form-label">Country Name</label>
                    <input type="text" class="form-control" id="countryName" name="countryName" placeholder="Enter country name" required>
                </div>
                <div class="mb-4">
                    <label for="countryImage" class="form-label">Upload Country Image</label>
                    <input type="file" class="form-control" id="countryImage" name="countryImage" accept="image/*" required>
                </div>
                <div class="text-center">
                    <button type="submit" name="submit" class="btn btn-success btn-lg w-100">Submit Country</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>

document.querySelectorAll('.form-check-input').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const country = this.id.split('_')[1]; 
        const availability = this.checked ? 'Available' : 'Not Available'; 

        fetch('update_country.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ 
                country: country, 
                availability: availability 
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Country availability updated successfully!');
            } else {
                alert('Error updating availability: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to update availability.');
        });
    });
});



function toggleForm() {
    const form = document.getElementById('addCountryForm');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}

function filterCountries() {
    const input = document.getElementById('searchInput').value.toLowerCase();
    const countryCards = document.querySelectorAll('.country-card');
    
    countryCards.forEach(card => {
        const countryName = card.querySelector('.country-name').textContent.toLowerCase();
        card.style.display = countryName.includes(input) ? 'block' : 'none';
    });
}
</script>

</body>
</html>
