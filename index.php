<?php

// Set error reporting for debugging during development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize variables
$page_title = 'Home';
$error_message = '';
$success_message = '';

// Include admin styles for the Add Artwork button
$extra_css = '<link rel="stylesheet" href="css/admin-styles.css">';

// Include database connection and functions
$conn = require_once 'includes/database.php';
require_once 'includes/functions.php';
require_once 'includes/display_helpers.php';

$featured_artworks = getFeaturedArtworks($conn);

// Get all categories for the sidebar
$categories = getAllCategories($conn);

// Process form submission for adding new artwork
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_artwork'])) {
    // Add the artwork
    $result = addArtwork($conn, $_POST, $_FILES['artwork_image'] ?? null);
    
    // Set success or error message
    if ($result['success']) {
        $success_message = $result['message'];
        // Refresh featured artworks
        $featured_artworks = getFeaturedArtworks($conn);
    } else {
        $error_message = $result['message'];
    }
}

include 'includes/header.php';

$breadcrumbs = [
    ['text' => 'Home']
];
echo renderBreadcrumbs($breadcrumbs);
?>

<div class="main-content-wrapper">
    <?php 
    // Display category sidebar
    echo renderCategorySidebar($categories);
    ?>
    
    <div class="content">
        <section class="featured-artworks">
            <h2>Featured Artwork</h2>
            
            <div class="artwork-grid">
                <?php if (empty($featured_artworks)): ?>
                    <p>No featured artwork available at this time.</p>
                <?php else: ?>
                    <?php echo renderArtworkGrid($featured_artworks); ?>
                <?php endif; ?>
            </div>
        </section>
    </div> <!-- end .content -->
</div> <!-- end .main-content-wrapper -->

<div class="add-artwork-button-container">
    <a href="admin.php" class="add-artwork-button">Add New Artwork</a>
</div>

<?php
include 'includes/footer.php';
?>
