<?php 
session_start(); 
include 'db.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semua Menu - Kopte Tarik</title>
    <link rel="icon" href="image/logokopte.jpeg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .fullmenu-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
       
        .menu-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }
        
        .menu-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .menu-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .menu-item h1 {
            font-size: 1.3rem;
            font-weight: bold;
            color: #333;
            margin: 15px 0 10px 0;
        }
        .menu-item h2 {
            font-size: 1.1rem;
            color: #ee0979;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .menu-description {
            color: #666;
            font-size: 0.9rem;
            margin: 0 20px 15px 20px;
            line-height: 1.4;
        }
        .menu-stock {
            color: #28a745;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }
        
        
        .no-results {
            text-align: center;
            padding: 50px;
            color: #666;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <header class="header">
        <div class="logo-container">
            <img src="image/logokopte.jpeg" alt="Kopte Logo" class="logo-image" />
            <a class="logo-text">Kopte Tarik</a>
        </div>
        <nav class="navbar">
            <a href="index.php#home">Home</a>
            <a href="index.php#about">Tentang</a>
            <a href="index.php#menu">Menu</a>
            <a href="index.php#galeri">Galeri</a>
            <a href="index.php#book">Kontak</a>
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
    </header>

    <div class="fullmenu-container" style="margin-top: 110px;">
      
        <h1 class="heading">Menu <span>Menu</span></h1>
        
       

        <div class="menu-container" id="menuGrid">
            <?php
            $menu_query = "SELECT * FROM products ORDER BY name ASC";
            $menu_result = $conn->query($menu_query);
            
            if ($menu_result->num_rows > 0):
                while($product = $menu_result->fetch_assoc()):
            ?>
            <div class="menu-item" data-name="<?php echo strtolower(htmlspecialchars($product['name'])); ?>" data-aos="fade-up">
                <img src="image/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                <h2>Harga: Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></h2>
                <?php if (!empty($product['description'])): ?>
                    <div class="menu-description"><?php echo htmlspecialchars($product['description']); ?></div>
                <?php endif; ?>
                <div class="menu-stock">
                    <i class="fas fa-box" style="margin-right: 5px;"></i>
                    Stok: <?php echo isset($product['stock']) ? $product['stock'] : '0'; ?>
                </div>
                <form method="POST" action="cart.php" style="display: inline;">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" name="add_to_cart" class="add-to-cart-btn" <?php echo (isset($product['stock']) && $product['stock'] <= 0) ? 'disabled' : ''; ?>>
                        <?php if (isset($product['stock']) && $product['stock'] <= 0): ?>
                            Stok Habis
                        <?php else: ?>
                            Tambahkan ke Keranjang
                        <?php endif; ?>
                    </button>
                </form>
            </div>
            <?php 
                endwhile;
            else:
            ?>
            <div class="no-results">
                <i class="fas fa-utensils" style="font-size: 3rem; color: #ddd; margin-bottom: 20px;"></i>
                <p>Belum ada menu tersedia</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
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
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
        
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
</body>
</html> 