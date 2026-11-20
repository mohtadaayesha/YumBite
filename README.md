# YumBite – Food Ordering and Delivery Website

YumBite is a web-based food ordering and delivery platform developed using PHP, MySQL, HTML, CSS, and JavaScript. It provides users with an easy-to-use interface to explore food items, browse restaurants, add food to their carts, and place food orders.

The platform also includes an administrative panel for managing food items, restaurants, and customer orders.

## Technologies Used

- **Frontend:** HTML5, CSS3, JavaScript
- **Backend:** PHP
- **Database:** MySQL
- **Development Environment:** XAMPP
- **Additional Resources:** Font Awesome

## Features

### User Features
- User registration and login
- Browse food menus
- Explore food categories and cuisines
- Search and filter food items
- Add food items to the shopping cart
- Place food orders
- View user profile
- Track order status
- Submit contact messages and reviews

### Admin Features
- Admin dashboard
- Manage food items
- Manage menus and categories
- Manage restaurant information
- View and manage customer orders
- Monitor customer contact submissions

## Project Structure

```text
YumBite/
│
├── css/
├── images/
├── uploads/
│
├── add_food.php
├── admin.php
├── admin_dashboard.php
├── admin_menu.php
├── admin_orders.php
├── cart.php
├── contact.php
├── database.php
├── index.php
├── login.php
├── logout.php
├── menu.php
├── navbar.php
├── profile.php
├── signup.php
├── tracking.php
├── user_orders.php
├── user_reviews.php
│
├── yum.sql
└── README.md
```

## ⚙️ Installation and Setup

### Prerequisites

- [XAMPP](https://www.apachefriends.org/)
- Web browser
- Visual Studio Code or another code editor

### Step 1: Clone the Repository

```bash
git clone YOUR_REPOSITORY_URL
```

Alternatively, download the repository as a ZIP file and extract it.

### Step 2: Move the Project into XAMPP

Copy the project folder into:

```text
C:\xampp\htdocs\
```

### Step 3: Start XAMPP

1. Open XAMPP Control Panel.
2. Start Apache.
3. Start MySQL.

### Step 4: Create and Import the Database

1. Open `http://localhost/phpmyadmin/`.
2. Create a database named `yum`.
3. Select the database.
4. Click the **Import** tab.
5. Select the `yum.sql` file.
6. Click **Import** or **Go**.

### Step 5: Configure Database Connection

Open `database.php` and verify the database connection settings:

```php
$hostName = "localhost";
$dbUser = "root";
$dbPassword = "";
$dbName = "yum";
```

Update the credentials if necessary.

### Step 6: Run the Project

Open your browser and visit:

```text
http://localhost/YumBite/
```

## Database Information

The project includes a MySQL database export named `yum.sql`.

Main database tables include:

- `users`
- `food`
- `categories`
- `cuisines`
- `restaurants`
- `orders`
- `contact`

Import the database before accessing database-dependent features.

## Development Notes

- The project is configured for local PHP and MySQL development.
- Food images and visual assets are stored in the `images/` directory.
- Uploaded images are stored in the `uploads/` directory.
- Database connectivity is handled through `database.php`.

## Project Information

**Project Name:** YumBite

**Project Type:** Food Ordering and Delivery Website

**Technologies:** PHP, MySQL, HTML, CSS and JavaScript

## License

No license has currently been specified for this project. Please contact the project owner before reusing or distributing its code and assets.
