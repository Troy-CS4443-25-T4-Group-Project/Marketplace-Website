<?php
/**
 * Configuration Settings for the Art Marketplace Website
 * This file contains all configuration settings for the application.
 */

// Set error reporting for debugging during development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
define('DB_HOST', 'localhost');      // Database host
define('DB_NAME', 'art_marketplace'); // Database name
define('DB_USER', 'root');           // Database username (default for XAMPP)
define('DB_PASS', '');               // Database password (blank for default XAMPP)

// PDO connection options
$db_options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,       // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,  // Return associative arrays
    PDO::ATTR_EMULATE_PREPARES => false                // Use real prepared statements
];

// Site configuration
define('SITE_NAME', 'Art Market Place');
define('SITE_URL', 'http://localhost/Marketplace-Website'); // Adjust as needed

// File upload settings
define('UPLOAD_DIR', 'assets/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);
