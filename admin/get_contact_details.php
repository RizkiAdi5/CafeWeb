<?php
session_start();
include '../db.php';

if (!isset($_SESSION['admin_login'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM contacts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($contact = $result->fetch_assoc()) {
        $contact['date_visit'] = date('d/m/Y', strtotime($contact['date_visit']));
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'contact' => $contact]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Contact not found']);
    }
    
    $stmt->close();
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'ID not provided']);
}
?> 