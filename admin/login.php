<?php
session_start();
include '../db.php';

// Check if admins table exists, if not create it
$check_table = $conn->query("SHOW TABLES LIKE 'admins'");
if ($check_table->num_rows == 0) {
    // Create admins table
    $sql = "CREATE TABLE admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->query($sql);
    
    // Add default admin
    $username = 'admin';
    $email = 'admin@kopte.com';
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO admins (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);
    $stmt->execute();
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_login'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            header('Location: index.php');
            exit();
        } else {
            $error = 'Password salah!';
        }
    } else {
        $error = 'Username tidak ditemukan!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Kopte Tarik</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', Arial, sans-serif;
        }
        
        .admin-login-container {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            padding: 3rem;
            width: 100%;
            max-width: 35rem;
            text-align: center;
        }
        
        .logo-admin {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .admin-login-container h2 {
            color: #443;
            margin-bottom: 2rem;
            font-size: 2rem;
            font-weight: 600;
        }
        
        .admin-login-container input {
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
        
        .admin-login-container input:focus {
            border-color: #443;
            outline: none;
            background: #fff;
            box-shadow: 0 0 0 2px rgba(68, 67, 67, 0.1);
        }
        
        .admin-login-container button {
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
        
        .admin-login-container button:hover {
            background: #333;
            transform: translateY(-2px);
        }
        
        .admin-login-container .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 8px;
            font-size: 1.3rem;
            font-weight: 500;
        }
        
        
        .back-link {
            margin-top: 1rem;
            display: block;
            color: #443;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 1.2rem;
        }
        
        .back-link:hover {
            color: #333;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="admin-login-container">
        <img src="../image/logokopte.jpeg" alt="Kopte Tarik Logo" class="logo-admin">
        <h2>Login Admin</h2>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required autofocus>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>
        
        
        
        <a href="../index.php" class="back-link">
            <i class="fas fa-arrow-left" style="margin-right: 5px;"></i>Kembali ke Website
        </a>
    </div>
</body>
</html> 