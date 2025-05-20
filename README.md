# Art Marketplace Website

A simplified web platform for artists to showcase their artwork.

## Project Overview

This project is a streamlined art marketplace that allows users to:
- Browse artworks by category
- View featured artworks on the homepage
- Add new artwork through a simple admin interface

## Project Structure

### Main Files
- **index.php**: Homepage with featured artwork display
- **gallery.php**: Gallery page with artwork filtering by category
- **about.php**: Information about the team members
- **admin.php**: Simple admin interface for adding new artwork

### Directory Structure
- **css/**: Contains all CSS files
  - `marketPlaceStyle.css`: Main styling
  - `artwork-styles.css`: Styling for artwork display
  - `footer-styles.css`: Footer styling
  - `about-styles.css`: About page styling
  - `reset.css`: CSS reset
- **includes/**: PHP includes for common elements
  - `header.php`: Common header across all pages
  - `footer.php`: Common footer across all pages
  - `config.php`: Configuration settings
  - `database.php`: Database connection and setup
- **art/**: Directory for artwork images
  - `paintings/`: Painting images
  - `photography/`: Photography images
  - `sculptures/`: Sculpture images

## Database Structure

The database has been simplified to just two tables:

- **Categories**: Stores art categories (Paintings, Sculptures, Photography, etc.)
- **Artworks**: Stores artwork information (title, artist, price, image, etc.)

For more details, see [database-documentation.md](database-documentation.md).

## Features

- **Dynamic Content**: Artworks and categories are stored in and retrieved from a database
- **Responsive Design**: The website is designed to work on various screen sizes
- **Category Filtering**: Users can filter artworks by category
- **Admin Interface**: Simple interface for adding new artwork to the database
- **Image Upload**: Support for uploading artwork images

## Setup Instructions

1. Clone the repository to your local environment
2. Set up a web server with PHP and MySQL (e.g., XAMPP, WAMP)
3. Place the project files in your web server's document root
4. Access the website through your web browser
5. The database and tables will be automatically created on first access

## Database Auto-Initialization

The website includes an automatic database setup system that:
- Creates the database if it doesn't exist
- Creates the necessary tables if they don't exist
- Populates the tables with sample data

This means no manual database setup is required.

## Development Team

Created by Austin Cain, Brandon Horn, Phoenix Hussey, Rhett Parker, James Ward, Teresa Williams
