# iKarRental

A simple PHP-based car rental system with:
- Public homepage listing cars with filters (including date range).
- Registration/login for users to book cars.
- Date selection that blocks booked dates (bonus).
- AJAX-based booking confirmation without page refresh (bonus).
- Admin panel to add/edit/delete cars and manage bookings.

## Setup

1. Copy this folder to a PHP-capable webserver.
2. Ensure the `data/` folder (and its `.json` files) is writable (e.g. `chmod 666 data/*.json`).
3. Default admin credentials:
   - Email: admin@ikarrental.hu
   - Password: admin

## Files

- data/cars.json, data/users.json, data/bookings.json: JSON data
- inc/storage.php, inc/auth.php: logic for data handling and session/auth
- index.php: homepage with filters
- car_details.php: single car page, with date-based availability
- booking_handler.php: AJAX booking endpoint
- fetch_booked_dates.php: returns already-booked dates for a given car in JSON
- login.php, register.php, logout.php: user auth
- profile.php: user bookings
- admin.php: admin panel (shows all bookings, all cars)
- add_car.php, edit_car.php: admin functions for adding/editing cars
- css/style.css: minimal styling
- README.md: this file

## Usage

- Open `index.php` to view cars.
- Filter them by transmission, passengers, price, and date range.
- Click a car to see details and book it (must be logged in).
- Booking is handled via AJAX, showing a modal for success/failure.
- Admin sees admin panel at `admin.php`, can add/edit/delete cars, and delete bookings.

All minimum and core tasks are covered. Bonus tasks:
- Dates already booked are disabled in the datepicker (custom JS).
- Booking uses AJAX to show a custom modal without page refresh.

