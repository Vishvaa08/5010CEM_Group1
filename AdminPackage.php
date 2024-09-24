<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Country Management</title>
    <link rel="stylesheet" href="css/Package.css">
    <link href="https://fonts.googleapis.com/css2?family=Itim&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>

<?php
include 'firebase_connection.php'; 
include 'firebase_data.php'; 

if (isset($_POST['submit'])) {
    $countryName = $_POST['countryName'];
    $countryImage = $_FILES['countryImage']['name'];
    
    move_uploaded_file($_FILES['countryImage']['tmp_name'], 'images/' . $countryImage);

    $postData = [
        'CountryImage' => 'images/' . $countryImage
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
                <h2>Dashboard / Travel Package</h2>
                <p>Travel Package</p>
            </div>

            <div class="header-right d-flex align-items-center">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Search" onkeyup="filterCountries()">
                </div>

                <div class="user-wrapper">
                    <p>Hi, Admin</p>
                    <a href="AdminLogin.php"><img src="images/logout.png" alt="Logout Icon" class="logout-icon"></a>
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
                    echo '<a href="PackageDetails.php?country=' . urlencode($country) . '" class="view-btn">View More</a>';
                    echo '</div>';
                }
                ?>
            </div> 
        </div>

        <div class="add-package-btn">
            <button type="button" class="btn btn-primary" onclick="toggleForm()">Add Country</button>
        </div>

        <div id="addCountryForm" style="display:none; margin-top: 20px;">
            <h5>Add New Country</h5>
            <form action="AdminPackage.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="countryName" class="form-label">Country Name</label>
                    <input type="text" class="form-control" id="countryName" name="countryName" required>
                </div>
                <div class="mb-3">
                    <label for="countryImage" class="form-label">Upload Country Image</label>
                    <input type="file" class="form-control" id="countryImage" name="countryImage" accept="image/*" required>
                </div>
                <button type="submit" name="submit" class="btn btn-primary">Submit Country</button>
            </form>
        </div>
    </div>
</div>

<script>
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

        <!-- Display existing countries -->
        <div class="countries-display">
            <div class="country-grid">
                <?php
                if ($countries) {
                    foreach ($countries as $id => $country) {
                        
                        if (!empty($country['imagePath']) && !empty($country['countryName'])) {
                            echo '<div class="country-card">';
                            echo '<img src="' . htmlspecialchars($country['imagePath']) . '" alt="' . htmlspecialchars($country['countryName']) . '" style="width: 150px; height: 150px;">';
                            echo '<h4>' . htmlspecialchars($country['countryName']) . '</h4>';
                            echo '</div>';
                        }
                    }
                } else {
                    echo '<p>No countries found.</p>';
                }
                ?>
            </div>
        </div>


        <!-- Add country button -->
        <div class="add-package-btn">
            <button onclick="openModal()">+</button>
        </div>

        <!-- Modal for adding a new country -->
        <div id="addCountryModal" class="modal">
            <div class="modal-content">
                <h3>Add New Country</h3>
                <form action="AdminPackage.php" method="POST" enctype="multipart/form-data">
                    <label for="countryName">Country Name</label>
                    <input type="text" id="countryName" name="countryName" required><br><br>
                    <label for="countryImage">Upload Country Image</label>
                    <input type="file" id="countryImage" name="countryImage" required><br><br>
                    <button type="submit" name="submit">Submit Country</button>
                </form>
                <button class="close-modal" onclick="closeModal()">Close</button>
            </div>
        </div>

    </div>

    <script>
        function openModal() {
            document.getElementById('addCountryModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('addCountryModal').style.display = 'none';
        }
    </script>

</body>
</html>