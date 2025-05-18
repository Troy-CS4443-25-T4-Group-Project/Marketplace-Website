# Art Marketplace Database Schema - ER Diagram Description

## Core Entities

### Users
- **Primary Key**: `user_id`
- Stores basic user information including authentication data
- Tracks whether a user is an artist, admin, or regular user
- Linked to user_contact for detailed contact information

### Artist Profiles
- **Primary Key**: `artist_id`
- **Foreign Key**: `user_id` → users(user_id)
- Extended information for users who are artists
- Contains professional details like specialty, experience, exhibitions

### Artworks
- **Primary Key**: `artwork_id`
- **Foreign Key**: `artist_id` → artist_profiles(artist_id)
- **Foreign Key**: `category_id` → categories(category_id)
- **Foreign Key**: `style_id` → styles(style_id)
- **Foreign Key**: `media_id` → media_types(media_id)
- Central entity for all art pieces in the marketplace
- Contains detailed artwork information including dimensions, pricing
- Tracks inventory and sales status

### Categories
- **Primary Key**: `category_id`
- **Foreign Key**: `parent_category_id` → categories(category_id)
- Hierarchical structure for art categories
- Self-referencing for subcategories

## Supporting Entities

### Styles
- **Primary Key**: `style_id`
- Art styles (Abstract, Realism, Impressionism, etc.)

### Media Types
- **Primary Key**: `media_id`
- Materials and techniques (Oil, Acrylic, Clay, etc.)

### Tags
- **Primary Key**: `tag_id`
- Keywords for improved artwork searchability

### Artwork Images
- **Primary Key**: `image_id`
- **Foreign Key**: `artwork_id` → artworks(artwork_id)
- Multiple images per artwork with display order

## User Interactions

### Wishlists
- **Primary Key**: `wishlist_id`
- **Foreign Key**: `user_id` → users(user_id)
- Users can have multiple named wishlists

### Shopping Carts
- **Primary Key**: `cart_id`
- **Foreign Key**: `user_id` → users(user_id)
- Supports guest shopping with session_id

### Orders
- **Primary Key**: `order_id`
- **Foreign Key**: `user_id` → users(user_id)
- Complete order information including shipping, billing, payment status

### Reviews
- **Primary Key**: `review_id`
- **Foreign Key**: `user_id` → users(user_id)
- **Foreign Key**: `artwork_id` → artworks(artwork_id)
- **Foreign Key**: `order_item_id` → order_items(order_item_id)
- User ratings and feedback on purchased art

### Follows
- **Primary Key**: `follow_id`
- **Foreign Keys**: `follower_id` → users(user_id), `artist_id` → artist_profiles(artist_id)
- Tracks which users follow which artists

## Many-to-Many Relationships

### artwork_tags
- Links artworks to multiple tags
- **Composite Primary Key**: (artwork_id, tag_id)

### wishlist_items
- Links wishlists to artworks
- **Composite Primary Key**: (wishlist_id, artwork_id)

### event_artists
- Links events to participating artists
- **Composite Primary Key**: (event_id, artist_id)

## Content Entities

### Messages
- **Primary Key**: `message_id`
- **Foreign Keys**: `sender_id`, `recipient_id` → users(user_id)
- Private communication between users

### Blog Posts
- **Primary Key**: `post_id`
- **Foreign Key**: `author_id` → users(user_id)
- Content marketing for the marketplace

### Events
- **Primary Key**: `event_id`
- Exhibitions, auctions, workshops, etc.
- Can be linked to multiple artists

## Key Database Relationships

1. **One-to-Many**:
   - User → Artist Profile (if the user is an artist)
   - Artist → Multiple Artworks
   - Category → Multiple Artworks
   - User → Multiple Orders
   - Order → Multiple Order Items

2. **Many-to-Many**:
   - Artworks ↔ Tags (through artwork_tags)
   - Wishlists ↔ Artworks (through wishlist_items)
   - Events ↔ Artists (through event_artists)

3. **Self-Referencing**:
   - Categories can have parent categories for hierarchical organization

## Indexing Strategy

Indexes are created on frequently queried columns for optimal performance:
- Price, status, category, artist on the artworks table
- User ID and status on the orders table
- Artwork ID on reviews table
