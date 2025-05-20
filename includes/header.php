<?php
/**
 * Header Template
 * This file contains the common header used across all pages.
 */

// Get the current page filename
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' - ' : '' ?>Art Market Place</title>
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/marketPlaceStyle.css">
    <link rel="stylesheet" href="css/artwork-styles.css">
    <link rel="stylesheet" href="css/footer-styles.css">
    <link rel="stylesheet" href="css/about-styles.css">
    <link rel="stylesheet" href="css/artwork-detail.css">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <?php if (isset($extra_css)): ?>
        <?= $extra_css ?>
    <?php endif; ?>
</head>
<body>
    <header>
        <h1 class="fancy-heading">A Marketplace for Art</h1>
        
        <nav class="main-nav">
            <ul>
                <li><a href="index.php" <?= $current_page == 'index.php' ? 'class="active"' : '' ?>>Home</a></li>
                <li><a href="gallery.php" <?= $current_page == 'gallery.php' ? 'class="active"' : '' ?>>Gallery</a></li>
                <li><a href="about.php" <?= $current_page == 'about.php' ? 'class="active"' : '' ?>>About Us</a></li>
            </ul>
        </nav>
    </header>
    
    <div class="breadcrumb-nav">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol>
                    <li><a href="index.php">Home</a></li>
                    <?php if ($current_page == 'gallery.php'): ?>
                        <li>Gallery</li>
                    <?php elseif ($current_page == 'about.php'): ?>
                        <li>About Us</li>
                    <?php else: ?>
                        <li><?= isset($page_title) ? $page_title : 'Page' ?></li>
                    <?php endif; ?>
                </ol>
            </nav>
        </div>
    </div>
    
    <main>
        <!-- Display success/error messages if any -->
        <?php if (isset($success_message) && !empty($success_message)): ?>
            <div class="success-message"><?= $success_message ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message) && !empty($error_message)): ?>
            <div class="error-message"><?= $error_message ?></div>
        <?php endif; ?>
