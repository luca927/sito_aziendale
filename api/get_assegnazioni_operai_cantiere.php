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

try {
    // Questa query usa i nomi standard, ma se uno è sbagliato darà errore invece di NULL
   $sql = "SELECT c.*, 
               GROUP_CONCAT(DISTINCT CONCAT(d.nome, ' ', d.cognome) SEPARATOR ', ') AS nome_dipendente
        FROM cantieri c
        LEFT JOIN assegnazioni_dipendenti_cantiere adc ON c.id = adc.id_cantiere
        LEFT JOIN dipendenti d ON adc.id_dipendente = d.id
        GROUP BY c.id";

    $result = $conn->query($sql);

    if (!$result) {
        // Se c'è un errore nei nomi delle colonne, lo vedrai qui nel browser!
        die(json_encode(["errore_sql" => $conn->error]));
    }

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode($data);

} catch (Exception $e) {
    echo json_encode(["errore" => $e->getMessage()]);
}