<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SESSION['ruolo'] !== 'admin') {
    // Dipendente che cerca di accedere a pagina admin
    header('Location: dashboard_dipendente.php'); // o index.php
    exit;
}