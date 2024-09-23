<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Country Management</title>
    <link rel="stylesheet" href="css/Package.css">
    <link href="https://fonts.googleapis.com/css2?family=Itim&display=swap" rel="stylesheet">
    <style>
        .country-grid { display: flex; flex-wrap: wrap; gap: 20px; }
        .country-card { border: 1px solid #ddd; padding: 20px; width: 150px; text-align: center; }
        .add-package-btn { margin-top: 20px; }
        .add-package-btn button { padding: 10px 20px; background-color: black; color: white; cursor: pointer; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); justify-content: center; align-items: center; }
        .modal-content { background-color: white; padding: 20px; border-radius: 10px; }
        .close-modal { background-color: red; color: white; cursor: pointer; padding: 5px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header">
            <h2>TravelTrail</h2>
        </div>
        <ul>
            <li><img src="images/home dark.jpg" alt="Dashboard Icon"><a href="AdminDashboard.php">Dashboard</a></li>
            <li class="active"><img src="images/package dark.jpg" alt="Packages Icon"><a href="AdminPackage.php">Travel Packages</a></li>
            <li><img src="images/users.png" alt="User Icon"><a href="#">User Management</a></li>
            <li><img src="images/inventory.png" alt="Inventory Icon"><a href="#">Inventory Status</a></li>
            <li><img src="images/report.png" alt="Report Icon"><a href="#">Report</a></li>
        </ul>
    </div>

    <div class="main-content">
        <header>
            <div class="header-left">
                <h2>Dashboard / Travel Package</h2>
                <p>Travel Package</p>
            </div>
            <div class="header-right">
                <div class="search-box"><input type="text" placeholder="Search"></div>
                <div class="user-wrapper">
                    <p>Hi, Admin</p>
                    <a href="AdminLogin.php"><img src="images/logout.png" alt="Logout Icon" class="logout-icon"></a>
                </div>
            </div>
        </header>

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
