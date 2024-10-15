<?php
include 'firebase_connection.php';

$country = isset($_GET['country']) ? $_GET['country'] : '';
$city = isset($_GET['city']) ? $_GET['city'] : '';
$hotel = isset($_GET['hotel']) ? $_GET['hotel'] : '';

$countryRef = $database->getReference('Packages');
$countries = $countryRef->getValue() ?: [];

$cityRef = $database->getReference('Packages/' . $country);
$cities = $cityRef->getValue() ?: [];

$hotelRef = $database->getReference('Packages/' . $country . '/' . $city . '/Hotels');
$hotels = $hotelRef->getValue() ?: [];

$roomRef = $database->getReference('Packages/' . $country . '/' . $city . '/Hotels/' . $hotel . '/Rooms');
$rooms = $roomRef->getValue() ?: [];
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

        <div class="inventory-container">
            <form method="GET">
                <div class="inventory-header">
                    <div class="toggle-buttons">
                        <button type="button" class="active">Hotel</button>
                        <button type="button">Flight</button>
                    </div>
                    <div class="dropdowns">
                        <label for="country">Package:</label>
                        <select name="country" id="country" onchange="this.form.submit()">
                            <option value="">Select Country</option>
                            <?php foreach ($countries as $countryName => $countryData) : ?>
                                <option value="<?= $countryName ?>" <?= $countryName === $country ? 'selected' : '' ?>>
                                    <?= $countryName ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <label for="city">City:</label>
                        <select name="city" id="city" onchange="this.form.submit()">
                            <option value="">Select City</option>
                            <?php foreach ($cities as $cityName => $cityData) : ?>
                                <option value="<?= $cityName ?>" <?= $cityName === $city ? 'selected' : '' ?>>
                                    <?= $cityName ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <label for="hotel">Hotel:</label>
                        <select name="hotel" id="hotel" onchange="this.form.submit()">
                            <option value="">Select Hotel</option>
                            <?php foreach ($hotels as $hotelName => $hotelData) : ?>
                                <option value="<?= $hotelName ?>" <?= $hotelName === $hotel ? 'selected' : '' ?>>
                                    <?= $hotelName ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </form>

            <?php if (!empty($rooms)) : ?>
                <h3>Room Availability for <?= htmlspecialchars($hotel) ?></h3>
                <table>
                    <thead>
                        <tr>
                            <th>Rooms</th>
                            <th>Available</th>
                            <th>Booked</th>
                            <th>Not Available</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rooms as $roomType => $roomData) : 
                            $totalRooms = isset($roomData['Total']) ? (int)$roomData['Total'] : 0;
                            $bookedRooms = isset($roomData['Booked']) ? (int)$roomData['Booked'] : 0;
                            $availableRooms = max($totalRooms - $bookedRooms, 0);
                            $isNotAvailable = $availableRooms === 0;
                        ?>
                            <tr>
                                <td class="room-type"><?= htmlspecialchars($roomType) ?></td>
                                <td class="availability"><?= htmlspecialchars($availableRooms) ?></td>
                                <td class="booked"><?= htmlspecialchars($bookedRooms) ?></td>
                                <td>
                                    <input type="checkbox" <?= $isNotAvailable ? 'checked' : '' ?> disabled>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>Please select a package, city, and hotel to view room availability.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>