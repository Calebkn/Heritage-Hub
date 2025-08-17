-- MarketPlace Database Schema
-- Run this SQL to create the database structure

CREATE DATABASE IF NOT EXISTS marketplace_db;
USE marketplace_db;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    avatar VARCHAR(500),
    role ENUM('vendor', 'buyer') NOT NULL DEFAULT 'buyer',
    google_id VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vendor_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category VARCHAR(100) NOT NULL,
    image VARCHAR(500),
    stock INT NOT NULL DEFAULT 0,
    rating DECIMAL(3, 2) DEFAULT 0.00,
    reviews INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (vendor_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Orders table
CREATE TABLE orders (
    id VARCHAR(20) PRIMARY KEY,
    buyer_id INT NOT NULL,
    vendor_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'confirmed', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    tracking_number VARCHAR(50),
    shipping_address TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (vendor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Tracking events table
CREATE TABLE tracking_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tracking_number VARCHAR(50) NOT NULL,
    status VARCHAR(100) NOT NULL,
    location VARCHAR(255),
    description TEXT,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tracking_number (tracking_number)
);

-- Messages table (for vendor-buyer communication)
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    order_id VARCHAR(20),
    subject VARCHAR(255),
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL
);

-- Wishlists table
CREATE TABLE wishlists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_wishlist (user_id, product_id)
);

-- Sample data
INSERT INTO users (email, name, avatar, role) VALUES
('vendor1@example.com', 'TechStore Pro', 'https://api.dicebear.com/7.x/avataaars/svg?seed=vendor1', 'vendor'),
('vendor2@example.com', 'Fashion Hub', 'https://api.dicebear.com/7.x/avataaars/svg?seed=vendor2', 'vendor'),
('buyer1@example.com', 'John Doe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=buyer1', 'buyer');

INSERT INTO products (vendor_id, title, description, price, category, image, stock, rating, reviews) VALUES
(1, 'Wireless Bluetooth Headphones', 'Premium quality wireless headphones with noise cancellation and 30-hour battery life.', 199.99, 'Electronics', 'https://images.pexels.com/photos/3394650/pexels-photo-3394650.jpeg', 25, 4.8, 124),
(2, 'Premium Cotton T-Shirt', 'Comfortable and stylish cotton t-shirt available in multiple colors and sizes.', 29.99, 'Fashion', 'https://images.pexels.com/photos/1020585/pexels-photo-1020585.jpeg', 50, 4.5, 89),
(1, 'Smart Fitness Watch', 'Advanced fitness tracking with heart rate monitor, GPS, and smartphone integration.', 299.99, 'Electronics', 'https://images.pexels.com/photos/437037/pexels-photo-437037.jpeg', 15, 4.7, 67);

INSERT INTO orders (id, buyer_id, vendor_id, product_id, quantity, total_amount, status, tracking_number, shipping_address) VALUES
('ORD001', 3, 1, 1, 1, 199.99, 'shipped', 'TRK123456789', '123 Main St, Johannesburg, GP 2000');

INSERT INTO tracking_events (tracking_number, status, location, description) VALUES
('TRK123456789', 'Shipped', 'Warehouse - Cape Town', 'Package has been shipped'),
('TRK123456789', 'In Transit', 'Distribution Center - Johannesburg', 'Package is on its way to destination');