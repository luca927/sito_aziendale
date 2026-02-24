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

require_once __DIR__ . '/../backend/auth.php';
require_once __DIR__ . '/../backend/db.php';

// 1. Leggo i dati JSON PRIMA DI TUTTO
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "error" => "Dati mancanti"]);
    exit;
}

// 2. Estraggo i dati
$dipendente_id = $_SESSION['user_id'];
$lat_user = $data['lat'];
$lng_user = $data['lng'];
$tipo = $data['tipo'];
$causale_id = $data['causale_id'] ?: null;

// 3. Controllo sequenza Entrata/Uscita
$check_sql = "SELECT tipo FROM timbrature WHERE dipendente_id = ? ORDER BY data_ora_server DESC LIMIT 1";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $dipendente_id);
$check_stmt->execute();
$last_status = $check_stmt->get_result()->fetch_assoc();

if ($last_status && $tipo === $last_status['tipo']) {
    echo json_encode([
        "success" => false,
        "error" => "Non puoi inserire due $tipo consecutive."
    ]);
    exit;
}

$dipendente_id = $_SESSION['user_id'];
$lat_user = $data['lat'];
$lng_user = $data['lng'];
$tipo = $data['tipo'];
$causale_id = $data['causale_id'] ?: null;

// Funzione Haversine per calcolare la distanza in metri
function haversineDist($lat1, $lng1, $lat2, $lng2) {
    $earth_radius = 6371000;
    $dLat = deg2rad($lat2 - $lat1);
    $dLng = deg2rad($lng2 - $lng1);
    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng/2) * sin($dLng/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    return round($earth_radius * $c);
}

// 1. Cerchiamo il cantiere più vicino tra quelli attivi
$sql = "SELECT id, nome, lat, lng FROM cantieri";
$res = $conn->query($sql);

$cantiere_trovato = null;
$distanza_minima = 9999999;
$raggio = 100; // raggio in metri

while ($row = $res->fetch_assoc()) {
    $dist = haversineDist($lat_user, $lng_user, $row['lat'], $row['lng']);

    if ($dist <= $raggio) {
        $cantiere_trovato = $row;
        $distanza_minima = $dist;
        break;
    }
}


// Se non è vicino a nessun cantiere, blocchiamo la timbratura (se vuoi geofencing obbligatorio)
if (!$cantiere_trovato) {
    echo json_encode(["success" => false, "error" => "Ti trovi fuori dal raggio di qualsiasi cantiere (Distanza minima: $distanza_minima metri)"]);
    exit;
}

// 2. Inserimento nel Database
$cantiere_id = $cantiere_trovato['id'];

$stmt = $conn->prepare("
    INSERT INTO timbrature 
    (dipendente_id, cantiere_id, tipo, causale_id, lat, lng, distanza_rilevatore, data_ora, data_ora_server)
    VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
");


$stmt->bind_param("iissddi", 
    $dipendente_id, 
    $cantiere_id, 
    $tipo, 
    $causale_id, 
    $lat_user, 
    $lng_user, 
    $distanza_minima
);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true, 
        "ora" => date('H:i:s'), 
        "distanza" => $distanza_minima,
        "cantiere" => $cantiere_trovato['nome']
    ]);
} else {
    echo json_encode(["success" => false, "error" => "Errore DB: " . $conn->error]);
}