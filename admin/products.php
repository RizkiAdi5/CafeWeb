<?php
session_start();
include '../db.php';
if (!isset($_SESSION['admin_login'])) {
    header('Location: login.php');
    exit();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $product = $conn->query("SELECT image FROM products WHERE id = $id")->fetch_assoc();
    if ($product) {
        $conn->query("DELETE FROM products WHERE id = $id");
        if ($product['image'] && file_exists('../image/' . $product['image'])) {
            unlink('../image/' . $product['image']);
        }
        header('Location: products.php?deleted=1');
        exit();
    }
}

// Handle Add/Edit Form Submit
if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $description = $_POST['description'] ?? '';
    $edit_id = $_POST['edit_id'] ?? null;
    
    // Handle file upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $newname = uniqid() . '.' . $ext;
            $destination = '../image/' . $newname;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $image = $newname;
            }
        }
    }
    
    if ($edit_id) {
        // Update existing product
        if ($image) {
            // Delete old image if new image uploaded
            $old_product = $conn->query("SELECT image FROM products WHERE id = $edit_id")->fetch_assoc();
            if ($old_product['image'] && file_exists('../image/' . $old_product['image'])) {
                unlink('../image/' . $old_product['image']);
            }
            $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, stock = ?, description = ?, image = ? WHERE id = ?");
            $stmt->bind_param("siissi", $name, $price, $stock, $description, $image, $edit_id);
        } else {
            $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, stock = ?, description = ? WHERE id = ?");
            $stmt->bind_param("siisi", $name, $price, $stock, $description, $edit_id);
        }
        
        if ($stmt->execute()) {
            header('Location: products.php?updated=1');
            exit();
        }
    } else {
        // Add new product
        if ($image) {
            $stmt = $conn->prepare("INSERT INTO products (name, price, stock, description, image) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("siiss", $name, $price, $stock, $description, $image);
            
            if ($stmt->execute()) {
                header('Location: products.php?success=1');
                exit();
            }
        } else {
            $error = 'Gambar wajib diupload untuk produk baru';
        }
    }
}

// Get product for editing
$edit_product = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $edit_product = $conn->query("SELECT * FROM products WHERE id = $edit_id")->fetch_assoc();
}

// Get all products for listing
$products = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Produk - Admin Kopte Tarik</title>
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
            max-width: 1200px;
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
        
        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
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
        
        .form-group input[type="file"] {
            padding: 8px;
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
        
        .current-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            margin-top: 8px;
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
            <h1>Data Produk</h1>
            <a href="index.php" style="color:#443;font-weight:bold;">Dashboard</a>
            
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="success" style="text-align:center;background:#d4edda;color:#155724;padding:12px;border-radius:8px;margin:16px auto;max-width:1200px;">Produk berhasil ditambahkan!</div>
        <?php endif; ?>
        <?php if (isset($_GET['updated'])): ?>
            <div class="success" style="text-align:center;background:#d4edda;color:#155724;padding:12px;border-radius:8px;margin:16px auto;max-width:1200px;">Produk berhasil diupdate!</div>
        <?php endif; ?>
        <?php if (isset($_GET['deleted'])): ?>
            <div class="success" style="text-align:center;background:#d4edda;color:#155724;padding:12px;border-radius:8px;margin:16px auto;max-width:1200px;">Produk berhasil dihapus!</div>
        <?php endif; ?>

        <?php if ($edit_product || isset($_GET['show_form'])): ?>
        <div class="form-container">
            <h3><?php echo $edit_product ? 'Edit Produk' : 'Tambah Produk Baru'; ?></h3>
            <?php if (isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                <?php if ($edit_product): ?>
                    <input type="hidden" name="edit_id" value="<?php echo $edit_product['id']; ?>">
                <?php endif; ?>
                <div class="form-group">
                    <label>Nama Produk</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($edit_product['name'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>Harga</label>
                    <input type="number" name="price" value="<?php echo $edit_product['price'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Stok</label>
                    <input type="number" name="stock" value="<?php echo isset($edit_product['stock']) ? $edit_product['stock'] : '0'; ?>" required>
                </div>
                <div class="form-group">
                    <label>Deskripsi Produk</label>
                    <textarea name="description" rows="4" placeholder="Masukkan deskripsi produk..."><?php echo htmlspecialchars($edit_product['description'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Gambar Produk</label>
                    <input type="file" name="image" accept="image/*" <?php echo $edit_product ? '' : 'required'; ?>>
                    <?php if ($edit_product): ?>
                        <p style="color:#666;font-size:0.9rem;margin-top:4px;">Biarkan kosong jika tidak ingin mengubah gambar</p>
                        <img src="../image/<?php echo htmlspecialchars($edit_product['image']); ?>" class="current-image">
                    <?php endif; ?>
                </div>
                <button type="submit" name="submit" class="submit-btn <?php echo $edit_product ? 'edit' : ''; ?>">
                    <?php echo $edit_product ? 'Update Produk' : 'Tambah Produk'; ?>
                </button>
                <a href="products.php" style="background:#6c757d;color:#fff;border:none;border-radius:8px;padding:12px 24px;font-size:1.1rem;font-weight:bold;cursor:pointer;text-decoration:none;margin-left:10px;">Batal</a>
            </form>
        </div>
        <?php endif; ?>

        <div class="admin-table-container">
            <h3>Daftar Produk</h3>
            <?php if (!$edit_product && !isset($_GET['show_form'])): ?>
                <div style="margin-bottom: 20px;">
                    <button onclick="showForm()" style="background:#28a745;color:#fff;border:none;border-radius:6px;padding:10px 20px;font-weight:bold;cursor:pointer;font-size:1rem;">+ Tambah Produk Baru</button>
                </div>
            <?php endif; ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Deskripsi</th>
                        <th>Gambar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $products->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td>Rp <?php echo number_format($row['price'],0,',','.'); ?></td>
                        <td><?php echo isset($row['stock']) ? $row['stock'] : '0'; ?></td>
                        <td style="max-width: 200px; word-wrap: break-word;">
                            <?php echo !empty($row['description']) ? htmlspecialchars(substr($row['description'], 0, 100)) . (strlen($row['description']) > 100 ? '...' : '') : '-'; ?>
                        </td>
                        <td><img src="../image/<?php echo htmlspecialchars($row['image']); ?>" class="product-img"></td>
                        <td>
                            <a href="?edit=<?php echo $row['id']; ?>" style="color:#007bff;font-size:1.2rem;margin-right:8px;" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus produk ini?')" style="color:#dc3545;font-size:1.2rem;" title="Hapus"><i class="fas fa-trash"></i></a>
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