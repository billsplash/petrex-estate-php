CREATE DATABASE IF NOT EXISTS petrex_estate;
USE petrex_estate;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(255),
  email VARCHAR(255) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  phone VARCHAR(50),
  role ENUM('user', 'admin') DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE agents (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  phone VARCHAR(50) NOT NULL,
  avatar_url VARCHAR(500),
  bio TEXT,
  properties_sold INT DEFAULT 0,
  rating DECIMAL(3,2) DEFAULT 5.00,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE properties (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  price DECIMAL(15,2) NOT NULL,
  location VARCHAR(255) NOT NULL,
  state VARCHAR(100) NOT NULL,
  bedrooms INT DEFAULT 0,
  bathrooms INT DEFAULT 0,
  toilets INT DEFAULT 0,
  size DECIMAL(10,2),
  type ENUM('sale', 'rent') NOT NULL,
  status ENUM('available', 'sold', 'rented') DEFAULT 'available',
  featured TINYINT(1) DEFAULT 0,
  image_url VARCHAR(500),
  agent_id INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (agent_id) REFERENCES agents(id) ON DELETE SET NULL
);

CREATE TABLE blog_posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  content LONGTEXT NOT NULL,
  excerpt TEXT,
  image_url VARCHAR(500),
  author_name VARCHAR(255),
  published TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE inquiries (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  phone VARCHAR(50),
  message TEXT NOT NULL,
  property_id INT,
  status ENUM('pending', 'replied', 'closed') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE SET NULL
);

INSERT INTO users (full_name, email, password, role) VALUES ('Admin User', 'admin@petrex-estate.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

INSERT INTO agents (full_name, email, phone, bio, properties_sold, rating) VALUES
('Emeka Johnson', 'emeka@petrex-estate.com', '+234 801 234 5678', 'Experienced real estate agent with 10 years in Lagos market.', 45, 4.8),
('Chioma Okafor', 'chioma@petrex-estate.com', '+234 802 345 6789', 'Specialist in Abuja luxury properties and commercial real estate.', 38, 4.9),
('Bola Adeleke', 'bola@petrex-estate.com', '+234 803 456 7890', 'Port Harcourt based agent with expertise in oil workers housing.', 29, 4.7),
('Fatima Musa', 'fatima@petrex-estate.com', '+234 804 567 8901', 'Affordable housing specialist serving Kano and Kaduna states.', 22, 4.6);

INSERT INTO properties (title, description, price, location, state, bedrooms, bathrooms, toilets, size, type, status, featured) VALUES
('Luxury 4 Bedroom Duplex', 'A stunning luxury duplex in the heart of Lekki Phase 1. Features modern finishes, fitted kitchen, and 24/7 security.', 85000000, 'Lekki Phase 1, Lagos', 'Lagos', 4, 4, 5, 350, 'sale', 'available', 1),
('Modern 3 Bedroom Apartment', 'Newly built 3 bedroom apartment in Gwarinpa. Close to schools, hospitals and shopping malls.', 45000000, 'Gwarinpa, Abuja', 'Abuja', 3, 3, 4, 200, 'sale', 'available', 1),
('2 Bedroom Flat for Rent', 'Spacious 2 bedroom flat in Ikeja GRA. Includes parking space and generator.', 1500000, 'Ikeja GRA, Lagos', 'Lagos', 2, 2, 3, 120, 'rent', 'available', 1),
('5 Bedroom Mansion', 'Exquisite 5 bedroom mansion with swimming pool, gym, and smart home features in Maitama.', 250000000, 'Maitama, Abuja', 'Abuja', 5, 5, 6, 600, 'sale', 'available', 1),
('3 Bedroom Bungalow', 'Beautiful bungalow in a serene estate in Port Harcourt.', 25000000, 'GRA Phase 2, Port Harcourt', 'Rivers', 3, 2, 3, 180, 'sale', 'available', 0),
('1 Bedroom Self Contain', 'Clean and affordable self contain in Surulere. Perfect for young professionals.', 600000, 'Surulere, Lagos', 'Lagos', 1, 1, 1, 60, 'rent', 'available', 0);

INSERT INTO blog_posts (title, content, excerpt, author_name, published) VALUES
('Top 10 Real Estate Investment Tips in Nigeria 2024', 'Investing in Nigerian real estate has never been more exciting. Here are the top 10 tips to guide your investment journey: 1. Research the location thoroughly before buying. 2. Verify all property documents including C of O and Survey Plan. 3. Work with reputable agents like those at Petrex Estate. 4. Consider both short-term and long-term returns. 5. Invest in developing areas for better appreciation. 6. Have a lawyer review all contracts before signing. 7. Inspect the property multiple times before purchase. 8. Understand the true cost including taxes and agency fees. 9. Consider rental yield if investing for income. 10. Diversify your real estate portfolio across states.', 'Discover the best strategies for real estate investment in Nigeria this year.', 'Emeka Johnson', 1),
('How to Buy Your First Home in Lagos', 'Buying your first home in Lagos can be overwhelming but with the right guidance it becomes easy. Start by determining your budget and getting pre-approved for a mortgage if needed. Research neighbourhoods that suit your lifestyle — from Lekki to Surulere, each area offers unique advantages. Always verify the property title documents, especially the Certificate of Occupancy (C of O). Hire a qualified lawyer to review all documentation. Inspect the property thoroughly for structural issues, water supply, and security. Negotiate the price and ensure all agreements are in writing. Finally, complete the transaction at the appropriate land registry office.', 'A complete guide for first-time home buyers in Lagos, Nigeria.', 'Chioma Okafor', 1),
('Understanding Property Documents in Nigeria', 'Before buying any property in Nigeria, you must understand the key documents. The Certificate of Occupancy (C of O) is the most important — it proves legal ownership. The Survey Plan shows the exact boundaries of the land. The Deed of Assignment transfers ownership from seller to buyer. The Governor\'s Consent is required for properties in states where the government has interest. The Building Plan Approval confirms the structure was legally constructed. Always conduct a search at the land registry to confirm there are no encumbrances on the property. Working with a reputable real estate company like Petrex Estate ensures all documents are verified before you commit your funds.', 'Learn about C of O, Survey Plan, Deed of Assignment and other property documents.', 'Admin User', 1);
