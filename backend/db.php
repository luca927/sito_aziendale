<?php
$servername = "localhost";
$username = "root"; // Utente predefinito XAMPP
$password = "";     // Password predefinita XAMPP  //'testpassword123'
$dbname = "sitoarsnet_db";
$port = 3306; // Porta predefinita MySQL


// Tenta la connessione
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Controlla la connessione
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "Errore connessione DB",
        "dettaglio" => $conn->connect_error
    ]);
    exit;
}
?>