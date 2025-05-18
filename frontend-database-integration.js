/**
 * frontend-database-integration.js
 * 
 * This file demonstrates how to connect your existing HTML frontend
 * to the database using fetch API and modern JavaScript.
 * It assumes a backend API exists that communicates with the database.
 */

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Load featured artworks on homepage
    loadFeaturedArtworks();
    
    // Setup event listeners for navigation and filters
    setupNavigation();
    setupFilters();
});

/**
 * Load featured artworks for homepage
 */
async function loadFeaturedArtworks() {
    try {
        const response = await fetch('/api/artworks/featured');
        
        if (!response.ok) {
            throw new Error('Failed to fetch featured artworks');
        }
        
        const artworks = await response.json();
        displayArtworks(artworks);
    } catch (error) {
        console.error('Error:', error);
        displayErrorMessage('Failed to load featured artworks. Please try again later.');
    }
}

/**
 * Display artworks in the main content area
 */
function displayArtworks(artworks) {
    const mainContent = document.querySelector('.content');
    
    // Clear existing content
    if (mainContent) {
        mainContent.innerHTML = '';
        
        if (artworks.length === 0) {
            mainContent.innerHTML = '<p class="no-results">No artworks found.</p>';
            return;
        }
        
        // Create a grid container for the artworks
        const artworkGrid = document.createElement('div');
        artworkGrid.className = 'artwork-grid';
        
        // Add each artwork to the grid
        artworks.forEach(artwork => {
            const artworkElement = createArtworkElement(artwork);
            artworkGrid.appendChild(artworkElement);
        });
        
        mainContent.appendChild(artworkGrid);
    }
}

/**
 * Create HTML element for an artwork
 */
function createArtworkElement(artwork) {
    const { artwork_id, title, price, currency, ArtworkImages, ArtistProfile } = artwork;
    
    // Create article element for the artwork
    const artworkElement = document.createElement('article');
    artworkElement.className = 'artwork-card';
    artworkElement.dataset.artworkId = artwork_id;
    
    // Get the primary image URL or use a placeholder
    const imageUrl = ArtworkImages && ArtworkImages.length > 0 
        ? ArtworkImages[0].image_url 
        : 'placeholder.jpg';
    
    // Format price with currency
    const formattedPrice = new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency || 'USD'
    }).format(price);
    
    // Create the HTML structure
    artworkElement.innerHTML = `
        <div class="artwork-image">
            <a href="/artwork/${artwork_id}">
                <img src="${imageUrl}" alt="${title}" loading="lazy">
            </a>
        </div>
        <div class="artwork-details">
            <h3 class="artwork-title">
                <a href="/artwork/${artwork_id}">${title}</a>
            </h3>
            <p class="artwork-artist">
                <a href="/artist/${ArtistProfile.artist_id}">${ArtistProfile.artist_name}</a>
            </p>
            <p class="artwork-price">${formattedPrice}</p>
            <div class="artwork-actions">
                <button class="btn-wishlist" data-artwork-id="${artwork_id}">
                    <i class="icon-heart"></i> Save
                </button>
                <button class="btn-add-to-cart" data-artwork-id="${artwork_id}">
                    <i class="icon-cart"></i> Add to Cart
                </button>
            </div>
        </div>
    `;
    
    // Add event listeners
    const wishlistButton = artworkElement.querySelector('.btn-wishlist');
    wishlistButton.addEventListener('click', function() {
        toggleWishlist(artwork_id);
    });
    
    const cartButton = artworkElement.querySelector('.btn-add-to-cart');
    cartButton.addEventListener('click', function() {
        addToCart(artwork_id);
    });
    
    return artworkElement;
}

/**
 * Setup navigation event listeners
 */
function setupNavigation() {
    // Main navigation
    const mainNavLinks = document.querySelectorAll('.main-nav a');
    mainNavLinks.forEach(link => {
        link.addEventListener('click', function(event) {
            // Remove active class from all links
            mainNavLinks.forEach(item => item.classList.remove('active'));
            // Add active class to clicked link
            this.classList.add('active');
            
            // If it's a category link, load artworks for that category
            if (this.dataset.category) {
                event.preventDefault();
                loadArtworksByCategory(this.dataset.category);
            }
        });
    });
    
    // Sidebar navigation
    const sidebarLinks = document.querySelectorAll('.sidebar-nav a');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            
            // Handle category links
            if (this.dataset.category) {
                loadArtworksByCategory(this.dataset.category);
            }
            
            // Handle style links
            if (this.dataset.style) {
                loadArtworksByStyle(this.dataset.style);
            }
            
            // Handle media links
            if (this.dataset.media) {
                loadArtworksByMedia(this.dataset.media);
            }
        });
    });
}

/**
 * Setup filter event listeners
 */
function setupFilters() {
    // Price range filter
    const priceFilterForm = document.getElementById('price-filter-form');
    if (priceFilterForm) {
        priceFilterForm.addEventListener('submit', function(event) {
            event.preventDefault();
            
            const minPrice = document.getElementById('min-price').value;
            const maxPrice = document.getElementById('max-price').value;
            
            loadArtworksByPriceRange(minPrice, maxPrice);
        });
    }
    
    // Other filters can be added here
}

/**
 * Load artworks by category
 */
async function loadArtworksByCategory(categoryId) {
    try {
        const response = await fetch(`/api/artworks?category=${categoryId}`);
        
        if (!response.ok) {
            throw new Error('Failed to fetch artworks for this category');
        }
        
        const artworks = await response.json();
        displayArtworks(artworks);
        
        // Update breadcrumb
        updateBreadcrumb('Category', getCategoryNameById(categoryId));
    } catch (error) {
        console.error('Error:', error);
        displayErrorMessage('Failed to load artworks. Please try again later.');
    }
}

/**
 * Load artworks by style
 */
async function loadArtworksByStyle(styleId) {
    try {
        const response = await fetch(`/api/artworks?style=${styleId}`);
        
        if (!response.ok) {
            throw new Error('Failed to fetch artworks for this style');
        }
        
        const artworks = await response.json();
        displayArtworks(artworks);
        
        // Update breadcrumb
        updateBreadcrumb('Style', getStyleNameById(styleId));
    } catch (error) {
        console.error('Error:', error);
        displayErrorMessage('Failed to load artworks. Please try again later.');
    }
}

/**
 * Load artworks by media type
 */
async function loadArtworksByMedia(mediaId) {
    try {
        const response = await fetch(`/api/artworks?medium=${mediaId}`);
        
        if (!response.ok) {
            throw new Error('Failed to fetch artworks for this medium');
        }
        
        const artworks = await response.json();
        displayArtworks(artworks);
        
        // Update breadcrumb
        updateBreadcrumb('Medium', getMediaNameById(mediaId));
    } catch (error) {
        console.error('Error:', error);
        displayErrorMessage('Failed to load artworks. Please try again later.');
    }
}

/**
 * Load artworks by price range
 */
async function loadArtworksByPriceRange(minPrice, maxPrice) {
    try {
        let url = '/api/artworks?';
        
        if (minPrice) {
            url += `priceMin=${minPrice}&`;
        }
        
        if (maxPrice) {
            url += `priceMax=${maxPrice}`;
        }
        
        const response = await fetch(url);
        
        if (!response.ok) {
            throw new Error('Failed to fetch artworks in this price range');
        }
        
        const artworks = await response.json();
        displayArtworks(artworks);
        
        // Update breadcrumb
        updateBreadcrumb('Price', `$${minPrice || '0'} - $${maxPrice || 'Max'}`);
    } catch (error) {
        console.error('Error:', error);
        displayErrorMessage('Failed to load artworks. Please try again later.');
    }
}

/**
 * Add artwork to wishlist
 */
async function toggleWishlist(artworkId) {
    // Check if user is logged in
    if (!isUserLoggedIn()) {
        showLoginPrompt();
        return;
    }
    
    try {
        const response = await fetch('/api/wishlist/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ artworkId })
        });
        
        if (!response.ok) {
            throw new Error('Failed to update wishlist');
        }
        
        const result = await response.json();
        
        // Update the UI based on whether the item was added or removed
        const wishlistButton = document.querySelector(`.btn-wishlist[data-artwork-id="${artworkId}"]`);
        
        if (wishlistButton) {
            if (result.inWishlist) {
                wishlistButton.classList.add('in-wishlist');
                wishlistButton.innerHTML = '<i class="icon-heart-filled"></i> Saved';
                showToast('Artwork added to your wishlist');
            } else {
                wishlistButton.classList.remove('in-wishlist');
                wishlistButton.innerHTML = '<i class="icon-heart"></i> Save';
                showToast('Artwork removed from your wishlist');
            }
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Failed to update wishlist. Please try again.', 'error');
    }
}

/**
 * Add artwork to cart
 */
async function addToCart(artworkId) {
    try {
        const response = await fetch('/api/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ 
                artworkId,
                quantity: 1
            })
        });
        
        if (!response.ok) {
            throw new Error('Failed to add to cart');
        }
        
        const result = await response.json();
        
        // Update cart count in the UI
        updateCartCount(result.cartCount);
        
        // Show success message
        showToast('Artwork added to your cart');
    } catch (error) {
        console.error('Error:', error);
        showToast('Failed to add to cart. Please try again.', 'error');
    }
}

/**
 * Update the cart count display in the header
 */
function updateCartCount(count) {
    const cartCountElement = document.querySelector('.cart-count');
    
    if (cartCountElement) {
        cartCountElement.textContent = count;
        
        if (count > 0) {
            cartCountElement.classList.add('has-items');
        } else {
            cartCountElement.classList.remove('has-items');
        }
    }
}

/**
 * Update the breadcrumb navigation
 */
function updateBreadcrumb(filterType, filterValue) {
    const breadcrumbContainer = document.querySelector('.breadcrumb-nav ol');
    
    if (breadcrumbContainer) {
        // Keep the Home link, remove others
        breadcrumbContainer.innerHTML = '<li><a href="index.html">Home</a></li>';
        
        // Add Gallery link
        const galleryItem = document.createElement('li');
        galleryItem.innerHTML = '<a href="gallery.html">Gallery</a>';
        breadcrumbContainer.appendChild(galleryItem);
        
        // Add the current filter
        const filterItem = document.createElement('li');
        filterItem.textContent = `${filterType}: ${filterValue}`;
        breadcrumbContainer.appendChild(filterItem);
    }
}

/**
 * Display an error message to the user
 */
function displayErrorMessage(message) {
    const mainContent = document.querySelector('.content');
    
    if (mainContent) {
        mainContent.innerHTML = `
            <div class="error-message">
                <p>${message}</p>
                <button class="btn-retry">Try Again</button>
            </div>
        `;
        
        const retryButton = document.querySelector('.btn-retry');
        if (retryButton) {
            retryButton.addEventListener('click', loadFeaturedArtworks);
        }
    }
}

/**
 * Show a toast notification
 */
function showToast(message, type = 'success') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    
    // Append to body
    document.body.appendChild(toast);
    
    // Show toast
    setTimeout(() => {
        toast.classList.add('show');
    }, 10);
    
    // Remove after 3 seconds
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}

/**
 * Check if user is logged in
 */
function isUserLoggedIn() {
    // Check for auth token in localStorage or sessionStorage
    return !!localStorage.getItem('authToken') || !!sessionStorage.getItem('authToken');
}

/**
 * Show login prompt
 */
function showLoginPrompt() {
    // Create modal overlay
    const modal = document.createElement('div');
    modal.className = 'modal login-modal';
    
    modal.innerHTML = `
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h2>Sign In Required</h2>
            <p>Please sign in to add items to your wishlist.</p>
            <div class="modal-actions">
                <a href="login.html" class="btn btn-primary">Sign In</a>
                <a href="register.html" class="btn btn-secondary">Create Account</a>
            </div>
        </div>
    `;
    
    // Add to body
    document.body.appendChild(modal);
    
    // Show modal
    setTimeout(() => {
        modal.classList.add('show');
    }, 10);
    
    // Handle close button
    const closeButton = modal.querySelector('.close-modal');
    closeButton.addEventListener('click', () => {
        modal.classList.remove('show');
        setTimeout(() => {
            document.body.removeChild(modal);
        }, 300);
    });
}

// Helper functions to get names from IDs (in a real app, these would fetch from the database)
function getCategoryNameById(categoryId) {
    // This would typically fetch from the API, but for demonstration:
    const categories = {
        1: 'Paintings',
        2: 'Sculptures',
        3: 'Photography',
        4: 'Digital Art',
        5: 'Mixed Media'
    };
    
    return categories[categoryId] || 'Unknown Category';
}

function getStyleNameById(styleId) {
    const styles = {
        1: 'Abstract',
        2: 'Realism',
        3: 'Impressionism',
        4: 'Expressionism',
        5: 'Surrealism',
        6: 'Minimalism',
        7: 'Pop Art',
        8: 'Contemporary'
    };
    
    return styles[styleId] || 'Unknown Style';
}

function getMediaNameById(mediaId) {
    const media = {
        1: 'Oil',
        2: 'Acrylic',
        3: 'Watercolor',
        4: 'Charcoal',
        5: 'Digital',
        6: 'Clay',
        7: 'Bronze',
        8: 'Mixed Media'
    };
    
    return media[mediaId] || 'Unknown Medium';
}
