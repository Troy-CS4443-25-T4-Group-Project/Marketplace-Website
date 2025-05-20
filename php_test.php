<?php
// Simple test file to check if PHP is working correctly
// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start output buffer to prevent header issues
ob_start();

// Style for the page
echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>PHP Test</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            line-height: 1.6; 
            max-width: 900px; 
            margin: 0 auto; 
            padding: 20px; 
        }
        h1 { color: #2c3e50; }
        h2 { 
            color: #3498db; 
            margin-top: 30px; 
            border-bottom: 1px solid #eee; 
            padding-bottom: 10px; 
        }
        .success { color: #27ae60; }
        .error { color: #e74c3c; }
        .warning { color: #f39c12; }
        .info-box {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
        }
        code { 
            background-color: #f8f9fa; 
            padding: 2px 5px; 
            border-radius: 3px; 
        }
        hr { margin: 30px 0; }
        .nav-links {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        .nav-links a {
            display: inline-block;
            padding: 8px 15px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin: 15px 0;
        }
        th, td {
            text-align: left;
            padding: 8px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>PHP Diagnostic Tool</h1>
    <p>This page provides diagnostic information about your PHP and database configuration.</p>";

// Show PHP information
echo "<h2>PHP Environment</h2>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Server Software:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>Script Path:</strong> " . $_SERVER['SCRIPT_FILENAME'] . "</p>";

// Check if essential PHP extensions are enabled
echo "<h3>PHP Extensions</h3>";
echo "<table>
    <tr>
        <th>Extension</th>
        <th>Status</th>
    </tr>";
$required_extensions = ['pdo', 'pdo_mysql', 'mysqli', 'session', 'json'];
foreach ($required_extensions as $ext) {
    $loaded = extension_loaded($ext);
    $status_class = $loaded ? 'success' : 'error';
    $status_text = $loaded ? 'Enabled' : 'Disabled';
    echo "<tr>
        <td>$ext</td>
        <td class='$status_class'>$status_text</td>
    </tr>";
}
echo "</table>";

// Check file permissions for important directories
echo "<h3>Directory Permissions</h3>";
echo "<table>
    <tr>
        <th>Directory</th>
        <th>Exists</th>
        <th>Readable</th>
        <th>Writable</th>
    </tr>";
$directories = [
    'Root Directory' => dirname(__FILE__),
    'Images Directory' => dirname(__FILE__) . '/images',
    'Art Directory' => dirname(__FILE__) . '/art'
];
foreach ($directories as $name => $path) {
    $exists = file_exists($path);
    $readable = $exists && is_readable($path);
    $writable = $exists && is_writable($path);
    echo "<tr>
        <td>$name</td>
        <td class='" . ($exists ? 'success' : 'error') . "'>" . ($exists ? 'Yes' : 'No') . "</td>
        <td class='" . ($readable ? 'success' : 'error') . "'>" . ($readable ? 'Yes' : 'No') . "</td>
        <td class='" . ($writable ? 'success' : 'error') . "'>" . ($writable ? 'Yes' : 'No') . "</td>
    </tr>";
}
echo "</table>";

// Test database connection
echo "<h2>Database Connection Test</h2>";

// Include database configuration
require_once 'db_config.php';

echo "<h3>Database Configuration</h3>";
echo "<table>
    <tr>
        <th>Parameter</th>
        <th>Value</th>
    </tr>
    <tr>
        <td>Host</td>
        <td>$db_host</td>
    </tr>
    <tr>
        <td>Database Name</td>
        <td>$db_name</td>
    </tr>
    <tr>
        <td>Username</td>
        <td>$db_user</td>
    </tr>
    <tr>
        <td>Password</td>
        <td>" . (empty($db_pass) ? "<span class='warning'>Empty (this is normal for XAMPP defaults)</span>" : "******") . "</td>
    </tr>
</table>";

// Test direct database connection
echo "<h3>Direct Database Connection Test</h3>";
try {
    // First try connecting to the MySQL server without specifying a database
    $test_conn = new PDO("mysql:host=$db_host", $db_user, $db_pass, $db_options);
    echo "<p class='success'>Successfully connected to MySQL server!</p>";
    
    // Try to select the database
    try {
        $test_conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass, $db_options);
        echo "<p class='success'>Successfully connected to '$db_name' database!</p>";
        
        // Test query execution
        $stmt = $test_conn->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo "<p><strong>Tables in database:</strong></p>";
        echo "<ul>";
        if (count($tables) > 0) {
            foreach ($tables as $table) {
                echo "<li>$table</li>";
            }
        } else {
            echo "<li class='warning'>No tables found in database. The auto-initialization might not have run yet.</li>";
        }
        echo "</ul>";
        
    } catch (PDOException $e) {
        echo "<p class='error'>Could not connect to database: " . $e->getMessage() . "</p>";
        
        // Try to create the database
        echo "<p>Attempting to create database...</p>";
        try {
            $test_conn->exec("CREATE DATABASE IF NOT EXISTS `$db_name`");
            echo "<p class='success'>Database '$db_name' created successfully!</p>";
            
            // Try connecting again
            $test_conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass, $db_options);
            echo "<p class='success'>Successfully connected to newly created database!</p>";
            
        } catch (PDOException $e2) {
            echo "<p class='error'>Failed to create database: " . $e2->getMessage() . "</p>";
        }
    }
} catch (PDOException $e) {
    echo "<p class='error'>MySQL connection failed: " . $e->getMessage() . "</p>";
    echo "<div class='info-box'>
        <h4>Common Connection Issues:</h4>
        <ul>
            <li>MySQL service is not running</li>
            <li>Invalid username or password</li>
            <li>Hostname is incorrect</li>
        </ul>
    </div>";
}

// Check db_connect.php integration
echo "<h3>Testing Connection Through db_connect.php</h3>";
try {
    // Access global connection variable
    global $conn;
    
    // Include the connection file
    include_once 'db_connect.php';
    
    if (isset($conn) && $conn instanceof PDO) {
        echo "<p class='success'>Connection established through db_connect.php!</p>";
        
        // Test query
        try {
            $stmt = $conn->query("SELECT 1");
            echo "<p class='success'>Query execution successful!</p>";
        } catch (PDOException $e) {
            echo "<p class='error'>Query failed: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p class='error'>Connection variable not available after including db_connect.php</p>";
        echo "<p>Type of \$conn: " . (isset($conn) ? gettype($conn) : "undefined") . "</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>Error including db_connect.php: " . $e->getMessage() . "</p>";
}

// Navigation links
echo "<hr>";
echo "<div class='nav-links'>
    <a href='index.php'>Homepage</a>
    <a href='gallery.php'>Gallery</a>
    <a href='cart.php'>Shopping Cart</a>
    <a href='database_test.php'>Database Test</a>
</div>";

echo "</body></html>";

// End output buffer and send to browser
ob_end_flush();
?>
