# CineVerse Movie Ticket Booking

## Run on XAMPP

1. Copy this folder to `C:\xampp\htdocs\movie-booking`.
2. Start Apache and MySQL from the XAMPP control panel.
3. In phpMyAdmin, import `database/movie_booking.sql`.
4. Open `http://localhost/movie-booking/`.

The default database connection is XAMPP's `root` account with a blank password; amend `config/database.php` if required.

Admin demo login: `admin@cineverse.test` / `admin123`.

## Included

- Dark, responsive Bootstrap cinema UI with animations, hero, search, movie information and trailer modal.
- Password-hashed sessions, registration/login/logout and a reset-password placeholder ready for SMTP integration.
- AJAX seating map (A–J / 1–12), maximum ten seats, transactional conflict prevention, tickets and QR entry code.
- Admin dashboard and Chart.js revenue view, movie, show, user, booking and review management foundations.
- Normalized MySQL schema with indexes, foreign keys, ten sample films and seeded theater/show data.

For production, point the QR endpoint at a local QR library, configure SMTP for actual email delivery, and use a payment gateway before marking a payment paid.
