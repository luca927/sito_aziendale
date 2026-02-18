<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Solo dipendenti, non admin
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
$stmt = $conn->prepare("SELECT dipendente_id, username FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$userRow = $stmt->get_result()->fetch_assoc();
$stmt->close();

$dipendente_id = $userRow['dipendente_id'];
$nomeDisplay = $userRow['username'];

if ($dipendente_id) {
    $stmt = $conn->prepare("SELECT CONCAT(nome, ' ', cognome) AS nome_completo FROM dipendenti WHERE id = ?");
    $stmt->bind_param("i", $dipendente_id);
    $stmt->execute();
    $dip = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($dip) $nomeDisplay = $dip['nome_completo'];
}

$conn->close();
$iniziale = strtoupper(substr($nomeDisplay, 0, 1));
?>
<!doctype html>
<html lang="it">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Timbrature - Arsnet</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    body { background: #f5f7fa; font-family: 'Poppins', sans-serif; padding-top: 70px; }
    .navbar { background: #1a2a4a !important; }
    .navbar-brand span { color: #fff; }
    .avatar {
      width: 38px; height: 38px; border-radius: 50%;
      background: linear-gradient(135deg, #0d6efd, #6610f2);
      display: flex; align-items: center; justify-content: center;
      font-size: 1rem; color: #fff; font-weight: 700;
    }
    .clock-container {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white; padding: 30px 20px; border-radius: 15px;
      margin-bottom: 20px; box-shadow: 0 8px 25px rgba(0,0,0,0.15); text-align: center;
    }
    .clock-display { font-size: 2.5rem; font-weight: bold; margin-bottom: 10px; }
    .date-display { font-size: 1rem; opacity: 0.9; text-transform: capitalize; }
    .timbratura-card {
      background: white; border-radius: 12px; padding: 20px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08); margin-bottom: 15px;
    }
    .buttons-container { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .btn-timbratura {
      padding: 25px 15px; font-size: 1rem; font-weight: bold;
      border-radius: 12px; border: none; transition: all 0.3s;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15); cursor: pointer;
      display: flex; flex-direction: column; align-items: center; justify-content: center;
    }
    .btn-timbratura:hover { transform: translateY(-2px); }
    .btn-timbratura i { font-size: 1.8rem; margin-bottom: 8px; display: block; }
    .btn-entrata { background: linear-gradient(135deg, #28a745, #20c997); color: white; }
    .btn-uscita  { background: linear-gradient(135deg, #dc3545, #fd7e14); color: white; }
    .status-location {
      padding: 12px; background: #f0f0f0; border-radius: 8px;
      font-size: 0.9rem; color: #666; text-align: center; margin-top: 12px;
    }
    .cronologia-item {
      background: #f8f9fa; border-left: 4px solid #0056D2;
      padding: 12px; margin-bottom: 10px; border-radius: 8px;
    }
    .badge-entrata { background: #28a745; color: white; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; }
    .badge-uscita  { background: #dc3545; color: white; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; }
    @media (max-width: 576px) {
      .buttons-container { grid-template-columns: 1fr; }
      .clock-display { font-size: 1.8rem; }
    }
  </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg fixed-top shadow-sm">
  <div class="container-fluid px-4">
    <a class="navbar-brand d-flex align-items-center gap-2" href="dipendente.php">
      <img src="assets/img/logo_delta.png" alt="logo" style="height:32px">
      <span class="fw-semibold">Arsnet</span>
    </a>
    <div class="ms-auto d-flex align-items-center gap-4">
      <a href="dipendente.php" class="text-white text-decoration-none" title="Dashboard">
        <i class="fa-solid fa-house"></i>
      </a>
      <a href="timbrature_dipendente.php" class="text-white text-decoration-none" title="Timbrature">
        <i class="fa-solid fa-clock"></i>
      </a>
      <a href="profilo.php" class="text-white text-decoration-none" title="Profilo">
        <i class="fa-solid fa-user"></i>
      </a>
      <a href="logout.php" class="text-white text-decoration-none" title="Logout">
        <i class="fa-solid fa-right-from-bracket text-danger"></i>
      </a>
    </div>
  </div>
</nav>

<div class="container py-4" style="max-width: 600px;">

  <!-- Orologio -->
  <div class="clock-container">
    <div id="clock" class="clock-display">--:--:--</div>
    <div id="current-date" class="date-display"></div>
  </div>

  <!-- Card Timbratura -->
  <div class="timbratura-card">
    <h3 class="mb-4"><i class="fas fa-clock me-2"></i>Timbra Presenza</h3>

    <div class="mb-4">
      <label class="fw-bold mb-2 d-block">Causale (Opzionale):</label>
      <select id="causale" class="form-select">
        <option value="">Lavoro Ordinario</option>
        <option value="1">Straordinario</option>
        <option value="2">Permesso</option>
      </select>
    </div>

    <div class="buttons-container">
      <button onclick="effettuaTimbratura('Entrata')" class="btn-timbratura btn-entrata">
        <i class="fas fa-sign-in-alt"></i>ENTRATA
      </button>
      <button onclick="effettuaTimbratura('Uscita')" class="btn-timbratura btn-uscita">
        <i class="fas fa-sign-out-alt"></i>USCITA
      </button>
    </div>

    <div id="status-location" class="status-location">
      <i class="fas fa-location-dot"></i> Ricerca posizione GPS...
    </div>
  </div>

  <!-- Cronologia -->
  <div class="timbratura-card">
    <h5 class="fw-bold border-bottom pb-3 mb-3">
      <i class="fas fa-history me-2"></i>Timbrature di Oggi
    </h5>
    <div id="cronologia-list">
      <p class="text-muted text-center py-3">Nessuna timbratura registrata oggi.</p>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Tutto il JS è identico alla tua timbrature.php originale
let serverTime;

async function syncClock() {
    try {
        const res = await fetch('api/get_server_time.php');
        const data = await res.json();
        serverTime = new Date(data.datetime);
        document.getElementById('current-date').innerText = serverTime.toLocaleDateString('it-IT', {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        });
    } catch (error) {
        console.error('Errore sincronizzazione orologio:', error);
    }
}

setInterval(() => {
    if (serverTime) {
        serverTime.setSeconds(serverTime.getSeconds() + 1);
        document.getElementById('clock').innerText = serverTime.toLocaleTimeString('it-IT');
    }
}, 1000);

async function caricaCronologia() {
    try {
        const res = await fetch('api/get_timbrature_oggi.php');
        const data = await res.json();
        const container = document.getElementById('cronologia-list');

        if (!Array.isArray(data) || data.length === 0) {
            container.innerHTML = '<p class="text-muted text-center py-3">Nessuna timbratura registrata oggi.</p>';
            return;
        }

        container.innerHTML = data.map(t => {
            const ora = new Date(t.data_ora_server).toLocaleTimeString('it-IT', {hour: '2-digit', minute: '2-digit'});
            const badgeClass = t.tipo === 'Entrata' ? 'badge-entrata' : 'badge-uscita';
            return `
                <div class="cronologia-item">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                        <div>
                            <span class="${badgeClass} me-2">${t.tipo.toUpperCase()}</span>
                            <strong class="d-block mt-1">${t.cantiere_nome || 'Cantiere non specificato'}</strong>
                            <small class="text-muted"><i class="far fa-clock me-1"></i>${ora}</small>
                        </div>
                        <small class="text-muted"><i class="fas fa-location-dot me-1"></i>${t.distanza_rilevatore}m</small>
                    </div>
                </div>`;
        }).join('');
    } catch (err) {
        document.getElementById('cronologia-list').innerHTML =
            '<p class="text-danger text-center py-3">Errore nel caricamento.</p>';
    }
}

async function effettuaTimbratura(tipo) {
    if (!navigator.geolocation) { alert("❌ Geolocalizzazione non supportata."); return; }

    const statusEl = document.getElementById('status-location');
    statusEl.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Acquisizione coordinate...';

    navigator.geolocation.getCurrentPosition(async (position) => {
        const payload = {
            tipo: tipo,
            lat: position.coords.latitude,
            lng: position.coords.longitude,
            causale_id: document.getElementById('causale').value || null
        };

        try {
            const res = await fetch('api/add_timbratura.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const result = JSON.parse(await res.text());

            if (!result.success && result.redirect) {
                alert(result.error);
                window.location.href = result.redirect;
                return;
            }

            if (result.success) {
                const cantiereInfo = result.cantiere ? `\n📍 Cantiere: ${result.cantiere}` : '';
                alert(`✅ Timbratura ${tipo} registrata alle ${result.ora}!${cantiereInfo}`);
                statusEl.innerHTML = '<i class="fas fa-check-circle text-success"></i> Posizione acquisita';
                await caricaCronologia();
                document.getElementById('causale').value = '';
            } else {
                alert("❌ " + (result.error || 'Errore sconosciuto'));
                statusEl.innerHTML = '<i class="fas fa-exclamation-circle"></i> Errore';
            }
        } catch (err) {
            alert("❌ Errore di comunicazione con il server.");
            statusEl.innerHTML = '<i class="fas fa-exclamation-circle"></i> Errore';
        }
    }, () => {
        alert("❌ Attiva la geolocalizzazione sul tuo dispositivo.");
        statusEl.innerHTML = '<i class="fas fa-exclamation-circle"></i> GPS negato';
    }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });
}

syncClock();
caricaCronologia();
</script>
</body>
</html>