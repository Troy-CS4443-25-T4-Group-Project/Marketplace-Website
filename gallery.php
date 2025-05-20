<?php
/**
 * Gallery Page
 * This page displays all artworks with category filtering.
 */

// Set error reporting for debugging during development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize variables
$page_title = 'Gallery';
$error_message = '';
$success_message = '';
$artworks = [];
$categories = [];
$current_category_name = "All Artworks";

// Include database connection and functions
$conn = require_once 'includes/database.php';
require_once 'includes/functions.php';
require_once 'includes/display_helpers.php';

// Get filters from URL if present
$category_id = isset($_GET['category']) ? (int) $_GET['category'] : null;
$artist = isset($_GET['artist']) ? $_GET['artist'] : null;

// Get artworks from database, filtered by category or artist if specified
$artworks = getArtworks($conn, $category_id, $artist);

// Get all categories for the sidebar
$categories = getAllCategories($conn);

// Set up the page title based on filters
$current_category_name = getCurrentCategoryName($categories, $category_id, $artist);

$extra_css = '<link rel="stylesheet" href="css/gallery-styles.css">';


include 'includes/header.php';


$breadcrumbs = [
    ['text' => 'Home', 'url' => 'index.php'],
    ['text' => 'Gallery']
];
echo renderBreadcrumbs($breadcrumbs);
?>

<div class="main-content-wrapper">
    <?php 
    // Display category sidebar
    echo renderCategorySidebar($categories, $category_id);
    ?>
    
    <div class="content">
        <div class="gallery-header">
            <h2><?= $current_category_name; ?></h2>
        </div>
        
        <div class="artwork-grid">
            <?php 
            // Display artwork grid
            echo renderArtworkGrid($artworks);
            ?>
        </div>
    </div> <!-- end .content -->
</div> <!-- end .main-content-wrapper -->

<?php
// Include the footer
include 'includes/footer.php';
?>
