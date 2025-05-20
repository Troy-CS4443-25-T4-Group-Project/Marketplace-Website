<?php
/**
 * Common Functions
 * This file contains reusable functions used across the website
 */

/**
 * Get artwork details by ID
 * 
 * @param PDO $conn Database connection
 * @param int $artwork_id The ID of the artwork to retrieve
 * @return array|false Artwork data or false if not found
 */
function getArtworkById($conn, $artwork_id) {
    try {
        $sql = "SELECT a.*, c.name as category_name
                FROM artworks a
                LEFT JOIN categories c ON a.category_id = c.category_id
                WHERE a.artwork_id = :artwork_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':artwork_id', $artwork_id);
        $stmt->execute();
        return $stmt->fetch();
    } catch (PDOException $e) {
        logError("Error fetching artwork: " . $e->getMessage());
        return false;
    }
}

/**
 * Get all categories
 * 
 * @param PDO $conn Database connection
 * @return array Categories data
 */
function getAllCategories($conn) {
    try {
        $sql = "SELECT * FROM categories ORDER BY name";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        logError("Error fetching categories: " . $e->getMessage());
        return [];
    }
}

/**
 * Get featured artworks
 * 
 * @param PDO $conn Database connection
 * @param int $limit Maximum number of artworks to return
 * @return array Featured artworks data
 */
function getFeaturedArtworks($conn, $limit = 6) {
    try {
        $sql = "SELECT a.*, c.name as category_name
                FROM artworks a
                LEFT JOIN categories c ON a.category_id = c.category_id
                WHERE a.is_featured = 1
                LIMIT :limit";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        logError("Error fetching featured artworks: " . $e->getMessage());
        return [];
    }
}

/**
 * Get artworks with optional filtering by category and/or artist
 * 
 * @param PDO $conn Database connection
 * @param int|null $category_id Category ID to filter by (optional)
 * @param string|null $artist Artist name to filter by (optional)
 * @return array Artworks data
 */
function getArtworks($conn, $category_id = null, $artist = null) {
    try {
        $params = [];
        
        if ($category_id && $artist) {
            // Filter by both category and artist
            $sql = "SELECT a.*, c.name as category_name
                    FROM artworks a
                    LEFT JOIN categories c ON a.category_id = c.category_id
                    WHERE a.category_id = :category_id AND a.artist_name = :artist_name
                    ORDER BY a.created_at DESC";
            $params[':category_id'] = $category_id;
            $params[':artist_name'] = $artist;
        } else if ($category_id) {
            // Filter by category only
            $sql = "SELECT a.*, c.name as category_name
                    FROM artworks a
                    LEFT JOIN categories c ON a.category_id = c.category_id
                    WHERE a.category_id = :category_id
                    ORDER BY a.created_at DESC";
            $params[':category_id'] = $category_id;
        } else if ($artist) {
            // Filter by artist only
            $sql = "SELECT a.*, c.name as category_name
                    FROM artworks a
                    LEFT JOIN categories c ON a.category_id = c.category_id
                    WHERE a.artist_name = :artist_name
                    ORDER BY a.created_at DESC";
            $params[':artist_name'] = $artist;
        } else {
            // No filters
            $sql = "SELECT a.*, c.name as category_name
                    FROM artworks a
                    LEFT JOIN categories c ON a.category_id = c.category_id
                    ORDER BY a.created_at DESC";
        }
        
        $stmt = $conn->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        logError("Error fetching artworks: " . $e->getMessage());
        return [];
    }
}

/**
 * Add a new artwork to the database
 * 
 * @param PDO $conn Database connection
 * @param array $artwork_data Artwork data (title, artist_name, description, etc.)
 * @param array $image_file Image file data ($_FILES['artwork_image'])
 * @return array Result with success status and message
 */
function addArtwork($conn, $artwork_data, $image_file = null) {
    // Initialize result
    $result = [
        'success' => false,
        'message' => '',
        'errors' => []
    ];
    
    // Extract artwork data
    $title = trim($artwork_data['title'] ?? '');
    $artist_name = trim($artwork_data['artist_name'] ?? '');
    $description = trim($artwork_data['description'] ?? '');
    $category_id = (int) ($artwork_data['category_id'] ?? 0);
    $price = (float) ($artwork_data['price'] ?? 0);
    $is_featured = isset($artwork_data['is_featured']) ? 1 : 0;
    
    // Validate data
    if (empty($title)) $result['errors'][] = "Title is required";
    if (empty($artist_name)) $result['errors'][] = "Artist name is required";
    if (empty($price)) $result['errors'][] = "Price is required";
    
    // If validation fails, return with errors
    if (!empty($result['errors'])) {
        $result['message'] = "Please fix the following errors: " . implode(", ", $result['errors']);
        return $result;
    }
    
    // Process image upload if provided
    $image_url = '';
    if ($image_file && isset($image_file['error']) && $image_file['error'] === UPLOAD_ERR_OK) {
        $upload_result = handleImageUpload($image_file);
        
        if ($upload_result['success']) {
            $image_url = $upload_result['file_path'];
        } else {
            $result['message'] = $upload_result['message'];
            return $result;
        }
    }
    
    // Insert artwork into database
    try {
        $sql = "INSERT INTO artworks (title, artist_name, description, category_id, price, image_url, is_featured)
                VALUES (:title, :artist_name, :description, :category_id, :price, :image_url, :is_featured)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':artist_name', $artist_name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':image_url', $image_url);
        $stmt->bindParam(':is_featured', $is_featured);
        $stmt->execute();
        
        $result['success'] = true;
        $result['message'] = "Artwork added successfully!";
        return $result;
    } catch (PDOException $e) {
        $result['message'] = "Database error: " . $e->getMessage();
        logError($result['message']);
        return $result;
    }
}

/**
 * Handle image upload for artworks
 * 
 * @param array $file The uploaded file ($_FILES['artwork_image'])
 * @return array Result with success status, message, and file path
 */
function handleImageUpload($file) {
    $result = [
        'success' => false,
        'message' => '',
        'file_path' => ''
    ];
    
    // Check if file was uploaded without errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $result['message'] = "Upload error: " . getUploadErrorMessage($file['error']);
        return $result;
    }
    
    // Get file info
    $temp_name = $file['tmp_name'];
    $original_name = $file['name'];
    $file_ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
    $file_size = $file['size'];
    
    // Validate file type
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($file_ext, $allowed_extensions)) {
        $result['message'] = "Invalid file type. Allowed types: " . implode(', ', $allowed_extensions);
        return $result;
    }
    
    // Validate file size (max 5MB)
    $max_size = 5 * 1024 * 1024; // 5MB
    if ($file_size > $max_size) {
        $result['message'] = "File is too large. Maximum size: 5MB";
        return $result;
    }
    
    // Generate unique filename
    $upload_dir = 'art/';
    $new_filename = uniqid('artwork_') . '.' . $file_ext;
    $destination = $upload_dir . $new_filename;
    
    // Move the uploaded file
    if (move_uploaded_file($temp_name, $destination)) {
        $result['success'] = true;
        $result['file_path'] = $destination;
        return $result;
    } else {
        $result['message'] = "Failed to move uploaded file.";
        return $result;
    }
}

/**
 * Get a human-readable error message for file upload errors
 * 
 * @param int $error_code The error code from $_FILES['file']['error']
 * @return string Error message
 */
function getUploadErrorMessage($error_code) {
    switch ($error_code) {
        case UPLOAD_ERR_INI_SIZE:
            return "The uploaded file exceeds the upload_max_filesize directive in php.ini";
        case UPLOAD_ERR_FORM_SIZE:
            return "The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form";
        case UPLOAD_ERR_PARTIAL:
            return "The uploaded file was only partially uploaded";
        case UPLOAD_ERR_NO_FILE:
            return "No file was uploaded";
        case UPLOAD_ERR_NO_TMP_DIR:
            return "Missing a temporary folder";
        case UPLOAD_ERR_CANT_WRITE:
            return "Failed to write file to disk";
        case UPLOAD_ERR_EXTENSION:
            return "A PHP extension stopped the file upload";
        default:
            return "Unknown upload error";
    }
}

/**
 * Get the current category name based on filter
 * 
 * @param array $categories All categories
 * @param int|null $category_id Selected category ID (optional)
 * @param string|null $artist Selected artist name (optional)
 * @return string Current category name
 */
function getCurrentCategoryName($categories, $category_id = null, $artist = null) {
    if ($category_id && $artist) {
        // Both category and artist are specified
        foreach ($categories as $category) {
            if ($category['category_id'] == $category_id) {
                return "Artwork by " . htmlspecialchars($artist) . " in " . $category['name'];
            }
        }
    } else if ($category_id) {
        // Only category is specified
        foreach ($categories as $category) {
            if ($category['category_id'] == $category_id) {
                return $category['name'];
            }
        }
    } else if ($artist) {
        // Only artist is specified
        return "Artwork by " . htmlspecialchars($artist);
    }
    
    // Default
    return "All Artworks";
}

/**
 * Log an error message
 * 
 * @param string $message Error message to log
 * @return void
 */
function logError($message) {
    // For now, just set a global error message
    // In a real application, you might want to log to a file or database
    $GLOBALS['error_message'] = $message;
}
