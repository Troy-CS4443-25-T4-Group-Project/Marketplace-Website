/**
 * database.js - Database connection and model setup for Art Marketplace
 * 
 * This is a sample implementation using Node.js, Express, and Sequelize ORM
 * to connect the frontend to the database schema created.
 */

// Required packages
// npm install express sequelize mysql2 dotenv
const { Sequelize, DataTypes } = require('sequelize');
require('dotenv').config();

// Database connection
const sequelize = new Sequelize(
  process.env.DB_NAME,
  process.env.DB_USER,
  process.env.DB_PASSWORD,
  {
    host: process.env.DB_HOST,
    dialect: 'mysql',
    logging: false, // Set to console.log to see SQL queries
    pool: {
      max: 5,
      min: 0,
      acquire:
30000,
      idle: 10000
    }
  }
);

// Test the connection
async function testConnection() {
  try {
    await sequelize.authenticate();
    console.log('Database connection has been established successfully.');
  } catch (error) {
    console.error('Unable to connect to the database:', error);
  }
}

// Define Models
const User = sequelize.define('User', {
  user_id: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true
  },
  username: {
    type: DataTypes.STRING(50),
    allowNull: false,
    unique: true
  },
  email: {
    type: DataTypes.STRING(100),
    allowNull: false,
    unique: true,
    validate: {
      isEmail: true
    }
  },
  password_hash: {
    type: DataTypes.STRING(255),
    allowNull: false
  },
  first_name: DataTypes.STRING(50),
  last_name: DataTypes.STRING(50),
  profile_image: DataTypes.STRING(255),
  bio: DataTypes.TEXT,
  is_artist: {
    type: DataTypes.BOOLEAN,
    defaultValue: false
  },
  is_admin: {
    type: DataTypes.BOOLEAN,
    defaultValue: false
  }
}, {
  tableName: 'users',
  timestamps: true,
  createdAt: 'created_at',
  updatedAt: 'updated_at'
});

const UserContact = sequelize.define('UserContact', {
  contact_id: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true
  },
  phone: DataTypes.STRING(20),
  address_line1: DataTypes.STRING(100),
  address_line2: DataTypes.STRING(100),
  city: DataTypes.STRING(50),
  state: DataTypes.STRING(50),
  postal_code: DataTypes.STRING(20),
  country: DataTypes.STRING(50)
}, {
  tableName: 'user_contact',
  timestamps: false
});

const ArtistProfile = sequelize.define('ArtistProfile', {
  artist_id: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true
  },
  artist_name: DataTypes.STRING(100),
  specialty: DataTypes.STRING(100),
  years_of_experience: DataTypes.INTEGER,
  education: DataTypes.TEXT,
  exhibitions: DataTypes.TEXT,
  awards: DataTypes.TEXT,
  website: DataTypes.STRING(255),
  social_media_links: DataTypes.JSON,
  commission_available: {
    type: DataTypes.BOOLEAN,
    defaultValue: false
  }
}, {
  tableName: 'artist_profiles',
  timestamps: false
});

const Category = sequelize.define('Category', {
  category_id: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true
  },
  name: {
    type: DataTypes.STRING(50),
    allowNull: false,
    unique: true
  },
  description: DataTypes.TEXT
}, {
  tableName: 'categories',
  timestamps: false
});

const Style = sequelize.define('Style', {
  style_id: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true
  },
  name: {
    type: DataTypes.STRING(50),
    allowNull: false,
    unique: true
  },
  description: DataTypes.TEXT
}, {
  tableName: 'styles',
  timestamps: false
});

const MediaType = sequelize.define('MediaType', {
  media_id: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true
  },
  name: {
    type: DataTypes.STRING(50),
    allowNull: false,
    unique: true
  },
  description: DataTypes.TEXT
}, {
  tableName: 'media_types',
  timestamps: false
});

const Artwork = sequelize.define('Artwork', {
  artwork_id: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true
  },
  title: {
    type: DataTypes.STRING(100),
    allowNull: false
  },
  description: DataTypes.TEXT,
  creation_date: DataTypes.DATEONLY,
  width: DataTypes.DECIMAL(10, 2),
  height: DataTypes.DECIMAL(10, 2),
  depth: DataTypes.DECIMAL(10, 2),
  dimensions_unit: {
    type: DataTypes.STRING(10),
    defaultValue: 'cm'
  },
  price: {
    type: DataTypes.DECIMAL(10, 2),
    allowNull: false
  },
  currency: {
    type: DataTypes.STRING(3),
    defaultValue: 'USD'
  },
  inventory_count: {
    type: DataTypes.INTEGER,
    defaultValue: 1
  },
  is_original: {
    type: DataTypes.BOOLEAN,
    defaultValue: true
  },
  is_framed: {
    type: DataTypes.BOOLEAN,
    defaultValue: false
  },
  is_signed: {
    type: DataTypes.BOOLEAN,
    defaultValue: false
  },
  is_featured: {
    type: DataTypes.BOOLEAN,
    defaultValue: false
  },
  status: {
    type: DataTypes.ENUM('available', 'sold', 'reserved', 'hidden'),
    defaultValue: 'available'
  },
  views: {
    type: DataTypes.INTEGER,
    defaultValue: 0
  }
}, {
  tableName: 'artworks',
  timestamps: true,
  createdAt: 'created_at',
  updatedAt: 'updated_at'
});

const ArtworkImage = sequelize.define('ArtworkImage', {
  image_id: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true
  },
  image_url: {
    type: DataTypes.STRING(255),
    allowNull: false
  },
  is_primary: {
    type: DataTypes.BOOLEAN,
    defaultValue: false
  },
  display_order: {
    type: DataTypes.INTEGER,
    defaultValue: 0
  }
}, {
  tableName: 'artwork_images',
  timestamps: true,
  createdAt: 'created_at',
  updatedAt: false
});

// Setup table relationships
User.hasOne(UserContact, {
  foreignKey: 'user_id',
  onDelete: 'CASCADE'
});
UserContact.belongsTo(User, {
  foreignKey: 'user_id'
});

User.hasOne(ArtistProfile, {
  foreignKey: 'user_id',
  onDelete: 'CASCADE'
});
ArtistProfile.belongsTo(User, {
  foreignKey: 'user_id'
});

Category.hasMany(Artwork, {
  foreignKey: 'category_id'
});
Artwork.belongsTo(Category, {
  foreignKey: 'category_id'
});

Category.belongsTo(Category, {
  as: 'ParentCategory',
  foreignKey: 'parent_category_id'
});
Category.hasMany(Category, {
  as: 'SubCategories',
  foreignKey: 'parent_category_id'
});

Style.hasMany(Artwork, {
  foreignKey: 'style_id'
});
Artwork.belongsTo(Style, {
  foreignKey: 'style_id'
});

MediaType.hasMany(Artwork, {
  foreignKey: 'media_id'
});
Artwork.belongsTo(MediaType, {
  foreignKey: 'media_id'
});

ArtistProfile.hasMany(Artwork, {
  foreignKey: 'artist_id'
});
Artwork.belongsTo(ArtistProfile, {
  foreignKey: 'artist_id'
});

Artwork.hasMany(ArtworkImage, {
  foreignKey: 'artwork_id',
  onDelete: 'CASCADE'
});
ArtworkImage.belongsTo(Artwork, {
  foreignKey: 'artwork_id'
});

// Example function to fetch featured artworks for homepage
async function getFeaturedArtworks() {
  try {
    const featuredArtworks = await Artwork.findAll({
      where: {
        is_featured: true,
        status: 'available'
      },
      include: [
        {
          model: ArtworkImage,
          where: { is_primary: true },
          required: false
        },
        {
          model: ArtistProfile,
          attributes: ['artist_id', 'artist_name']
        },
        {
          model: Category,
          attributes: ['category_id', 'name']
        }
      ],
      limit: 8,
      order: [['created_at', 'DESC']]
    });
    
    return featuredArtworks;
  } catch (error) {
    console.error('Error fetching featured artworks:', error);
    return [];
  }
}

// Example function to search for artworks with filters
async function searchArtworks(filters) {
  const { 
    category, 
    style, 
    medium, 
    priceMin, 
    priceMax,
    artist,
    searchTerm 
  } = filters;
  
  // Build where conditions
  const whereConditions = {
    status: 'available'
  };
  
  if (searchTerm) {
    whereConditions[Sequelize.Op.or] = [
      { title: { [Sequelize.Op.like]: `%${searchTerm}%` } },
      { description: { [Sequelize.Op.like]: `%${searchTerm}%` } }
    ];
  }
  
  if (category) {
    whereConditions.category_id = category;
  }
  
  if (style) {
    whereConditions.style_id = style;
  }
  
  if (medium) {
    whereConditions.media_id = medium;
  }
  
  if (priceMin || priceMax) {
    whereConditions.price = {};
    
    if (priceMin) {
      whereConditions.price[Sequelize.Op.gte] = priceMin;
    }
    
    if (priceMax) {
      whereConditions.price[Sequelize.Op.lte] = priceMax;
    }
  }
  
  // Artist filter needs to join with artist_profiles
  const includeConditions = [
    {
      model: ArtworkImage,
      where: { is_primary: true },
      required: false
    },
    {
      model: Category,
      attributes: ['category_id', 'name']
    },
    {
      model: Style,
      attributes: ['style_id', 'name']
    },
    {
      model: MediaType,
      attributes: ['media_id', 'name']
    },
    {
      model: ArtistProfile,
      attributes: ['artist_id', 'artist_name', 'user_id']
    }
  ];
  
  if (artist) {
    includeConditions.find(inc => inc.model === ArtistProfile).where = { artist_id: artist };
  }
  
  try {
    const artworks = await Artwork.findAll({
      where: whereConditions,
      include: includeConditions,
      order: [['created_at', 'DESC']]
    });
    
    return artworks;
  } catch (error) {
    console.error('Error searching artworks:', error);
    return [];
  }
}

// Example function to get artist details with their artworks
async function getArtistWithArtworks(artistId) {
  try {
    const artist = await ArtistProfile.findByPk(artistId, {
      include: [
        {
          model: User,
          attributes: ['username', 'profile_image', 'bio']
        },
        {
          model: Artwork,
          where: { status: 'available' },
          required: false,
          include: [
            {
              model: ArtworkImage,
              where: { is_primary: true },
              required: false
            }
          ]
        }
      ]
    });
    
    return artist;
  } catch (error) {
    console.error('Error fetching artist details:', error);
    return null;
  }
}

// Export models and functions
module.exports = {
  sequelize,
  testConnection,
  models: {
    User,
    UserContact,
    ArtistProfile,
    Category,
    Style,
    MediaType,
    Artwork,
    ArtworkImage
  },
  queries: {
    getFeaturedArtworks,
    searchArtworks,
    getArtistWithArtworks
  }
};
