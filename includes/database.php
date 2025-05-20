<?php
/**
 * Database Connection File
 * This file handles the database connection and initializes the database if needed.
 */

// Include configuration
require_once 'config.php';

/**
 * Get a database connection
 * This function creates a PDO connection to the database.
 * If the database doesn't exist, it creates it and initializes the tables.
 * 
 * @return PDO The database connection
 */
function getConnection() {
    static $conn = null;
    
    if ($conn === null) {
        try {
            // First connect without specifying the database
            $conn = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS, $GLOBALS['db_options']);
            
            // Create the database if it doesn't exist
            $conn->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "`");
            
            // Now connect to the specific database
            $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS, $GLOBALS['db_options']);
            
            // Initialize the database if needed
            initializeDatabase($conn);
            
        } catch(PDOException $e) {
            die("Database Connection Failed: " . $e->getMessage());
        }
    }
    
    return $conn;
}

/**
 * Initialize the database tables and insert sample data if needed
 * 
 * @param PDO $conn The database connection
 * @return bool Success status
 */
function initializeDatabase($conn) {
    try {
        // Check if our tables already exist
        $stmt = $conn->query("SHOW TABLES LIKE 'artworks'");
        $tableExists = $stmt->rowCount() > 0;
        
        // If tables don't exist, create them and insert sample data
        if (!$tableExists) {
            // Create and populate database
            createTables($conn);
            insertSampleData($conn);
        }
        return true;
    } catch (PDOException $e) {
        // No need to output HTML directly here, just set the error message
        $GLOBALS['error_message'] = "Database initialization error: " . $e->getMessage();
        return false;
    }
}

/**
 * Create database tables
 * 
 * @param PDO $conn The database connection
 * @return void
 */
function createTables($conn) {
    // Create categories table
    $conn->exec("
        CREATE TABLE categories (
            category_id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(50) NOT NULL UNIQUE,
            description TEXT
        )
    ");
    
    // Create artworks table
    $conn->exec("
        CREATE TABLE artworks (
            artwork_id INT PRIMARY KEY AUTO_INCREMENT,
            title VARCHAR(100) NOT NULL,
            artist_name VARCHAR(100) NOT NULL,
            description TEXT,
            category_id INT,
            price DECIMAL(10,2) NOT NULL,
            image_url VARCHAR(255),
            is_featured BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE SET NULL
        )
    ");
    
    // Create indexes
    $conn->exec("CREATE INDEX idx_artworks_price ON artworks(price)");
    $conn->exec("CREATE INDEX idx_artworks_category ON artworks(category_id)");
    $conn->exec("CREATE INDEX idx_artworks_featured ON artworks(is_featured)");
}

/**
 * Insert sample data into the database
 * 
 * @param PDO $conn The database connection
 * @return void
 */
function insertSampleData($conn) {
    // Insert sample categories
    $categories = [
        ['name' => 'Paintings', 'description' => 'Traditional painted artwork on canvas, paper, or other media'],
        ['name' => 'Sculptures', 'description' => 'Three-dimensional art objects'],
        ['name' => 'Photography', 'description' => 'Photographic prints and digital photography'],
        ['name' => 'Digital Art', 'description' => 'Art created or presented using digital technology'],
        ['name' => 'Mixed Media', 'description' => 'Artwork incorporating multiple materials or techniques']
    ];
    
    $stmt = $conn->prepare("INSERT INTO categories (name, description) VALUES (:name, :description)");
    
    foreach ($categories as $category) {
        $stmt->bindParam(':name', $category['name']);
        $stmt->bindParam(':description', $category['description']);
        $stmt->execute();
    }
    
    // Insert sample artworks
    $artworks = [
        [
            'title' => 'Starry Night Reimagined',
            'artist_name' => 'Emily Johnson',
            'description' => 'A modern take on Van Gogh\'s classic, with vibrant blues and swirling patterns.',
            'category_id' => 1, // Paintings
            'price' => 1200.00,
            'image_url' => 'art/painting1.jpg',
            'is_featured' => true
        ],
        [
            'title' => 'Urban Landscape',
            'artist_name' => 'Marcus Chen',
            'description' => 'A photographic series capturing the contrast between nature and urban development.',
            'category_id' => 3, // Photography
            'price' => 800.00,
            'image_url' => 'art/photo1.jpg',
            'is_featured' => true
        ],
        [
            'title' => 'Abstract Emotions',
            'artist_name' => 'Sophia Rodriguez',
            'description' => 'A digital artwork exploring the range of human emotions through color and form.',
            'category_id' => 4, // Digital Art
            'price' => 650.00,
            'image_url' => 'art/digital2.jpg',
            'is_featured' => true
        ],
        [
            'title' => 'Marble Serenity',
            'artist_name' => 'David Kim',
            'description' => 'A sculpture carved from white marble, depicting tranquility and peace.',
            'category_id' => 2, // Sculptures
            'price' => 2500.00,
            'image_url' => 'art/sculpture1.jpg',
            'is_featured' => false
        ],
        [
            'title' => 'Spring Meadow',
            'artist_name' => 'Lisa Taylor',
            'description' => 'A vibrant landscape showcasing a colorful spring meadow in full bloom.',
            'category_id' => 1, // Paintings
            'price' => 950.00,
            'image_url' => 'art/painting2.jpg',
            'is_featured' => false
        ],
        [
            'title' => 'Creative Fusion',
            'artist_name' => 'Michael Brown',
            'description' => 'A mixed media piece blending various techniques and materials.',
            'category_id' => 5, // Mixed Media
            'price' => 1100.00,
            'image_url' => 'art/mixed1.jpg',
            'is_featured' => false
        ]
    ];
    
    $stmt = $conn->prepare("
        INSERT INTO artworks (
            title, artist_name, description, category_id, price, image_url, is_featured
        ) VALUES (
            :title, :artist_name, :description, :category_id, :price, :image_url, :is_featured
        )
    ");
    
    foreach ($artworks as $artwork) {
        $stmt->bindParam(':title', $artwork['title']);
        $stmt->bindParam(':artist_name', $artwork['artist_name']);
        $stmt->bindParam(':description', $artwork['description']);
        $stmt->bindParam(':category_id', $artwork['category_id']);
        $stmt->bindParam(':price', $artwork['price']);
        $stmt->bindParam(':image_url', $artwork['image_url']);
        $stmt->bindParam(':is_featured', $artwork['is_featured'], PDO::PARAM_BOOL);
        $stmt->execute();
    }
}

// Get a database connection
$conn = getConnection();

// Return the connection
return $conn;
