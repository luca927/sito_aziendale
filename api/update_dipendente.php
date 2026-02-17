<?php
require_once __DIR__ . '/../backend/db.php';
header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        throw new Exception("Dati mancanti");
    }

    $id = $data['id'] ?? null;
    $nome = $data['nome'] ?? '';
    $cognome = $data['cognome'] ?? '';
    $dataNascita = $data['data_nascita'] ?? null;
    $dataAssunzione = $data['data_assunzione'] ?? null;
    $sesso = $data['sesso'] ?? null;
    $statoCivile = $data['stato_civile'] ?? null;
    $telefono = $data['telefono'] ?? null;
    $email = $data['email'] ?? null;
    $residenza = $data['residenza'] ?? null;
    $codiceFiscale = $data['codice_fiscale'] ?? null;
    $formazione = $data['formazione'] ?? null;
    $esperienze = $data['esperienze'] ?? null;
    $competenze = $data['competenze'] ?? null;
    $livello = $data['livello_esperienza'] ?? null;

    function fmt($d) {
        if (!$d) return null;
        return date("Y-m-d", strtotime($d));
    }

    $dataNascita = fmt($dataNascita);

    if ($id) {
        // UPDATE
        $sql = "UPDATE dipendenti SET 
            nome=?, 
            cognome=?, 
            dataDiNascita=?, 
            data_assunzione=?,
            sesso=?, 
            stato_civile=?, 
            telefono=?, 
            recapitieMail=?, 
            indirizzoResidenza=?, 
            codice_fiscale=?,
            Corsi_e_Formazione=?,
            Esperienze=?,
            Competenze=?,
            livello_esperienza=?
            WHERE id=?";

        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Errore prepare: " . $conn->error);
        }

        $stmt->bind_param(
            "sssssssssssssii",
            $nome, $cognome, $dataNascita, $dataAssunzione, $sesso, $statoCivile, $telefono,
            $email, $residenza, $codiceFiscale, $formazione,
            $esperienze, $competenze, $livello,
            $id
        );

        if (!$stmt->execute()) {
            throw new Exception("Errore execute: " . $stmt->error);
        }

        ob_end_clean(); // Pulisci il buffer
        echo json_encode(["success" => true]);

    } else {
        // INSERT
        $sql = "INSERT INTO dipendenti 
            (nome, cognome, dataDiNascita, data_assunzione, sesso, stato_civile, telefono, recapitieMail,
             indirizzoResidenza, codice_fiscale, Corsi_e_Formazione, Esperienze, Competenze, livello_esperienza)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            throw new Exception("Errore prepare: " . $conn->error);
        }

        $stmt->bind_param(
            "sssssssssssssii",
            $nome, $cognome, $dataNascita, $dataAssunzione, $sesso, $statoCivile, $telefono,
            $email, $residenza, $codiceFiscale, $formazione,
            $esperienze, $competenze, $livello
        );

        if (!$stmt->execute()) {
            throw new Exception("Errore execute: " . $stmt->error);
        }

        ob_end_clean(); // Pulisci il buffer
        echo json_encode(["success" => true]);
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    ob_end_clean(); // Pulisci il buffer prima di inviare errore
    http_response_code(500);
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>