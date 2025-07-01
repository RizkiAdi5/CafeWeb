<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $product_id = $input['product_id'] ?? null;
    
    if ($product_id !== null) {
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
        
        // Hitung total
        $total = 0;
        if (!empty($_SESSION['cart'])) {
            include 'db.php';
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
            }
            $stmt->close();
        }
        
        echo json_encode([
            'success' => true,
            'total' => $total,
            'total_items' => array_sum($_SESSION['cart']),
            'cart_count' => count($_SESSION['cart'])
        ]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?> 