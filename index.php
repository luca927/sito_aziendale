<?php
session_start();

// Controllo completo come in auth.php
if (isset($_SESSION['user_id']) && isset($_SESSION['loggedin'])) {
    header('Location: dashboard.php');
} else {
    header('Location: login.php');
}
exit;
?>