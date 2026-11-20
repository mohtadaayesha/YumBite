<?php
// tracking.php

// Connect DB
$conn = new mysqli("localhost", "root", "", "yum");

// Get last placed order
$order = $conn->query("SELECT o.id, u.address AS delivery_address, r.address AS restaurant_address 
                       FROM orders o
                       JOIN users u ON o.user_id = u.id
                       JOIN restaurants r ON o.restaurant_id = r.id
                       ORDER BY o.id DESC LIMIT 1")->fetch_assoc();

// Delivery address from user
$deliveryAddress = $order['delivery_address'];
$restaurantAddress = $order['restaurant_address'];

// Use Nominatim API to get coordinates (lat,lng) from addresses
function geocode($address) {
    $url = "https://nominatim.openstreetmap.org/search?format=json&q=" . urlencode($address);

    // Create a stream context with User-Agent header
    $opts = [
        "http" => [
            "header" => "User-Agent: YumBiteApp/1.0 (your_email@example.com)\r\n"
        ]
    ];
    $context = stream_context_create($opts);

    $json = file_get_contents($url, false, $context);

    if ($json === FALSE) {
        return null; // API request failed
    }

    $data = json_decode($json, true);

    if (is_array($data) && count($data) > 0) {
        $lat = $data[0]['lat'];
        $lon = $data[0]['lon'];
        return [$lat, $lon];
    } else {
        return null;
    }
}

$pickup = geocode($restaurantAddress);      // restaurant
$destination = geocode($deliveryAddress);   // user delivery
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>YumBite | Order</title>
  <link rel="icon" href="images/logo1.png" type="image/png">

  <link rel="stylesheet" href="css/index.css">
  <link rel="stylesheet" href="css/menu.css">

  <!-- Main CSS -->
  <link rel="stylesheet" href="css/style.css" id="theme-style">
  <link rel="stylesheet" href="css/style-dark.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

  <style>
    body { text-align: center; margin-top: 20px; font-family: Arial, sans-serif; }
    #status { font-size: 18px; margin: 10px; }
    #map { width: 90%; height: 500px; margin: 20px auto; border: 2px solid #333; border-radius: 8px; }
    .container { margin: 20px auto; padding: 20px; border: 2px solid #333; border-radius: 8px; width: 80%; max-width: 500px; }
    .btn-primary { display: inline-block; margin-top: 10px; padding: 10px 20px; background-color: #007BFF; color: white; text-decoration: none; border-radius: 5px; }
    .btn-primary:hover { background-color: #0056b3; }
    .rider-emoji { font-size: 48px; text-align: center; }
  </style>
</head>
<body>
    
  <!-- Navbar -->
  <?php include "navbar.php"; ?>

  <div class="container">
    <h1>Order Placed Successfully!</h1>
    <p>Thank you for your order. Your food is being prepared.</p>
    <a href="index.php" class="btn-primary">Back to Home</a>
  </div>

  
  <div id="status">Preparing your order...</div>
  <div id="map"></div>

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>
    // Pickup & Destination from PHP
    var pickup = [<?= $pickup[0] ?>, <?= $pickup[1] ?>];
    var destination = [<?= $destination[0] ?>, <?= $destination[1] ?>];

    // Initialize Map
    var map = L.map('map').setView(pickup, 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap'
    }).addTo(map);

    // Markers
    L.marker(pickup).addTo(map).bindPopup("Pickup: Restaurant").openPopup();
    L.marker(destination).addTo(map).bindPopup("Drop: Your Address");

    // Delivery Man Marker with Emoji
    var riderMarker = L.marker(pickup, {
      icon: L.divIcon({
        className: "rider-emoji",
        html: "🏍️",
        iconSize: [100, 100],
        iconAnchor: [50, 50] // center
      })
    }).addTo(map);

    document.getElementById("status").innerText = "Delivery Man has picked up your order!";

    // Request route from OSRM API
    fetch(`https://router.project-osrm.org/route/v1/driving/${pickup[1]},${pickup[0]};${destination[1]},${destination[0]}?overview=full&geometries=geojson`)
      .then(res => res.json())
      .then(data => {
        var route = data.routes[0].geometry.coordinates.map(c => [c[1], c[0]]);
        
        // Draw route
        var routeLine = L.polyline(route, { color: "blue", weight: 5 }).addTo(map);
        map.fitBounds(routeLine.getBounds());

        // Animate Rider
        let step = 0;
        function moveRider() {
          if (step < route.length) {
            riderMarker.setLatLng(route[step]);
            if (step === 1) {
              document.getElementById("status").innerText = "Delivery is on the way...";
            }
            step++;
            setTimeout(moveRider, 500); // move every 1s
          } else {
            document.getElementById("status").innerText = "✅ Your order has been delivered!";
            riderMarker.bindPopup("Destination Reached").openPopup();
          }
        }
        moveRider();
      });
  </script>

</body>
</html>
