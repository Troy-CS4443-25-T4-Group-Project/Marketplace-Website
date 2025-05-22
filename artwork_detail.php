<?php
/**
 * Artwork Detail Page
 * Displays detailed information about a specific artwork in a card format.
 * Acts as a configurable template that maps to any item from the database.
 */

// Set error reporting for debugging during development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize variables
$error_message = '';
$artwork = null;

// Include database connection and functions
$conn = require_once 'includes/database.php';
require_once 'includes/functions.php';
require_once 'includes/display_helpers.php';

// Get artwork ID from URL
$artwork_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// If no ID provided, redirect to gallery
if ($artwork_id <= 0) {
    header('Location: gallery.php');
    exit;
}

// Get artwork details from database
$artwork = getArtworkById($conn, $artwork_id);

// If artwork not found, set error message
if (!$artwork) {
    $error_message = "Artwork not found.";
} else {
    // Set the page title to the artwork title
    $page_title = $artwork['title'];
}

// Include the header
include 'includes/header.php';

// Define breadcrumbs for this page
$breadcrumbs = [
    ['text' => 'Home', 'url' => 'index.php'],
    ['text' => 'Gallery', 'url' => 'gallery.php'],
    ['text' => isset($artwork['title']) ? htmlspecialchars($artwork['title']) : 'Artwork Detail']
];

// Display breadcrumbs
echo renderBreadcrumbs($breadcrumbs);
?>

<div class="main-content-wrapper">
    <div class="content">
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?= $error_message ?></div>
            <a href="gallery.php" class="back-button button">Back to Gallery</a>
        <?php elseif ($artwork): ?>
            <div class="artwork-detail-card">
                <div class="artwork-image-container">
                    <?php if (!empty($artwork['image_url']) && file_exists($artwork['image_url'])): ?>
                        <img src="<?= $artwork['image_url']; ?>" alt="<?= htmlspecialchars($artwork['title']); ?>" class="artwork-image-large">
                    <?php else: ?>
                        <img src="art/monaLisa.jpg" alt="<?= htmlspecialchars($artwork['title']); ?>" class="artwork-image-large">
                    <?php endif; ?>
                </div>
                
                <div class="artwork-info-container">
                    <h1 class="artwork-title"><?= htmlspecialchars($artwork['title']); ?></h1>
                    <p class="artwork-artist">by <?= htmlspecialchars($artwork['artist_name']); ?></p>
                    
                    <div class="artwork-meta">
                        <div class="artwork-meta-item">
                            <p class="meta-label">Category</p>
                            <p class="meta-value"><?= htmlspecialchars($artwork['category_name']); ?></p>
                        </div>
                        
                        <?php if (!empty($artwork['created_at'])): ?>
                        <div class="artwork-meta-item">
                            <p class="meta-label">Added</p>
                            <p class="meta-value"><?= formatDate($artwork['created_at']); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="artwork-price"><?= formatPrice($artwork['price']); ?></div>
                    
                    <?php if (!empty($artwork['description'])): ?>
                        <div class="artwork-description">
                            <p><?= nl2br(htmlspecialchars($artwork['description'])); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <div class="action-buttons">
                        <a href="gallery.php" class="back-button button">Back to Gallery</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
// Include the footer
include 'includes/footer.php';
?>
