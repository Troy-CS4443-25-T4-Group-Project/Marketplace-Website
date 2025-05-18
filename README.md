# Art Marketplace Website

A comprehensive web platform for artists to showcase and sell their artwork to collectors and art enthusiasts.

## Project Structure

- **marketPlaceWebsite.html**: Original HTML file
- **marketplace-integrated.html**: Updated HTML file with database integration
- **marketPlaceStyle.css**: Main CSS styles
- **artwork-styles.css**: CSS specifically for artwork display
- **reset.css**: CSS reset file
- **frontend-database-integration.js**: JS file for connecting frontend to database
- **database.js**: Sequelize ORM setup for database models
- **database_schema.sql**: SQL database schema
- **database_schema_er_diagram.md**: Documentation of database relationships

## Database Schema Overview

The database for the Art Marketplace includes the following main entities:

### Core Entities

1. **Users**
   - Stores user authentication and profile information
   - Differentiates between regular users, artists, and administrators

2. **Artist Profiles**
   - Extended information for users who are artists
   - Professional details like experience, education, exhibitions

3. **Artworks**
   - Central entity for all art pieces in the marketplace
   - Details include dimensions, pricing, materials, creation date
   - Status tracking (available, sold, reserved)

4. **Categories**
   - Hierarchical structure for artwork classification
   - Main categories include Paintings, Sculptures, Photography, etc.

### Supporting Entities

- **Styles**: Different art styles (Abstract, Realism, etc.)
- **Media Types**: Materials and techniques used (Oil, Acrylic, Digital, etc.)
- **Tags**: Keywords for improved artwork searchability
- **Artwork Images**: Multiple images per artwork

### User Interaction Entities

- **Wishlists & Wishlist Items**: Save favorite artworks
- **Shopping Carts & Cart Items**: E-commerce functionality
- **Orders & Order Items**: Purchase history and details
- **Reviews**: User feedback and ratings

### Communication & Content

- **Messages**: Direct communication between users
- **Blog Posts**: Content marketing
- **Events**: Exhibitions, auctions, workshops

## Frontend-Database Integration

The project includes JavaScript that enables the frontend to communicate with the backend database through RESTful API calls. Key features include:

- Dynamic loading of artworks from the database
- Filtering by category, style, medium, and price
- User authentication and account management
- Shopping cart and wishlist functionality
- Artist and artwork details

## Running the Project

1. Set up the database using the SQL schema in `database_schema.sql`
2. Configure a `.env` file with your database credentials
3. Install Node.js dependencies: `npm install express sequelize mysql2 dotenv`
4. Set up the backend API server (not included)
5. Open `marketplace-integrated.html` in your browser

## Development Team

Created by Austin Cain, Brandon Horn, Phoenix Hussey, Rhett Parker, James Ward, Teresa Williams
