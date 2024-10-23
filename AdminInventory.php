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

$country = isset($_GET['country']) ? $_GET['country'] : '';
$city = isset($_GET['city']) ? $_GET['city'] : '';
$hotelIndex = isset($_GET['hotel']) ? (int)$_GET['hotel'] : null;
$view = isset($_GET['view']) ? $_GET['view'] : 'hotel'; 

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
if ($country && $city) {
    $hotelRef = $database->getReference('Packages/' . $country . '/' . $city . '/Hotels');
    $hotelData = $hotelRef->getValue() ?: [];
    foreach ($hotelData as $index => $hotelDetail) {
        if (is_array($hotelDetail) && isset($hotelDetail['Hotel'])) {
            $hotels[$index] = $hotelDetail['Hotel'];
        }
    }
}

$bookingsRef = $database->getReference('Admin/newBookings');
$bookingsData = $bookingsRef->getValue() ?: [];

$maxRoomsPerType = 10;

$rooms = [];
if ($country && $city && $hotelIndex !== null && isset($hotelData[$hotelIndex])) {
    $selectedHotel = $hotelData[$hotelIndex];
    $roomData = $selectedHotel['Rooms'] ?? [];
    
    foreach ($roomData as $roomType => $roomDetails) {
        $availableRooms = $maxRoomsPerType; 
        $bookedRooms = 0; 

        foreach ($bookingsData as $bookingId => $bookingDetails) {
            if ($bookingDetails['country'] === $country && 
                $bookingDetails['city'] === $city && 
                $bookingDetails['hotelID'] == $hotelIndex && 
                $bookingDetails['roomType'] === $roomType) {
                $bookedRooms++;
            }
        }

        $availableRooms = max(0, $maxRoomsPerType - $bookedRooms);

        $rooms[$roomType] = [
            'availability' => $availableRooms,
            'booked' => $bookedRooms
        ];
    }
}

$maxSeats = 20; 

$flights = [];
if ($country && $city) {
    $flightRef = $database->getReference('Packages/' . $country . '/' . $city . '/Flights');
    $flightData = $flightRef->getValue() ?: [];
    foreach ($flightData as $class => $details) {
        $flights[$class] = [
            'seats' => isset($details['Seats']) ? (int)$details['Seats'] : 0,
        ];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_availability'])) {

        // Update hotel room availability 
        if ($view === 'hotel' && $country && $city && $hotelIndex !== null) {
            foreach ($_POST['availability'] as $roomType => $availability) {
                $availability = (int)$availability;
                $bookedRooms = 10 - $availability;

                $database->getReference('Packages/' . $country . '/' . $city . '/Hotels/' . $hotelIndex . '/Rooms/' . $roomType)
                         ->update(['Availability' => $availability]);

                $database->getReference('Packages/' . $country . '/' . $city . '/Hotels/' . $hotelIndex . '/Rooms/' . $roomType)
                         ->update(['Booked' => $bookedRooms]);
            }

            $hotelAvailability = isset($_POST['hotel_availability']) ? 'Available' : 'N/A';
            $database->getReference('Packages/' . $country . '/' . $city . '/Hotels/' . $hotelIndex)
                     ->update(['Availability' => $hotelAvailability]);

            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit();
        }

        // Update flight availability
        if ($view === 'flight' && $country && $city) {
            foreach ($_POST['availability'] as $flightClass => $availability) {
                $availability = (int)$availability;
                $bookedSeats = 20 - $availability;
                $database->getReference('Packages/' . $country . '/' . $city . '/Flights/' . $flightClass)
                         ->update(['Seats' => $availability]);

                $database->getReference('Packages/' . $country . '/' . $city . '/Flights/' . $flightClass)
                         ->update(['Booked' => $bookedSeats]);
            }
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit();
        }
    }

    // Hotel deletion
    if (isset($_POST['delete_hotel']) && $view === 'hotel' && $country && $city && $hotelIndex !== null) {
        $database->getReference('Packages/' . $country . '/' . $city . '/Hotels/' . $hotelIndex)->remove();
        header('Location: AdminInventory.php?view=hotel&country=' . urlencode($country) . '&city=' . urlencode($city));
        exit();
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
    <link href="https://fonts.googleapis.com/css2?family=Itim&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            <li class="active">
                <img src="images/inventory dark.jpg" alt="Inventory Icon">
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

            <?php if (!empty($rooms)) : ?>
    <h3>Room Availability for <?= htmlspecialchars($selectedHotel['Hotel']) ?></h3>
    <form method="POST" id="availabilityForm">
        <table class="room-table">
            <thead>
                <tr>
                    <th>Rooms</th>
                    <th>Available</th>
                    <th>Booked</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rooms as $roomType => $roomInfo) : 
                    $availability = $roomInfo['availability'];
                    $bookedRooms = $roomInfo['booked']; 
                ?>
                    <tr>
                        <td><button type="button" class="room-type-btn"><?= htmlspecialchars($roomType) ?></button></td>
                        <td>
                            <input type="number" class="availability" name="availability[<?= $roomType ?>]" 
                                   value="<?= htmlspecialchars($availability) ?>" min="0" max="10"
                                   oninput="updateBookedRooms(this, '<?= $roomType ?>')">
                        </td>
                        <td><span class="booked" id="<?= $roomType ?>-booked"><?= htmlspecialchars($bookedRooms) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Checkbox for Hotel Availability -->
        <label>Hotel Availability:</label>
        <input type="checkbox" name="hotel_availability" id="hotel-availability" <?= ($selectedHotel['Availability'] === 'Available') ? 'checked' : '' ?>>
        <label for="hotel-availability">Available</label>

        <button type="submit" name="update_availability" class="update-btn">Update</button>
        <button type="button" name="delete_hotel" class="delete-btn" onclick="deleteHotel()">Delete Hotel</button>
        </form>
        <?php else : ?>
            <p>Please select a package, city, and hotel to view room availability.</p>
        <?php endif; ?>


    <!-- Flight Inventory Content -->
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

            <?php if (!empty($flights)) : ?>
                <h3>Flight Seat Availability</h3>
                <form method="POST">
                <table class="room-table">
                    <thead>
                        <tr>
                            <th>Class</th>
                            <th>Available</th>
                            <th>Booked</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($flights as $class => $flightInfo) : 
                            $seats = $flightInfo['seats'];
                            $bookedSeats = $maxSeats - $seats; 
                        ?>
                            <tr>
                                <td><button type="button" class="room-type-btn"><?= htmlspecialchars($class) ?></button></td>
                                <td>
                                    <input type="number" class="availability" name="availability[<?= $class ?>]" 
                                        value="<?= htmlspecialchars($seats) ?>" min="0" max="<?= $maxSeats ?>"
                                        oninput="updateBookedSeats(this, '<?= $class ?>')">
                                </td>
                                <td><span class="booked" id="<?= $class ?>-booked"><?= htmlspecialchars($bookedSeats) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit" name="update_availability" class="update-btn">Update Seat Availability</button>
            </form>
            <?php else : ?>
                <p>Please select a package and city to view flight seat availability.</p>
            <?php endif; ?>
        </div>
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
        const deleteButton = document.createElement('input');
        deleteButton.type = 'hidden';
        deleteButton.name = 'delete_hotel';
        deleteButton.value = 'true';
        document.getElementById('availabilityForm').appendChild(deleteButton);
        document.getElementById('availabilityForm').submit();
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


function updateBookedSeats(input, flightClass) {
    const availability = parseInt(input.value);
    if (availability < 0) {
        alert("Seat availability cannot be negative.");
        input.value = 0;
        return;
    }
    const bookedSpan = document.getElementById(flightClass + '-booked');
    const bookedSeats = 20 - availability;
    bookedSpan.textContent = Math.max(0, bookedSeats); 

    checkFlightAvailability();
}

function checkFlightAvailability() {
    const availabilityInputs = document.querySelectorAll('input[name^="availability"]');
    let allAvailable = true; 
    availabilityInputs.forEach(input => {
        const availableSeats = parseInt(input.value);
        if (availableSeats <= 0) {
            allAvailable = false; 
        }
    });

}

</script>

</body>
</html>
