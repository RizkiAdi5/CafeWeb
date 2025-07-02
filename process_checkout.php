<?php
session_start();
include 'db.php';

if (!isset($_SESSION['login_user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'User belum login']);
    exit();
}

if (empty($_SESSION['cart'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Keranjang belanja kosong']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $delivery_address = $input['delivery_address'] ?? '';
    $phone = $input['phone'] ?? '';
    $payment_method = $input['payment_method'] ?? '';
    
    if (empty($delivery_address) || empty($phone) || empty($payment_method)) {
        http_response_code(400);
        echo json_encode(['error' => 'Semua field harus diisi']);
        exit();
    }
    
    $username = $_SESSION['login_user'];
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if (!$user) {
        http_response_code(400);
        echo json_encode(['error' => 'Data user tidak ditemukan']);
        exit();
    }
    
    // Hitung total
    $total = 0;
    $cart_items = [];
    
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
            'quantity' => $quantity,
            'subtotal' => $subtotal
        ];
    }
    $stmt->close();
    
    $conn->begin_transaction();
    
    try {
        $sql = "INSERT INTO orders (user_id, total_amount, delivery_address, phone, payment_method) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("idsss", $user['id'], $total, $delivery_address, $phone, $payment_method);
        
        if (!$stmt->execute()) {
            throw new Exception("Gagal membuat pesanan");
        }
        
        $order_id = $conn->insert_id;
        
        $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        foreach ($cart_items as $item) {
            $stmt->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $item['price']);
            if (!$stmt->execute()) {
                throw new Exception("Gagal menyimpan item pesanan");
            }
        }
        
        $conn->commit();
        
        unset($_SESSION['cart']);
        
        echo json_encode([
            'success' => true,
            'order_id' => $order_id,
            'message' => 'Pesanan berhasil dibuat',
            'redirect' => "order_success.php?order_id=$order_id"
        ]);
        
    } catch (Exception $e) {
        $conn->rollback();
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    
    $stmt->close();
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method tidak diizinkan']);
}
?> 