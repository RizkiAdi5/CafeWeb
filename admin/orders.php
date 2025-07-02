<?php
session_start();
include '../db.php';
if (!isset($_SESSION['admin_login'])) {
    header('Location: login.php');
    exit();
}

// Status update
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);
    
    if ($stmt->execute()) {
        header('Location: orders.php?updated=1');
        exit();
    }
}

// Rad 
$edit_order = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $edit_order = $conn->query("SELECT o.*, u.username FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.id = $edit_id")->fetch_assoc();
}

// Read
$orders = $conn->query("SELECT o.*, u.username FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.order_date DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pesanan - Admin Kopte Tarik</title>
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
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.95rem;
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
        
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
        }
        
        .submit-btn {
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
        }
        
        .success {
            color: #28a745;
            margin-bottom: 16px;
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
            <h1>Data Pesanan</h1>
            <a href="index.php" style="color:#443;font-weight:bold;">Dashboard</a>
            
        </div>

        <?php if (isset($_GET['updated'])): ?>
            <div class="success" style="text-align:center;background:#d4edda;color:#155724;padding:12px;border-radius:8px;margin:16px auto;max-width:1200px;">Status pesanan berhasil diupdate!</div>
        <?php endif; ?>

        <?php if ($edit_order): ?>
        <div class="form-container">
            <h3>Update Status Pesanan #<?php echo str_pad($edit_order['id'], 6, '0', STR_PAD_LEFT); ?></h3>
            <form method="POST">
                <input type="hidden" name="order_id" value="<?php echo $edit_order['id']; ?>">
                <div class="form-group">
                    <label>User</label>
                    <input type="text" value="<?php echo htmlspecialchars($edit_order['username'] ?? '-'); ?>" readonly style="background:#f8f9fa;">
                </div>
                <div class="form-group">
                    <label>Total</label>
                    <input type="text" value="Rp <?php echo number_format($edit_order['total_amount'],0,',','.'); ?>" readonly style="background:#f8f9fa;">
                </div>
                <div class="form-group">
                    <label>Alamat</label>
                    <input type="text" value="<?php echo htmlspecialchars($edit_order['delivery_address']); ?>" readonly style="background:#f8f9fa;">
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" required>
                        <option value="pending" <?php echo $edit_order['status'] == 'pending' ? 'selected' : ''; ?>>Menunggu Konfirmasi</option>
                        <option value="confirmed" <?php echo $edit_order['status'] == 'confirmed' ? 'selected' : ''; ?>>Dikonfirmasi</option>
                        <option value="completed" <?php echo $edit_order['status'] == 'completed' ? 'selected' : ''; ?>>Selesai</option>
                        <option value="cancelled" <?php echo $edit_order['status'] == 'cancelled' ? 'selected' : ''; ?>>Dibatalkan</option>
                    </select>
                </div>
                <button type="submit" name="update_status" class="submit-btn">Update Status</button>
                <a href="orders.php" style="background:#6c757d;color:#fff;border:none;border-radius:8px;padding:12px 24px;font-size:1.1rem;font-weight:bold;cursor:pointer;text-decoration:none;margin-left:10px;">Batal</a>
            </form>
        </div>
        <?php endif; ?>

        <div class="admin-table-container">
            <h3>Daftar Pesanan</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Alamat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $orders->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo str_pad($row['id'], 6, '0', STR_PAD_LEFT); ?></td>
                        <td><?php echo htmlspecialchars($row['username'] ?? '-'); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($row['order_date'])); ?></td>
                        <td>Rp <?php echo number_format($row['total_amount'],0,',','.'); ?></td>
                        <td><span class="status-badge status-<?php echo $row['status']; ?>"><?php 
                            $status_text = [
                                'pending' => 'Menunggu Konfirmasi',
                                'confirmed' => 'Dikonfirmasi',
                                'completed' => 'Selesai',
                                'cancelled' => 'Dibatalkan'
                            ];
                            echo $status_text[$row['status']];
                        ?></span></td>
                        <td><?php echo htmlspecialchars($row['delivery_address']); ?></td>
                        <td>
                            <a href="?edit=<?php echo $row['id']; ?>" style="color:#007bff;font-size:1.2rem;" title="Update Status"><i class="fas fa-edit"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html> 