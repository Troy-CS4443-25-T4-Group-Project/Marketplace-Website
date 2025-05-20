# Art Marketplace Database Documentation

This document provides an overview of the simplified database structure used in the Art Marketplace project. The database has been streamlined to focus only on the core functionality required for displaying and categorizing artwork.

## Database Structure

The database consists of just two primary tables:

### Categories Table

Stores the different types of artwork categories available on the site.

```sql
CREATE TABLE categories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT
);
```

**Fields:**
- `category_id`: Unique identifier for each category
- `name`: Category name (e.g., Paintings, Sculptures)
- `description`: Optional detailed description of the category

### Artworks Table

Stores information about individual artworks in the marketplace.

```sql
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
```

**Fields:**
- `artwork_id`: Unique identifier for each artwork
- `title`: The name of the artwork
- `artist_name`: Name of the artist who created the artwork
- `description`: Detailed description of the artwork
- `category_id`: Foreign key linking to the categories table
- `price`: The price of the artwork in dollars
- `image_url`: Path to the artwork's image file (typically stored in the `art/` directory)
- `is_featured`: Boolean flag indicating whether the artwork should be featured on the homepage
- `created_at`: Timestamp recording when the artwork was added to the database

## Database Indexes

To optimize query performance, the following indexes have been created:

```sql
CREATE INDEX idx_artworks_price ON artworks(price);
CREATE INDEX idx_artworks_category ON artworks(category_id);
CREATE INDEX idx_artworks_featured ON artworks(is_featured);
```

These indexes improve search performance when:
- Filtering artworks by price range
- Filtering artworks by category
- Selecting featured artworks for the homepage

## Default Categories

The database is initialized with the following standard art categories:

1. **Paintings**: Traditional painted artwork on canvas, paper, or other media
2. **Sculptures**: Three-dimensional art objects
3. **Photography**: Photographic prints and digital photography
4. **Digital Art**: Art created or presented using digital technology
5. **Mixed Media**: Artwork incorporating multiple materials or techniques

## Sample Data

The database is automatically populated with sample artwork data to provide content for demonstration purposes. Each sample artwork includes:
- Title
- Artist name
- Description
- Category assignment
- Price
- Image reference
- Featured status

## Automatic Database Initialization

The database is automatically created and initialized when the website is first accessed. The process works as follows:

1. The application attempts to connect to the database
2. If the database doesn't exist, it is created automatically
3. If the tables don't exist, they are created with the proper structure
4. If the tables are empty, sample data is inserted

This approach ensures the website works immediately after setup without requiring manual database configuration.

## Database Connection

Database connection is managed in the `includes/database.php` file, which:
- Establishes a connection to MySQL
- Creates the database if it doesn't exist
- Creates the necessary tables if they don't exist
- Populates the tables with sample data if needed
- Returns a PDO connection object for use throughout the application

## Image Storage

While image references are stored in the database as URLs, the actual image files are stored in the filesystem in the `art/` directory, which contains:
- `paintings/`: Images of paintings
- `photography/`: Photographic artwork
- `sculptures/`: Images of sculptural pieces

This approach keeps the database efficient while maintaining organized storage of artwork images.
