<!DOCTYPE html>
<html lang="es">
<head>
    <?php session_start(); ?>
    <?php include 'db.php'; ?>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KOPTE TARIK</title>
    <link rel="icon" href="image/logokopte.jpeg">
     <!-- Logo Medsos -->
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

     <!-- Audio -->
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- SWIPER -->
    <link rel="icon" href="image/logokopte.jpeg">
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css" />

    <!-- Font-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <link rel="stylesheet" href="css/style.css" />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />

    <style>
  
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
        <a href="#home" class="nav-link active" data-section="home">Home</a>
        <a href="#about" class="nav-link" data-section="about">Tentang</a>
        <a href="#menu" class="nav-link" data-section="menu">Menu</a>
        <a href="#galeri" class="nav-link" data-section="galeri">Galeri</a>
        <a href="#book" class="nav-link" data-section="book">Kontak</a>
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

    <!-- HOME -->
    <section class="home" id="home">
      <div class="row">
        <div class="content">
          <h3>
            Rasakan <br />
            Kehangatan, Nikmati <br />
            Kesempurnaan.
          </h3>
          <a href="fullmenu.php" class="btn"><b>Pesan Sekarang</b></a>
        </div>

        <div class="image">
          <img src="image/home-img-7.png" class="main-home-image" alt="" />
        </div>
      </div>
    </section>

    <!-- ABOUT -->
    <section class="about" id="about">
      <h1 class="heading">Tentang Kami <span>Tentang Kami</span></h1>

      <div class="row" data-aos="fade-up" data-aos-duration="500">
        <div class="image">
          <iframe
            width="560"
            height="315"
            src="https://www.youtube.com/embed/sIvYTt3BRZw?si=oK8I56k3KCJga20Z"
            title="YouTube video player"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            referrerpolicy="strict-origin-when-cross-origin"
            allowfullscreen
          ></iframe>
        </div>

        <div
          class="content"
          data-aos="fade-up"
          data-aos-anchor-placement="top-bottom"
        >
          <h3 class="title">KOPTE TARIK</h3>
          <p>
            KOPTE adalah salah satu brand Dum Dum Group dengan konsep "Kopi &
            Teh Tarik" yang membawa nuansa peranakan modern. Brand message dan
            tagline KOPTE adalah #TarikSemangatmu. Dengan tagline ini KOPTE
            ingin mengajak #PejuangKopte untuk memberikan energi positif dengan
            membangkitkan atau mendorong semangat melakukan segala sesuatu dan
            menghadapi masalah apapun.
            <br />
            <br />
            Mempunyai daya jual dengan konsep menarik, harga bersaing dan produk
            berkualitas dengan harapan seluruh konsumen KOPTE bisa ngopi atau
            ngeteh setiap hari tanpa harus banyak menghabiskan budget di
            kantong.
            <br />
            <br />
            Menu KOPTE adalah kopi dan teh tarik, serta varian minuman lainnya
            seperti kopi dan teh tarik cincau. Selain berkualitas, bahan produk
            yang digunakan juga halal seperti semua makanan dan minuman brand
            Dum Dum Group lainnya
          </p>
          <a
            href="https://franchisepedia.id/listing/kopte/"
            target="_blank"
            class="btn"
            >Bagian lainnya</a
          >
        </div>
      </div>
    </section>

    <!-- MENU -->
    <section class="menu" id="menu" style="margin-top: 110px;">
        <h1 class="heading">MENU<span>Menu</span></h1>
        <div class="menu-container">
          <?php
          $menu_query = "SELECT * FROM products ORDER BY id ASC LIMIT 6";
          $menu_result = $conn->query($menu_query);
          
          while($product = $menu_result->fetch_assoc()):
          ?>
          <div class="menu-item" data-aos="fade-up">
            <img src="image/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" />
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            <h2>Harga: Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></h2>
            <form method="POST" action="cart.php" style="display: inline;">
              <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
              <input type="hidden" name="quantity" value="1">
              <button type="submit" name="add_to_cart" class="add-to-cart-btn">Tambahkan ke Keranjang</button>
            </form>
          </div>
          <?php endwhile; ?>
        </div>
        
        <div style="text-align: center; margin-top: 40px;">
          <a href="fullmenu.php" class="btn" style="font-weight: bold;">
            <i class="fas fa-utensils" style="margin-right: 8px;"></i>Lihat Semua Menu
          </a>
        </div>
      </section>

    <!-- Galleri-->
    <section class="galeri" id="galeri">
        <h1 class="heading">Galleri <span>Momen Kami</span></h1>
        <div class="swiper gallery-slider">
          <div class="swiper-wrapper">
            <div class="swiper-slide">
              <img src="image/gallry1.png" alt="Gallery Image 1" />
            </div>
            <div class="swiper-slide">
              <img src="image/gallry6.png" alt="Gallery Image 2" />
            </div>
            <div class="swiper-slide">
              <img src="image/gallry5.png.jpg.png" alt="Gallery Image 3" />
            </div>
            <div class="swiper-slide">
              <img src="image/gallry3.png.jpg.png" alt="Gallery Image 4" />
            </div>
            <div class="swiper-slide">
              <img src="image/gallry2.png.jpg" alt="Gallery Image 5" />
            </div>
            <div class="swiper-slide">
              <img src="image/gallry7.png" alt="Gallery Image 5" />
            </div>
          </div>
  
          <div class="swiper-pagination"></div>
  
          <div class="swiper-button-next"></div>
          <div class="swiper-button-prev"></div>
        </div>
      </section>

    <script src="https://unpkg.com/swiper@7/swiper-bundle.min.js"></script>
    <script>
      const swiper = new Swiper(".gallery-slider", {
        slidesPerView: 1,
        spaceBetween: 10,
        navigation: {
          nextEl: ".swiper-button-next",
          prevEl: ".swiper-button-prev",
        },
        pagination: {
          el: ".swiper-pagination",
          clickable: true,
        },
        loop: true,
        autoplay: {
          delay: 3000,
          disableOnInteraction: false,},});
    </script>

    <!-- Kontak -->
    <section class="book" id="book">
      <h1 class="heading">Kontak <span>Kontak</span></h1>
      <div class="contact-form">
        <div class="contact-container">
          <div class="map" data-aos="fade-right">
            <h2>Lokasi Kami</h2>

            <iframe
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d406.80724260628017!2d109.33112976060276!3d-0.04456728537934252!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e1d592d0d7b3543%3A0x6b3c6992b8a8c26!2sBoxpark!5e0!3m2!1sid!2sid!4v1735536270058!5m2!1sid!2sid"
              width="600"
              height="450"
              style="border: 0"
              allowfullscreen=""
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
            ></iframe>
          </div>

          <?php if (isset($_SESSION['contact_success'])): ?>
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                <?php echo $_SESSION['contact_success']; ?>
            </div>
            <?php unset($_SESSION['contact_success']); ?>
          <?php endif; ?>
          
          <?php if (isset($_SESSION['contact_error'])): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                <?php echo $_SESSION['contact_error']; ?>
            </div>
            <?php unset($_SESSION['contact_error']); ?>
          <?php endif; ?>
          
          <?php if (isset($_SESSION['contact_errors'])): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <strong>Error:</strong>
                <ul style="margin: 5px 0; padding-left: 20px;">
                    <?php foreach ($_SESSION['contact_errors'] as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php unset($_SESSION['contact_errors']); ?>
          <?php endif; ?>
          
          <form action="process_contact.php" method="POST" data-aos="fade-left">
            <input type="text" name="name" class="box" placeholder="Nama Anda" value="<?php echo isset($_SESSION['contact_data']['name']) ? htmlspecialchars($_SESSION['contact_data']['name']) : ''; ?>" required />
            <input type="email" name="email" class="box" placeholder="Email Anda" value="<?php echo isset($_SESSION['contact_data']['email']) ? htmlspecialchars($_SESSION['contact_data']['email']) : ''; ?>" required />
            <input type="text" name="phone" class="box" placeholder="Nomor Telepon" value="<?php echo isset($_SESSION['contact_data']['phone']) ? htmlspecialchars($_SESSION['contact_data']['phone']) : ''; ?>" required />
            <input type="date" name="date_visit" class="box" value="<?php echo isset($_SESSION['contact_data']['date_visit']) ? $_SESSION['contact_data']['date_visit'] : ''; ?>" required />
            <input type="time" name="time_visit" class="box" value="<?php echo isset($_SESSION['contact_data']['time_visit']) ? $_SESSION['contact_data']['time_visit'] : ''; ?>" required />
            <textarea name="message" class="box" placeholder="Pesan Anda" required><?php echo isset($_SESSION['contact_data']['message']) ? htmlspecialchars($_SESSION['contact_data']['message']) : ''; ?></textarea>
            <input type="submit" value="Kirim" class="btn" />
          </form>
          
          <?php if (isset($_SESSION['contact_data'])): unset($_SESSION['contact_data']); endif; ?>
        </div>
      </div>
    </section>

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

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init();
</script>



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

    // Active navigation highlighting
    window.addEventListener('scroll', function() {
      const sections = document.querySelectorAll('section[id]');
      const navLinks = document.querySelectorAll('.nav-link');
      
      let current = '';
      sections.forEach(section => {
        const sectionTop = section.offsetTop;
        const sectionHeight = section.clientHeight;
        if (scrollY >= (sectionTop - 200)) {
          current = section.getAttribute('id');
        }
      });

      navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('data-section') === current) {
          link.classList.add('active');
        }
      });
    });
  </script>
