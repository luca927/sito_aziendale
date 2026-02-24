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

try {
    // Ricezione dati dal frontend
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    $dip_id = !empty($data['dipendente_id']) ? intval($data['dipendente_id']) : null;
    $can_id = !empty($data['cantiere_id']) ? intval($data['cantiere_id']) : null;
    $mez_id = !empty($data['mezzo_id']) ? intval($data['mezzo_id']) : null;
    $tipo   = !empty($data['tipo_attivita']) ? $data['tipo_attivita'] : "LAVORAZIONE";

    $tipi_ammessi = ['LAVORAZIONE', 'SPOSTAMENTO'];
    if (!in_array($tipo, $tipi_ammessi)) {
        echo json_encode(["error" => "Tipo non valido"]);
        exit;
    }


    // Recuperiamo i dati assicurandoci che se sono vuoti diventino NULL
    $lat_man = isset($data['lat_man']) && $data['lat_man'] !== '' ? floatval($data['lat_man']) : null;
    $lng_man = isset($data['lng_man']) && $data['lng_man'] !== '' ? floatval($data['lng_man']) : null;

    // 1. Recupero coordinate del cantiere
    $stmt_c = $conn->prepare("SELECT lat, lng FROM cantieri WHERE id = ?");
    $stmt_c->bind_param("i", $can_id);
    $stmt_c->execute();
    $res_c = $stmt_c->get_result()->fetch_assoc();

    $lat_db = (isset($res_c['lat']) && $res_c['lat'] !== "") ? floatval($res_c['lat']) : null;
    $lng_db = (isset($res_c['lng']) && $res_c['lng'] !== "") ? floatval($res_c['lng']) : null;

    // 2. Logica finale corretta: 
    // Se l'utente ha forzato una posizione (GPS o Manuale), usa quella. 
    // Altrimenti usa quella del cantiere.
    $lat_final = ($lat_man !== null) ? $lat_man : ($lat_db !== null ? $lat_db : 0.0);
    $lng_final = ($lng_man !== null) ? $lng_man : ($lng_db !== null ? $lng_db : 0.0);
    
    // 3. INSERT nella dashboard
    $sql = "INSERT INTO dashboard (tipo_attivita, dipendente_id, cantiere_id, mezzo_id, lat, lng, data_attivita)
            VALUES (?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siiidd", $tipo, $dip_id, $can_id, $mez_id, $lat_final, $lng_final);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>