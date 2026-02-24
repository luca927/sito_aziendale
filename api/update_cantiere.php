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
header('Content-Type: application/json');
require_once __DIR__ . '/../backend/db.php';

// Recuperiamo i dati inviati come JSON
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['id'])) {
    echo json_encode(["success" => false, "error" => "Dati mancanti"]);
    exit;
}

$id_cantiere = intval($data['id']);
$operai = isset($data['operai']) ? $data['operai'] : []; // Questo è l'array degli ID [1, 5, 8]

// Inizia una transazione per essere sicuri che tutto vada a buon fine
$conn->begin_transaction();

try {
    // 1. Aggiorna i dati base del cantiere
    $sql = "UPDATE cantieri SET 
            nome = ?, indirizzo = ?, referente = ?, giorni_lavoro = ?, 
            data_inizio = ?, data_fine = ?, note = ?, lat = ?, lng = ?, 
            stato = ?, coordinatore_sicurezza = ?, piano_sicurezza = ? 
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssssi", 
        $data['nome'], $data['indirizzo'], $data['referente'], $data['giorni_lavoro'],
        $data['data_inizio'], $data['data_fine'], $data['note'], $data['lat'], $data['lng'],
        $data['stato'], $data['coordinatore_sicurezza'], $data['piano_sicurezza'], $id_cantiere
    );
    $stmt->execute();

    // 2. GESTIONE OPERAI: Svuota le vecchie assegnazioni
    $sqlDelete = "DELETE FROM assegnazioni_dipendenti_cantiere WHERE id_cantiere = ?";
    $stmtDelete = $conn->prepare($sqlDelete);
    $stmtDelete->bind_param("i", $id_cantiere);
    $stmtDelete->execute();

    // 3. Inserisci le nuove assegnazioni
    if (!empty($operai)) {
        $sqlInsert = "INSERT INTO assegnazioni_dipendenti_cantiere (id_cantiere, id_dipendente) VALUES (?, ?)";
        $stmtInsert = $conn->prepare($sqlInsert);
        foreach ($operai as $id_operaio) {
            $id_op = intval($id_operaio);
            $stmtInsert->bind_param("ii", $id_cantiere, $id_op);
            $stmtInsert->execute();
        }
    }

    $conn->commit();
    echo json_encode(["success" => true]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}