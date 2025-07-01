<?php
session_start();
include 'db.php';

// Inisialisasi cart jika belum ada
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle add to cart
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'] ?? 1;
    
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
    
    header('Location: cart.php');
    exit();
}

// Handle remove from cart
if (isset($_GET['remove'])) {
    $product_id = $_GET['remove'];
    unset($_SESSION['cart'][$product_id]);
    header('Location: cart.php');
    exit();
}

// Handle update quantity
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $product_id => $quantity) {
        if ($quantity > 0) {
            $_SESSION['cart'][$product_id] = $quantity;
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
    }
    header('Location: cart.php');
    exit();
}

// Ambil data produk dari cart
$cart_items = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
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
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - KOPTE TARIK</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .cart-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }
        
        .cart-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .cart-header h1 {
            color: #443;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .cart-empty {
            text-align: center;
            padding: 50px;
            color: #666;
        }
        
        .cart-empty i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #ddd;
        }
        
        .cart-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .cart-table th {
            background: #443;
            color: white;
            padding: 15px;
            text-align: left;
        }
        
        .cart-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .product-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .quantity-btn {
            background: #443;
            color: white;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 14px;
        }
        
        .quantity-input {
            width: 50px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 5px;
        }
        
        .remove-btn {
            background: #ff4757;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            transition: background-color 0.2s;
        }
        
        .remove-btn:hover {
            background: #e63946;
        }
        
        .cart-summary {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .summary-row:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .cart-actions {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .btn-continue {
            background: #666;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-checkout {
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
        
        .btn-clear {
            background: #ff4757;
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
        
        .back-to-menu {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-to-menu a {
            color: #443;
            text-decoration: none;
            font-weight: bold;
        }
        
        .back-to-menu a:hover {
            text-decoration: underline;
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

    <div class="cart-container" style="margin-top: 110px;">
        <div class="cart-header">
            <h1><i class="fas fa-shopping-cart"></i> Keranjang Belanja</h1>
        </div>

        <?php if (empty($cart_items)): ?>
            <div class="cart-empty">
                <i class="fas fa-shopping-cart"></i>
                <h2>Keranjang Belanja Kosong</h2>
                <p>Belum ada item yang ditambahkan ke keranjang.</p>
                <div class="back-to-menu">
                    <a href="index.php#menu">Lihat Menu</a>
                </div>
            </div>
        <?php else: ?>
            <form method="POST" action="">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                                                    <?php foreach ($cart_items as $item): ?>
                                <tr data-product-id="<?php echo $item['id']; ?>">
                                <td>
                                    <div class="product-info">
                                        <img src="image/<?php echo htmlspecialchars($item['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                             class="product-image">
                                        <div>
                                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                        </div>
                                    </div>
                                </td>
                                <td>Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                                <td>
                                    <div class="quantity-controls">
                                        <button type="button" class="quantity-btn" 
                                                onclick="updateQuantity(<?php echo $item['id']; ?>, -1)">-</button>
                                        <input type="number" name="quantity[<?php echo $item['id']; ?>]" 
                                               value="<?php echo $item['quantity']; ?>" 
                                               min="1" class="quantity-input" 
                                               onchange="updateQuantityAjax(<?php echo $item['id']; ?>, this.value)">
                                        <button type="button" class="quantity-btn" 
                                                onclick="updateQuantity(<?php echo $item['id']; ?>, 1)">+</button>
                                    </div>
                                </td>
                                <td>Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></td>
                                <td>
                                    <button type="button" 
                                            class="remove-btn" 
                                            onclick="removeItem(<?php echo $item['id']; ?>)">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="cart-summary">
                    <div class="summary-row">
                        <span>Total Item:</span>
                        <span><?php echo array_sum($_SESSION['cart']); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Total Harga:</span>
                        <span>Rp <?php echo number_format($total, 0, ',', '.'); ?></span>
                    </div>
                    
                    <div class="cart-actions">
                        <a href="index.php#menu" class="btn-continue">
                            <i class="fas fa-arrow-left"></i> Lanjutkan Belanja
                        </a>
                        <a href="clear_cart.php" class="btn-clear" onclick="return confirm('Yakin ingin mengosongkan keranjang?')">
                            <i class="fas fa-trash"></i> Kosongkan Keranjang
                        </a>
                        <a href="checkout.php" class="btn-checkout">
                            <i class="fas fa-credit-card"></i> Checkout
                        </a>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <script>
        function updateQuantity(productId, change) {
            const input = document.querySelector(`input[name="quantity[${productId}]"]`);
            const newValue = parseInt(input.value) + change;
            if (newValue >= 1) {
                input.value = newValue;
                updateQuantityAjax(productId, newValue);
            }
        }
        
        function updateQuantityAjax(productId, quantity) {
            fetch('update_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: parseInt(quantity)
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update total display
                    const totalElement = document.querySelector('.summary-row:last-child span:last-child');
                    if (totalElement) {
                        totalElement.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.total);
                    }
                    
                    // Update total items
                    const totalItemsElement = document.querySelector('.summary-row:first-child span:last-child');
                    if (totalItemsElement) {
                        totalItemsElement.textContent = data.total_items;
                    }
                    
                    // Update cart count in header
                    const cartCountElement = document.querySelector('.cart-count');
                    if (cartCountElement) {
                        cartCountElement.textContent = data.total_items;
                    }
                    
                                         // Reload page if cart is empty
                     if (data.cart_count === 0) {
                         location.reload();
                     }
                 }
             })
             .catch(error => {
                 console.error('Error:', error);
                 alert('Terjadi kesalahan saat mengupdate quantity');
             });
         }
         
         function removeItem(productId) {
             if (confirm('Yakin ingin menghapus item ini?')) {
                 fetch('remove_item.php', {
                     method: 'POST',
                     headers: {
                         'Content-Type': 'application/json',
                     },
                     body: JSON.stringify({
                         product_id: productId
                     })
                 })
                 .then(response => response.json())
                 .then(data => {
                     if (data.success) {
                         // Remove the row from table
                         const row = document.querySelector(`tr[data-product-id="${productId}"]`);
                         if (row) {
                             row.remove();
                         }
                         
                         // Update total display
                         const totalElement = document.querySelector('.summary-row:last-child span:last-child');
                         if (totalElement) {
                             totalElement.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.total);
                         }
                         
                         // Update total items
                         const totalItemsElement = document.querySelector('.summary-row:first-child span:last-child');
                         if (totalItemsElement) {
                             totalItemsElement.textContent = data.total_items;
                         }
                         
                         // Update cart count in header
                         const cartCountElement = document.querySelector('.cart-count');
                         if (cartCountElement) {
                             cartCountElement.textContent = data.total_items;
                         }
                         
                         // Reload page if cart is empty
                         if (data.cart_count === 0) {
                             location.reload();
                         }
                     }
                 })
                 .catch(error => {
                     console.error('Error:', error);
                     alert('Terjadi kesalahan saat menghapus item');
                 });
             }
         }

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