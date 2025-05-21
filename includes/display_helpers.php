<?php
/**
 * Display Helper Functions
 * This file contains functions for displaying common elements across the site
 */

/**
 * Render artwork card HTML
 * 
 * @param array $artwork Artwork data
 * @return string HTML for the artwork card
 */
function renderArtworkCard($artwork) {
    $html = '<div class="artwork-card">';
    $html .= '<a href="artwork_detail.php?id=' . $artwork['artwork_id'] . '" class="artwork-link">';
    $html .= '<div class="artwork-image">';
    
    // Image URL handling
    if (!empty($artwork['image_url']) && file_exists($artwork['image_url'])) {
        $html .= '<img src="' . $artwork['image_url'] . '" alt="' . htmlspecialchars($artwork['title']) . '">';
    } else {
        // Fallback image if no URL or file not found
        $html .= '<img src="art/monaLisa.jpg" alt="' . htmlspecialchars($artwork['title']) . '">';
    }
    
    $html .= '</div></a>';
    $html .= '<div class="artwork-info">';
    $html .= '<h3>' . htmlspecialchars($artwork['title']) . '</h3>';
    $html .= '<p class="artist">by ' . htmlspecialchars($artwork['artist_name']) . '</p>';
    $html .= '<p class="category">' . htmlspecialchars($artwork['category_name']) . '</p>';
    $html .= '<p class="price">$' . number_format($artwork['price'], 2) . '</p>';
    $html .= '</div></div>';
    
    return $html;
}

/**
 * Render a list of artwork cards
 * 
 * @param array $artworks Array of artwork data
 * @return string HTML for all artwork cards
 */
function renderArtworkGrid($artworks) {
    if (empty($artworks)) {
        return '<div class="no-results">
                    <i class="fas fa-search"></i>
                    <h3>No artworks found</h3>
                    <p>No artwork matches your current selection.</p>
                </div>';
    }
    
    $html = '';
    foreach ($artworks as $artwork) {
        $html .= renderArtworkCard($artwork);
    }
    
    return $html;
}

/**
 * Render category sidebar navigation
 * 
 * @param array $categories Array of category data
 * @param int|null $active_category_id Currently selected category ID (optional)
 * @return string HTML for category navigation
 */
function renderCategorySidebar($categories, $active_category_id = null) {
    $html = '<aside class="sidebar">';
    $html .= '<nav class="sidebar-nav">';
    $html .= '<h3>Categories</h3>';
    $html .= '<ul>';
    $html .= '<li><a href="gallery.php" ' . (!$active_category_id ? 'class="active"' : '') . '>All Categories</a></li>';
    
    foreach ($categories as $category) {
        $html .= '<li>';
        $html .= '<a href="gallery.php?category=' . $category['category_id'] . '" ';
        $html .= ($active_category_id == $category['category_id'] ? 'class="active"' : '') . '>';
        $html .= htmlspecialchars($category['name']) . '</a>';
        $html .= '</li>';
    }
    
    $html .= '</ul>';
    $html .= '</aside>';
    
    return $html;
}

/**
 * Render breadcrumb navigation
 * 
 * @param array $crumbs Array of breadcrumb items, each with 'text' and 'url' (optional)
 * @return string HTML for breadcrumb navigation
 */
function renderBreadcrumbs($crumbs) {
    $html = '<div class="breadcrumb-nav">';
    $html .= '<div class="container">';
    $html .= '<nav aria-label="breadcrumb">';
    $html .= '<ol>';
    
    foreach ($crumbs as $index => $crumb) {
        if ($index === count($crumbs) - 1 || empty($crumb['url'])) {
            // Last item or item without URL is not linked
            $html .= '<li>' . htmlspecialchars($crumb['text']) . '</li>';
        } else {
            // Other items are linked
            $html .= '<li><a href="' . $crumb['url'] . '">' . htmlspecialchars($crumb['text']) . '</a></li>';
        }
    }
    
    $html .= '</ol>';
    $html .= '</nav>';
    $html .= '</div>';
    $html .= '</div>';
    
    return $html;
}

/**
 * Format price with proper currency symbol and decimals
 * 
 * @param float $price The price to format
 * @param string $currency The currency symbol (default: $)
 * @return string Formatted price string
 */
function formatPrice($price, $currency = '$') {
    return $currency . number_format($price, 2);
}

/**
 * Format date in a human-readable format
 * 
 * @param string $date The date string to format
 * @param string $format The date format (default: 'F j, Y')
 * @return string Formatted date string
 */
function formatDate($date, $format = 'F j, Y') {
    return date($format, strtotime($date));
}
