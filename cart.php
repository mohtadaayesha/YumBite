<?php
session_start();
if (!isset($_SESSION["email"]) || !isset($_SESSION["full_name"]) || !isset($_SESSION["user_id"])) {
  header("Location: login.php");
  exit();
}

require_once "database.php";

$errors = array();
$user_email = $_SESSION["email"];

// Fetch user data
$sql = "SELECT full_name, phone, email, address FROM users WHERE email = ?";
$stmt = mysqli_stmt_init($conn);
if (mysqli_stmt_prepare($stmt, $sql)) {
  mysqli_stmt_bind_param($stmt, "s", $user_email);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $user_data = mysqli_fetch_assoc($result);
  mysqli_stmt_close($stmt);
} else {
  die("Something went wrong with fetching user data");
}

// Handle order submission
if (isset($_POST["confirm_order"])) {
    $cart_json     = $_POST['cart_data'] ?? '[]';
    $cart          = json_decode($cart_json, true);
    $restaurant_id = $_POST['restaurant_id'] ?? null;
    $delivery_addr = trim($_POST['address'] ?? '');

    if (empty($cart)) {
        $errors[] = "Cart is empty";
    } elseif (!$restaurant_id) {
        $errors[] = "Restaurant not specified for order";
    } elseif ($delivery_addr === '') {
        $errors[] = "Delivery address is required";
    } else {
        $conn->begin_transaction();
        try {
            $user_id = (int)$_SESSION['user_id'];
            $status  = "Pending";

            $order_sql = "INSERT INTO orders (user_id, food_id, quantity, amount, status, restaurant_id, created_at) 
                          VALUES (?, ?, ?, ?, ?, ?, NOW())";
            $order_stmt = mysqli_stmt_init($conn);

            if (!mysqli_stmt_prepare($order_stmt, $order_sql)) {
                throw new Exception("Failed to prepare order insertion");
            }

            foreach ($cart as $item) {
                $food_id     = (int)$item['id'];
                $quantity    = (int)$item['quantity'];
                $item_amount = (float)$item['price'] * (int)$item['quantity'];
                mysqli_stmt_bind_param($order_stmt, "iiidsi", $user_id, $food_id, $quantity, $item_amount, $status, $restaurant_id);
                mysqli_stmt_execute($order_stmt);
            }
            mysqli_stmt_close($order_stmt);

            // Fetch restaurant name + address for pickup
            $r_sql = "SELECT name, address FROM restaurants WHERE id = ?";
            $r_stmt = mysqli_stmt_init($conn);
            $restaurant_name = "Restaurant";
            $restaurant_address = "";

            if (mysqli_stmt_prepare($r_stmt, $r_sql)) {
                mysqli_stmt_bind_param($r_stmt, "i", $restaurant_id);
                mysqli_stmt_execute($r_stmt);
                $r_res = mysqli_stmt_get_result($r_stmt);
                if ($r_res && $row = mysqli_fetch_assoc($r_res)) {
                    $restaurant_name = $row['name'] ?: $restaurant_name;
                    $restaurant_address = $row['address'] ?: "";
                }
                mysqli_stmt_close($r_stmt);
            }

            $conn->commit();

            // Redirect to tracking with pickup/drop addresses
            // We JSON-encode to safely pass to JS, then encodeURIComponent in the browser.
            echo "<script>
                    try {
                      localStorage.removeItem('cart');
                    } catch (e) {}
                    const pickup      = " . json_encode($restaurant_address) . ";
                    const pickupName  = " . json_encode($restaurant_name) . ";
                    const dropAddress = " . json_encode($delivery_addr) . ";

                    const url = 'tracking.php'
                      + '?status=success'
                      + '&pickup=' + encodeURIComponent(pickup || '')
                      + '&pickup_name=' + encodeURIComponent(pickupName || '')
                      + '&drop=' + encodeURIComponent(dropAddress || '');

                    window.location.href = url;
                  </script>";
            exit();

        } catch (Exception $e) {
            $conn->rollback();
            $errors[] = "Error placing order: " . $e->getMessage();
        }
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>YumBite | Cart</title>
  <link rel="icon" href="images/logo1.png" type="image/png">
  <link id="theme-style" rel="stylesheet" href="css/style-dark.css" />
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/index.css">
  <link rel="stylesheet" href="css/cart.css">
</head>
<body>
  <header>
    <nav>
      <div class="logo">
        <img src="images/logo1.png" alt="Logo1" class="logo1" />
        <img src="images/logo.png" alt="YumBite" />
      </div>
      <div class="hamburger" id="hamburger">
        <span></span><span></span><span></span>
      </div>
      <ul class="nav-links" id="navLinks">
        <li><a href="index.php">Home</a></li>
        <li><a href="menu.php">Menu</a></li>
        <li><a href="#" onclick="openAbout()">About Us</a></li>
        <li><a href="contact.php">Contact</a></li>
        <li>
          <?php if (isset($_SESSION["full_name"])): ?>
            <div class="profile-circle" id="profileCircle">
              <?php echo strtoupper(substr($_SESSION["full_name"], 0, 1)); ?>
              <div class="dropdown-menu" id="dropdownMenu">
                <a href="profile.php">My Account</a>
                <a href="logout.php">Logout</a>
              </div>
            </div>
          <?php else: ?>
            <a href="login.php" class="login-btn">Login →</a>
          <?php endif; ?>
        </li>
        <li class="cart"><a href="cart.php">🛒</a></li>
      </ul>
    </nav>
  </header>

  <section class="order">
    <div class="container">
      <h2 class="text-center">Your Order List</h2>

      <?php
      if (!empty($errors)) {
        foreach ($errors as $error) {
          echo "<p style='color:red;'>$error</p>";
        }
      }
      ?>

      <table class="tbl-full" border="0">
        <tr>
          <th>S.N.</th>
          <th>Food</th>
          <th>Name</th>
          <th>Price</th>
          <th>Quantity</th>
          <th>Total</th>
          <th>Action</th>
        </tr>
        <tbody id="cartItems"></tbody>
        <tr>
          <th colspan="5">Total</th>
          <th id="cartTotal">TK. 0.00</th>
          <th></th>
        </tr>
      </table>

      <form action="" method="POST" class="form" id="orderForm">
        <fieldset>
          <legend>Delivery Details</legend>
          <p class="label">Full Name</p>
          <input type="text" name="fullName" value="<?php echo htmlspecialchars($user_data['full_name'] ?? ''); ?>" readonly>
          <p class="label">Phone Number</p>
          <input type="tel" name="phone" placeholder="Enter your phone..." required>
          <p class="label">Email</p>
          <input type="email" name="email" value="<?php echo htmlspecialchars($user_data['email'] ?? ''); ?>" readonly>
          <p class="label">Address</p>
          <input type="text" name="address" placeholder="Enter your address..." required>

          <!-- Hidden inputs -->
          <input type="hidden" id="cart_data" name="cart_data" value="">
          <input type="hidden" name="restaurant_id" id="restaurant_id" value="">

          <input type="submit" name="confirm_order" value="Confirm Order" class="btn-primary">
        </fieldset>
      </form>
    </div>
  </section>

  <div class="popup" id="aboutPopup">
    <div class="popup-content">
      <span onclick="closeAbout()">&times;</span>
      <h2>About Us</h2>
      <p>Launched in 2021, Our technology platform connects customers, restaurant partners and delivery partners...</p>
    </div>
  </div>

  <footer>
    <p>&copy; 2025 YumBite. All Rights Reserved.</p>
  </footer>

  <script>
    // Cart helpers
    function getCart() {
      return JSON.parse(localStorage.getItem('cart')) || [];
    }
    function saveCart(cart) {
      localStorage.setItem('cart', JSON.stringify(cart));
      updateCartDisplay();
    }

    function updateCartDisplay() {
      const cart = getCart();
      const cartItems = document.getElementById('cartItems');
      const cartTotal = document.getElementById('cartTotal');
      cartItems.innerHTML = '';

      let total = 0;
      cart.forEach((item, index) => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${index + 1}</td>
          <td><img src="${item.image}" alt="${item.name}" style="width:60px;height:60px;object-fit:cover;"></td>
          <td>${item.name}</td>
          <td>TK. ${Number(item.price).toFixed(2)}</td>
          <td>
            <div class="quantity-control">
              <button class="minus-btn" data-id="${item.id}">-</button>
              <input type="number" class="quantity-input" value="${item.quantity}" min="1" data-id="${item.id}">
              <button class="plus-btn" data-id="${item.id}">+</button>
            </div>
          </td>
          <td>TK. ${itemTotal.toFixed(2)}</td>
          <td><a href="#" class="btn-delete" data-id="${item.id}">&times;</a></td>
        `;
        cartItems.appendChild(row);
      });

      cartTotal.textContent = `TK. ${total.toFixed(2)}`;

      const cartDataInput = document.getElementById('cart_data');
      if (cartDataInput) {
        cartDataInput.value = JSON.stringify(cart);
      }
    }

    document.addEventListener('DOMContentLoaded', function () {
      // nav
      const hamburger = document.getElementById("hamburger");
      const navLinks = document.getElementById("navLinks");
      hamburger.addEventListener("click", () => {
        hamburger.classList.toggle("active");
        navLinks.classList.toggle("show");
      });
      document.querySelectorAll('.nav-links a').forEach(link => {
        link.addEventListener('click', () => {
          hamburger.classList.remove('active');
          navLinks.classList.remove('show');
        });
      });

      // initial render
      updateCartDisplay();

      // submit hook
      const orderForm = document.getElementById('orderForm');
      orderForm.addEventListener('submit', function (e) {
        const cart = getCart();
        if (cart.length === 0) {
          e.preventDefault();
          alert('Your cart is empty!');
          return;
        }
        document.getElementById('cart_data').value = JSON.stringify(cart);

        // IMPORTANT: Ensure every cart item (created on menu.php) has restaurant_id set.
        // We take the first item's restaurant_id (assuming same restaurant).
        const rid = cart[0]?.restaurant_id;
        if (!rid) {
          e.preventDefault();
          alert('Missing restaurant for this cart. Please re-add items.');
          return;
        }
        document.getElementById('restaurant_id').value = rid;
      });

      // cart interactions (plus/minus/delete)
      document.addEventListener('click', function (e) {
        const id = e.target.getAttribute('data-id');
        if (!id) return;

        const cart = getCart();

        if (e.target.classList.contains('plus-btn')) {
          const item = cart.find(i => String(i.id) === String(id));
          if (item) { item.quantity++; saveCart(cart); }
        } else if (e.target.classList.contains('minus-btn')) {
          const item = cart.find(i => String(i.id) === String(id));
          if (item && item.quantity > 1) { item.quantity--; saveCart(cart); }
        } else if (e.target.classList.contains('btn-delete')) {
          e.preventDefault();
          const updatedCart = cart.filter(i => String(i.id) !== String(id));
          saveCart(updatedCart);
        }
      });

      // change quantity by input
      document.addEventListener('change', function (e) {
        if (!e.target.classList.contains('quantity-input')) return;
        const id = e.target.getAttribute('data-id');
        const value = Math.max(1, parseInt(e.target.value || '1', 10));
        e.target.value = value;
        const cart = getCart();
        const item = cart.find(i => String(i.id) === String(id));
        if (item) { item.quantity = value; saveCart(cart); }
      });

      // set hidden cart on load too
      const initialCart = getCart();
      if (initialCart.length > 0) {
        document.getElementById('cart_data').value = JSON.stringify(initialCart);
      }
    });

    function openAbout() { document.getElementById("aboutPopup").style.display = "block"; }
    function closeAbout() { document.getElementById("aboutPopup").style.display = "none"; }
  </script>
</body>
</html>
