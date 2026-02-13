<?php
// api/export_report.php
require_once __DIR__ . '/../backend/db.php';


// Export all results (or filtered by search)
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sql = "SELECT tipo, dipendente, cantiere, mezzo, lat_cantiere, lng_cantiere, lat_inizio, lng_inizio, lat_fine, lng_fine, created_at FROM azioni_dipendenti";
$params = [];
if ($search !== '') {
$sql .= " WHERE dipendente LIKE :s OR cantiere LIKE :s OR tipo LIKE :s";
$params[':s'] = "%$search%";
}
$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
if ($search !== '') $stmt->bindValue(':s', $params[':s'], PDO::PARAM_STR);
$stmt->execute();


header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=report.csv');
$out = fopen('php://output', 'w');
fputcsv($out, ['Tipo','Dipendente','Cantiere','Mezzo','Lat cantiere','Lng cantiere','Lat inizio','Lng inizio','Lat fine','Lng fine','Data']);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
fputcsv($out, $row);
}
fclose($out);
exit;
?>