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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>City Packages</title>
    <link rel="stylesheet" href="css/Package.css">
    <link href="https://fonts.googleapis.com/css2?family=Itim&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Joti+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet"> 
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
        <div class="header-left1">
            <h2>Dashboard / Travel Package</h2>
            <p>City Package</p>
        </div>
        <div id="addCityForm" class="card">
        <div class="card-body">
        <h5 class="card-title">Add New City</h5>
        <form id="cityForm" enctype="multipart/form-data">
            <div class="form-group d-flex align-items-center" style="margin-bottom: 15px;">
                <label for="cityName" style="margin-right: 10px;">City Name</label>
                <input type="text" class="form-control" id="cityName" placeholder="Enter city name" required style="flex: 1;">
            </div>
            <div class="form-group d-flex align-items-center" style="margin-bottom: 15px;">
                <label for="cityImage" style="margin-right: 10px;">City Image</label>
                <input type="file" class="form-control-file" id="cityImage" accept="image/*" required style="flex: 1;">
            </div>
            <button type="submit" class="btn btn-success">Submit City</button>
        </form>
    </div>
</div>
</header>

    <script>
            function toggleCityForm() {
                var form = document.getElementById('addCityForm');
                if (form.style.display === 'none' || form.style.display === '') {
                    form.style.display = 'block';
                } else {
                    form.style.display = 'none';
                }
            }
    </script>

    <div class="draggable-container">
        <div class="draggable" data-type="hotel">Add Hotel</div>
        <div class="draggable" data-type="flight">Add Flight</div>
        <div class="draggable" data-type="vehicle">Add Vehicle</div>
        <div class="draggable" data-type="itinerary">Add Itinerary</div>
    </div>

    <div class="city-grid">
        <?php
        include 'firebase_connection.php';
        include 'firebase_data.php';

        $country = isset($_GET['country']) ? $_GET['country'] : '';
        $reference = $database->getReference('Packages/' . $country);
        $snapshot = $reference->getSnapshot();
        $dataCities = $snapshot->getValue();

        if ($dataCities) {
            foreach ($dataCities as $city => $cityDetails) {
                if (!empty($city) && !empty($cityDetails['City'])) {
                    echo '<div class="city-card" data-country="' . htmlspecialchars($country) . '" data-city="' . htmlspecialchars($city) . '">';
                    echo '<h4 contenteditable="true" data-key="City">' . htmlspecialchars($cityDetails['City']) . '</h4>';
                    echo '<textarea data-key="CityDetail" class="fixed-textarea" placeholder="Enter City Description">' . htmlspecialchars($cityDetails['CityDetail'] ?? '') . '</textarea>';
                    $cityImage = htmlspecialchars($cityDetails['CityImage'] ?? 'https://example.com/path/to/default_image.jpg');
                    echo '<img src="' . $cityImage . '" alt="' . htmlspecialchars($cityDetails['City']) . '" style="width:100%;">';
                    echo '<input type="file" accept="image/*" class="image-upload" id="cityImage" name="cityImage">';
                    

                    // Availability checkbox
                    $availability = isset($cityDetails['Availability']) ? $cityDetails['Availability'] : 'N/A';
                    echo '<div style="display: flex; align-items: left; margin-top: -18px;">'; 
                    echo '<div class="form-check" style="margin-left: 330px;">'; 
                        echo '<input type="checkbox" class="form-check-input availability-checkbox" data-city="' . htmlspecialchars($city) . 
                        '" data-country="' . htmlspecialchars($country) . '" ' . ($availability == 'Available' ? 'checked' : '') . '>';
                        echo '<label class="form-check-label">Available</label>';
                    echo '</div>';
                    echo '</div>';
                
                    // Display hotels
                    if (isset($cityDetails['Hotels']) && is_array($cityDetails['Hotels'])) {
                        echo '<h5>Hotels:</h5>';
                        echo '<select class="hotel-dropdown mb-3"><option value="">Select a Hotel</option>';
                        foreach ($cityDetails['Hotels'] as $hotelIndex => $hotel) {
                            if (!empty($hotel)) {
                                echo '<option value="' . $hotelIndex . '">' . htmlspecialchars($hotel['Hotel'] ?? 'N/A') . '</option>';
                            }
                        }
                        echo '</select>';
                        echo '<div class="hotel-details"></div>';
                    }

                    // Display flight details
                    if (isset($cityDetails['Flights'])) {
                        echo '<h5>Flight Details:</h5>';
                        foreach ($cityDetails['Flights'] as $class => $details) {
                            echo '<div class="flight-item">';
                            echo '<span contenteditable="true" data-key="Class">' . htmlspecialchars($class) . '</span>';
                            echo '<input type="number" data-key="Price" value="' . htmlspecialchars($details['Price'] ?? '') . '" placeholder="Flight Price">';
                            echo '<input type="number" data-key="TotalSeats" value="' . htmlspecialchars($details['Seats'] ?? '') . '" placeholder="Total Seats" style="width: 100%;">';
                            echo '</div>';
                        }
                    }

                    // Data for vehicles
                    $reference = $database->getReference('Packages/' . $country . '/' . $city . '/Vehicle');
                    $snapshot = $reference->getSnapshot();
                    $dataVehicle = $snapshot->getValue();

                    if (isset($dataVehicle) && !empty($dataVehicle)) {
                        echo '<h5>Vehicles:</h5>';
                        foreach ($dataVehicle as $vehicleKey => $vehicleDetails) {
                            $vehicleType = htmlspecialchars($vehicleDetails['Type'] ?? 'N/A');
                            $vehiclePrice = htmlspecialchars($vehicleDetails['Price'] ?? 0);
                            echo '<div class="vehicle-item">';
                            echo '<strong>' . $vehicleType . '</strong>';
                            echo '<input type="number" data-key="VehiclePrice" value="' . $vehiclePrice . '" placeholder="Vehicle Price">';
                            echo '</div>';
                        }
                    }

                    $reference = $database->getReference('Packages/' . $country . '/' . $city . '/Itinerary');
                    $snapshot = $reference->getSnapshot();
                    $dataCityItinerary = $snapshot->getValue();

                    if (isset($dataCityItinerary) && is_array($dataCityItinerary)) {
                        echo '<h5>Itineraries:</h5>';
                        echo '<select class="itinerary-dropdown mb-3"><option value="">Select an Itinerary</option>';
                        foreach ($dataCityItinerary as $index => $itinerary) {
                            if (!empty($itinerary) && !empty($itinerary['Itinerary'])) {
                                echo '<option value="' . $index . '">' . htmlspecialchars($itinerary['Itinerary']) . '</option>';
                            }
                        }
                        echo '</select>';
                        echo '<div class="itinerary-details"></div>';
                    }
                    
                    echo '<div class="action-buttons">';
                    echo '<button class="edit-btn">Save</button>';
                    echo '<button class="image-btn">Update Image</button>';
                    echo '<button class="delete1-btn">Delete</button>';
                    echo '</div>';
                    echo '</div>'; 
                }
            }
        } else {
            echo '<p>No details found for this country.</p>';
        }
        ?>
    </div> 
</div>

<script>
    $(document).ready(function() {
    $(".draggable").draggable({ helper: "clone" });

    $(".city-card").droppable({
        accept: ".draggable",
        hoverClass: "highlight",
        drop: function(event, ui) {
            const type = $(ui.draggable).data("type");
            const cityCard = $(this);
            let isItemAdded = cityCard.data(`${type}Added`); 

            if (!isItemAdded) { 
                if (type === "hotel") {
                    const hotelHtml = `
                    <div class="hotel-item">
                        <input type="text" data-key="Hotel" placeholder="Enter Hotel Name" required>
                        <input type="file" name="hotelImage[]" class="hotel-image-upload" accept="image/*">
                        <div class="image-container">
                            <img src="" class="thumbnail hotel-thumbnail" alt="Hotel Image" style="display: none;">
                        </div>
                        <div class="room-item">
                            <span>Single</span>
                            <input type="number" data-key="SingleRooms" placeholder="Rooms" style="width: 40%;" min="0" max="10" value="10">
                            <input type="number" data-key="SinglePrice" placeholder="Price" style="width: 40%;">
                        </div>
                        <div class="room-item">
                            <span>Double</span>
                            <input type="number" data-key="DoubleRooms" placeholder="Rooms" style="width: 40%;" min="0" max="10" value="10">
                            <input type="number" data-key="DoublePrice" placeholder="Price" style="width: 40%;">
                        </div>
                        <div class="room-item">
                            <span>Suite</span>
                            <input type="number" data-key="SuiteRooms" placeholder="Rooms" style="width: 40%;" min="0" max="10" value="10">
                            <input type="number" data-key="SuitePrice" placeholder="Price" style="width: 40%;">
                        </div>
                        <textarea data-key="Description" placeholder="Enter Hotel Description"></textarea>
                    </div>`;
                    cityCard.append(hotelHtml);
                    cityCard.data(`${type}Added`, true);
                } else if (type === "flight") {
                    const flightHtml = `
                        <div class="flight-item">
                            <span contenteditable="true" data-key="Class">Flight Class</span>
                            <input type="number" data-key="Price" value="" placeholder="Flight Price">
                            <input type="number" data-key="TotalSeats" value="20" placeholder="Total Seats" style="width: 100%;" min="0">
                        </div>`;
                    cityCard.append(flightHtml);
                    cityCard.data(`${type}Added`, true);
                } else if (type === "vehicle") {
                    const vehicleHtml = `
                        <div class="vehicle-item">
                            <select data-key="VehicleType" class="vehicle-type">
                                <option value="TypeA">4-Seater</option>
                                <option value="TypeB">7-Seater</option>
                                <option value="TypeC">Van</option>
                            </select>
                            <input type="number" data-key="VehiclePrice" value="" placeholder="Vehicle Price">
                        </div>`;
                    cityCard.append(vehicleHtml);
                    cityCard.data(`${type}Added`, true);
                } else if (type === "itinerary") {
                    const itineraryHtml = `
                    <div class="itinerary-item d-flex align-items-center">
                        <div class="itinerary-details-container">
                            <strong contenteditable="true" data-key="Itinerary">New Itinerary</strong>
                            <input type="number" data-key="ItineraryPrice" placeholder="Itinerary Price">
                        </div>
                        <div class="image-container ml-3">
                            <input type="file" name="itineraryImage[]" accept="image/*" class="itinerary-image-upload" onchange="previewItineraryImage(this)">
                            <img src="" class="thumbnail itinerary-thumbnail mt-2" alt="Itinerary Image" style="display: none; width: 100px; height: 100px; object-fit: cover;">
                        </div>
                    </div>`;
                    cityCard.append(itineraryHtml);
                    cityCard.data(`${type}Added`, true);
                }
            } else {
                alert(`You can only add one ${type} at a time.`);
            }
        }
    });
});


        // Hotel dropdown  
        $('.hotel-dropdown').on('change', function() {
            const hotelIndex = $(this).val(); 
            const cityCard = $(this).closest('.city-card');
            const cityDetails = <?php echo json_encode($dataCities); ?>; 
            const city = cityCard.data('city'); 
            cityCard.find('.hotel-details').html(''); 

            if (hotelIndex && cityDetails[city]['Hotels'][hotelIndex]) {
                const hotel = cityDetails[city]['Hotels'][hotelIndex];

                const hotelHtml = `
                    <div class="hotel-item">
                        <div class="hotel-image-container">
                            <img src="${hotel.Image || 'https://example.com/path/to/default_image.jpg'}" class="hotel-image" alt="${hotel.Hotel}">
                        </div>
                        <div class="hotel-details-container">
                            <div class="room-detail">
                                <label>Double:</label>
                                <input type="number" class="form-control" value="${hotel.Rooms.Double.Availability || 0}" >
                            </div>
                            <div class="room-detail">
                                <label>Price:</label>
                                <input type="number" class="form-control" value="${hotel.Rooms.Double.Price || 0}">
                            </div>
                            <div class="room-detail">
                                <label>Single:</label>
                                <input type="number" class="form-control" value="${hotel.Rooms.Single.Availability || 0}" >
                            </div>
                            <div class="room-detail">
                                <label>Price:</label>
                                <input type="number" class="form-control" value="${hotel.Rooms.Single.Price || 0}" >
                            </div>
                            <div class="room-detail">
                                <label>Suite:</label>
                                <input type="number" class="form-control" value="${hotel.Rooms.Suite.Availability || 0}" >
                            </div>
                            <div class="room-detail">
                                <label>Price:</label>
                                <input type="number" class="form-control" value="${hotel.Rooms.Suite.Price || 0}">
                            </div>
                        </div>
                        <div class="description-container">
                            <label>Hotel Description:</label>
                            <textarea class="description1-textarea" readonly>${hotel.Description || ''}</textarea>
                        </div>
                    </div>`;
                cityCard.find('.hotel-details').html(hotelHtml); 
            }
        });

        // Itinerary dropdown  
        $('.itinerary-dropdown').on('change', function() {
            const itineraryIndex = $(this).val(); 
            const cityCard = $(this).closest('.city-card');
            const cityDetails = <?php echo json_encode($dataCities); ?>; 
            const city = cityCard.data('city'); 

            cityCard.find('.itinerary-details').html(''); 

            if (itineraryIndex && cityDetails[city]['Itinerary'][itineraryIndex]) {
                const itinerary = cityDetails[city]['Itinerary'][itineraryIndex];
                const itineraryHtml = `
            <div class="itinerary-item d-flex" data-index="${itineraryIndex}"> 
            <div class="itinerary-image-container">
                <img src="${itinerary.Image || 'https://example.com/path/to/default_image.jpg'}" class="itinerary-image" alt="${itinerary.Itinerary}">
            </div>
            <div class="itinerary-details-container">
            <div class="mb-3">
                <label>Itinerary:</label>
                <input type="text" class="form-control" value="${itinerary.Itinerary}" readonly>
            </div>
            <div class="mb-3">
                <label>Price:</label>
                <input type="number" class="form-control" value="${itinerary.Price}" readonly>
            </div>
                <i class="fas fa-times delete-itinerary-btn" style="cursor: pointer; color: red;"></i> <!-- Cross icon for delete -->
            </div>
            </div>`;

            cityCard.find('.itinerary-details').append(itineraryHtml); 

            }
        });

        function toggleCityForm() {
            var form = document.getElementById('addCityForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }

        $('#addCityForm').submit(function(e) {
            e.preventDefault();
            
            const country = '<?php echo htmlspecialchars($_GET["country"]); ?>'; 
            const cityName = $('#cityName').val().trim();
            const cityImageFile = $('#cityImage')[0].files[0];

            if (!cityName || !cityImageFile) {
                alert('Please fill all fields and select an image.');
                return;
            }

            const formData = new FormData();
            formData.append('country', country);
            formData.append('cityName', cityName);
            formData.append('cityImage', cityImageFile);

            $.ajax({
                url: 'php_functions/add_new_city.php', 
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    alert('City added successfully!');
                    location.reload(); 
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    alert('Error adding city: ' + error);
                }
            });
        });


    $(document).on('click', '.image-btn', function() {
        const card = $(this).closest('.city-card'); 
        const country = card.data('country'); 
        const city = card.data('city'); 
        const newImageFile = card.find('#cityImage')[0].files[0]; 

        if (!newImageFile) {
        alert('Please select a new image to upload.');
        return;
        }

        const imageType = prompt("Please enter the image type (City, Banner, or Banner2):").trim();

        if (!['City', 'Banner', 'Banner2'].includes(imageType)) {
        alert('Invalid image type. Please enter City, Banner, or Banner2.');
        return;
        }

        const formData = new FormData();
        formData.append('country', country);
        formData.append('city', city);
        formData.append('imageType', imageType); 
        formData.append('imageFile', newImageFile); 

        $.ajax({
        url: 'php_functions/upload_image.php', 
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            const result = JSON.parse(response);
            if (result.success) {
                alert(imageType + ' image updated successfully!');
                setTimeout(function() {
                    location.reload(); 
                }, 1000); 
            } else {
                alert('Error updating image: ' + result.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            alert('Failed to update image.');
        }
        });
        });



$(document).on('click', '.edit-btn', function() {
    const card = $(this).closest('.city-card'); 
    const country = card.data('country'); 
    const city = card.find('[data-key="City"]').text().trim(); 
    const cityDetail = card.find('[data-key="CityDetail"]').val().trim(); 
    const cityAvailable = card.find('input[type="checkbox"]').is(':checked') ? 'Available' : 'N/A';
    
    const hotels = {};
    const itineraries = [];
    const flights = {};
    const vehicles = {};
    const uploadPromises = []; 

    let hotelCounter = 1; 
       const MAX_SEATS = 20; 
       const MAX_ROOMS_PER_TYPE = 10;

       card.find('.hotel-item').each(function() {
        const hotelName = $(this).find('[data-key="Hotel"]').val().trim(); 
        const hotelDescription = $(this).find('[data-key="Description"]').val() || ''; 
        const singleRooms = parseInt($(this).find('[data-key="SingleRooms"]').val()) || 0;
        const singlePrice = parseFloat($(this).find('[data-key="SinglePrice"]').val()) || 0;
        const doubleRooms = parseInt($(this).find('[data-key="DoubleRooms"]').val()) || 0;
        const doublePrice = parseFloat($(this).find('[data-key="DoublePrice"]').val()) || 0;
        const suiteRooms = parseInt($(this).find('[data-key="SuiteRooms"]').val()) || 0;
        const suitePrice = parseFloat($(this).find('[data-key="SuitePrice"]').val()) || 0;
        const hotelImageFile = $(this).find('.hotel-image-upload')[0].files[0];

        const singleRoomsBooked = parseInt($(this).find('[data-key="SingleRooms"]').val()) || 0;
        const doubleRoomsBooked = parseInt($(this).find('[data-key="DoubleRooms"]').val()) || 0;
        const suiteRoomsBooked = parseInt($(this).find('[data-key="SuiteRooms"]').val()) || 0;

        const singleRoomsAvailable = MAX_ROOMS_PER_TYPE - singleRoomsBooked;
        const doubleRoomsAvailable = MAX_ROOMS_PER_TYPE - doubleRoomsBooked;
        const suiteRoomsAvailable = MAX_ROOMS_PER_TYPE - suiteRoomsBooked;

        if (hotelName) {
            let hotelId = hotelCounter; 
            
            const roomPrices = [singlePrice, doublePrice, suitePrice];
            const cheapestRoomPrice = Math.min(...roomPrices.filter(price => price > 0)); 
            
            hotels[hotelId] = {
                Hotel: hotelName,
                Description: hotelDescription,
                CheapestRoom: cheapestRoomPrice, 
                Rooms: {
                    Single: { Availability: singleRooms, Price: singlePrice, Booked: 0 },
                    Double: { Availability: doubleRooms, Price: doublePrice, Booked: 0 },
                    Suite: { Availability: suiteRooms, Price: suitePrice, Booked: 0 }
                },
                Availability: "N/A"
            };

            
            ['Single', 'Double', 'Suite'].forEach(roomType => {
                const bookedRooms = hotels[hotelCounter].Rooms[roomType].Booked;
                const availableRooms = hotels[hotelCounter].Rooms[roomType].Availability;

                if (bookedRooms > availableRooms) {
                    console.log(`Not enough ${roomType} rooms available`);
                } else {
                    console.log(`${availableRooms} ${roomType} rooms remaining`);
                }
            });


            if (hotelImageFile) {
                const imageFormData = new FormData();
                imageFormData.append('hotelImage', hotelImageFile);
                imageFormData.append('hotelId', hotelId); 
                imageFormData.append('country', country);
                imageFormData.append('city', city);
                imageFormData.append('hotelName', hotelName);
                imageFormData.append('hotelDescription', hotelDescription);
                imageFormData.append('rooms', JSON.stringify(hotels[hotelId].Rooms));

                const uploadPromise = $.ajax({
                    url: 'php_functions/upload_hotel_image.php',
                    type: 'POST',
                    data: imageFormData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        const result = JSON.parse(response);
                        if (result.success) {
                            hotels[hotelId].Image = result.imageUrl;
                        } else {
                            alert('Failed to upload hotel image');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Image upload error:', error);
                    }
                });

                uploadPromises.push(uploadPromise); 
            }

            hotelCounter++; 
        }
    });

    // Collect flight data
    card.find('.flight-item').each(function() {
        const flightClass = $(this).find('[data-key="Class"]').text().trim();
        const flightPrice = parseFloat($(this).find('[data-key="Price"]').val()) || 0;
        const flightSeats = parseInt($(this).find('[data-key="TotalSeats"]').val()) || 0;

        if (flightSeats > MAX_SEATS) {
            alert(flightClass + ' seats cannot exceed ' + MAX_SEATS);
            return; 
        }

        if (flightClass && flightPrice && flightSeats) {
            flights[flightClass] = {
                Price: flightPrice,
                Seats: flightSeats
            };
        }
    });

    // Collect vehicle data
    card.find('.vehicle-item').each(function() {
        const vehicleKey = $(this).find('[data-key="VehicleType"]').val(); 
        const vehicleType = $(this).find('[data-key="VehicleType"] option:selected').text(); 
        const vehiclePrice = parseFloat($(this).find('[data-key="VehiclePrice"]').val()) || 0;

        if (vehicleKey && vehiclePrice) {
            vehicles[vehicleKey] = {
                Type: vehicleType, 
                Price: vehiclePrice 
            };
        }
    });

    // Collect itinerary data
    card.find('.itinerary-item').each(function() {
        const itineraryName = $(this).find('[data-key="Itinerary"]').text().trim();
        const itineraryImageFile = $(this).find('input[type="file"]')[0].files[0]; 
        const itineraryPrice = parseFloat($(this).find('[data-key="ItineraryPrice"]').val()) || 0;

        if (itineraryName) {
            const itineraryObject = {
                Itinerary: itineraryName,
                ItineraryPrice: itineraryPrice,
            };

            const formData = new FormData();
            formData.append('country', country);
            formData.append('city', city);
            formData.append('data', JSON.stringify(itineraryObject)); 

            if (itineraryImageFile) {
                formData.append('itineraryImage', itineraryImageFile);

                const itineraryPromise = $.ajax({
                    url: 'php_functions/save_itinerary.php', 
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        const result = JSON.parse(response);
                        if (result.success) {
                            console.log('Itinerary saved with image URL:', result.imageUrl);
                        } else {
                            alert('Error saving itinerary: ' + result.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        alert('Failed to save itinerary.');
                    }
                });

                uploadPromises.push(itineraryPromise); 
            }
        }
    });

    Promise.all(uploadPromises).then(function() {
        const dataToUpdate = {
            CityDetail: cityDetail,
            Availability: cityAvailable,
            Hotels: hotels,
            Flights: flights, 
            Vehicle: vehicles, 
            Itinerary: itineraries 
        };

        const formData = new FormData();
        formData.append('country', country);
        formData.append('city', city);
        formData.append('data', JSON.stringify(dataToUpdate)); 

        $.ajax({
            url: 'php_functions/save_to_firebase.php', 
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                const result = JSON.parse(response);
                if (result.success) {
                    alert('City data saved successfully!');
                    hasItem = false;
                    $(".draggable").draggable("enable");
                    location.reload(); 
                } else {
                    alert('Error saving city data: ' + result.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('Failed to save city data.');
            }
        });
    }).catch(function(error) {
        console.error('Error during upload process:', error);
        alert('Error uploading data.');
    });

});


/// Delete Itinerary
$(document).on('click', '.delete-itinerary-btn', function() {
    const itineraryItem = $(this).closest('.itinerary-item');
    const itineraryIndex = itineraryItem.data('index'); 
    const cityCard = itineraryItem.closest('.city-card');
    const country = cityCard.data('country');
    const city = cityCard.data('city');

    if (confirm('Are you sure you want to delete this itinerary?')) {
        $.ajax({
            url: 'php_functions/delete_itinerary.php', 
            type: 'POST',
            data: { country: country, city: city, index: itineraryIndex }, 
            success: function(response) {
                const result = JSON.parse(response);
                if (result.success) {
                    alert('Itinerary deleted successfully!');
                    location.reload(); 
                } else {
                    alert('Error deleting itinerary: ' + result.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('Failed to delete itinerary.');
            }
        });
    }
});

// Delete City Package
$(document).on('click', '.delete1-btn', function() {
    const card = $(this).closest('.city-card'); 
    const country = card.data('country'); 
    const city = card.data('city'); 

    if (confirm('Are you sure you want to delete this city package?')) {
        $.ajax({
            url: 'php_functions/delete_city.php', 
            type: 'POST',
            data: { country: country, city: city }, 
            success: function(response) {
                const result = JSON.parse(response);
                if (result.success) {
                    alert('City package deleted successfully!');
                    card.remove(); 
                } else {
                    alert('Error deleting city package: ' + result.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('Failed to delete city package.');
            }
        });
    }
});


    
    
</script>

</body>
</html>
