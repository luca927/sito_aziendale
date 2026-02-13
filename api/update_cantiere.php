<?php
require_once __DIR__ . '/../backend/db.php';
header('Content-Type: application/json; charset=utf-8');

try {
    // Leggi input JSON
    $data = json_decode(file_get_contents("php://input"), true);

    // ID cantiere obbligatorio
    if (!isset($data['id'])) {
        throw new Exception("ID cantiere obbligatorio");
    }
    $id = (int)$data['id'];

    // Nome cantiere obbligatorio
    if (!isset($data['nome']) || trim($data['nome']) === '') {
        throw new Exception("Nome cantiere obbligatorio");
    }
    $nome = trim($data['nome']);

    // Campi opzionali
    $indirizzo = $data['indirizzo'] ?? null;
    $referente = $data['referente'] ?? null;
    $giorni = $data['giorni_lavoro'] ?? null;
    $inizio = $data['data_inizio'] ?? null;
    $fine = $data['data_fine'] ?? null;
    $lat = $data['lat'] ?? null;
    $lng = $data['lng'] ?? null;
    $note = $data['note'] ?? null;
    $stato = $data['stato'] ?? 'attivo';
    $coordinatore_sicurezza = $data['coordinatore_sicurezza'] ?? null;
    $piano_sicurezza = $data['piano_sicurezza'] ?? null;
    $numero_operai = isset($data['numero_operai']) ? (int)$data['numero_operai'] : null;

    // Query di aggiornamento
    $sql = "UPDATE cantieri SET
                nome = ?,
                indirizzo = ?,
                referente = ?,
                giorni_lavoro = ?,
                data_inizio = ?,
                data_fine = ?,
                lat = ?,
                lng = ?,
                note = ?,
                stato = ?,
                coordinatore_sicurezza = ?,
                piano_sicurezza = ?,
                numero_operai = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Errore prepare: " . $conn->error);
    }

    // Bind dei parametri
    $stmt->bind_param(
        "ssssssssssssii",
        $nome, $indirizzo, $referente, $giorni, $inizio, $fine,
        $lat, $lng, $note, $stato, $coordinatore_sicurezza, $piano_sicurezza, $numero_operai, $id
    );

    // Esegui e controlla
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode([
                "success" => true,
                "message" => "Cantiere aggiornato correttamente"
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Nessun cantiere aggiornato. Controlla che l'ID esista e i valori siano diversi da quelli attuali"
            ]);
        }
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
