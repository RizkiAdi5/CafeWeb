<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $date_visit = $_POST['date_visit'];
    $time_visit = $_POST['time_visit'];
    $message = trim($_POST['message']);
    
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Nama wajib diisi";
    }
    
    if (empty($email)) {
        $errors[] = "Email wajib diisi";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid";
    }
    
    if (empty($phone)) {
        $errors[] = "Nomor telepon wajib diisi";
    }
    
    if (empty($date_visit)) {
        $errors[] = "Tanggal kunjungan wajib diisi";
    }
    
    if (empty($time_visit)) {
        $errors[] = "Waktu kunjungan wajib diisi";
    }
    
    if (empty($message)) {
        $errors[] = "Pesan wajib diisi";
    }
    
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO contacts (name, email, phone, date_visit, time_visit, message) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $email, $phone, $date_visit, $time_visit, $message);
        
        if ($stmt->execute()) {
            $_SESSION['contact_success'] = "Pesan Anda berhasil dikirim! Kami akan segera menghubungi Anda.";
        } else {
            $_SESSION['contact_error'] = "Terjadi kesalahan saat mengirim pesan. Silakan coba lagi.";
        }
        
        $stmt->close();
    } else {
        $_SESSION['contact_errors'] = $errors;
        $_SESSION['contact_data'] = $_POST; 
    }
    header('Location: index.php#book');
    exit();
} else {
    header('Location: index.php');
    exit();
}
?> 