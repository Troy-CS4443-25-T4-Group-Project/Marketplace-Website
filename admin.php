<?php
/**
 * Admin Page
 * This is a simple admin page for adding new artwork.
 * In a real application, this would have authentication.
 */

// Set error reporting for debugging during development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize variables
$page_title = 'Admin - Add Artwork';
$error_message = '';
$success_message = '';

// Include the admin page CSS
$extra_css = '<link rel="stylesheet" href="css/admin-styles.css">';

// Include database connection and functions
$conn = require_once 'includes/database.php';
require_once 'includes/functions.php';
require_once 'includes/display_helpers.php';

// Get all categories for the dropdown
$categories = getAllCategories($conn);

// Process form submission for adding new artwork
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_artwork'])) {
    // Add the artwork
    $result = addArtwork($conn, $_POST, $_FILES['artwork_image'] ?? null);
    
    // Set success or error message
    if ($result['success']) {
        $success_message = $result['message'];
    } else {
        $error_message = $result['message'];
    }
}

// Include the header
include 'includes/header.php';

// Define breadcrumbs for this page
$breadcrumbs = [
    ['text' => 'Home', 'url' => 'index.php'],
    ['text' => 'Admin']
];

// Display breadcrumbs
echo renderBreadcrumbs($breadcrumbs);
?>

<div class="main-content-wrapper">
    <div class="content admin-content">
        <div class="admin-header">
            <h2>Add New Artwork</h2>
            <p>Use this form to add new artwork to the gallery</p>
        </div>
        
        <!-- Add New Artwork Form -->
        <section class="add-artwork-section">
            <form action="admin.php" method="post" enctype="multipart/form-data" class="artwork-form">
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
                        <option value="<?= $category['category_id']; ?>"><?= htmlspecialchars($category['name']); ?></option>
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
                    <small>Recommended size: 800x600 pixels. Images will be stored in the art directory.</small>
                </div>
                
                <div class="form-group checkbox-group">
                    <input type="checkbox" id="is_featured" name="is_featured">
                    <label for="is_featured">Feature this artwork on homepage</label>
                </div>
                
                <button type="submit" name="add_artwork" class="btn-submit">Add Artwork</button>
            </form>
        </section>
        
        <a href="index.php" class="admin-back-link">← Back to Homepage</a>
    </div>
</div>

<?php
// Include the footer
include 'includes/footer.php';
?>
