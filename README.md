
# Locate Med

Locate Med is a PHP and MySQL web application for finding medicines at nearby pharmacies. Users can register, sign in, allow GPS access, search for a medicine, and view pharmacies with available stock. Pharmacists can register their pharmacy and manage medicine inventory.

## Technology

- PHP
- MySQL/MariaDB
- HTML and CSS
- JavaScript for browser GPS access and search results
- Apache through XAMPP
- Remix Icon CDN

## Project Structure

```text
locate Med 2/
├── backend/
│   ├── add_medicine.php
│   ├── db_connect.php
│   ├── login.php
│   ├── logout.php
│   ├── register-pharmacist.php
│   ├── register-user.php
│   └── search-medicine.php
├── frontend/
│   ├── index.php
│   ├── index.js
│   ├── login.html
│   ├── pharmacist-dashboard.php
│   ├── user-dashboard.php
│   ├── signup-pharmacist.html
│   └── signup-user.html
└── README.md
```

## Requirements

- Windows with XAMPP installed
- Apache enabled in the XAMPP Control Panel
- MySQL enabled in the XAMPP Control Panel
- A browser with location access enabled

## Installation

1. Copy the project folder into:

	```text
	C:\xampp\htdocs\locate Med 2
	```

2. Start **Apache** and **MySQL** in XAMPP.

3. Open phpMyAdmin at:

	```text
	http://localhost/phpmyadmin
	```

4. Create a database named `locatemed_db`.

5. Create the required tables. The current PHP files expect these columns:

	```sql
	CREATE DATABASE IF NOT EXISTS locatemed_db;
	USE locatemed_db;

	CREATE TABLE users (
		 id INT AUTO_INCREMENT PRIMARY KEY,
		 role ENUM('user', 'pharmacist') NOT NULL,
		 full_name VARCHAR(150) NOT NULL,
		 email VARCHAR(150) NOT NULL UNIQUE,
		 phone VARCHAR(30) NOT NULL,
		 password_hash VARCHAR(255) NOT NULL
	);

	CREATE TABLE pharmacies (
		 id INT AUTO_INCREMENT PRIMARY KEY,
		 user_id INT NOT NULL,
		 pharmacy_name VARCHAR(150) NOT NULL,
		 license_number VARCHAR(100) NOT NULL,
		 address VARCHAR(255) NOT NULL,
		 city VARCHAR(100) NOT NULL,
		 pincode VARCHAR(20) NOT NULL,
		 latitude DECIMAL(10, 7) NOT NULL,
		 longitude DECIMAL(10, 7) NOT NULL,
		 FOREIGN KEY (user_id) REFERENCES users(id)
	);

	CREATE TABLE medicines (
		 id INT AUTO_INCREMENT PRIMARY KEY,
		 pharmacy_id INT NOT NULL,
		 name VARCHAR(150) NOT NULL,
		 category VARCHAR(100) NOT NULL,
		 quantity INT NOT NULL DEFAULT 0,
		 price DECIMAL(10, 2) NOT NULL DEFAULT 0,
		 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
		 FOREIGN KEY (pharmacy_id) REFERENCES pharmacies(id)
	);
	```

6. Confirm the connection settings in [`backend/db_connect.php`](backend/db_connect.php). The default configuration is:

	```text
	Host: localhost
	User: root
	Password: empty
	Database: locatemed_db
	```

## Running the Application

Open the landing page:

```text
http://localhost/locate%20Med%202/frontend/index.php
```

You can also open the login page directly:

```text
http://localhost/locate%20Med%202/frontend/login.html
```

## User Workflow

1. Open **Sign-up** and create a user account.
2. Log in as **Simple User**.
3. Click the GPS button and allow browser location access.
4. Enter a medicine name or select a popular medicine.
5. Click **Search Now**.
6. Results are matched against medicines with a quantity greater than zero and ordered by distance.

The search requires latitude and longitude. The browser GPS button supplies these values before the search request is sent.

## Pharmacist Workflow

1. Register as a pharmacist and provide the pharmacy location.
2. Log in as **Pharmacist**.
3. Add medicine name, category, quantity, and price.
4. Existing medicine records for the pharmacy are updated by adding the new quantity.
5. Medicines with quantity greater than zero appear in user searches.

## Important Files

- [`frontend/index.php`](frontend/index.php): landing page.
- [`frontend/login.html`](frontend/login.html): login form.
- [`frontend/user-dashboard.php`](frontend/user-dashboard.php): user search interface and result rendering.
- [`frontend/pharmacist-dashboard.php`](frontend/pharmacist-dashboard.php): pharmacist inventory interface.
- [`backend/search-medicine.php`](backend/search-medicine.php): authenticated medicine search endpoint and distance calculation.
- [`backend/add_medicine.php`](backend/add_medicine.php): saves pharmacist inventory.
- [`backend/db_connect.php`](backend/db_connect.php): MySQL connection settings.

## Troubleshooting

### Page redirects to login

Log in first. The dashboards and search endpoint require a PHP session with `user_id`.

### Search returns no results

Check that:

- GPS access was allowed.
- The medicine exists in the `medicines` table.
- `quantity` is greater than zero.
- The pharmacy has valid latitude and longitude values.
- Apache and MySQL are running.

### Database connection error

Confirm that the database is named `locatemed_db` and that the credentials in `backend/db_connect.php` match your XAMPP MySQL configuration.

### Browser still shows old CSS or JavaScript

Use `Ctrl+F5` to perform a hard refresh. The dashboard stylesheet includes a version query so updated CSS is fetched by the browser.

## Security Note

This is a development project. Before production use, database queries should consistently use prepared statements, input validation should be strengthened, and HTTPS should be enabled.
=======


