<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>City Packages</title>
    <link rel="stylesheet" href="css/Package.css">
    <link href="https://fonts.googleapis.com/css2?family=Itim&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    
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
            <p>City Package</p>
        </div>
        <div class="header-right d-flex align-items-center">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Search by City, Hotel, or Itinerary" onkeyup="filterUsers()">
            </div>
            <div class="add-city-btn text-center">
                <button type="button" class="btn btn-primary btn-lg rounded-circle" onclick="toggleCityForm()">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
    </header>

    <div id="addCityForm" class="card" style="display:none;">
        <div class="card-body">
            <h5 class="card-title">Add New City</h5>
            <form id="cityForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="cityName">City Name</label>
                    <input type="text" class="form-control" id="cityName" placeholder="Enter city name" required>
                </div>
                <div class="form-group">
                    <label for="cityImage">Upload City Image</label>
                    <input type="file" class="form-control-file" id="cityImage" accept="image/*" required>
                </div>
                <button type="submit" class="btn btn-success">Submit City</button>
            </form>
        </div>
    </div>

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
                    echo '<textarea data-key="CityDetail" placeholder="Enter City Description" style="width:100%; height: 60px;">' . htmlspecialchars($cityDetails['CityDetail'] ?? '') . '</textarea>';
                    $cityImage = htmlspecialchars($cityDetails['CityImage'] ?? 'https://example.com/path/to/default_image.jpg');
                    echo '<img src="' . $cityImage . '" alt="' . htmlspecialchars($cityDetails['City']) . '" style="width:100%;">';
                    echo '<input type="file" accept="image/*" class="image-upload">';

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
                            echo '<input type="number" data-key="VehiclePrice" value="' . $vehiclePrice . '" placeholder="Vehicle Price" readonly>';
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
                    echo '<button class="hide-btn">Hide</button>';
                    echo '<button class="delete-btn">Delete</button>';
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

                if (type === "hotel") {
                    const hotelHtml = `
                    <div class="hotel-item">
                        <input type="text" data-key="Hotel" placeholder="Enter Hotel Name" required>
                        <input type="file" name="hotelImage[]" class="hotel-image-upload" accept="image/*" onchange="previewHotelImage(this)">
                        <div class="image-container">
                            <img src="" class="thumbnail hotel-thumbnail" alt="Hotel Image" style="display: none;">
                        </div>
                        <div class="room-item">
                            <span>Single</span>
                            <input type="number" data-key="SingleRooms" placeholder="Rooms" style="width: 40%;">
                            <input type="number" data-key="SinglePrice" placeholder="Price" style="width: 40%;">
                        </div>
                        <div class="room-item">
                            <span>Double</span>
                            <input type="number" data-key="DoubleRooms" placeholder="Rooms" style="width: 40%;">
                            <input type="number" data-key="DoublePrice" placeholder="Price" style="width: 40%;">
                        </div>
                        <div class="room-item">
                            <span>Suite</span>
                            <input type="number" data-key="SuiteRooms" placeholder="Rooms" style="width: 40%;">
                            <input type="number" data-key="SuitePrice" placeholder="Price" style="width: 40%;">
                        </div>
                        <textarea data-key="Description" placeholder="Enter Hotel Description"></textarea>
                    </div>`;
                    cityCard.append(hotelHtml);
                } else if (type === "flight") {
                    const flightHtml = `
                        <div class="flight-item">
                            <span contenteditable="true" data-key="Class">Flight Class</span>
                            <input type="number" data-key="Price" value="" placeholder="Flight Price">
                            <input type="number" data-key="TotalSeats" placeholder="Total Seats" style="width: 100%;">
                        </div>`;
                    cityCard.append(flightHtml);
                } else if (type === "vehicle") {
                    const vehicleHtml = `
                        <div class="vehicle-item">
                            <select data-key="VehicleType" class="vehicle-type">
                                <option value="TypeA">4-Seater</option>
                                <option value="TypeB">7-Seater</option>
                                <option value="TypeC">Van</option>
                            </select>
                            <input type="number" data-key="VehiclePrice" value="" placeholder="Vehicle Price">
                            <button class="delete-vehicle-btn">Delete</button>
                        </div>`;
                    cityCard.append(vehicleHtml);
                } else if (type === "itinerary") {
                    const itineraryHtml = `
                    <div class="itinerary-item">
                        <strong contenteditable="true" data-key="Itinerary">New Itinerary</strong>
                        <input type="file" name="itineraryImage[]" accept="image/*" multiple>
                        <div class="image-container">
                            <img src="" class="thumbnail itinerary-thumbnail" alt="Itinerary Image" style="display: none;">
                        </div>
                        <input type="number" data-key="ItineraryPrice" placeholder="Itinerary Price">
                        <button class="delete-itinerary-btn">Delete</button>
                    </div>`;
                    cityCard.append(itineraryHtml);
                }
            }
        });

        // Hotel dropdown change event
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
                                <input type="number" class="form-control" value="${hotel.Rooms.Double.Availability || 0}" readonly>
                            </div>
                            <div class="room-detail">
                                <label>Price:</label>
                                <input type="number" class="form-control" value="${hotel.Rooms.Double.Price || 0}" readonly>
                            </div>
                            <div class="room-detail">
                                <label>Single:</label>
                                <input type="number" class="form-control" value="${hotel.Rooms.Single.Availability || 0}" readonly>
                            </div>
                            <div class="room-detail">
                                <label>Price:</label>
                                <input type="number" class="form-control" value="${hotel.Rooms.Single.Price || 0}" readonly>
                            </div>
                            <div class="room-detail">
                                <label>Suite:</label>
                                <input type="number" class="form-control" value="${hotel.Rooms.Suite.Availability || 0}" readonly>
                            </div>
                            <div class="room-detail">
                                <label>Price:</label>
                                <input type="number" class="form-control" value="${hotel.Rooms.Suite.Price || 0}" readonly>
                            </div>
                        </div>
                        <div class="description-container">
                            <label>Hotel Description:</label>
                            <textarea class="form-control description-textarea" readonly>${hotel.Description || ''}</textarea>
                        </div>
                    </div>`;
                cityCard.find('.hotel-details').html(hotelHtml); 
            }
        });

    // Itinerary dropdown change event
    $('.itinerary-dropdown').on('change', function() {
        const itineraryIndex = $(this).val(); 
        const cityCard = $(this).closest('.city-card');
        const cityDetails = <?php echo json_encode($dataCities); ?>; 
        const city = cityCard.data('city'); 

        cityCard.find('.itinerary-details').html(''); 

        if (itineraryIndex && cityDetails[city]['Itinerary'][itineraryIndex]) {
            const itinerary = cityDetails[city]['Itinerary'][itineraryIndex];

            const itineraryHtml = `
                <div class="itinerary-item d-flex">
                    <div class="itinerary-image-container">
                        <img src="${itinerary.Image || 'https://example.com/path/to/default_image.jpg'}" class="itinerary-image" alt="${itinerary.Itinerary}">
                    </div>
                    <div class="itinerary-details-container">
                        <div class="mb-3">
                            <label>Itinerary Name:</label>
                            <input type="text" class="form-control" value="${itinerary.Itinerary}" readonly>
                        </div>
                        <div class="mb-3">
                            <label>Price:</label>
                            <input type="number" class="form-control" value="${itinerary.Price}" readonly>
                        </div>
                    </div>
                </div>`;
            
            cityCard.find('.itinerary-details').html(itineraryHtml); 
        }
    });

        function toggleCityForm() {
            var form = document.getElementById('addCityForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }


        // Add New City 
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
                url: 'add_new_city.php', 
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

        $(document).on('click', '.edit-btn', function() {
            const card = $(this).closest('.city-card'); 
            const country = card.data('country'); 
            const city = card.find('[data-key="City"]').text().trim(); 
            const cityDetail = card.find('[data-key="CityDetail"]').val().trim(); 
            const hotels = {};
            const itineraries = [];

            // Collect hotel data
            card.find('.hotel-item').each(function(index) {
                const hotelName = $(this).find('[data-key="Hotel"]').val().trim(); 
                const hotelDescription = $(this).find('[data-key="Description"]').val() || ''; 
                const singleRooms = $(this).find('[data-key="SingleRooms"]').val() || '';
                const singlePrice = $(this).find('[data-key="SinglePrice"]').val() || '';
                const doubleRooms = $(this).find('[data-key="DoubleRooms"]').val() || '';
                const doublePrice = $(this).find('[data-key="DoublePrice"]').val() || '';
                const suiteRooms = $(this).find('[data-key="SuiteRooms"]').val() || '';
                const suitePrice = $(this).find('[data-key="SuitePrice"]').val() || '';
                const hotelImageFile = $(this).find('.hotel-image-upload')[0].files[0];

                if (hotelName) {
                    let hotelId = index + 1; 
                    hotels[hotelId] = {
                        Hotel: hotelName,
                        Description: hotelDescription,
                        Rooms: {
                            Single: { Availability: singleRooms, Price: singlePrice },
                            Double: { Availability: doubleRooms, Price: doublePrice },
                            Suite: { Availability: suiteRooms, Price: suitePrice }
                        }
                    };

                    if (hotelImageFile) {
                        const imageFormData = new FormData();
                        imageFormData.append('hotelImage', hotelImageFile);
                        imageFormData.append('hotelId', hotelId);
                        imageFormData.append('country', country);
                        imageFormData.append('city', city);

                        $.ajax({
                            url: 'upload_hotel_image.php',
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
                    }
                }
            });

            // Collect itinerary data
            card.find('.itinerary-item').each(function(index) {
                const itineraryName = $(this).find('[data-key="Itinerary"]').text().trim() || '';
                const itineraryImageFile = $(this).find('input[type="file"]')[0].files[0]; 
                const itineraryPrice = $(this).find('[data-key="ItineraryPrice"]').val() || '0';

                if (itineraryName) {
                    const itineraryObject = {
                        Itinerary: itineraryName,
                        ItineraryPrice: itineraryPrice,
                    };

                    const formData = new FormData();
                    formData.append('country', country);
                    formData.append('city', city);
                    formData.append('data', JSON.stringify(itineraryObject)); 
                    formData.append('itineraryImage', itineraryImageFile);

                    $.ajax({
                        url: 'save_itinerary.php', 
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            const result = JSON.parse(response);
                            if (result.success) {
                                alert('Itinerary saved successfully!');
                            } else {
                                alert('Error saving itinerary: ' + result.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                            alert('Failed to save itinerary.');
                        }
                    });
                }
            });

            const dataToUpdate = {
                CityDetail: cityDetail,
                Hotels: hotels,
                Itinerary: itineraries 
            };

            const formData = new FormData();
            formData.append('country', country);
            formData.append('city', city);
            formData.append('data', JSON.stringify(dataToUpdate)); 

            $.ajax({
                url: 'save_to_firebase.php', 
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    const result = JSON.parse(response);
                    if (result.success) {
                        alert('City data saved successfully!');
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
        });

        function previewHotelImage(input) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $(input).siblings('.image-container').find('.hotel-thumbnail').attr('src', e.target.result).show();
                };
                reader.readAsDataURL(file);
            }
        }

        function toggleCityForm() {
            var form = document.getElementById('addCityForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    });
</script>

</body>
</html>
