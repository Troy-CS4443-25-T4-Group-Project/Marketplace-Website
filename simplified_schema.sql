-- Simplified Art Marketplace Database Schema

-- Categories Table
CREATE TABLE categories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT
);

-- Insert default categories
INSERT INTO categories (name, description) VALUES
('Paintings', 'Traditional painted artwork on canvas, paper, or other media'),
('Sculptures', 'Three-dimensional art objects'),
('Photography', 'Photographic prints and digital photography'),
('Digital Art', 'Art created or presented using digital technology'),
('Mixed Media', 'Artwork incorporating multiple materials or techniques');

-- Artworks Table (simplified)
CREATE TABLE artworks (
    artwork_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    artist_name VARCHAR(100) NOT NULL,
    description TEXT,
    category_id INT,
    price DECIMAL(10,2) NOT NULL,
    image_url VARCHAR(255),
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE SET NULL
);

-- Create indexes for frequently queried columns
CREATE INDEX idx_artworks_price ON artworks(price);
CREATE INDEX idx_artworks_category ON artworks(category_id);
CREATE INDEX idx_artworks_featured ON artworks(is_featured);
