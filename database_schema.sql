-- Art Marketplace Database Schema

-- Users Table
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    profile_image VARCHAR(255),
    bio TEXT,
    is_artist BOOLEAN DEFAULT FALSE,
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- User Contact Information
CREATE TABLE user_contact (
    contact_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    phone VARCHAR(20),
    address_line1 VARCHAR(100),
    address_line2 VARCHAR(100),
    city VARCHAR(50),
    state VARCHAR(50),
    postal_code VARCHAR(20),
    country VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Artist Profiles (extends users who are artists)
CREATE TABLE artist_profiles (
    artist_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    artist_name VARCHAR(100),
    specialty VARCHAR(100),
    years_of_experience INT,
    education TEXT,
    exhibitions TEXT,
    awards TEXT,
    website VARCHAR(255),
    social_media_links JSON,
    commission_available BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Categories Table
CREATE TABLE categories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    parent_category_id INT,
    FOREIGN KEY (parent_category_id) REFERENCES categories(category_id) ON DELETE SET NULL
);

-- Insert default categories
INSERT INTO categories (name, description) VALUES
('Paintings', 'Traditional painted artwork on canvas, paper, or other media'),
('Sculptures', 'Three-dimensional art objects'),
('Photography', 'Photographic prints and digital photography'),
('Digital Art', 'Art created or presented using digital technology'),
('Mixed Media', 'Artwork incorporating multiple materials or techniques');

-- Styles Table
CREATE TABLE styles (
    style_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT
);

-- Insert common art styles
INSERT INTO styles (name) VALUES
('Abstract'), ('Realism'), ('Impressionism'), ('Expressionism'), 
('Surrealism'), ('Minimalism'), ('Pop Art'), ('Contemporary'),
('Modern'), ('Traditional'), ('Folk'), ('Renaissance');

-- Media Types Table
CREATE TABLE media_types (
    media_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT
);

-- Insert common media types
INSERT INTO media_types (name) VALUES
('Oil'), ('Acrylic'), ('Watercolor'), ('Charcoal'), ('Pencil'),
('Digital'), ('Clay'), ('Bronze'), ('Marble'), ('Wood'),
('Mixed Media'), ('Paper'), ('Fabric'), ('Glass');

-- Artworks Table
CREATE TABLE artworks (
    artwork_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    artist_id INT NOT NULL,
    description TEXT,
    category_id INT,
    style_id INT,
    media_id INT,
    creation_date DATE,
    width DECIMAL(10,2),
    height DECIMAL(10,2),
    depth DECIMAL(10,2),
    dimensions_unit VARCHAR(10) DEFAULT 'cm',
    price DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'USD',
    inventory_count INT DEFAULT 1,
    is_original BOOLEAN DEFAULT TRUE,
    is_framed BOOLEAN DEFAULT FALSE,
    is_signed BOOLEAN DEFAULT FALSE,
    is_featured BOOLEAN DEFAULT FALSE,
    status ENUM('available', 'sold', 'reserved', 'hidden') DEFAULT 'available',
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (artist_id) REFERENCES artist_profiles(artist_id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE SET NULL,
    FOREIGN KEY (style_id) REFERENCES styles(style_id) ON DELETE SET NULL,
    FOREIGN KEY (media_id) REFERENCES media_types(media_id) ON DELETE SET NULL
);

-- Artwork Images Table
CREATE TABLE artwork_images (
    image_id INT PRIMARY KEY AUTO_INCREMENT,
    artwork_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (artwork_id) REFERENCES artworks(artwork_id) ON DELETE CASCADE
);

-- Tags Table
CREATE TABLE tags (
    tag_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE
);

-- Artwork Tags (Many-to-Many relationship)
CREATE TABLE artwork_tags (
    artwork_id INT,
    tag_id INT,
    PRIMARY KEY (artwork_id, tag_id),
    FOREIGN KEY (artwork_id) REFERENCES artworks(artwork_id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(tag_id) ON DELETE CASCADE
);

-- Wishlists Table
CREATE TABLE wishlists (
    wishlist_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    name VARCHAR(100) DEFAULT 'My Wishlist',
    is_public BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Wishlist Items (Many-to-Many relationship)
CREATE TABLE wishlist_items (
    wishlist_id INT,
    artwork_id INT,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (wishlist_id, artwork_id),
    FOREIGN KEY (wishlist_id) REFERENCES wishlists(wishlist_id) ON DELETE CASCADE,
    FOREIGN KEY (artwork_id) REFERENCES artworks(artwork_id) ON DELETE CASCADE
);

-- Shopping Cart Table
CREATE TABLE shopping_carts (
    cart_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    session_id VARCHAR(100), -- For non-logged-in users
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Cart Items
CREATE TABLE cart_items (
    cart_item_id INT PRIMARY KEY AUTO_INCREMENT,
    cart_id INT NOT NULL,
    artwork_id INT NOT NULL,
    quantity INT DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cart_id) REFERENCES shopping_carts(cart_id) ON DELETE CASCADE,
    FOREIGN KEY (artwork_id) REFERENCES artworks(artwork_id) ON DELETE CASCADE
);

-- Orders Table
CREATE TABLE orders (
    order_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded') DEFAULT 'pending',
    subtotal DECIMAL(10,2) NOT NULL,
    tax DECIMAL(10,2) DEFAULT 0,
    shipping_cost DECIMAL(10,2) DEFAULT 0,
    discount DECIMAL(10,2) DEFAULT 0,
    total_amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'USD',
    billing_name VARCHAR(100),
    billing_email VARCHAR(100),
    billing_address JSON,
    shipping_name VARCHAR(100),
    shipping_address JSON,
    payment_method VARCHAR(50),
    payment_status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    shipping_method VARCHAR(50),
    tracking_number VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
);

-- Order Items
CREATE TABLE order_items (
    order_item_id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    artwork_id INT,
    artwork_title VARCHAR(100) NOT NULL, -- Stored separately in case artwork is deleted
    artist_name VARCHAR(100) NOT NULL, -- Stored separately in case artist is deleted
    quantity INT DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'USD',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (artwork_id) REFERENCES artworks(artwork_id) ON DELETE SET NULL
);

-- Reviews Table
CREATE TABLE reviews (
    review_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    artwork_id INT,
    order_item_id INT,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    title VARCHAR(100),
    comment TEXT,
    is_verified_purchase BOOLEAN DEFAULT FALSE,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (artwork_id) REFERENCES artworks(artwork_id) ON DELETE CASCADE,
    FOREIGN KEY (order_item_id) REFERENCES order_items(order_item_id) ON DELETE SET NULL
);

-- Follows (Artist followers)
CREATE TABLE follows (
    follow_id INT PRIMARY KEY AUTO_INCREMENT,
    follower_id INT NOT NULL, -- user_id of follower
    artist_id INT NOT NULL, -- artist_id being followed
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (follower_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (artist_id) REFERENCES artist_profiles(artist_id) ON DELETE CASCADE,
    UNIQUE KEY (follower_id, artist_id) -- Prevent duplicate follows
);

-- Messages Table
CREATE TABLE messages (
    message_id INT PRIMARY KEY AUTO_INCREMENT,
    sender_id INT NOT NULL,
    recipient_id INT NOT NULL,
    subject VARCHAR(100),
    message_text TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    related_artwork_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (recipient_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (related_artwork_id) REFERENCES artworks(artwork_id) ON DELETE SET NULL
);

-- Blog Posts
CREATE TABLE blog_posts (
    post_id INT PRIMARY KEY AUTO_INCREMENT,
    author_id INT,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    content TEXT NOT NULL,
    featured_image VARCHAR(255),
    excerpt TEXT,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    published_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(user_id) ON DELETE SET NULL
);

-- Events (exhibitions, auctions, etc.)
CREATE TABLE events (
    event_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    event_type ENUM('exhibition', 'auction', 'workshop', 'other') NOT NULL,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    location VARCHAR(255),
    is_virtual BOOLEAN DEFAULT FALSE,
    virtual_link VARCHAR(255),
    featured_image VARCHAR(255),
    status ENUM('upcoming', 'ongoing', 'completed', 'cancelled') DEFAULT 'upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Event Artists (Many-to-Many)
CREATE TABLE event_artists (
    event_id INT,
    artist_id INT,
    PRIMARY KEY (event_id, artist_id),
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE,
    FOREIGN KEY (artist_id) REFERENCES artist_profiles(artist_id) ON DELETE CASCADE
);

-- Create indexes for frequently queried columns
CREATE INDEX idx_artworks_price ON artworks(price);
CREATE INDEX idx_artworks_status ON artworks(status);
CREATE INDEX idx_artworks_category ON artworks(category_id);
CREATE INDEX idx_artworks_artist ON artworks(artist_id);
CREATE INDEX idx_artworks_style ON artworks(style_id);
CREATE INDEX idx_artworks_media ON artworks(media_id);
CREATE INDEX idx_artworks_featured ON artworks(is_featured);
CREATE INDEX idx_orders_user ON orders(user_id);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_reviews_artwork ON reviews(artwork_id);
