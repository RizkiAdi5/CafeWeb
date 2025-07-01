<?php
session_start();
// Clear all session data
$_SESSION = array();
session_destroy();
header('Location: login.php');
exit();
?> 