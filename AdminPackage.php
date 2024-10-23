<?php
include 'firebase_connection.php'; 
include 'firebase_data.php'; 

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
    $countryDetail = $_POST['countryDescription'];  
    $tempImagePath = $_FILES['countryImage']['tmp_name'];

    $bucket = $storage->getBucket(); 
    $filePath = $countryImage; 

    $bucket->upload(
        fopen($tempImagePath, 'r'),
        ['name' => $filePath]
    );

    $bucketName = 'traveltrail-39e23.appspot.com'; 
    $imageUrl = sprintf(
        'https://firebasestorage.googleapis.com/v0/b/%s/o/%s?alt=media&token=', 
        $bucketName, 
        urlencode($filePath)
    );

    $postData = [
        'CountryImage' => $imageUrl,
        'CountryDetail' => $countryDetail,  
        'Availability' => 'Available'  
    ];

    $database->getReference('Packages/' . $countryName)->set($postData);

    echo "<meta http-equiv='refresh' content='0'>";
}

?>

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

                <a href="login.php" class="logout-link">
                <img src="images/logout.png" alt="Logout Icon" class="logout-icon">
                <span>Logout</span> 
            </a>
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

                    // Description Textarea 
                    $countryDetail = isset($cities['CountryDetail']) ? htmlspecialchars($cities['CountryDetail']) : 'No description available.';
                    echo '<div class="description-container">';
                    echo '<label for="descriptionTextarea_' . htmlspecialchars($country) . '"></label>';
                    echo '<textarea id="descriptionTextarea_' . htmlspecialchars($country) . '" class="form-control description-textarea">' . $countryDetail . '</textarea>';
                    echo '</div>';

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
                        <div class="mb-4">
                            <label for="countryDescription" class="form-label">Country Description</label>
                            <textarea class="form-control" id="countryDescription" name="countryDescription" rows="4" placeholder="Enter country description" required></textarea>
                        </div>
                        <div class="text-center">
                            <button type="submit" name="submit" class="btn btn-success btn-lg w-100">Submit Country</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>

        document.querySelectorAll('.description-textarea').forEach(textarea => {
        textarea.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault(); 

                const country = this.id.split('_')[1]; 
                const countryDetail = this.value; 

                console.log({country, countryDetail});

                const data = {
                    country: country,
                    countryDetail: countryDetail 
                };

                fetch('update_country.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Description updated successfully!');
                    } else {
                        alert('Error updating description: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to update description.');
                });
            }
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
