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

$id = $data['id'] ?? null;

if (!$id) {
    echo json_encode(["success" => false, "error" => "ID mancante"]);
    exit;
}

$nome_mezzo = $data['nome_mezzo'] ?? null;
$targa = $data['targa'] ?? null;
$tipo = $data['tipo'] ?? null;
$anno = $data['anno'] ?? null;
$portata_kg = $data['portata_kg'] ?? null;
$centro_costo = $data['centro_costo'] ?? null;
$pneumatici_attuali = $data['pneumatici_attuali'] ?? null;
$dotazioni = $data['dotazioni'] ?? null;
$ultima = $data['ultima_manutenzione'] ?? null;
$prossima = $data['prossima_manutenzione'] ?? null;
$stato = $data['stato'] ?? null;
$note = $data['note'] ?? null;

$sql = "UPDATE mezzi SET
            nome_mezzo=?,
            targa=?,
            tipo=?,
            anno=?,
            portata_kg=?,
            centro_costo=?,
            pneumatici_attuali=?,
            dotazioni=?,
            ultima_manutenzione=?,
            prossima_manutenzione=?,
            stato=?,
            note=?
        WHERE id=?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "sssissssssssi",
    $nome_mezzo,
    $targa,
    $tipo,
    $anno,
    $portata_kg,
    $centro_costo,
    $pneumatici_attuali,
    $dotazioni,
    $ultima,
    $prossima,
    $stato,
    $note,
    $id
);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
