<?php
session_start();
include '../db.php';
if (!isset($_SESSION['admin_login'])) {
    header('Location: login.php');
    exit();
}

// Update
if (isset($_POST['update_status'])) {
    $contact_id = $_POST['contact_id'];
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE contacts SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $contact_id);
    
    if ($stmt->execute()) {
        header('Location: contacts.php?updated=1');
        exit();
    }
}

// Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM contacts WHERE id = $id");
    header('Location: contacts.php?deleted=1');
    exit();
}

// fungsi read
$contacts = $conn->query("SELECT * FROM contacts ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kontak - Admin Kopte Tarik</title>
    <link rel="icon" href="image/logokopte.jpeg">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f7f7f7; }
        .admin-header { background: #fff; padding: 18px 32px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); display: flex; justify-content: space-between; align-items: center; }
        .admin-header h1 { font-size: 2rem; color: #443; }
        .admin-header .logout-btn { background: #ff4757; color: #fff; border: none; border-radius: 6px; padding: 8px 18px; font-weight: bold; cursor: pointer; }
        .admin-table-container { max-width: 1400px; margin: 32px auto; background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); padding: 24px; }
        .admin-table-container h3 { color: #443; margin-bottom: 18px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 8px; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #f5f5f5; color: #443; }
        tr:last-child td { border-bottom: none; }
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .status-unread { background: #fff3cd; color: #856404; }
        .status-read { background: #d1ecf1; color: #0c5460; }
        .status-replied { background: #d4edda; color: #155724; }
        .message-preview {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 600px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover { color: #000; }
        .contact-details { margin-top: 20px; }
        .contact-details p { margin: 10px 0; }
        .contact-details strong { color: #443; }
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #ee0979;
        }
        .stat-label {
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div style="margin-left:220px;">
        <div class="admin-header">
            <h1>Data Kontak</h1>
            <a href="index.php" style="color:#443;font-weight:bold;">Dashboard</a>
            
        </div>

        <?php if (isset($_GET['updated'])): ?>
            <div class="success" style="text-align:center;background:#d4edda;color:#155724;padding:12px;border-radius:8px;margin:16px auto;max-width:1400px;">Status kontak berhasil diupdate!</div>
        <?php endif; ?>
        <?php if (isset($_GET['deleted'])): ?>
            <div class="success" style="text-align:center;background:#d4edda;color:#155724;padding:12px;border-radius:8px;margin:16px auto;max-width:1400px;">Kontak berhasil dihapus!</div>
        <?php endif; ?>

        <div class="admin-table-container">
            <?php
            // Get statistics
            $total_contacts = $conn->query("SELECT COUNT(*) as total FROM contacts")->fetch_assoc()['total'];
            $unread_contacts = $conn->query("SELECT COUNT(*) as total FROM contacts WHERE status = 'unread'")->fetch_assoc()['total'];
            $replied_contacts = $conn->query("SELECT COUNT(*) as total FROM contacts WHERE status = 'replied'")->fetch_assoc()['total'];
            ?>
            
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_contacts; ?></div>
                    <div class="stat-label">Total Kontak</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $unread_contacts; ?></div>
                    <div class="stat-label">Belum Dibaca</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $replied_contacts; ?></div>
                    <div class="stat-label">Sudah Dibalas</div>
                </div>
            </div>

            <h3>Daftar Pesan Kontak</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Tanggal Kunjungan</th>
                        <th>Waktu Kunjungan</th>
                        <th>Pesan</th>
                        <th>Status</th>
                        <th>Tanggal Kirim</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $contacts->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($row['date_visit'])); ?></td>
                        <td><?php echo $row['time_visit']; ?></td>
                        <td>
                            <div class="message-preview"><?php echo htmlspecialchars($row['message']); ?></div>
                            <button onclick="viewMessage(<?php echo $row['id']; ?>)" style="background:none;border:none;color:#007bff;cursor:pointer;font-size:0.8rem;">Lihat Detail</button>
                        </td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="contact_id" value="<?php echo $row['id']; ?>">
                                <select name="status" onchange="this.form.submit()" style="padding:4px;border-radius:4px;border:1px solid #ddd;">
                                    <option value="unread" <?php echo $row['status'] == 'unread' ? 'selected' : ''; ?>>Belum Dibaca</option>
                                    <option value="read" <?php echo $row['status'] == 'read' ? 'selected' : ''; ?>>Sudah Dibaca</option>
                                    <option value="replied" <?php echo $row['status'] == 'replied' ? 'selected' : ''; ?>>Sudah Dibalas</option>
                                </select>
                                <input type="hidden" name="update_status" value="1">
                            </form>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                        <td>
                            <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus kontak ini?')" style="color:#dc3545;font-size:1.2rem;" title="Hapus"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal untuk detail pesan -->
    <div id="messageModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Detail Pesan</h2>
            <div id="messageContent"></div>
        </div>
    </div>

    <script>
        function viewMessage(contactId) {
            // Fetch message details via AJAX
            fetch('get_contact_details.php?id=' + contactId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const content = `
                            <div class="contact-details">
                                <p><strong>Nama:</strong> ${data.contact.name}</p>
                                <p><strong>Email:</strong> ${data.contact.email}</p>
                                <p><strong>Telepon:</strong> ${data.contact.phone}</p>
                                <p><strong>Tanggal Kunjungan:</strong> ${data.contact.date_visit}</p>
                                <p><strong>Waktu Kunjungan:</strong> ${data.contact.time_visit}</p>
                                <p><strong>Pesan:</strong></p>
                                <div style="background:#f8f9fa;padding:15px;border-radius:8px;margin-top:10px;">
                                    ${data.contact.message}
                                </div>
                            </div>
                        `;
                        document.getElementById('messageContent').innerHTML = content;
                        document.getElementById('messageModal').style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mengambil detail pesan');
                });
        }

        function closeModal() {
            document.getElementById('messageModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('messageModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html> 