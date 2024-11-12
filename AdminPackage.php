<?php

session_start();
include 'firebase_connection.php';
include 'firebase_data.php'; 

$pic = '';

if (isset($_SESSION['userName'])) {
    $pic = $_SESSION['profileImage'];
    $name = $_SESSION['userName'];
} else {
    $pic = 'images/user.png';
    $name = 'Admin';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['ajax']) && $_POST['ajax'] === 'updateAvailability') {
        $country = $_POST['country'];
        $availability = $_POST['availability'];

        if (empty($country) || empty($availability)) {
            echo json_encode(['success' => false, 'message' => 'Invalid or missing data']);
            exit();
        }

        $updateData = ['Availability' => $availability];

        try {
            $database->getReference('Packages/' . $country)->update($updateData);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            error_log($e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error updating availability: ' . $e->getMessage()]);
        }

        exit(); 
    }

    if (isset($_POST['ajax']) && $_POST['ajax'] === 'deleteCountry') {
        $country = $_POST['country'];

        if (empty($country)) {
            echo json_encode(['success' => false, 'message' => 'Invalid or missing country']);
            exit();
        }

        try {
            $database->getReference('Packages/' . $country)->remove();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            error_log($e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error deleting country: ' . $e->getMessage()]);
        }

        exit(); 
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
    <link rel="stylesheet" href="css/Country.css">
    <link href="https://fonts.googleapis.com/css2?family=Itim&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

</head>
<body>
<div class="sidebar">
    <div class="sidebar-header">
        <div class="admin-profile">
        <img src="<?php echo htmlspecialchars($pic); ?>" alt="Admin Profile Picture" class="profile-pic">
        <p><?php echo htmlspecialchars($name); ?></p>
        </div>
    </div>
        <ul>
            <li>
                <img src="images/home.webp" alt="Dashboard Icon">
                <a href="AdminDashboard.php">Dashboard</a>
            </li>
            <li class="active">
                <img src="images/packages.png" alt="Packages Icon">
                <a href="AdminPackage.php">Travel Packages</a>
            </li>
            <li>
                <img src="images/users.webp" alt="User Icon">
                <a href="AdminUser.php">User Management</a>
            </li>
            <li>
                <img src="images/inventory.webp" alt="Inventory Icon">
                <a href="AdminInventory.php">Hotel/Flight Management</a>
            </li>
            <li>
                <img src="images/payments.webp" alt="Report Icon">
                <a href="AdminReport.php">Bookings</a>
            </li>
        </ul>
            <a href="php_functions/logout.php"  class="logout-link">
                <img src="images/logout.png" alt="Logout Icon" class="logout-icon">
                <span>Logout</span>
            </a>
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

        <div class="add-country-section" id="addCountryForm" style="display: flex; margin-left: 20px;">
        <h4 class="form-title">Add New Country</h4>
        <form action="AdminPackage.php" method="POST" enctype="multipart/form-data" class="add-country-form">  
            <div class="form-row">
                <div class="form-group">
                    <label for="countryName">Country Name</label>
                    <input type="text" id="countryName" name="countryName" placeholder="Enter country name" required>
                </div>
                <div class="form-group">
                    <label for="countryImage">Upload Country Image</label>
                    <input type="file" id="countryImage" name="countryImage" accept="image/*" required>
                </div>
            </div>

            <div class="form-group">
                <label for="countryDescription">Country Description</label>
                <textarea id="countryDescription" name="countryDescription" rows="3" placeholder="Enter country description" required></textarea>
            </div>
            <div class="form-group">
                <button type="submit" name="submit" class="submit-btn-success">Submit</button>
            </div>
    </form>
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

                    // Description Textarea 
                    $countryDetail = isset($cities['CountryDetail']) ? htmlspecialchars($cities['CountryDetail']) : 'No description available.';
                    echo '<div class="description-container">';
                    echo '<label for="descriptionTextarea_' . htmlspecialchars($country) . '"></label>';
                    echo '<textarea id="descriptionTextarea_' . htmlspecialchars($country) . '" class="form-control D_textarea">' . $countryDetail . '</textarea>';
                    echo '</div>';

                    // Availability checkbox with onchange event
                    $availability = isset($cities['Availability']) ? $cities['Availability'] : 'Not Available';
                    $checked = ($availability === 'Available') ? 'checked' : '';
                    
                    echo '<div class="form-check">';
                    echo '<input type="checkbox" class="form-check-input" id="availabilityCheckbox_' . htmlspecialchars($country) . '" ' . $checked . 
                    ' onchange="updateAvailability(\'' . htmlspecialchars($country) . '\', this.checked)">';
                    echo '<label class="form-check-label" for="availabilityCheckbox_' . htmlspecialchars($country) . '">Available</label>';
                    echo '</div>';

                    echo '<a href="PackageDetails.php?country=' . urlencode($country) . '" class="view-btn">View</a>';
                    echo '<button class="delete-btn" onclick="deleteCountry(\'' . htmlspecialchars($country) . '\')">Delete</button>';

                    echo '</div>';
                }
                ?>
            </div> 
        </div>

   
        <div class="card-form-container" id="addCountryForm" style="display: none;">
            <h4 class="form-title">Add New Country</h4>
            <form action="AdminPackage.php" method="POST" enctype="multipart/form-data" class="add-country-form">
                <div class="form-group">
                    <label for="countryName">Country Name</label>
                    <input type="text" id="countryName" name="countryName" placeholder="Enter country name" required>
                </div>
                <div class="form-group">
                    <label for="countryImage">Upload Country Image</label>
                    <input type="file" id="countryImage" name="countryImage" accept="image/*" required>
                </div>
                <div class="form-group">
                    <label for="countryDescription">Country Description</label>
                    <textarea id="countryDescription" name="countryDescription" rows="3" placeholder="Enter country description" required></textarea>
                </div>
                <div class="form-group">
                <button type="submit" name="submit" class="submit-btn-success">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>

    document.querySelectorAll('.D_textarea').forEach(textarea => {
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

            fetch('php_functions/update_country.php', {
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


    function filterCountries() {
        const input = document.getElementById('searchInput').value.toLowerCase();
        const countryCards = document.querySelectorAll('.country-card');
        
        countryCards.forEach(card => {
            const countryName = card.querySelector('.country-name').textContent.toLowerCase();
            card.style.display = countryName.includes(input) ? 'block' : 'none';
        });
    }

    window.onload = function () {
        const form = document.getElementById('addCountryForm');
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    };

    function updateAvailability(country, isChecked) {
    const availability = isChecked ? 'Available' : 'Not Available';
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '', true); 
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            const status = xhr.status;
            if (status === 200) {
                console.log("Availability updated successfully");
                window.location.reload();
            } else {
                console.error("Error updating availability");
                
            }
        }
    };

    xhr.send('ajax=updateAvailability&country=' + encodeURIComponent(country) + '&availability=' + encodeURIComponent(availability));
}

    function deleteCountry(country) {
    if (confirm("Are you sure you want to delete " + country + "? This action cannot be undone.")) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '', true); 
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        alert("Country deleted successfully.");
                        window.location.reload();  
                    } else {
                        alert("Error deleting country: " + response.message);
                    }
                } else {
                    alert("An error occurred while processing the request.");
                }
            }
        };
        xhr.send('ajax=deleteCountry&country=' + encodeURIComponent(country));
    }
}



</script>

</body>
</html>
