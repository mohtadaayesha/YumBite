<?php
// tracking.php

// ==========================================
// 1. CONNECT TO DATABASE
// ==========================================

$conn = new mysqli("localhost", "root", "", "yum");

// Check database connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}


// ==========================================
// 2. GET LAST PLACED ORDER
// ==========================================

$sql = "SELECT 
            o.id, 
            u.address AS delivery_address, 
            r.address AS restaurant_address
        FROM orders o
        JOIN users u ON o.user_id = u.id
        JOIN restaurants r ON o.restaurant_id = r.id
        ORDER BY o.id DESC
        LIMIT 1";

$result = $conn->query($sql);

// Check if query worked
if (!$result) {
    die("Unable to retrieve order information.");
}

// Check if an order exists
if ($result->num_rows == 0) {
    die("No order found.");
}

$order = $result->fetch_assoc();


// ==========================================
// 3. GET ADDRESSES
// ==========================================

$deliveryAddress = trim($order['delivery_address']);
$restaurantAddress = trim($order['restaurant_address']);

// Check addresses
if (empty($deliveryAddress)) {
    die("Delivery address is missing.");
}

if (empty($restaurantAddress)) {
    die("Restaurant address is missing.");
}


// ==========================================
// 4. GEOCODING FUNCTION
//    Address → Latitude / Longitude
// ==========================================

function geocode($address) {

    $url = "https://nominatim.openstreetmap.org/search"
         . "?format=json"
         . "&limit=1"
         . "&q=" . urlencode($address);

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Identify your application
    curl_setopt(
        $ch,
        CURLOPT_USERAGENT,
        "YumBiteApp/1.0"
    );

    // Timeout
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    // Follow redirects
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    // Execute request
    $json = curl_exec($ch);

    // Check cURL error
    if ($json === false) {

        $error = curl_error($ch);

        curl_close($ch);

        return [
            "success" => false,
            "error" => "Geocoding request failed: " . $error
        ];
    }

    // Get HTTP status
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    // Check HTTP response
    if ($httpCode != 200) {

        return [
            "success" => false,
            "error" => "Geocoding service returned HTTP " . $httpCode
        ];
    }

    // Convert JSON to PHP array
    $data = json_decode($json, true);

    // Check JSON
    if (!is_array($data)) {

        return [
            "success" => false,
            "error" => "Invalid response from geocoding service."
        ];
    }

    // No results
    if (count($data) == 0) {

        return [
            "success" => false,
            "error" => "Location not found: " . $address
        ];
    }

    // Return coordinates
    return [
        "success" => true,
        "lat" => (float)$data[0]['lat'],
        "lon" => (float)$data[0]['lon']
    ];
}


// ==========================================
// 5. GEOCODE RESTAURANT
// ==========================================

$pickupResult = geocode($restaurantAddress);

// Check restaurant location
if (!$pickupResult["success"]) {

    die(
        "Unable to locate the restaurant.<br><br>" .
        htmlspecialchars($pickupResult["error"])
    );
}


// ==========================================
// 6. GEOCODE DELIVERY ADDRESS
// ==========================================

$destinationResult = geocode($deliveryAddress);

// Check delivery location
if (!$destinationResult["success"]) {

    die(
        "Unable to locate the delivery address.<br><br>" .
        htmlspecialchars($destinationResult["error"])
    );
}


// ==========================================
// 7. CREATE COORDINATES
// ==========================================

$pickupLat = $pickupResult["lat"];
$pickupLon = $pickupResult["lon"];

$destinationLat = $destinationResult["lat"];
$destinationLon = $destinationResult["lon"];

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>YumBite | Order Tracking</title>

    <link
        rel="icon"
        href="images/logo1.png"
        type="image/png"
    >

    <!-- Existing CSS -->
    <link
        rel="stylesheet"
        href="css/index.css"
    >

    <link
        rel="stylesheet"
        href="css/menu.css"
    >

    <link
        rel="stylesheet"
        href="css/style.css"
        id="theme-style"
    >

    <link
        rel="stylesheet"
        href="css/style-dark.css"
    >

    <!-- Leaflet CSS -->
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet/dist/leaflet.css"
    >


    <style>

        body {

            text-align: center;

            margin-top: 20px;

            font-family: Arial, sans-serif;

        }


        /* ==========================================
           ORDER INFORMATION BOX
           ========================================== */

        .container {

            margin: 20px auto;

            padding: 20px;

            border: 2px solid #333;

            border-radius: 8px;

            width: 80%;

            max-width: 600px;

        }


        /* ==========================================
           STATUS
           ========================================== */

        #status {

            font-size: 20px;

            font-weight: bold;

            margin: 20px;

        }


        /* ==========================================
           STATUS DESCRIPTION
           ========================================== */

        #status-description {

            font-size: 15px;

            margin-bottom: 15px;

        }


        /* ==========================================
           MAP
           ========================================== */

        #map {

            width: 90%;

            height: 500px;

            margin: 20px auto;

            border: 2px solid #333;

            border-radius: 8px;

        }


        /* ==========================================
           BUTTON
           ========================================== */

        .btn-primary {

            display: inline-block;

            margin-top: 10px;

            padding: 10px 20px;

            background-color: #007BFF;

            color: white;

            text-decoration: none;

            border-radius: 5px;

        }


        .btn-primary:hover {

            background-color: #0056b3;

        }


        /* ==========================================
           RIDER ICON
           ========================================== */

        .rider-icon {

            font-size: 35px;

            text-align: center;

            line-height: 40px;

        }


        /* ==========================================
           LOADING
           ========================================== */

        .loading {

            display: inline-block;

            width: 20px;

            height: 20px;

            border: 3px solid #ddd;

            border-top: 3px solid #007BFF;

            border-radius: 50%;

            animation: spin 1s linear infinite;

            vertical-align: middle;

            margin-right: 8px;

        }


        @keyframes spin {

            0% {

                transform: rotate(0deg);

            }

            100% {

                transform: rotate(360deg);

            }

        }


        /* ==========================================
           ERROR MESSAGE
           ========================================== */

        .error-message {

            color: #b00020;

            font-size: 18px;

            margin: 30px;

        }

    </style>

</head>


<body>


    <!-- ==========================================
         NAVBAR
         ========================================== -->

    <?php include "navbar.php"; ?>


    <!-- ==========================================
         ORDER SUCCESS BOX
         ========================================== -->

    <div class="container">

        <h1>
            Order Placed Successfully!
        </h1>

        <p>
            Thank you for your order.
        </p>


        <a
            href="index.php"
            class="btn-primary"
        >
            Back to Home
        </a>

    </div>


    <!-- ==========================================
         STATUS
         ========================================== -->

    <div id="status">

        <span class="loading"></span>

        Preparing your order...

    </div>


    <div id="status-description">

        Your restaurant is preparing your food.

    </div>


    <!-- ==========================================
         MAP
         ========================================== -->

    <div id="map"></div>


    <!-- ==========================================
         LEAFLET JS
         ========================================== -->

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>


    <script>


        // ==========================================
        // 1. GET COORDINATES FROM PHP
        // ==========================================

        const pickup = [
            <?= $pickupLat ?>,
            <?= $pickupLon ?>
        ];


        const destination = [
            <?= $destinationLat ?>,
            <?= $destinationLon ?>
        ];


        // ==========================================
        // 2. GET HTML ELEMENTS
        // ==========================================

        const statusElement =
            document.getElementById("status");


        const descriptionElement =
            document.getElementById("status-description");


        // ==========================================
        // 3. INITIALIZE MAP
        // ==========================================

        const map = L.map("map");


        // OpenStreetMap tiles
        L.tileLayer(
            "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
            {
                attribution:
                    "&copy; OpenStreetMap contributors",

                maxZoom: 19
            }
        ).addTo(map);


        // Initially show pickup location
        map.setView(
            pickup,
            14
        );


        // ==========================================
        // 4. RESTAURANT MARKER
        // ==========================================

        const pickupMarker =
            L.marker(pickup)
            .addTo(map)
            .bindPopup(
                "<b>🍴 Restaurant</b><br>" +
                "Pickup Location"
            );


        // ==========================================
        // 5. DESTINATION MARKER
        // ==========================================

        const destinationMarker =
            L.marker(destination)
            .addTo(map)
            .bindPopup(
                "<b>🏠 Delivery Address</b><br>" +
                "Your destination"
            );


        // ==========================================
        // 6. RIDER ICON
        // ==========================================

        const riderIcon = L.divIcon({

            className: "rider-icon",

            html: "🏍️",

            iconSize: [
                45,
                45
            ],

            iconAnchor: [
                22,
                22
            ]

        });


        // ==========================================
        // 7. CREATE RIDER MARKER
        // ==========================================

        const riderMarker =
            L.marker(
                pickup,
                {
                    icon: riderIcon
                }
            )
            .addTo(map);


        // ==========================================
        // 8. REQUEST ROUTE FROM OSRM
        // ==========================================

        const routeURL =
            "https://router.project-osrm.org/route/v1/driving/"
            +
            pickup[1]
            +
            ","
            +
            pickup[0]
            +
            ";"
            +
            destination[1]
            +
            ","
            +
            destination[0]
            +
            "?overview=full&geometries=geojson";


        fetch(routeURL)

            .then(function(response) {

                // Check HTTP response
                if (!response.ok) {

                    throw new Error(
                        "Route service returned HTTP " +
                        response.status
                    );

                }

                return response.json();

            })


            .then(function(data) {


                // ==================================
                // CHECK ROUTE DATA
                // ==================================

                if (
                    !data.routes ||
                    data.routes.length === 0
                ) {

                    throw new Error(
                        "No route was found."
                    );

                }


                // ==================================
                // GET ROUTE
                // ==================================

                const routeCoordinates =
                    data.routes[0]
                    .geometry
                    .coordinates
                    .map(function(coordinate) {

                        return [
                            coordinate[1],
                            coordinate[0]
                        ];

                    });


                // ==================================
                // DRAW ROUTE
                // ==================================

                const routeLine =
                    L.polyline(
                        routeCoordinates,
                        {
                            color: "blue",

                            weight: 5,

                            opacity: 0.8
                        }
                    )
                    .addTo(map);


                // ==================================
                // FIT MAP TO ROUTE
                // ==================================

                map.fitBounds(
                    routeLine.getBounds(),
                    {
                        padding: [
                            30,
                            30
                        ]
                    }
                );


                // ==================================
                // STATUS 1
                // PREPARING
                // ==================================

                statusElement.innerHTML =
                    "Preparing your order...";

                descriptionElement.innerText =
                    "Your restaurant is preparing your food.";


                // ==================================
                // WAIT 5 SECONDS
                // THEN STATUS 2
                // ==================================

                setTimeout(function() {


                    statusElement.innerHTML =
                        "🍴 Order picked up!";

                    descriptionElement.innerText =
                        "Your food has been picked up from the restaurant.";


                    // ==================================
                    // WAIT ANOTHER 3 SECONDS
                    // THEN STATUS 3
                    // ==================================

                    setTimeout(function() {


                        statusElement.innerHTML =
                            "🏍️ Delivery is on the way...";

                        descriptionElement.innerText =
                            "Your delivery rider is heading to your address.";


                        // ==================================
                        // START RIDER MOVEMENT
                        // ==================================

                        animateRider(
                            routeCoordinates
                        );


                    }, 10000);


                }, 15000);


            })


            .catch(function(error) {


                // ==================================
                // ROUTE ERROR
                // ==================================

                console.error(
                    "Route error:",
                    error
                );


                statusElement.innerHTML =
                    "⚠️ Unable to load route";


                descriptionElement.innerText =
                    "The map could not calculate a delivery route. " +
                    "Please try refreshing the page.";


                // Still show both locations
                const bounds =
                    L.latLngBounds(
                        pickup,
                        destination
                    );


                map.fitBounds(
                    bounds,
                    {
                        padding: [
                            50,
                            50
                        ]
                    }
                );


            });


        // ==========================================
        // 9. SMOOTH RIDER ANIMATION
        // ==========================================

        function animateRider(route) {


            let currentSegment = 0;


            function moveToNextPoint() {


                // ==================================
                // CHECK IF DELIVERY FINISHED
                // ==================================

                if (
                    currentSegment >=
                    route.length - 1
                ) {


                    riderMarker.setLatLng(
                        destination
                    );


                    // ==================================
                    // FINAL STATUS
                    // ==================================

                    statusElement.innerHTML =
                        "✅ Your order has been delivered!";


                    descriptionElement.innerText =
                        "Enjoy your meal! Thank you for ordering from YumBite.";


                    riderMarker
                        .bindPopup(
                            "<b>✅ Delivered!</b><br>" +
                            "Your food has arrived."
                        )
                        .openPopup();


                    return;

                }


                // ==================================
                // CURRENT POINT
                // ==================================

                const start =
                    route[currentSegment];


                // ==================================
                // NEXT POINT
                // ==================================

                const end =
                    route[currentSegment + 1];


                // ==================================
                // ANIMATION SETTINGS
                // ==================================

                const duration = 500;


                const startTime =
                    performance.now();


                // ==================================
                // SMOOTH MOVEMENT
                // ==================================

                function animate(time) {


                    const elapsed =
                        time - startTime;


                    let progress =
                        elapsed / duration;


                    // Prevent progress > 1
                    if (progress > 1) {

                        progress = 1;

                    }


                    // ==================================
                    // CALCULATE CURRENT POSITION
                    // ==================================

                    const lat =
                        start[0] +
                        (
                            end[0] -
                            start[0]
                        ) *
                        progress;


                    const lng =
                        start[1] +
                        (
                            end[1] -
                            start[1]
                        ) *
                        progress;


                    riderMarker.setLatLng(
                        [
                            lat,
                            lng
                        ]
                    );


                    // ==================================
                    // CONTINUE ANIMATION
                    // ==================================

                    if (
                        progress < 1
                    ) {


                        requestAnimationFrame(
                            animate
                        );


                    }

                    else {


                        currentSegment++;

                        moveToNextPoint();

                    }

                }


                requestAnimationFrame(
                    animate
                );

            }


            // Start animation
            moveToNextPoint();

        }

    </script>


</body>

</html>