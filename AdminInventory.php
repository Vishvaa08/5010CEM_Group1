<?php
include 'firebase_connection.php';

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

$rooms = [];
if ($country && $city && $hotelIndex !== null && isset($hotelData[$hotelIndex])) {
    $selectedHotel = $hotelData[$hotelIndex];
    $roomData = $selectedHotel['Rooms'] ?? [];
    foreach ($roomData as $roomType => $roomDetails) {
        $rooms[$roomType] = [
            'availability' => isset($roomDetails['Availability']) ? (int)$roomDetails['Availability'] : 0,
            'visible' => isset($roomDetails['visible']) ? (bool)$roomDetails['visible'] : true
        ];
    }
}

$maxRooms = 30; 
$maxSeats = 20; 

$flights = [];
if ($country && $city) {
    $flightRef = $database->getReference('Packages/' . $country . '/' . $city . '/Flights');
    $flightData = $flightRef->getValue() ?: [];
    foreach ($flightData as $class => $details) {
        $flights[$class] = [
            'seats' => isset($details['Seats']) ? (int)$details['Seats'] : 0,
            'visible' => isset($details['visible']) ? (bool)$details['visible'] : true
        ];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_availability'])) {
        if ($view === 'hotel' && $country && $city && $hotelIndex !== null) {
            foreach ($_POST['not_available'] as $roomType => $value) {
                $visible = $value !== 'on';
                $database->getReference('Packages/' . $country . '/' . $city . '/Hotels/' . $hotelIndex . '/Rooms/' . $roomType)
                         ->update(['Availability' => $visible ? $rooms[$roomType]['availability'] : 0, 'visible' => $visible]);
            }
        } elseif ($view === 'flight' && $country && $city) {
            foreach ($_POST['not_available'] as $class => $value) {
                $visible = $value !== 'on';
                $database->getReference('Packages/' . $country . '/' . $city . '/Flights/' . $class)
                         ->update(['Seats' => $visible ? $flights[$class]['seats'] : 0, 'visible' => $visible]);
            }
        }
    } elseif (isset($_POST['delete_hotel']) && $view === 'hotel' && $country && $city && $hotelIndex !== null) {
        $database->getReference('Packages/' . $country . '/' . $city . '/Hotels/' . $hotelIndex)->remove();
        header("Location: AdminInventory.php?view=hotel&country=$country&city=$city");
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
</head>
<body>
<div class="sidebar">
    <div class="sidebar-header">
        <h2>TravelTrail</h2>
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
        <h2>Dashboard / Inventory Status</h2>
        <div class="user-info">
            <span>Hi, Admin</span>
        </div>
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
                                <th>Not Available</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rooms as $roomType => $roomInfo) : 
                                $availability = $roomInfo['availability'];
                                $bookedRooms = $maxRooms - $availability;
                                $isNotAvailable = !$roomInfo['visible'];
                            ?>
                                <tr>
                                    <td><button type="button" class="room-type-btn"><?= htmlspecialchars($roomType) ?></button></td>
                                    <td><span class="availability"><?= htmlspecialchars($availability) ?></span></td>
                                    <td><span class="booked"><?= htmlspecialchars($bookedRooms) ?></span></td>
                                    <td>
                                        <input type="checkbox" name="not_available[<?= $roomType ?>]" <?= $isNotAvailable ? 'checked' : '' ?>>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <button type="submit" name="update_availability" class="update-btn">Update Availability</button>
                    <button type="button" name="delete_hotel" class="delete-btn" onclick="deleteHotel()">Delete Hotel</button>
                </form>
            <?php else : ?>
                <p>Please select a package, city, and hotel to view room availability.</p>
            <?php endif; ?>
        </div>
    </div>

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
                                <th>Not Available</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($flights as $class => $flightInfo) : 
                                $seats = $flightInfo['seats'];
                                $bookedSeats = $maxSeats - $seats;
                                $isNotAvailable = !$flightInfo['visible'];
                            ?>
                                <tr>
                                    <td><button type="button" class="room-type-btn"><?= htmlspecialchars($class) ?></button></td>
                                    <td><span class="availability"><?= htmlspecialchars($seats) ?></span></td>
                                    <td><span class="booked"><?= htmlspecialchars($bookedSeats) ?></span></td>
                                    <td>
                                        <input type="checkbox" name="not_available[<?= $class ?>]" <?= $isNotAvailable ? 'checked' : '' ?>>
                                    </td>
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
        document.querySelector('input[name="delete_hotel"]').disabled = false;
        document.getElementById('availabilityForm').submit();
        setTimeout(() => {
            location.reload();
        }, 500); 
    }
}
</script>

</body>
</html>
