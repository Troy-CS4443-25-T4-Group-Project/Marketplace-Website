# Art Marketplace Website

## Project Structure

This project is organized as follows:

### Main Pages
- `index.php` - Homepage showing featured artworks
- `gallery.php` - Gallery page with filtering options
- `artwork_detail.php` - Detail page for individual artworks
- `admin.php` - Admin page for adding new artworks
- `about.php` - About page with team information

### Include Files
- `includes/config.php` - Configuration settings
- `includes/database.php` - Database connection and initialization
- `includes/functions.php` - Core functions for data handling
- `includes/display_helpers.php` - Helper functions for displaying UI elements
- `includes/header.php` - Common header template
- `includes/footer.php` - Common footer template

### Assets
- `art/` - Directory for artwork images
- `images/` - Directory for site images (e.g., team photos)
- `css/` - Directory for stylesheets

## Code Organization

The codebase follows these organizational principles:

1. **Separation of Concerns**
   - Data access is handled in `functions.php`
   - Display logic is in `display_helpers.php`
   - Database operations are in `database.php`
   - Configuration settings are in `config.php`

2. **Function-Based Organization**
   - Code is organized into logical, reusable functions
   - Functions are named according to their purpose
   - Each function has a single responsibility

3. **Clean Templates**
   - PHP templates contain minimal logic
   - Display is handled by helper functions
   - Data is prepared before being passed to templates

4. **Consistent Error Handling**
   - Errors are caught and reported consistently
   - User-friendly error messages are displayed

5. **External CSS**
   - All styling is in external CSS files
   - No inline styles are used

## Key Functions

### Database Functions
- `getConnection()` - Gets a PDO database connection
- `initializeDatabase()` - Sets up database tables if they don't exist
- `createTables()` - Creates database tables
- `insertSampleData()` - Inserts sample data for testing

### Data Access Functions
- `getArtworkById()` - Gets an artwork by ID
- `getArtworks()` - Gets artworks with optional filtering
- `getFeaturedArtworks()` - Gets featured artworks
- `getAllCategories()` - Gets all categories
- `addArtwork()` - Adds a new artwork to the database
- `handleImageUpload()` - Handles artwork image uploads

### Display Helper Functions
- `renderArtworkCard()` - Renders an artwork card
- `renderArtworkGrid()` - Renders a grid of artwork cards
- `renderCategorySidebar()` - Renders the category sidebar
- `renderBreadcrumbs()` - Renders breadcrumb navigation
- `formatPrice()` - Formats price with currency symbol
- `formatDate()` - Formats date in a human-readable way

## Usage Examples

### Adding a New Artwork
```php
$artwork_data = [
    'title' => 'New Artwork',
    'artist_name' => 'Artist Name',
    'description' => 'Description of the artwork',
    'category_id' => 1,
    'price' => 100.00,
    'is_featured' => true
];
$result = addArtwork($conn, $artwork_data, $_FILES['artwork_image']);
```

### Displaying Artworks
```php
$artworks = getArtworks($conn);
echo renderArtworkGrid($artworks);
```

### Displaying Categories
```php
$categories = getAllCategories($conn);
echo renderCategorySidebar($categories);
```

## Maintenance

To add new features or modify existing ones:

1. Add database tables or modify existing ones in `database.php`
2. Add data access functions in `functions.php`
3. Add display helper functions in `display_helpers.php`
4. Update page templates to use the new functions
