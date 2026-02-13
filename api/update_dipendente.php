<?php
require_once __DIR__ . '/../backend/db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "error" => "Dati mancanti"]);
    exit;
}

$id = $data['id'] ?? null;

$nome = $data['nome'] ?? '';
$cognome = $data['cognome'] ?? '';
$dataNascita = $data['data_nascita'] ?? null;
$sesso = $data['sesso'] ?? null;
$statoCivile = $data['stato_civile'] ?? null;
$telefono = $data['telefono'] ?? null;
$email = $data['email'] ?? null;
$residenza = $data['residenza'] ?? null;
$codiceFiscale = $data['codice_fiscale'] ?? null;
$dataAssunzione = $data['data_assunzione'] ?? null;
$patenti = $data['patenti'] ?? null;
$iban = $data['iban'] ?? null;
$formazione = $data['formazione'] ?? null;

$esperienze = $data['esperienze'] ?? null;
$competenze = $data['competenze'] ?? null;
$livello = $data['livello_esperienza'] ?? null;
$datiBancari = $data['DatiBancari'] ?? null;
$lat = $data['lat'] ?? null;
$lng = $data['lng'] ?? null;
$documenti = $data['documentiIdentita'] ?? null;

function fmt($d) {
    if (!$d) return null;
    return date("Y-m-d", strtotime($d));
}

$dataNascita = fmt($dataNascita);
$dataAssunzione = fmt($dataAssunzione);

if ($id) {

    $sql = "UPDATE dipendenti SET 
        nome=?, 
        cognome=?, 
        dataDiNascita=?, 
        sesso=?, 
        stato_civile=?, 
        telefono=?, 
        recapitieMail=?, 
        indirizzoResidenza=?, 
        codice_fiscale=?, 
        data_assunzione=?, 
        patenti=?, 
        iban=?, 
        Corsi_e_Formazione=?,
        Esperienze=?,
        Competenze=?,
        livello_esperienza=?,
        DatiBancari=?,
        lat=?,
        lng=?,
        documentiIdentita=?
        WHERE id=?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "sssssssssssssssissssi",
        $nome, $cognome, $dataNascita, $sesso, $statoCivile, $telefono,
        $email, $residenza, $codiceFiscale, $dataAssunzione,
        $patenti, $iban, $formazione,
        $esperienze, $competenze, $livello,
        $datiBancari, $lat, $lng, $documenti,
        $id
    );

} else {

    $sql = "INSERT INTO dipendenti 
        (nome, cognome, dataDiNascita, sesso, stato_civile, telefono, recapitieMail,
         indirizzoResidenza, codice_fiscale, data_assunzione, patenti, iban, 
         Corsi_e_Formazione, Esperienze, Competenze, livello_esperienza,
         DatiBancari, lat, lng, documentiIdentita)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "sssssssssssssssissss",
        $nome, $cognome, $dataNascita, $sesso, $statoCivile, $telefono,
        $email, $residenza, $codiceFiscale, $dataAssunzione,
        $patenti, $iban, $formazione,
        $esperienze, $competenze, $livello,
        $datiBancari, $lat, $lng, $documenti
    );
}

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
