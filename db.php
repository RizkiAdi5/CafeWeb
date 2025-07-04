<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "coffe_db";

// Buat koneksi tanpa memilih database
$conn = new mysqli($servername, $username, $password);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Buat database jika belum ada
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    // Pilih database
    $conn->select_db($dbname);
    
    // Buat tabel users jika belum ada
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->query($sql);
    
    // Buat tabel admins jika belum ada
    $sql = "CREATE TABLE IF NOT EXISTS admins (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->query($sql);
    
    // Buat tabel products jika belum ada
    $sql = "CREATE TABLE IF NOT EXISTS products (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        image VARCHAR(255) NOT NULL,
        description TEXT,
        stock INT(11) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->query($sql);
    
    // Buat tabel orders jika belum ada
    $sql = "CREATE TABLE IF NOT EXISTS orders (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11) NOT NULL,
        total_amount DECIMAL(10,2) NOT NULL,
        status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
        order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        delivery_address TEXT NOT NULL,
        phone VARCHAR(20) NOT NULL,
        payment_method VARCHAR(50) NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $conn->query($sql);
    
    // Buat tabel order_items jika belum ada
    $sql = "CREATE TABLE IF NOT EXISTS order_items (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        order_id INT(11) NOT NULL,
        product_id INT(11) NOT NULL,
        quantity INT(11) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )";
    $conn->query($sql);
    
    // Buat tabel contacts jika belum ada
    $sql = "CREATE TABLE IF NOT EXISTS contacts (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        date_visit DATE NOT NULL,
        time_visit TIME NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->query($sql);
    
    // Cek apakah kolom 'status' sudah ada di tabel contacts
    $check_status_col = $conn->query("SHOW COLUMNS FROM contacts LIKE 'status'");
    if ($check_status_col->num_rows == 0) {
        $conn->query("ALTER TABLE contacts ADD COLUMN status VARCHAR(20) NOT NULL DEFAULT 'unread'");
    }
    
    // Insert data produk default jika tabel kosong
    $check_products = $conn->query("SELECT COUNT(*) as count FROM products");
    $product_count = $check_products->fetch_assoc()['count'];
    
    if ($product_count == 0) {
        $products = [
            ['name' => 'Kopi Hitam', 'price' => 15000, 'image' => 'menu1.png', 'description' => 'Kopi hitam murni tanpa gula', 'stock' => 50],
            ['name' => 'Kopi Susu', 'price' => 20000, 'image' => 'menu2.png', 'description' => 'Kopi dengan susu segar', 'stock' => 45],
            ['name' => 'Kopi Tarik', 'price' => 25000, 'image' => 'menu3.png', 'description' => 'Kopi tarik tradisional', 'stock' => 40],
            ['name' => 'Kopi Cappuccino', 'price' => 30000, 'image' => 'menu4.png', 'description' => 'Cappuccino dengan foam susu', 'stock' => 35],
            ['name' => 'Kopi Latte', 'price' => 28000, 'image' => 'menu5.png', 'description' => 'Latte dengan susu steamed', 'stock' => 30],
            ['name' => 'Kopi Mocha', 'price' => 32000, 'image' => 'menu6.png', 'description' => 'Mocha dengan cokelat', 'stock' => 25]
        ];
        
        foreach ($products as $product) {
            $stmt = $conn->prepare("INSERT INTO products (name, price, image, description, stock) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sdssi", $product['name'], $product['price'], $product['image'], $product['description'], $product['stock']);
            $stmt->execute();
        }
    }
    
    // Insert admin default jika tabel kosong
    $check_admins = $conn->query("SELECT COUNT(*) as count FROM admins");
    $admin_count = $check_admins->fetch_assoc()['count'];
    
    if ($admin_count == 0) {
        $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO admins (username, email, password) VALUES (?, ?, ?)");
        $admin_username = 'admin';
        $admin_email = 'admin@kopte.com';
        $stmt->bind_param("sss", $admin_username, $admin_email, $admin_password);
        $stmt->execute();
    }
    
} else {
    die("Error membuat database: " . $conn->error);
}
?>
