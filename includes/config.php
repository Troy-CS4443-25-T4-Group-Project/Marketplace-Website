<?php
/**
 * Configuration Settings for the Art Marketplace Website
 * This file contains all configuration settings for the application.
 */

// Set error reporting for debugging during development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Auto-detect environment based on server name
$is_production = (strpos($_SERVER['SERVER_NAME'] ?? '', 'localhost') === false && 
                  strpos($_SERVER['SERVER_NAME'] ?? '', '127.0.0.1') === false);

// Database configuration - automatically switches between production and local
if ($is_production) {
    // Production database
    define('DB_HOST', 'sql211.infinityfree.com');
    define('DB_NAME', 'if0_38904230_art_marketplace');
    define('DB_USER', 'if0_38904230');
    // Password removed for standard security practices
    // Uncomment the line below to set the password for production
    // define('DB_PASS', 'your_production_password_here');
    
    // Production site URL
    define('SITE_URL', 'https://troyjw.ct.ws/marketplace');
} else {
    // Local development database
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'art_marketplace');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    
    // Local site URL
    define('SITE_URL', 'http://localhost/Marketplace-Website');
}

// PDO connection options
$db_options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,       // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,  // Return associative arrays
    PDO::ATTR_EMULATE_PREPARES => false                // Use real prepared statements
];

// Site configuration
define('SITE_NAME', 'Art Market Place');

// File upload settings
define('UPLOAD_DIR', 'assets/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);
