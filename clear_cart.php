<?php
session_start();

// Clear cart
if (isset($_SESSION['cart'])) {
    unset($_SESSION['cart']);
}

// Redirect back to cart page
header('Location: cart.php');
exit();
?> 