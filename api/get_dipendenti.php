<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// 1. Controlla se loggato
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Non autorizzato"]);
    exit;
}

// 2. Controlla se la sessione è scaduta (1 ora)
if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > 3600) {
    session_destroy();
    http_response_code(401);
    echo json_encode(["error" => "Sessione scaduta"]);
    exit;
}
$_SESSION['last_activity'] = time();
require_once __DIR__ . '/../backend/db.php';
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Query allineata alla TUA tabella MySQL
$sql = "SELECT 
            id,
            nome,
            cognome,
            dataDiNascita,
            sesso,
            stato_civile,
            telefono,
            recapitieMail,
            indirizzoResidenza,
            codice_fiscale,
            data_assunzione,
            patenti,
            iban,
            Corsi_e_Formazione,
            created_at,
            updated_at
        FROM dipendenti
        ORDER BY id ASC";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode([
        "success" => false,
        "error" => $conn->error
    ]);
    exit;
}

$dipendenti = [];

while ($row = $result->fetch_assoc()) {
    $dipendenti[] = $row;
}

echo json_encode([
    "success" => true,
    "data" => $dipendenti
]);

$conn->close();
?>