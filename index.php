<?php
/**
 * Homepage
 * This is the main landing page for the Art Marketplace website.
 */

// Set error reporting for debugging during development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize variables
$page_title = 'Home';
$error_message = '';
$success_message = '';

// Include database connection
$conn = require_once 'includes/database.php';

// Get featured artworks from database
$featured_artworks = [];
try {
    $sql = "SELECT a.*, c.name as category_name
            FROM artworks a
            LEFT JOIN categories c ON a.category_id = c.category_id
            WHERE a.is_featured = 1
            LIMIT 6";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $featured_artworks = $stmt->fetchAll();
} catch (PDOException $e) {
    $error_message = "Error loading featured artworks: " . $e->getMessage();
}

// Get all categories for the sidebar
$categories = [];
try {
    $sql = "SELECT * FROM categories ORDER BY name";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    $error_message .= "Error loading categories: " . $e->getMessage();
}

// Process form submission for adding new artwork
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_artwork'])) {
    // Get form data
    $title = trim($_POST['title']);
    $artist_name = trim($_POST['artist_name']);
    $description = trim($_POST['description']);
    $category_id = (int) $_POST['category_id'];
    $price = (float) $_POST['price'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    // Image handling
    $image_url = '';
    if (isset($_FILES['artwork_image']) && $_FILES['artwork_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'art/';
        $temp_name = $_FILES['artwork_image']['tmp_name'];
        $original_name = $_FILES['artwork_image']['name'];
        $file_ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
        
        // Generate unique filename
        $new_filename = uniqid('artwork_') . '.' . $file_ext;
        $destination = $upload_dir . $new_filename;
        
        // Move the uploaded file
        if (move_uploaded_file($temp_name, $destination)) {
            $image_url = $destination;
        } else {
            $error_message = "Failed to upload image.";
        }
    }
    
    // Validate form data
    $errors = [];
    if (empty($title)) $errors[] = "Title is required";
    if (empty($artist_name)) $errors[] = "Artist name is required";
    if (empty($price)) $errors[] = "Price is required";
    
    // If validation passes, insert into database
    if (empty($errors)) {
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
            
            $success_message = "Artwork added successfully!";
            
            // Refresh featured artworks
            $sql = "SELECT a.*, c.name as category_name
                    FROM artworks a
                    LEFT JOIN categories c ON a.category_id = c.category_id
                    WHERE a.is_featured = 1
                    LIMIT 6";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $featured_artworks = $stmt->fetchAll();
        } catch (PDOException $e) {
            $error_message = "Database error: " . $e->getMessage();
        }
    } else {
        $error_message = "Please fix the following errors: " . implode(", ", $errors);
    }
}

// Include the header
include 'includes/header.php';
?>

<div class="content-container">
    <!-- Sidebar for categories -->
    <aside class="sidebar">
        <h2>Categories</h2>
        <ul class="category-list">
            <li><a href="index.php">All Categories</a></li>
            <?php foreach ($categories as $category): ?>
            <li><a href="gallery.php?category=<?= $category['category_id']; ?>"><?= $category['name']; ?></a></li>
            <?php endforeach; ?>
        </ul>
    </aside>
    
    <div class="main-content">
        <section class="featured-artworks">
            <h2>Featured Artwork</h2>
            
            <div class="artwork-grid">
                <?php if (empty($featured_artworks)): ?>
                    <p>No featured artwork available at this time.</p>
                <?php else: ?>
                    <?php foreach ($featured_artworks as $artwork): ?>
                    <div class="artwork-card">
                        <div class="artwork-image">
                            <?php if (!empty($artwork['image_url'])): ?>
                                <img src="<?= $artwork['image_url']; ?>" alt="<?= $artwork['title']; ?>">
                            <?php else: ?>
                                <img src="art/monaLisa.jpg" alt="Placeholder">
                            <?php endif; ?>
                        </div>
                        <div class="artwork-info">
                            <h3><?= $artwork['title']; ?></h3>
                            <p class="artist">by <?= $artwork['artist_name']; ?></p>
                            <p class="category"><?= $artwork['category_name']; ?></p>
                            <p class="price">$<?= number_format($artwork['price'], 2); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
        
        <!-- Add New Artwork Form -->
        <section class="add-artwork-section">
            <h2>Add New Artwork</h2>
            <form action="index.php" method="post" enctype="multipart/form-data" class="artwork-form">
                <div class="form-group">
                    <label for="title">Artwork Title:</label>
                    <input type="text" id="title" name="title" required>
                </div>
                
                <div class="form-group">
                    <label for="artist_name">Artist Name:</label>
                    <input type="text" id="artist_name" name="artist_name" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="4"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="category_id">Category:</label>
                    <select id="category_id" name="category_id">
                        <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['category_id']; ?>"><?= $category['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="price">Price ($):</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="artwork_image">Artwork Image:</label>
                    <input type="file" id="artwork_image" name="artwork_image" accept="image/*">
                </div>
                
                <div class="form-group checkbox-group">
                    <input type="checkbox" id="is_featured" name="is_featured">
                    <label for="is_featured">Feature this artwork on homepage</label>
                </div>
                
                <button type="submit" name="add_artwork" class="btn-submit">Add Artwork</button>
            </form>
        </section>
    </div>
</div>

<?php
// Include the footer
include 'includes/footer.php';
?>
