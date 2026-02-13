<?php
require_once __DIR__ . '/../backend/db.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $data = json_decode(file_get_contents("php://input"), true);

    // Campi obbligatori
    $nome = $data['nome'] ?? null;
    if (!$nome) {
        throw new Exception("Nome cantiere obbligatorio");
    }

    // Campi opzionali
    $indirizzo = $data['indirizzo'] ?? null;
    $referente = $data['referente'] ?? null;
    $giorni = $data['giorni_lavoro'] ?? null;
    $inizio = $data['data_inizio'] ?? null;
    $fine = $data['data_fine'] ?? null;
    $note = $data['note'] ?? null;
    $lat = $data['lat'] ?? null;
    $lng = $data['lng'] ?? null;
    $stato = $data['stato'] ?? 'attivo';
    $coordinatore_sicurezza = $data['coordinatore_sicurezza'] ?? null;
    $piano_sicurezza = $data['piano_sicurezza'] ?? null;
    $numero_operai = isset($data['numero_operai']) ? (int)$data['numero_operai'] : null;

    $sql = "INSERT INTO cantieri 
            (nome, indirizzo, referente, giorni_lavoro, data_inizio, data_fine, 
             lat, lng, note, stato, coordinatore_sicurezza, piano_sicurezza, numero_operai)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Errore prepare: " . $conn->error);
    }

    $stmt->bind_param("ssssssssssssi",
        $nome, $indirizzo, $referente, $giorni, $inizio, $fine, 
        $lat, $lng, $note, $stato, $coordinatore_sicurezza, $piano_sicurezza, $numero_operai
    );

    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Cantiere aggiunto",
            "id" => $conn->insert_id
        ]);
    } else {
        throw new Exception("Errore execute: " . $stmt->error);
    }

    $stmt->close();

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}

$conn->close();
?>