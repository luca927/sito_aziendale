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

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "error" => "Dati mancanti"]);
    exit;
}

$nome = $data['nome'] ?? null;
$cognome = $data['cognome'] ?? null;
$dataDiNascita = $data['data_nascita'] ?? null;
$sesso = $data['sesso'] ?? null;
$stato_civile = $data['stato_civile'] ?? null;
$telefono = $data['telefono'] ?? null;
$email = $data['email'] ?? null;
$residenza = $data['residenza'] ?? null;
$codice_fiscale = $data['codice_fiscale'] ?? null;
$data_assunzione = $data['data_assunzione'] ?? null;
$patenti = $data['patenti'] ?? null;
$iban = $data['iban'] ?? null;
$formazione = $data['formazione'] ?? null;
$esperienze = $data['esperienze'] ?? null;
$competenze = $data['competenze'] ?? null;
$livello = $data['livello_esperienza'] ?? null;

$sql = "INSERT INTO dipendenti (
    nome, cognome, dataDiNascita, sesso, stato_civile,
    telefono, recapitieMail, indirizzoResidenza, codice_fiscale,
    data_assunzione, patenti, iban, Corsi_e_Formazione,
    Esperienze, Competenze, livello_esperienza
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "sssssssssssssssi",
    $nome, $cognome, $dataDiNascita, $sesso, $stato_civile,
    $telefono, $email, $residenza, $codice_fiscale,
    $data_assunzione, $patenti, $iban, $formazione,
    $esperienze, $competenze, $livello
);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
