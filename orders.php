<?php
session_start();
include 'db.php';

// Redirect jika user belum login
if (!isset($_SESSION['login_user'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['login_user'];

// Ambil data user
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Ambil riwayat pesanan
$sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan - KOPTE TARIK</title>
    <link rel="icon" href="image/logokopte.jpeg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .orders-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }
        
        .orders-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .orders-header h1 {
            color: #443;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .order-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .order-header {
            background: #443;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .order-id {
            font-weight: bold;
            font-size: 1.1rem;
        }
        
        .order-date {
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        .order-content {
            padding: 20px;
        }
        
        .order-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
        }
        
        .info-label {
            font-weight: bold;
            color: #443;
            margin-bottom: 5px;
        }
        
        .info-value {
            color: #666;
        }
        
        .order-items {
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        
        .order-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .order-item img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }
        
        .order-item-info {
            flex: 1;
        }
        
        .order-item-name {
            font-weight: bold;
            color: #443;
        }
        
        .order-item-details {
            color: #666;
            font-size: 0.9rem;
        }
        
        .order-total {
            text-align: right;
            font-weight: bold;
            font-size: 1.2rem;
            color: #443;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #eee;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: bold;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-confirmed {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .status-completed {
            background: #d4edda;
            color: #155724;
        }
        
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        
        .no-orders {
            text-align: center;
            padding: 50px;
            color: #666;
        }
        
        .no-orders i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #ddd;
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
            margin-top: 20px;
        }
        
        .btn-back:hover {
            opacity: 0.9;
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
    </style>
</head>
<body>
    <!-- HEADER -->
    <header class="header">
      <div class="logo-container">
        <img src="image/logokopte.jpeg" alt="Kopte Logo" class="logo-image" />
        <a class="logo-text"
          >Kopte Tarik</a>
      </div>
      <nav class="navbar">
        <a href="index.php#home" class="nav-link active" data-section="home">Home</a>
        <a href="index.php#about" class="nav-link" data-section="about">Tentang</a>
        <a href="index.php#menu" class="nav-link" data-section="menu">Menu</a>
        <a href="index.php#galeri" class="nav-link" data-section="galeri">Galeri</a>
        <a href="index.php#book" class="nav-link" data-section="book">Kontak</a>
      </nav>
      
      <div class="header-right">
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
              <span class="username-header" onclick="toggleDropdown()"><i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['login_user']); ?></span>
              <div class="dropdown-content" id="profileDropdown">
                <a href="orders.php"><i class="fas fa-list"></i> Riwayat Pesanan</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
              </div>
            </div>
          <?php else: ?>
            <div class="profile-dropdown">
              <a href="#" onclick="toggleDropdown()" title="Login/Signup"><i class="fas fa-user"></i></a>
              <div class="dropdown-content" id="profileDropdown">
                <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                <a href="signup.php"><i class="fas fa-user-plus"></i> Signup</a>
              </div>
            </div>
          <?php endif; ?>
        </div>
        </div>
      <div class="audio-1">
        <audio id="audio" src="lofi-song-toybox-by-lofium-242708.mp3" preload="auto" autoplay loop></audio>
        <button id="playPauseBtn" class="play-pause-btn" style="display:none;">
          <i class="fas fa-play"></i>
        </button>
              </div>
    </header>

    <div class="orders-container" style="margin-top: 110px;">
        <div class="orders-header">
            <h1><i class="fas fa-list"></i> Riwayat Pesanan</h1>
        </div>

        <?php if (empty($orders)): ?>
            <div class="no-orders">
                <i class="fas fa-shopping-bag"></i>
                <h2>Belum Ada Pesanan</h2>
                <p>Anda belum memiliki riwayat pesanan.</p>
                <a href="index.php#menu" class="btn-back">
                    <i class="fas fa-utensils"></i> Pesan Sekarang
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <div class="order-id">Order #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></div>
                            <div class="order-date"><?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></div>
                        </div>
                        <div>
                            <span class="status-badge status-<?php echo $order['status']; ?>">
                                <?php 
                                $status_text = [
                                    'pending' => 'Menunggu Konfirmasi',
                                    'confirmed' => 'Dikonfirmasi',
                                    'completed' => 'Selesai',
                                    'cancelled' => 'Dibatalkan'
                                ];
                                echo $status_text[$order['status']];
                                ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="order-content">
                        <div class="order-info">
                            <div class="info-item">
                                <span class="info-label">Total Pembayaran</span>
                                <span class="info-value">Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Metode Pembayaran</span>
                                <span class="info-value">
                                    <?php 
                                    $payment_text = [
                                        'cash' => 'Bayar di Tempat (Cash)',
                                        'transfer' => 'Transfer Bank',
                                        'ewallet' => 'E-Wallet'
                                    ];
                                    echo $payment_text[$order['payment_method']];
                                    ?>
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Alamat Pengiriman</span>
                                <span class="info-value"><?php echo htmlspecialchars($order['delivery_address']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Nomor Telepon</span>
                                <span class="info-value"><?php echo htmlspecialchars($order['phone']); ?></span>
                            </div>
                        </div>
                        
                        <div class="order-items">
                            <h3>Item Pesanan:</h3>
                            <?php
                            // Ambil detail order items
                            $sql = "SELECT oi.*, p.name, p.image FROM order_items oi 
                                    JOIN products p ON oi.product_id = p.id 
                                    WHERE oi.order_id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $order['id']);
                            $stmt->execute();
                            $order_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                            $stmt->close();
                            ?>
                            
                            <?php foreach ($order_items as $item): ?>
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
                                        Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <div class="order-total">
                                Total: Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
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