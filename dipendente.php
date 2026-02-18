<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Solo utenti loggati e NON admin
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
if ($_SESSION['ruolo'] === 'admin') {
    header('Location: dashboard.php');
    exit;
}

require_once __DIR__ . '/backend/db.php';
$conn->set_charset("utf8mb4");

$userId = $_SESSION['user_id'];

// Recupera il dipendente_id collegato all'account
$stmt = $conn->prepare("SELECT dipendente_id, username FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$userRow = $stmt->get_result()->fetch_assoc();
$stmt->close();

$dipendente_id = $userRow['dipendente_id'];

// Dati anagrafici del dipendente
$dipendente = null;
if ($dipendente_id) {
    $stmt = $conn->prepare("SELECT * FROM dipendenti WHERE id = ?");
    $stmt->bind_param("i", $dipendente_id);
    $stmt->execute();
    $dipendente = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Attività assegnate al dipendente (ultime 10)
$attivita = [];
if ($dipendente_id) {
    $stmt = $conn->prepare("
        SELECT d.*, c.nome AS cantiere_nome, c.indirizzo AS cantiere_indirizzo,
               c.lat AS cantiere_lat, c.lng AS cantiere_lng,
               m.nome_mezzo AS mezzo_nome, m.targa
        FROM dashboard d
        LEFT JOIN cantieri c ON d.cantiere_id = c.id
        LEFT JOIN mezzi m ON d.mezzo_id = m.id
        WHERE d.dipendente_id = ?
        ORDER BY d.data_attivita DESC
        LIMIT 10
    ");
    $stmt->bind_param("i", $dipendente_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $attivita[] = $row;
    }
    $stmt->close();
}

// Ultima attività (cantiere e mezzo correnti)
$ultimaAttivita = $attivita[0] ?? null;

$conn->close();

$nomeDisplay = $dipendente ? ($dipendente['nome'] . ' ' . $dipendente['cognome']) : $userRow['username'];
$iniziale = strtoupper(substr($nomeDisplay, 0, 1));
?>
<!doctype html>
<html lang="it">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Area Personale - Arsnet</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
  <style>
    body { background: #f4f6fb; font-family: 'Poppins', sans-serif; padding-top: 70px; }

    /* NAVBAR */
    .navbar { background: #1a2a4a !important; }
    .navbar-brand span { color: #fff; }
    .nav-link { color: rgba(255,255,255,0.8) !important; }
    .nav-link:hover { color: #fff !important; }

    /* AVATAR */
    .avatar {
      width: 50px; height: 50px; border-radius: 50%;
      background: linear-gradient(135deg, #0d6efd, #6610f2);
      display: flex; align-items: center; justify-content: center;
      font-size: 1.3rem; color: #fff; font-weight: 700;
    }
    .avatar-lg {
      width: 80px; height: 80px; font-size: 2rem;
      border-radius: 50%;
      background: linear-gradient(135deg, #0d6efd, #6610f2);
      display: flex; align-items: center; justify-content: center;
      color: #fff; font-weight: 700; margin: 0 auto 1rem;
    }

    /* CARD */
    .card { border: none; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.07); }
    .card-header-custom {
      background: linear-gradient(135deg, #1a2a4a, #0d6efd);
      color: #fff; border-radius: 16px 16px 0 0 !important;
      padding: 1rem 1.5rem; font-weight: 600;
    }

    /* KPI CARDS */
    .kpi-card {
      border-radius: 14px; padding: 1.2rem 1.5rem;
      color: #fff; display: flex; align-items: center; gap: 1rem;
    }
    .kpi-card .kpi-icon { font-size: 2rem; opacity: 0.85; }
    .kpi-card .kpi-label { font-size: 0.78rem; opacity: 0.85; text-transform: uppercase; letter-spacing: 0.05em; }
    .kpi-card .kpi-value { font-size: 1.2rem; font-weight: 700; }
    .kpi-blue   { background: linear-gradient(135deg, #0d6efd, #0a58ca); }
    .kpi-green  { background: linear-gradient(135deg, #198754, #146c43); }
    .kpi-orange { background: linear-gradient(135deg, #fd7e14, #dc6502); }

    /* BADGE ATTIVITÀ */
    .badge-lavorazione { background: #198754; }
    .badge-spostamento { background: #0dcaf0; color: #000; }

    /* MAPPA */
    #mapDipendente { height: 250px; border-radius: 0 0 16px 16px; }

    /* INFO ROW */
    .info-row { display: flex; align-items: center; gap: 0.6rem; padding: 0.5rem 0;
                border-bottom: 1px solid #f0f0f0; font-size: 0.9rem; }
    .info-row:last-child { border-bottom: none; }
    .info-row i { width: 20px; color: #0d6efd; text-align: center; }
    .info-label { color: #6c757d; min-width: 120px; font-size: 0.82rem; }

    /* TABELLA */
    .table th { font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.04em;
                color: #6c757d; border-bottom: 2px solid #e9ecef; }
    .table td { font-size: 0.88rem; vertical-align: middle; }
  </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg fixed-top shadow-sm">
  <div class="container-fluid px-4">
    <a class="navbar-brand d-flex align-items-center gap-2" href="#">
      <img src="assets/img/logo_delta.png" alt="logo" style="height:32px">
      <span class="fw-semibold">Arsnet</span>
    </a>
    <div class="ms-auto d-flex align-items-center gap-3">
      <a href="timbrature_dipendente.php" class="text-white text-decoration-none small">
        <i class="fa-solid fa-clock"></i>
      </a>
      <a href="profilo.php" class="text-white text-decoration-none small">
        <i class="fa-solid fa-user"></i>
      </a>
      <a href="logout.php" class="text-white text-decoration-none small">
        <i class="fa-solid fa-right-from-bracket"></i>
      </a>
    </div>
  </div>
</nav>

<div class="container py-4">

 <!-- HEADER BENVENUTO -->
<div class="mb-4 d-flex align-items-center gap-3">
  <div class="avatar-lg" style="width:55px;height:55px;font-size:1.4rem;flex-shrink:0;">
    <?= $iniziale ?>
  </div>
  <div>
    <h4 class="fw-bold mb-0">
      Buongiorno, <?= htmlspecialchars(explode(' ', $nomeDisplay)[0]) ?> 👋
    </h4>
    <p class="text-muted small mb-0"><?= date('l d F Y') ?></p>
  </div>
</div>

  <!-- KPI CARDS -->
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="kpi-card kpi-blue">
        <div class="kpi-icon"><i class="fa-solid fa-hard-hat"></i></div>
        <div>
          <div class="kpi-label">Cantiere Attuale</div>
          <div class="kpi-value"><?= htmlspecialchars($ultimaAttivita['cantiere_nome'] ?? 'Nessuno') ?></div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="kpi-card kpi-green">
        <div class="kpi-icon"><i class="fa-solid fa-truck"></i></div>
        <div>
          <div class="kpi-label">Mezzo Assegnato</div>
          <div class="kpi-value"><?= htmlspecialchars($ultimaAttivita['mezzo_nome'] ?? 'Nessuno') ?></div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="kpi-card kpi-orange">
        <div class="kpi-icon"><i class="fa-solid fa-list-check"></i></div>
        <div>
          <div class="kpi-label">Attività Totali</div>
          <div class="kpi-value"><?= count($attivita) ?></div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">

    <!-- COLONNA SINISTRA: Profilo + Cantiere -->
    <div class="col-lg-4">

      <!-- Card Profilo -->
      <div class="card mb-4">
        <div class="card-body text-center pt-4">
          <div class="avatar-lg"><?= $iniziale ?></div>
          <h5 class="fw-semibold mb-1"><?= htmlspecialchars($nomeDisplay) ?></h5>
          <?php if ($dipendente): ?>
            <span class="badge bg-primary rounded-pill mb-3">
              <?php
                $livelli = [1 => 'Junior', 2 => 'Middle', 3 => 'Senior', 4 => 'Expert'];
                echo $livelli[$dipendente['livello_esperienza']] ?? 'Operatore';
              ?>
            </span>
            <div class="text-start px-2">
              <?php if ($dipendente['telefono']): ?>
              <div class="info-row">
                <i class="fa-solid fa-phone"></i>
                <span class="info-label">Telefono</span>
                <span><?= htmlspecialchars($dipendente['telefono']) ?></span>
              </div>
              <?php endif; ?>
              <?php if ($dipendente['recapitieMail']): ?>
              <div class="info-row">
                <i class="fa-solid fa-envelope"></i>
                <span class="info-label">Email</span>
                <span><?= htmlspecialchars($dipendente['recapitieMail']) ?></span>
              </div>
              <?php endif; ?>
              <?php if ($dipendente['patenti']): ?>
              <div class="info-row">
                <i class="fa-solid fa-id-card"></i>
                <span class="info-label">Patenti</span>
                <span><?= htmlspecialchars($dipendente['patenti']) ?></span>
              </div>
              <?php endif; ?>
              <?php if ($dipendente['data_assunzione']): ?>
              <div class="info-row">
                <i class="fa-solid fa-briefcase"></i>
                <span class="info-label">Assunto il</span>
                <span><?= date('d/m/Y', strtotime($dipendente['data_assunzione'])) ?></span>
              </div>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Card Cantiere con Mappa -->
      <?php if ($ultimaAttivita && $ultimaAttivita['cantiere_lat']): ?>
      <div class="card">
        <div class="card-header-custom">
          <i class="fa-solid fa-location-dot me-2"></i>Cantiere Attuale
        </div>
        <div class="card-body pb-0">
          <p class="fw-semibold mb-1"><?= htmlspecialchars($ultimaAttivita['cantiere_nome']) ?></p>
          <?php if ($ultimaAttivita['cantiere_indirizzo']): ?>
          <p class="text-muted small mb-2">
            <i class="fa-solid fa-map-marker-alt me-1"></i>
            <?= htmlspecialchars($ultimaAttivita['cantiere_indirizzo']) ?>
          </p>
          <?php endif; ?>
        </div>
        <div id="mapDipendente"></div>
      </div>
      <?php endif; ?>

    </div>

    <!-- COLONNA DESTRA: Attività -->
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header-custom">
          <i class="fa-solid fa-clipboard-list me-2"></i>Le Mie Attività Recenti
        </div>
        <div class="card-body p-0">
          <?php if (empty($attivita)): ?>
            <div class="text-center py-5 text-muted">
              <i class="fa-solid fa-inbox fa-3x mb-3 opacity-25"></i>
              <p>Nessuna attività assegnata al momento.</p>
            </div>
          <?php else: ?>
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th class="ps-3">Tipo</th>
                  <th>Cantiere</th>
                  <th>Mezzo</th>
                  <th>Targa</th>
                  <th>Data</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($attivita as $a): ?>
                <tr>
                  <td class="ps-3">
                    <?php $badgeCls = $a['tipo_attivita'] === 'SPOSTAMENTO' ? 'badge-spostamento' : 'badge-lavorazione'; ?>
                    <span class="badge <?= $badgeCls ?> rounded-pill">
                      <?= htmlspecialchars($a['tipo_attivita']) ?>
                    </span>
                  </td>
                  <td class="fw-medium"><?= htmlspecialchars($a['cantiere_nome'] ?? '-') ?></td>
                  <td><?= htmlspecialchars($a['mezzo_nome'] ?? '-') ?></td>
                  <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($a['targa'] ?? '-') ?></span></td>
                  <td class="text-muted"><?= $a['data_attivita'] ? date('d/m/Y', strtotime($a['data_attivita'])) : '-' ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Card Mezzo dettaglio -->
      <?php if ($ultimaAttivita && $ultimaAttivita['mezzo_nome']): ?>
      <div class="card mt-4">
        <div class="card-header-custom">
          <i class="fa-solid fa-truck me-2"></i>Mezzo Assegnato
        </div>
        <div class="card-body">
          <div class="row g-3 align-items-center">
            <div class="col-auto">
              <div style="width:60px;height:60px;background:#e8f0fe;border-radius:12px;
                          display:flex;align-items:center;justify-content:center;font-size:1.8rem;">
                🚛
              </div>
            </div>
            <div class="col">
              <h5 class="fw-bold mb-1"><?= htmlspecialchars($ultimaAttivita['mezzo_nome']) ?></h5>
              <?php if ($ultimaAttivita['targa']): ?>
              <span class="badge bg-dark rounded-pill">
                <i class="fa-solid fa-hashtag me-1"></i><?= htmlspecialchars($ultimaAttivita['targa']) ?>
              </span>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
      <?php endif; ?>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
<?php if ($ultimaAttivita && $ultimaAttivita['cantiere_lat'] && $ultimaAttivita['cantiere_lng']): ?>
const lat = <?= floatval($ultimaAttivita['cantiere_lat']) ?>;
const lng = <?= floatval($ultimaAttivita['cantiere_lng']) ?>;
const nomeCantiere = <?= json_encode($ultimaAttivita['cantiere_nome']) ?>;

const map = L.map('mapDipendente').setView([lat, lng], 15);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
}).addTo(map);
L.marker([lat, lng])
    .addTo(map)
    .bindPopup(`<strong>${nomeCantiere}</strong>`)
    .openPopup();
<?php endif; ?>
</script>
</body>
</html>