-- Query untuk membuat database dan tabel-tabel koptte_db
-- Jalankan query ini di phpMyAdmin atau MySQL command line

-- 1. Buat database
CREATE DATABASE IF NOT EXISTS koptte_db;

-- 2. Gunakan database
USE koptte_db;

-- 3. Buat tabel users
CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. Buat tabel admins
CREATE TABLE IF NOT EXISTS admins (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 5. Buat tabel products
CREATE TABLE IF NOT EXISTS products (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) NOT NULL,
    description TEXT,
    stock INT(11) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 6. Buat tabel orders
CREATE TABLE IF NOT EXISTS orders (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    delivery_address TEXT NOT NULL,
    phone VARCHAR(20) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 7. Buat tabel order_items
CREATE TABLE IF NOT EXISTS order_items (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    order_id INT(11) NOT NULL,
    product_id INT(11) NOT NULL,
    quantity INT(11) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- 8. Buat tabel contacts
CREATE TABLE IF NOT EXISTS contacts (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 9. Insert data produk default
INSERT INTO products (name, price, image, description, stock) VALUES
('Kopi Hitam', 15000.00, 'menu1.png', 'Kopi hitam murni tanpa gula', 50),
('Kopi Susu', 20000.00, 'menu2.png', 'Kopi dengan susu segar', 45),
('Kopi Tarik', 25000.00, 'menu3.png', 'Kopi tarik tradisional', 40),
('Kopi Cappuccino', 30000.00, 'menu4.png', 'Cappuccino dengan foam susu', 35),
('Kopi Latte', 28000.00, 'menu5.png', 'Latte dengan susu steamed', 30),
('Kopi Mocha', 32000.00, 'menu6.png', 'Mocha dengan cokelat', 25);

-- 10. Insert admin default
INSERT INTO admins (username, email, password) VALUES
('admin', 'admin@kopte.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- 11. Tampilkan struktur tabel yang dibuat
SHOW TABLES;

-- 12. Tampilkan struktur detail setiap tabel
DESCRIBE users;
DESCRIBE admins;
DESCRIBE products;
DESCRIBE orders;
DESCRIBE order_items;
DESCRIBE contacts; 