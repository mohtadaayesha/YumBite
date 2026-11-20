<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>YumBite | Signup</title>
  <link rel="icon" href="images/logo1.png" type="image/png">

  <link id="theme-style" rel="stylesheet" href="css/style-dark.css" />
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/signup.css" />
</head>

<body>
  <header>
    <nav>
      <div class="logo">
        <img src="images/logo1.png" alt="Logo1" class="logo1" />
        <img src="images/logo.png" alt="YumBite" />
      </div>

      <div class="hamburger" id="hamburger">
        <span></span>
        <span></span>
        <span></span>
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

  <div class="wrapper-container">
    <div class="wrapper">
      <?php
      if (isset($_POST["submit"])) {
        $fullName = $_POST["fullname"];
        $phone = $_POST["phone"];
        $email = $_POST["email"];
        $password = $_POST["password"];
        $confirmPassword = $_POST["confirm_password"];
        $address = $_POST["address"];
        $role = "user";

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $errors = array();

        if (empty($fullName) || empty($phone) || empty($email) || empty($password) || empty($confirmPassword) || empty($address)) {
          array_push($errors, "All Fields are required");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          array_push($errors, "Email is not valid");
        }

        if (substr($email, -10) !== "@gmail.com") {
          array_push($errors, "Only Gmail addresses are allowed");
        }

        if (strlen($password) < 8) {
          array_push($errors, "Password must be at least 8 characters long");
        }

        if ($password != $confirmPassword) {
          array_push($errors, "Password does not match");
        }

        require_once "database.php";

        $sql = "SELECT * FROM users WHERE email = '$email'";

        $result = mysqli_query($conn, $sql);

        $rowCount = mysqli_num_rows($result);

        if ($rowCount > 0) {
          array_push($errors, "Email already exists!");
        }

        if (count($errors) > 0) {
          foreach ($errors as $error) {
            echo "<div class='error'>$error</div>";
          }
        } else {
          $sql = "INSERT INTO users (full_name, phone, email, password, address, role) VALUES ( ?, ?, ?, ?, ?, ? )";
          $stmt = mysqli_stmt_init($conn);
          $prepareStmt = mysqli_stmt_prepare($stmt, $sql);

          if ($prepareStmt) {
            mysqli_stmt_bind_param($stmt, "ssssss", $fullName, $phone, $email, $passwordHash, $address, $role);
            mysqli_stmt_execute($stmt);
            echo "<div class='success'>Account created successfully</div>";
            header("Location: login.php");
          } else {
            die("Something went wrong");
          }
        }
      }
      ?>

      <form action="signup.php" method="post">
        <h1>Sign Up</h1>

        <div class="input-box">
          <input type="text" name="fullname" placeholder="Full Name" />
        </div>

        <div class="input-box">
          <input type="tel" name="phone" placeholder="Phone" />
        </div>

        <div class="input-box">
          <input type="email" name="email" placeholder="Email" />
        </div>

        <div class="input-box">
          <input type="password" name="password" placeholder="Password" />
        </div>

        <div class="input-box">
          <input type="password" name="confirm_password" placeholder="Confirm Password" />
        </div>

        <div class="input-box">
          <input type="text" name="address" placeholder="Address" />
        </div>

        <div>
          <input type="submit" value="Signup" name="submit" class="btn">
        </div>

        <div class="register-link">
          <p>Already have an account? <a href="login.php">Login</a></p>
        </div>
      </form>
    </div>
  </div>

  <!-- Triggered by clicking "About Us" link in nav -->
  <!-- About Us Popup -->
  <div class="popup" id="aboutPopup">
    <div class="popup-content">
      <span onclick="closeAbout()">&times;</span>
            <h2>About Us</h2>
            <p>
            Launched in 2025 in Chattogram, our platform was built to make food ordering simple, fast, and reliable.<br>
            We connect people with their favorite restaurants and ensure that delicious meals reach their doorstep with ease.<br><br>

            For customers, our service is more than just food delivery – it’s about discovering new tastes, exploring local favorites, and enjoying a hassle-free ordering experience.<br>
            With just a few clicks, anyone can find restaurants, browse menus, and place an order.<br><br>

            For restaurant partners, we provide a digital platform to expand their reach, attract new customers, and manage orders efficiently.<br>
            We believe in helping local businesses grow by giving them the tools to showcase their food to a wider audience.<br><br>

            For our delivery partners, we create flexible opportunities to earn while being part of a reliable and growing community.<br><br>

            At the heart of our mission is a simple promise:<br>
            <b>Bringing good food closer to you, anytime, anywhere in Chattogram.</b>
            </p>
    </div>
  </div>

  <footer>
    <p>&copy; 2025 YumBite. All Rights Reserved.</p>
  </footer>

  <script>
    // Hamburger Menu Toggle
    document.addEventListener('DOMContentLoaded', function () {
      const hamburger = document.getElementById("hamburger");
      const navLinks = document.getElementById("navLinks");

      // Toggle Hamburger Menu
      hamburger.addEventListener("click", function () {
        hamburger.classList.toggle("active");
        navLinks.classList.toggle("show");
      });

      // Close menu on link click (mobile UX)
      document.querySelectorAll('.nav-links a').forEach(link => {
        link.addEventListener('click', () => {
          hamburger.classList.remove('active');
          navLinks.classList.remove('show');
        });
      });
    });
  </script>

  <script>
    function openAbout() {
      document.getElementById("aboutPopup").style.display = "block";
    }

    function closeAbout() {
      document.getElementById("aboutPopup").style.display = "none";
    }
  </script>
</body>

</html>