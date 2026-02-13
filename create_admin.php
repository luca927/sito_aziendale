<?php
// FILE: init_admin.php (o come lo hai chiamato)

// 1. Inclusione del file di Connessione al Database
require_once __DIR__ . '/backend/db.php'; 

// DATI DELL'ADMIN DA CREARE
$username = "admin";
$password = "admin123";

// Hash sicuro
$hash = password_hash($password, PASSWORD_DEFAULT);

// ==========================================================
// PASSAGGIO 1: Verifica se l'utente 'admin' esiste già
// ==========================================================

$check_sql = "SELECT id FROM users WHERE username = ?";
$check_stmt = $conn->prepare($check_sql);

if (!$check_stmt) {
    die("Errore nella query di verifica: " . $conn->error);
}

$check_stmt->bind_param("s", $username);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    // Utente Trovato, non fare nulla e avvisa
    echo "<h2>⚠️ Attenzione: Utente '{$username}' esiste già. Inserimento saltato.</h2>";
    echo "<p>Ora puoi fare login su <a href='login.php'>login.php</a></p>";
    $check_stmt->close();
    $conn->close();
    exit;
}

$check_stmt->close();

// ==========================================================
// PASSAGGIO 2: Inserimento dell'utente (solo se non esiste)
// ==========================================================

$insert_sql = "INSERT INTO users (username, password_hash) VALUES (?, ?)";
$insert_stmt = $conn->prepare($insert_sql);

if (!$insert_stmt) {
    die("Errore nella query di inserimento: " . $conn->error);
}

$insert_stmt->bind_param("ss", $username, $hash);

if ($insert_stmt->execute()) {
    echo "<h2>✔ Utente admin creato con successo!</h2>";
    echo "<p><strong>Username:</strong> {$username}</p>";
    echo "<p><strong>Password:</strong> {$password}</p>";
    echo "<p>Ora puoi fare login su <a href='login.php'>login.php</a></p>";
} else {
    echo "Errore inserimento: " . $insert_stmt->error;
}

$insert_stmt->close();
$conn->close();
?>