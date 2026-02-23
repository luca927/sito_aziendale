<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SESSION['ruolo'] === 'admin' || $_SESSION['ruolo'] === 'manager') {
    header('Location: dashboard.php'); // dashboard admin
    exit;
}