<?php
session_start();
include 'db.php';

// Redirect jika cart kosong
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit();
}

// Redirect jika user belum login
if (!isset($_SESSION['login_user'])) {
    header('Location: login.php?redirect=checkout.php');
    exit();
}

// Ambil data user
$username = $_SESSION['login_user'];
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Ambil data produk dari cart
$cart_items = [];
$total = 0;

$product_ids = array_keys($_SESSION['cart']);
$placeholders = str_repeat('?,', count($product_ids) - 1) . '?';
$sql = "SELECT * FROM products WHERE id IN ($placeholders)";
$stmt = $conn->prepare($sql);
$stmt->bind_param(str_repeat('i', count($product_ids)), ...$product_ids);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $quantity = $_SESSION['cart'][$row['id']];
    $subtotal = $row['price'] * $quantity;
    $total += $subtotal;
    
    $cart_items[] = [
        'id' => $row['id'],
        'name' => $row['name'],
        'price' => $row['price'],
        'image' => $row['image'],
        'quantity' => $quantity,
        'subtotal' => $subtotal
    ];
}
$stmt->close();

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - KOPTE TARIK</title>
    <link rel="icon" href="image/logokopte.jpeg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .checkout-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        
        .checkout-header {
            grid-column: 1 / -1;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .checkout-header h1 {
            color: #443;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .order-summary {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            height: fit-content;
        }
        
        .order-summary h2 {
            color: #443;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }
        
        .order-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .order-item img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .order-item-info {
            flex: 1;
        }
        
        .order-item-name {
            font-weight: bold;
            color: #443;
            margin-bottom: 5px;
        }
        
        .order-item-details {
            color: #666;
            font-size: 0.9rem;
        }
        
        .order-total {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #eee;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .total-row.final {
            font-weight: bold;
            font-size: 1.2rem;
            color: #443;
        }
        
        .checkout-form {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .checkout-form h2 {
            color: #443;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #443;
            font-weight: bold;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            box-sizing: border-box;
        }
        
        .form-group textarea {
            height: 100px;
            resize: vertical;
        }
        
        .error-message {
            background: #ff4757;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .btn-checkout {
            background: linear-gradient(45deg, #ee0979, #ff6a00);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: bold;
            width: 100%;
            margin-top: 20px;
        }
        
        .btn-checkout:hover {
            opacity: 0.9;
        }
        
        .btn-back {
            background: #666;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
        }
        
        .cart-icon {
            position: relative;
        }
        
        .cart-icon a {
            display: block;
            transition: transform 0.2s;
        }
        
        .cart-icon a:hover {
            transform: scale(1.1);
        }
        
        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ff4757;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }
        
        @media (max-width: 768px) {
            .checkout-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="logo-container">
            <img src="image/logokopte.jpeg" alt="Kopte Logo" class="logo-image" />
            <a class="logo-text">Kopte Tarik</a>
        </div>
        <nav class="navbar">
            <a href="index.php#home" class="nav-link active" data-section="home">Home</a>
            <a href="index.php#about" class="nav-link" data-section="about">Tentang</a>
            <a href="index.php#menu" class="nav-link" data-section="menu">Menu</a>
            <a href="index.php#galeri" class="nav-link" data-section="galeri">Galeri</a>
            <a href="index.php#book" class="nav-link" data-section="book">Kontak</a>
        </nav>
        
        <div class="cart-icon">
            <a href="cart.php">
                <img src="image/icon-keranjang.png" alt="Keranjang" class="cart-image" />
                <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                    <span class="cart-count"><?php echo array_sum($_SESSION['cart']); ?></span>
                <?php endif; ?>
            </a>
        </div>
        
        <div class="user-icon">
            <?php if(isset($_SESSION['login_user'])): ?>
                <div class="profile-dropdown">
                    <span class="username-header" onclick="toggleDropdown()">
                        <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['login_user']); ?>
                    </span>
                    <div class="dropdown-content" id="profileDropdown">
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <div class="checkout-container" style="margin-top: 110px;">
        <div class="checkout-header">
            <h1><i class="fas fa-credit-card"></i> Checkout</h1>
        </div>

        <div class="order-summary">
            <h2><i class="fas fa-shopping-bag"></i> Ringkasan Pesanan</h2>
            
            <?php foreach ($cart_items as $item): ?>
                <div class="order-item">
                    <img src="image/<?php echo htmlspecialchars($item['image']); ?>" 
                         alt="<?php echo htmlspecialchars($item['name']); ?>">
                    <div class="order-item-info">
                        <div class="order-item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                        <div class="order-item-details">
                            <?php echo $item['quantity']; ?> x Rp <?php echo number_format($item['price'], 0, ',', '.'); ?>
                        </div>
                    </div>
                    <div class="order-item-total">
                        Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <div class="order-total">
                <div class="total-row">
                    <span>Total Item:</span>
                    <span><?php echo array_sum($_SESSION['cart']); ?></span>
                </div>
                <div class="total-row final">
                    <span>Total Pembayaran:</span>
                    <span>Rp <?php echo number_format($total, 0, ',', '.'); ?></span>
                </div>
            </div>
        </div>

        <div class="checkout-form">
            <h2><i class="fas fa-user"></i> Informasi Pengiriman</h2>
            
            <?php if (isset($error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form id="checkoutForm">
                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" id="name" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                </div>
                
                <div class="form-group">
                    <label for="phone">Nomor Telepon *</label>
                    <input type="tel" id="phone" name="phone" required 
                           placeholder="Contoh: 081234567890">
                </div>
                
                <div class="form-group">
                    <label for="delivery_address">Alamat Pengiriman *</label>
                    <textarea id="delivery_address" name="delivery_address" required 
                              placeholder="Masukkan alamat lengkap pengiriman"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="payment_method">Metode Pembayaran *</label>
                    <select id="payment_method" name="payment_method" required>
                        <option value="">Pilih metode pembayaran</option>
                        <option value="cash">Bayar di Tempat (Cash)</option>
                        <option value="transfer">Transfer Bank</option>
                        <option value="ewallet">E-Wallet (OVO, DANA, GoPay)</option>
                    </select>
                </div>
                
                <button type="submit" class="btn-checkout" id="submitBtn">
                    <i class="fas fa-check"></i> Konfirmasi Pesanan
                </button>
            </form>
            
            <a href="cart.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> Kembali ke Keranjang
            </a>
        </div>
    </div>

    <script>
        function toggleDropdown() {
            document.getElementById("profileDropdown").classList.toggle("show");
        }

        window.onclick = function(event) {
            if (!event.target.matches('.username-header') && !event.target.matches('.fas') && !event.target.matches('a')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }

        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
            
            const formData = {
                delivery_address: document.getElementById('delivery_address').value,
                phone: document.getElementById('phone').value,
                payment_method: document.getElementById('payment_method').value
            };
            
            fetch('process_checkout.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Pesanan berhasil dibuat!');
                    window.location.href = data.redirect;
                } else {
                    alert('Error: ' + data.error);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memproses pesanan');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    </script>

    <!-- Footer -->
    <footer style="background-color: #f8f9fa; padding: 20px; text-align: center;">
      <div>
          <h4 style="padding: 10px; font-size: 13px;">Ikuti Kami di Media Sosial</h4>
          <a href="https://www.instagram.com/kopte.id?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw=="
              target="_blank" style="text-decoration: none; color: #E1306C; margin-right: 15px;">
              <i class="fab fa-instagram" style="font-size: 24px;"></i>
              <b>Instagram</b>
          </a>
          <a href="https://www.tiktok.com/@kopte.id?is_from_webapp=1&sender_device=pc" target="_blank"
              style="text-decoration: none; color: #000000;">
              <i class="fab fa-tiktok" style="font-size: 24px;"></i>
              <b>TikTok</b>
          </a>
      </div>
      <hr style="margin: 20px 0; border: 1px solid #373636;">
      <p style="margin-top: 10px; font-size: 12px;"> <b>Our Team:</b> Ardiansyah, Abang Malik Syahidar, Mohammad Dimas Al
          Fateh |<b>&copy; 2025</b>
      </p>
  </footer>
</body>
</html> 