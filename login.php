<?php
session_start();
include 'db.php';
if (isset($_POST['login'])) {
  $username = $conn->real_escape_string($_POST['login_username']);
  $password = $conn->real_escape_string($_POST['login_password']);
  $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
  $result = $conn->query($sql);
  if ($result && $result->num_rows > 0) {
    $_SESSION['login_user'] = $username;
    $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
    $_SESSION['success_message'] = "Login berhasil! Selamat datang, $username";
    header("Location: $redirect");
    exit();
  } else {
    $_SESSION['error_message'] = "Username atau password salah!";
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Kopte Tarik</title>
  <link rel="icon" href="image/logokopte.jpeg">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="css/style.css">
  <style>
    body {
      min-height: 100vh;
      background: #f5f5f5;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Poppins', Arial, sans-serif;
    }
    
    .login-container {
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.1);
      padding: 3rem;
      width: 100%;
      max-width: 35rem;
      text-align: center;
    }
    
    .logo-login {
      width: 80px;
      height: 80px;
      margin: 0 auto 1.5rem;
      border-radius: 50%;
      object-fit: cover;
    }
    
    .login-container h2 {
      color: #443;
      margin-bottom: 2rem;
      font-size: 2rem;
      font-weight: 600;
    }
    
    .input-auth {
      width: 100%;
      padding: 1rem;
      margin: 0.8rem 0;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-size: 1.4rem;
      background: #f9f9f9;
      transition: all 0.3s ease;
      color: #443;
    }
    
    .input-auth:focus {
      border-color: #443;
      outline: none;
      background: #fff;
      box-shadow: 0 0 0 2px rgba(68, 67, 67, 0.1);
    }
    
    .btn-auth-submit {
      width: 100%;
      background: #443;
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 1rem 0;
      font-size: 1.6rem;
      font-weight: 600;
      margin-top: 1.5rem;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .btn-auth-submit:hover {
      background: #333;
      transform: translateY(-2px);
    }
    
    .signup-link {
      margin-top: 1.5rem;
      display: block;
      color: #443;
      font-weight: 500;
      text-decoration: none;
      transition: all 0.3s ease;
      font-size: 1.3rem;
    }
    
    .signup-link:hover {
      color: #333;
      text-decoration: underline;
    }
    
    .alert {
      padding: 1rem;
      margin-bottom: 1.5rem;
      border-radius: 8px;
      font-size: 1.3rem;
      font-weight: 500;
    }
    
    .alert-success {
      background: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }
    
    .alert-error {
      background: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <img src="image/logokopte.jpeg" alt="Kopte Tarik Logo" class="logo-login">
    <h2>Login</h2>
    
    <?php if (isset($_SESSION['success_message'])): ?>
      <div class="alert alert-success">
        <?php 
        echo $_SESSION['success_message']; 
        unset($_SESSION['success_message']);
        ?>
      </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
      <div class="alert alert-error">
        <?php 
        echo $_SESSION['error_message']; 
        unset($_SESSION['error_message']);
        ?>
      </div>
    <?php endif; ?>
    
    <form method="POST" action="">
      <input type="text" name="login_username" placeholder="Username" required class="input-auth" />
      <input type="password" name="login_password" placeholder="Password" required class="input-auth" />
      <button type="submit" name="login" class="btn-auth-submit">Login</button>
    </form>
    <a href="signup.php" class="signup-link">Belum punya akun? Daftar di sini</a>
  </div>

  <!-- Footer -->
  <footer style="position: fixed; bottom: 0; left: 0; right: 0; background-color: rgba(248, 249, 250, 0.9); padding: 10px; text-align: center; font-size: 11px;">
    <div>
        <span style="color: #666;">Ikuti Kami: </span>
        <a href="https://www.instagram.com/kopte.id?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw=="
            target="_blank" style="text-decoration: none; color: #E1306C; margin-right: 10px;">
            <i class="fab fa-instagram" style="font-size: 16px;"></i>
        </a>
        <a href="https://www.tiktok.com/@kopte.id?is_from_webapp=1&sender_device=pc" target="_blank"
            style="text-decoration: none; color: #000000;">
            <i class="fab fa-tiktok" style="font-size: 16px;"></i>
        </a>
    </div>
    <div style="margin-top: 5px; color: #666;">
        <b>Our Team:</b> Ardiansyah, Abang Malik Syahidar, Mohammad Dimas Al Fateh | <b>&copy; 2025</b>
    </div>
  </footer>
</body>
</html> 