<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link id="theme-style" rel="stylesheet" href="css/style-dark.css" />
    <link rel="stylesheet" href="css/style.css" />
    <title>Document</title>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>

    <script>
        // Theme Switcher
        function switchTheme(path) {
            document.getElementById("theme-style").setAttribute("href", path);
            localStorage.setItem("theme", path);
        }

        // Hamburger Menu Toggle
        document.addEventListener('DOMContentLoaded', function () {
            const hamburger = document.getElementById("hamburger");
            const navLinks = document.getElementById("navLinks");

            // Restore Theme
            const savedTheme = localStorage.getItem("theme");
            if (savedTheme) {
                switchTheme(savedTheme);
            }

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

</body>

</html>