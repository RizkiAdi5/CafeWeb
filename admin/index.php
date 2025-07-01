<?php
session_start();
include '../db.php';
if (!isset($_SESSION['admin_login'])) {
    header('Location: login.php');
    exit();
}
// Query ringkasan
$total_users = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$total_products = $conn->query("SELECT COUNT(*) as total FROM products")->fetch_assoc()['total'];
$total_orders = $conn->query("SELECT COUNT(*) as total FROM orders")->fetch_assoc()['total'];
$total_income = $conn->query("SELECT IFNULL(SUM(total_amount),0) as total FROM orders WHERE status != 'cancelled'")->fetch_assoc()['total'];
$total_admins = $conn->query("SELECT COUNT(*) as total FROM admins")->fetch_assoc()['total'];
$total_contacts = $conn->query("SELECT COUNT(*) as total FROM contacts")->fetch_assoc()['total'];
$unread_contacts = $conn->query("SELECT COUNT(*) as total FROM contacts WHERE status = 'unread'")->fetch_assoc()['total'];
// Query pesanan terbaru
$orders = $conn->query("SELECT o.*, u.username FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.order_date DESC LIMIT 10");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Kopte Tarik</title>
    <link rel="stylesheet" href="../css/style.css">
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
        
        .admin-summary {
            display: flex;
            gap: 32px;
            margin: 32px 0 24px 0;
            justify-content: center;
        }
        
        .summary-box {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            padding: 24px 32px;
            text-align: center;
            min-width: 180px;
        }
        
        .summary-box h2 {
            color: #443;
            font-size: 2.2rem;
            margin-bottom: 8px;
        }
        
        .summary-box p {
            color: #666;
            font-size: 1.1rem;
        }
        
        .admin-table-container {
            max-width: 1100px;
            margin: 0 auto;
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
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div style="margin-left:220px;">
    <div class="admin-header">
        <h1>Dashboard Admin Kopte Tarik</h1>

    </div>
    <div class="admin-summary">
        <div class="summary-box">
            <h2><?php echo $total_users; ?></h2>
            <p>User</p>
        </div>
        <div class="summary-box">
            <h2><?php echo $total_products; ?></h2>
            <p>Produk</p>
        </div>
        <div class="summary-box">
            <h2><?php echo $total_orders; ?></h2>
            <p>Total Pesanan</p>
        </div>
        <div class="summary-box">
            <h2>Rp <?php echo number_format($total_income,0,',','.'); ?></h2>
            <p>Total Pendapatan</p>
        </div>
        <div class="summary-box">
            <h2><?php echo $total_admins; ?></h2>
            <p>Admin</p>
        </div>
        <div class="summary-box">
            <h2><?php echo $total_contacts; ?></h2>
            <p>Total Kontak</p>
        </div>
        <div class="summary-box">
            <h2><?php echo $unread_contacts; ?></h2>
            <p>Kontak Belum Dibaca</p>
        </div>
    </div>
    <div class="admin-table-container">
        <h3>Pesanan Terbaru</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Alamat</th>
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
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    </div>
</body>
</html> 