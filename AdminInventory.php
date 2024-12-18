<?php

use Kreait\Firebase\Exception\FirebaseException;

session_start();
include 'firebase_connection.php';

$pic = isset($_SESSION['userName']) ? $_SESSION['profileImage'] : 'images/user.png';
$name = isset($_SESSION['userName']) ? $_SESSION['userName'] : 'Admin';

// Get country, city, and hotel data from 
$country = isset($_GET['country']) ? $_GET['country'] : '';
$city = isset($_GET['city']) ? $_GET['city'] : '';
$hotelIndex = isset($_GET['hotel']) ? (int)$_GET['hotel'] : null;
$view = isset($_GET['view']) ? $_GET['view'] : 'hotel';

// Fetch country, city, and hotel data from Firebase
$countryRef = $database->getReference('Packages');
$countryData = $countryRef->getValue() ?: [];
$countries = array_keys($countryData);

$cities = [];
if ($country) {
    $cityRef = $database->getReference('Packages/' . $country);
    $cityData = $cityRef->getValue() ?: [];
    foreach ($cityData as $cityName => $details) {
        if (is_array($details) && isset($details['City'])) {
            $cities[] = $cityName;
        }
    }
}

$hotels = [];
$flights = [];
$hotelDescription = '';
$imageUrl = '';
$rooms = [];

// Fetch hotel data when a country and city are selected
if ($country && $city) {
    $hotelRef = $database->getReference('Packages/' . $country . '/' . $city . '/Hotels');
    $hotelData = $hotelRef->getValue() ?: [];
    foreach ($hotelData as $index => $hotelDetail) {
        if (is_array($hotelDetail) && isset($hotelDetail['Hotel'])) {
            $hotels[$index] = $hotelDetail['Hotel'];
        }
    }

    // Fetch selected hotel details when hotel is selected
    if ($hotelIndex !== null && isset($hotelData[$hotelIndex])) {
        $selectedHotel = $hotelData[$hotelIndex];
        $imageUrl = $selectedHotel['Image'] ?? 'images/error.jpg';
        $hotelDescription = isset($selectedHotel['Description']) ? $selectedHotel['Description'] : '';
        $roomData = $selectedHotel['Rooms'] ?? [];

        foreach ($roomData as $roomType => $roomDetails) {
            $rooms[$roomType] = [
                'availability' => $roomDetails['Availability'] ?? 0,
                'price' => $roomDetails['Price'] ?? 0,
            ];
        }
    }

    // Fetch flight data for the selected city
    $flightRef = $database->getReference('Packages/' . $country . '/' . $city . '/Flights');
    $flightData = $flightRef->getValue() ?: [];
    foreach ($flightData as $class => $details) {
        $flights[$class] = [
            'price' => isset($details['Price']) ? (int)$details['Price'] : 0,
            'seats' => isset($details['Seats']) ? (int)$details['Seats'] : 0,
        ];
    }
}

// Handle form submission for hotel and flight updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check and update hotel data
    if ($view === 'hotel' && $country && $city && $hotelIndex !== null) {
        $hotelPath = "Packages/$country/$city/Hotels/$hotelIndex";
        $hotelRef = $database->getReference($hotelPath);
        $existingHotelData = $hotelRef->getValue();

        if ($existingHotelData !== null) {
            // Update hotel description and availability
            if (isset($_POST['hotel_description']) || isset($_POST['hotel_availability'])) {
                $updateData = [];
                if (isset($_POST['hotel_description'])) {
                    $updateData['Description'] = $_POST['hotel_description'];
                }
                if (isset($_POST['hotel_availability'])) {
                    $updateData['Availability'] = $_POST['hotel_availability'] ? 'Available' : 'N/A';
                }
                $hotelRef->update($updateData);
            }

            // Update room data
            if (isset($_POST['availability']) && isset($_POST['price'])) {
                foreach ($_POST['availability'] as $roomType => $availability) {
                    $availability = (int)$availability;
                    $price = isset($_POST['price'][$roomType]) ? (float)$_POST['price'][$roomType] : 0;

                    $roomPath = "$hotelPath/Rooms/$roomType";
                    $database->getReference($roomPath)->update([
                        'Availability' => $availability,
                        'Price' => $price,
                    ]);
                }
            }

            // Handle hotel image update
            if (isset($_FILES['hotel_image']) && $_FILES['hotel_image']['error'] === UPLOAD_ERR_OK) {
                $imageFile = $_FILES['hotel_image'];
                $imageName = basename($imageFile['name']);
                $imageTempPath = $imageFile['tmp_name'];

                $bucket = $storage->getBucket();
                $storagePath = "hotel_images/{$country}/{$city}/{$hotelIndex}/{$imageName}";

                try {
                    $bucket->upload(fopen($imageTempPath, 'r'), [
                        'name' => $storagePath,
                    ]);

                    $imageUrl = "https://firebasestorage.googleapis.com/v0/b/traveltrail-39e23.appspot.com/o/" . urlencode($storagePath) . "?alt=media";
                    $hotelRef->update(['Image' => $imageUrl]);
                } catch (FirebaseException $e) {
                    echo "Error uploading image: " . $e->getMessage();
                }
            }

            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit();
        } else {
            echo "Error: Hotel does not exist.";
        }
    }

    // Update flight data 
    if ($view === 'flight' && $country && $city) {
        $flightPath = "Packages/$country/$city/Flights";
        $flightRef = $database->getReference($flightPath);

        if (isset($_POST['availability']) && isset($_POST['price'])) {
            foreach ($_POST['availability'] as $flightClass => $availability) {
                $availability = (int)$availability;
                $price = isset($_POST['price'][$flightClass]) ? (float)$_POST['price'][$flightClass] : 0;

                $flightClassPath = "$flightPath/$flightClass";
                $database->getReference($flightClassPath)->update([
                    'Seats' => $availability,
                    'Price' => $price,
                ]);
            }

            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit();
        } else {
            echo "Error: Flight data is missing or incomplete.";
        }
    }


    if (isset($_POST['delete_hotel']) && $view === 'hotel' && $country && $city && $hotelIndex !== null) {
        try {
            $database->getReference('Packages/' . $country . '/' . $city . '/Hotels/' . $hotelIndex)->remove();
            header('Location: AdminInventory.php?view=hotel&country=' . urlencode($country) . '&city=' . urlencode($city));
            exit();
        } catch (Exception $e) {
            error_log('Error deleting hotel: ' . $e->getMessage());
        }
    }    

}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Status</title>
    <link rel="stylesheet" href="css/Inventory.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Joti+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet"> 
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
    <ul>
            <li>
                <img src="images/home.webp" alt="Dashboard Icon">
                <a href="AdminDashboard.php">Dashboard</a>
            </li>
            <li>
                <img src="images/packages.png" alt="Packages Icon">
                <a href="AdminPackage.php">Travel Packages</a>
            </li>
            <li>
                <img src="images/users.webp" alt="User Icon">
                <a href="AdminUser.php">User Management</a>
            </li>
            <li class="active">
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
        <h2>Dashboard / Hotel/Flight Management</h2>
    </header>

    <div class="toggle-buttons">
        <button type="button" class="toggle-btn <?= $view === 'hotel' ? 'active' : '' ?>" onclick="showHotel()">Hotel</button>
        <button type="button" class="toggle-btn <?= $view === 'flight' ? 'active' : '' ?>" onclick="showFlight()">Flight</button>
    </div>

    <!-- Hotel Inventory Content -->
    <div id="hotel-content" style="display: <?= $view === 'hotel' ? 'block' : 'none' ?>;">
    <div class="inventory-container">
        <form method="GET">
            <input type="hidden" name="view" value="hotel">
            <div class="selection-buttons">
                <label>Package:</label>
                <select name="country" onchange="this.form.submit()">
                    <option value="">Select Country</option>
                    <?php foreach ($countries as $countryName) : ?>
                        <option value="<?= $countryName ?>" <?= $countryName === $country ? 'selected' : '' ?>>
                            <?= htmlspecialchars($countryName) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <?php if ($country) : ?>
                    <label>City:</label>
                    <select name="city" onchange="this.form.submit()">
                        <option value="">Select City</option>
                        <?php foreach ($cities as $cityName) : ?>
                            <option value="<?= $cityName ?>" <?= $cityName === $city ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cityName) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>

                <?php if ($city) : ?>
                    <label>Hotel:</label>
                    <select name="hotel" onchange="this.form.submit()">
                        <option value="">Select Hotel</option>
                        <?php foreach ($hotels as $index => $hotelName) : ?>
                            <option value="<?= $index ?>" <?= $index === $hotelIndex ? 'selected' : '' ?>>
                                <?= htmlspecialchars($hotelName) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
            </div>
        </form>

        
        <?php if (!empty($rooms) && $hotelIndex !== null) : ?>
            <h3>Room Availability for <?= htmlspecialchars($selectedHotel['Hotel']) ?></h3>
            <form method="POST" enctype="multipart/form-data" id="availabilityForm">
            <div class="hotel-description-container">
            <label for="hotel_description">Description:</label>
            <textarea name="hotel_description" class="hotel-description" rows="4"><?= htmlspecialchars($hotelDescription) ?></textarea>
        </div>
            <div class="hotel-image-container">
            <label for="hotel_image">Hotel Image:</label>
            <img src="<?= htmlspecialchars($imageUrl) ?>" alt="Hotel Image" class="hotel-image-preview">
            <input type="file" name="hotel_image" accept="image/*">
        </div>
                <table class="room-table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Rooms</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rooms as $roomType => $roomInfo) : 
                            $availability = $roomInfo['availability'];
                            $price = $roomInfo['price']; 
                        ?>
                            <tr>
                                <td><button type="button" class="room-type-btn"><?= htmlspecialchars($roomType) ?></button></td>
                                <td>
                                    <input type="number" class="availability" name="availability[<?= $roomType ?>]" 
                                           value="<?= htmlspecialchars($availability) ?>" min="0" max="10">
                                </td>
                                <td>
                                    <input type="number" class="price" name="price[<?= $roomType ?>]" 
                                           value="<?= htmlspecialchars($price) ?>" min="0">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Checkbox for Hotel Availability -->
                <label class="availability-label">Hotel Availability:</label>
                <input type="checkbox" class="check_box" name="hotel_availability" id="hotel-availability" <?= ($selectedHotel['Availability'] === 'Available') ? 'checked' : '' ?>>
                <label for="hotel-availability" class="availability_label">Available</label>

                <button type="submit" name="update_hotel_availability" class="update-btn" value="update">Update Hotel</button>
                <button type="button" name="delete_hotel" class="delete-btn" onclick="deleteHotel()">Delete Hotel</button>

            </form>
        <?php else : ?>
            <p>Please select a package, city, and hotel to view room availability.</p>
        <?php endif; ?>
    </div>
</div>



<div id="flight-content" style="display: <?= $view === 'flight' ? 'block' : 'none' ?>;">
    <div class="inventory-container">
        <form method="GET">
            <input type="hidden" name="view" value="flight">
            <div class="selection-buttons">
                <label>Package:</label>
                <select name="country" onchange="this.form.submit()">
                    <option value="">Select Country</option>
                    <?php foreach ($countries as $countryName) : ?>
                        <option value="<?= $countryName ?>" <?= $countryName === $country ? 'selected' : '' ?>>
                            <?= htmlspecialchars($countryName) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <?php if ($country) : ?>
                    <label>City:</label>
                    <select name="city" onchange="this.form.submit()">
                        <option value="">Select City</option>
                        <?php foreach ($cities as $cityName) : ?>
                            <option value="<?= $cityName ?>" <?= $cityName === $city ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cityName) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
            </div>
        </form>

        <?php if (!empty($flights)): ?>
            <h3>Flight Seat Availability</h3>
            <form method="POST">
                <table class="room-table">
                    <thead>
                        <tr>
                            <th>Class</th>
                            <th>Seats</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($flights as $class => $flightInfo): ?>
                            <tr>
                                <td><button type="button" class="room-type-btn"><?= htmlspecialchars($class) ?></td>
                                <td>
                                    <input type="number" class="availability" name="availability[<?= htmlspecialchars($class) ?>]" 
                                           value="<?= htmlspecialchars($flightInfo['seats']) ?>" min="0" max="100">
                                </td>
                                <td>
                                    <input type="number" class="price" name="price[<?= htmlspecialchars($class) ?>]" 
                                           value="<?= htmlspecialchars($flightInfo['price']) ?>" min="0" step="0.01">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit" name="update_flight_availability" class="update-btn" value="update">Update Flight</button>
            </form>
        <?php else: ?>
            <p>Please select a package and city to view flight seat availability.</p>
        <?php endif; ?>
    </div>
</div>


<script>

function showHotel() {
    document.querySelector('.toggle-btn.active').classList.remove('active');
    document.querySelector('.toggle-buttons .toggle-btn:nth-child(1)').classList.add('active');
    document.querySelector('input[name="view"]').value = 'hotel';
    document.forms[0].submit();
}

function showFlight() {
    document.querySelector('.toggle-btn.active').classList.remove('active');
    document.querySelector('.toggle-buttons .toggle-btn:nth-child(2)').classList.add('active');
    document.querySelector('input[name="view"]').value = 'flight';
    document.forms[0].submit();
}

function deleteHotel() {
    if (confirm("Are you sure you want to delete this hotel?")) {
        const country = "<?php echo htmlspecialchars($country); ?>";
        const city = "<?php echo htmlspecialchars($city); ?>";
        const hotelIndex = "<?php echo htmlspecialchars($hotelIndex); ?>";

        $.ajax({
            url: 'php_functions/delete_hotel.php',  
            type: 'POST',
            data: {
                country: country,
                city: city,
                hotelIndex: hotelIndex
            },
            success: function(response) {
                const result = JSON.parse(response);
                if (result.success) {
                    alert("Hotel deleted successfully!");
                    location.reload();
                } else {
                    alert("Error: " + result.message);
                }
            },
            error: function(xhr, status, error) {
                alert("Failed to delete hotel.");
                console.error("Error:", error);
            }
        });
    }
}


function updateBookedRooms(input, roomType) {
    const availability = parseInt(input.value);

    if (availability < 0) {
        alert("Availability cannot be negative.");
        input.value = 0;
        return;
    }

    const maxRooms = 10;
    const bookedSpan = document.getElementById(roomType + '-booked');
    const bookedRooms = maxRooms - availability;

    bookedSpan.textContent = Math.max(0, bookedRooms);

    checkHotelAvailability();
}

function checkHotelAvailability() {
    const availabilityInputs = document.querySelectorAll('input[name^="availability"]');
    let anyRoomAvailable = false;

    availabilityInputs.forEach(input => {
        const availableRooms = parseInt(input.value);
        if (availableRooms > 0) {
            anyRoomAvailable = true;
        }
    });

    const hotelAvailabilityCheckbox = document.getElementById('hotel-availability');
    hotelAvailabilityCheckbox.checked = anyRoomAvailable;
}

$(document).on('change', 'input[name="hotel_image"]', function () {
    const formData = new FormData($('#availabilityForm')[0]);
    $.ajax({
        url: 'AdminInventory.php', 
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            const result = JSON.parse(response);
            if (result.success) {
                alert('Hotel image updated successfully!');
                location.reload(); 
            } else {
                alert('Error updating hotel image: ' + result.message);
            }
        },
        error: function (xhr, status, error) {
            alert('Failed to update hotel image.');
            console.error('Error:', error);
        }
    });
});

</script>

</body>
</html>
