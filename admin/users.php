<?php
session_start();
include '../db.php';
if (!isset($_SESSION['admin_login'])) {
    header('Location: login.php');
    exit();
}

// Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM users WHERE id = $id");
    header('Location: users.php?deleted=1');
    exit();
}

// Create & update
if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $edit_id = $_POST['edit_id'] ?? null;
    
    if ($edit_id) {
        if ($password) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
            $stmt->bind_param("sssi", $username, $email, $hashed_password, $edit_id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
            $stmt->bind_param("ssi", $username, $email, $edit_id);
        }
        
        if ($stmt->execute()) {
            header('Location: users.php?updated=1');
            exit();
        }
    } else {
        if ($password) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashed_password);
            
            if ($stmt->execute()) {
                header('Location: users.php?success=1');
                exit();
            }
        } else {
            $error = 'Password wajib diisi untuk user baru';
        }
    }
}

// Get user
$edit_user = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $edit_user = $conn->query("SELECT * FROM users WHERE id = $edit_id")->fetch_assoc();
}

$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data User - Admin Kopte Tarik</title>
    <link rel="icon" href="image/logokopte.jpeg">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <style>
        body {
            background: #f7f7f7;
        }
        
        .admin-header {
            background: #fff;
            padding: 18px 32px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .admin-header h1 {
            font-size: 2rem;
            color: #443;
        }
        
        .admin-header .logout-btn {
            background: #ff4757;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 8px 18px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .admin-table-container {
            max-width: 1000px;
            margin: 32px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            padding: 24px;
        }
        
        .admin-table-container h3 {
            color: #443;
            margin-bottom: 18px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 10px 8px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }
        
        th {
            background: #f5f5f5;
            color: #443;
        }
        
        tr:last-child td {
            border-bottom: none;
        }
        
        .form-container {
            max-width: 600px;
            margin: 32px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            padding: 32px;
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
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
        }
        
        .submit-btn {
            background: #28a745;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
        }
        
        .submit-btn.edit {
            background: #007bff;
        }
        
        .error {
            color: #dc3545;
            margin-bottom: 16px;
        }
        
        .success {
            color: #28a745;
            margin-bottom: 16px;
        }
        
        .toggle-form {
            background: #28a745;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 8px 16px;
            font-weight: bold;
            cursor: pointer;
            margin-right: 8px;
        }
        
        .cancel-btn {
            background: #6c757d;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 8px 16px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div style="margin-left:220px;">
        <div class="admin-header">
            <h1>Data User</h1>
            <a href="index.php" style="color:#443;font-weight:bold;">Dashboard</a>
            
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="success" style="text-align:center;background:#d4edda;color:#155724;padding:12px;border-radius:8px;margin:16px auto;max-width:1000px;">User berhasil ditambahkan!</div>
        <?php endif; ?>
        <?php if (isset($_GET['updated'])): ?>
            <div class="success" style="text-align:center;background:#d4edda;color:#155724;padding:12px;border-radius:8px;margin:16px auto;max-width:1000px;">User berhasil diupdate!</div>
        <?php endif; ?>
        <?php if (isset($_GET['deleted'])): ?>
            <div class="success" style="text-align:center;background:#d4edda;color:#155724;padding:12px;border-radius:8px;margin:16px auto;max-width:1000px;">User berhasil dihapus!</div>
        <?php endif; ?>

        <?php if ($edit_user || isset($_GET['show_form'])): ?>
        <div class="form-container">
            <h3><?php echo $edit_user ? 'Edit User' : 'Tambah User Baru'; ?></h3>
            <?php if (isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST">
                <?php if ($edit_user): ?>
                    <input type="hidden" name="edit_id" value="<?php echo $edit_user['id']; ?>">
                <?php endif; ?>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($edit_user['username'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($edit_user['email'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" <?php echo $edit_user ? '' : 'required'; ?>>
                    <?php if ($edit_user): ?>
                        <p style="color:#666;font-size:0.9rem;margin-top:4px;">Biarkan kosong jika tidak ingin mengubah password</p>
                    <?php endif; ?>
                </div>
                <button type="submit" name="submit" class="submit-btn <?php echo $edit_user ? 'edit' : ''; ?>">
                    <?php echo $edit_user ? 'Update User' : 'Tambah User'; ?>
                </button>
                <a href="users.php" style="background:#6c757d;color:#fff;border:none;border-radius:8px;padding:12px 24px;font-size:1.1rem;font-weight:bold;cursor:pointer;text-decoration:none;margin-left:10px;">Batal</a>
            </form>
        </div>
        <?php endif; ?>

        <div class="admin-table-container">
            <h3>Daftar User</h3>
            <?php if (!$edit_user && !isset($_GET['show_form'])): ?>
                <div style="margin-bottom: 20px;">
                    <button onclick="showForm()" style="background:#28a745;color:#fff;border:none;border-radius:6px;padding:10px 20px;font-weight:bold;cursor:pointer;font-size:1rem;">+ Tambah User Baru</button>
                </div>
            <?php endif; ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Tanggal Daftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                        <td>
                            <a href="?edit=<?php echo $row['id']; ?>" style="color:#007bff;font-size:1.2rem;margin-right:8px;" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus user ini?')" style="color:#dc3545;font-size:1.2rem;" title="Hapus"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function showForm() {
            window.location.href = '?show_form=1';
        }
    </script>
</body>
</html> 