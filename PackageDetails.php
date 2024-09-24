<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Package Details</title>
    <link rel="stylesheet" href="css/Package.css">
    <link href="https://fonts.googleapis.com/css2?family=Itim&display=swap" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            <h2>Dashboard / Packages</h2>
            <p>Packages</p>
        </div>
        <div class="header-right">
            <div class="search-box"><input type="text" placeholder="Search"></div>
            <div class="user-wrapper">
                <p>Hi, Admin</p>
                <a href="AdminLogin.php"><img src="images/logout.png" alt="Logout Icon" class="logout-icon"></a>
            </div>
        </div>
    </header>

    <div class="countries-display">
        <div class="country-grid">
            <?php
            
            foreach ($data as $country => $cities) {
                echo '<div class="country-card">';
                
                if (isset($cities['CountryImage'])) {
                    echo '<img src="' . htmlspecialchars($cities['CountryImage']) . '" class="card-img">';
                } else {
                    echo '<img src="images/error.jpg" class="card-img">';
                }
                
                echo '<h4>' . ucfirst($country) . '</h4>';
                echo '<a href="Packages.php?country=' . urlencode($country) . '" class="view-btn">View More</a>';
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

<script>
function toggleForm() {
    const form = document.getElementById('addCountryForm');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}
</script>

</body>
</html>