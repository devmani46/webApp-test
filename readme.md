# User Profile Management System

This project is a simple user profile management system built using PHP and MySQL. It allows users to update their profile details, including name, email, phone number, password, and profile image. The system also handles user authentication and authorization via sessions.

## Features

- User registration and login system
- Update user profile details (name, email, phone, password, and image)
- Password validation and update functionality
- Image upload for profile picture
- Form validation and error handling
- Secure user data handling with prepared statements and sanitization

## File Structure

```plaintext
/ ── Root directory
│
├── config.php             // Database connection file with PDO setup
├── index.php              // Homepage displaying a welcome message or dashboard
├── login.php              // User login page
├── register.php           // User registration page
├── update_profile.php     // User profile update page
├── home.php               // User's home/dashboard page
├── uploaded_img/          // Directory where uploaded profile images are stored
│
├── css/
│   ├── style.css          // Custom CSS for styling the pages
│   └── login.css          // CSS specifically for the login page
│
└── images/
    └── default-avatar.png // Default avatar image shown when no profile picture is uploaded
