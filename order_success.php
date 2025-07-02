<?php
session_start();
include 'db.php';

if (!isset($_GET['order_id'])) {
    header('Location: index.php');
    exit();
}

$order_id = $_GET['order_id'];

// Ambil data order
$sql = "SELECT o.*, u.username, u.email FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        WHERE o.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    header('Location: index.php');
    exit();
}

// Ambil detail order items
$sql = "SELECT oi.*, p.name, p.image FROM order_items oi 
        JOIN products p ON oi.product_id = p.id 
        WHERE oi.order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Berhasil - KOPTE TARIK</title>
    <link rel="icon" href="image/logokopte.jpeg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .success-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            text-align: center;
        }
        
        .success-icon {
            font-size: 5rem;
            color: #28a745;
            margin-bottom: 20px;
        }
        
        .success-title {
            color: #443;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .success-message {
            color: #666;
            font-size: 1.2rem;
            margin-bottom: 30px;
        }
        
        .order-details {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            text-align: left;
        }
        
        .order-details h2 {
            color: #443;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .detail-row:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .order-items {
            margin-top: 20px;
        }
        
        .order-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
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
        
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn-primary {
            background: linear-gradient(45deg, #ee0979, #ff6a00);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-weight: bold;
        }
        
        .btn-secondary {
            background: #666;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn:hover {
            opacity: 0.9;
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

    <div class="success-container" style="margin-top: 110px;">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        
        <h1 class="success-title">Pesanan Berhasil!</h1>
        <p class="success-message">
            Terima kasih telah berbelanja di KOPTE TARIK. Pesanan Anda telah kami terima dan sedang diproses.
        </p>
        
        <div class="order-details">
            <h2><i class="fas fa-receipt"></i> Detail Pesanan</h2>
            
            <div class="detail-row">
                <span>Order ID:</span>
                <span>#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></span>
            </div>
            
            <div class="detail-row">
                <span>Tanggal Pesanan:</span>
                <span><?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></span>
            </div>
            
            <div class="detail-row">
                <span>Status:</span>
                <span>
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
                </span>
            </div>
            
            <div class="detail-row">
                <span>Metode Pembayaran:</span>
                <span>
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
            
            <div class="detail-row">
                <span>Alamat Pengiriman:</span>
                <span><?php echo htmlspecialchars($order['delivery_address']); ?></span>
            </div>
            
            <div class="detail-row">
                <span>Nomor Telepon:</span>
                <span><?php echo htmlspecialchars($order['phone']); ?></span>
            </div>
            
            <div class="order-items">
                <h3>Item Pesanan:</h3>
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
            </div>
            
            <div class="detail-row">
                <span>Total Pembayaran:</span>
                <span>Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></span>
            </div>
        </div>
        
        <div class="action-buttons">
            <a href="index.php" class="btn-primary">
                <i class="fas fa-home"></i> Kembali ke Beranda
            </a>
            <a href="index.php#menu" class="btn-secondary">
                <i class="fas fa-utensils"></i> Pesan Lagi
            </a>
        </div>
        
        <div style="margin-top: 30px; color: #666; font-size: 0.9rem;">
            <p>
                <i class="fas fa-info-circle"></i> 
                Tim kami akan menghubungi Anda segera untuk konfirmasi pesanan.
            </p>
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