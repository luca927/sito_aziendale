<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Protezione: solo utenti loggati
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/backend/db.php';

// Carica dati utente dal DB
$stmt = $conn->prepare("SELECT id, username, nome, email, ruolo, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    header('Location: logout.php');
    exit;
}
?>
<!doctype html>
<html lang="it">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Profilo - Arsnet</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="assets/css/style.css?v=2" rel="stylesheet">
  <style>
    body { padding-top: 80px; background: #f4f6fb; font-family: 'Poppins', sans-serif; }
    .navbar { background: #1a2a4a !important; }
    .navbar-brand span { color: #fff; }
    .profile-card { border: none; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
    .profile-avatar {
        width: 90px; height: 90px; border-radius: 50%;
        background: linear-gradient(135deg, #0d6efd, #6610f2);
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem; color: white; font-weight: 600; margin: 0 auto 1rem;
    }
    .badge-ruolo { font-size: 0.75rem; padding: 5px 12px; border-radius: 20px; }
    .section-title { font-size: 0.85rem; font-weight: 600; text-transform: uppercase;
        letter-spacing: 0.05em; color: #6c757d; margin-bottom: 1rem; }
    .form-label { font-size: 0.85rem; font-weight: 500; color: #495057; }
    .alert { border-radius: 10px; font-size: 0.9rem; }
  </style>
</head>
<body>

<!-- NAVBAR (uguale alla dashboard) -->
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

<div class="container py-4" style="max-width: 800px;">

  <!-- Alert messaggi -->
  <div id="alertBox" class="d-none mb-3"></div>

  <div class="row g-4">

    <!-- Card Avatar + Info base -->
    <div class="col-md-4">
      <div class="card profile-card p-4 text-center h-100">
        <div class="profile-avatar">
          <?= strtoupper(substr($user['username'], 0, 1)) ?>
        </div>
        <h5 class="fw-semibold mb-1"><?= htmlspecialchars($user['nome'] ?: $user['username']) ?></h5>
        <p class="text-muted small mb-2"><?= htmlspecialchars($user['email'] ?: 'Nessuna email') ?></p>
        <?php
          $badgeColor = match($user['ruolo']) {
            'admin'    => 'bg-danger',
            'manager'  => 'bg-warning text-dark',
            default    => 'bg-primary'
          };
        ?>
        <span class="badge <?= $badgeColor ?> badge-ruolo">
          <i class="fa-solid fa-shield-halved me-1"></i><?= htmlspecialchars($user['ruolo']) ?>
        </span>
        <hr>
        <p class="text-muted small mb-0">
          <i class="fa-regular fa-calendar me-1"></i>
          Membro dal <?= date('d/m/Y', strtotime($user['created_at'])) ?>
        </p>
      </div>
    </div>

    <!-- Card Form modifica -->
    <div class="col-md-8">

      <!-- Sezione dati personali -->
      <div class="card profile-card p-4 mb-4">
        <p class="section-title"><i class="fa-solid fa-user me-2"></i>Dati Personali</p>
        <form id="formProfilo">
          <div class="row g-3">
            <div class="col-sm-6">
              <label class="form-label">Username</label>
              <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" disabled>
              <small class="text-muted">Lo username non può essere modificato</small>
            </div>
            <div class="col-sm-6">
              <label class="form-label">Nome Completo</label>
              <input type="text" id="nome" class="form-control" 
                     value="<?= htmlspecialchars($user['nome'] ?? '') ?>" 
                     placeholder="Es. Mario Rossi">
            </div>
            <div class="col-sm-12">
              <label class="form-label">Email</label>
              <input type="email" id="email" class="form-control" 
                     value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                     placeholder="es. mario@azienda.it">
            </div>
          </div>
          <div class="mt-3 text-end">
            <button type="button" class="btn btn-primary rounded-pill px-4" onclick="salvaProfilo()">
              <i class="fa-solid fa-floppy-disk me-2"></i>Salva Modifiche
            </button>
          </div>
        </form>
      </div>

      <!-- Sezione cambio password -->
      <div class="card profile-card p-4">
        <p class="section-title"><i class="fa-solid fa-lock me-2"></i>Cambia Password</p>
        <form id="formPassword">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">Password Attuale</label>
              <div class="input-group">
                <input type="password" id="pwdAttuale" class="form-control" placeholder="Password attuale">
                <button class="btn btn-outline-secondary" type="button" onclick="togglePwd('pwdAttuale')">
                  <i class="fa-regular fa-eye"></i>
                </button>
              </div>
            </div>
            <div class="col-sm-6">
              <label class="form-label">Nuova Password</label>
              <div class="input-group">
                <input type="password" id="pwdNuova" class="form-control" placeholder="Minimo 8 caratteri">
                <button class="btn btn-outline-secondary" type="button" onclick="togglePwd('pwdNuova')">
                  <i class="fa-regular fa-eye"></i>
                </button>
              </div>
            </div>
            <div class="col-sm-6">
              <label class="form-label">Conferma Password</label>
              <div class="input-group">
                <input type="password" id="pwdConferma" class="form-control" placeholder="Ripeti la nuova password">
                <button class="btn btn-outline-secondary" type="button" onclick="togglePwd('pwdConferma')">
                  <i class="fa-regular fa-eye"></i>
                </button>
              </div>
            </div>
            <!-- Barra forza password -->
            <div class="col-12">
              <div class="progress" style="height:6px; border-radius:10px;">
                <div id="pwdStrength" class="progress-bar" role="progressbar" style="width:0%"></div>
              </div>
              <small id="pwdStrengthLabel" class="text-muted"></small>
            </div>
          </div>
          <div class="mt-3 text-end">
            <button type="button" class="btn btn-danger rounded-pill px-4" onclick="cambiaPassword()">
              <i class="fa-solid fa-key me-2"></i>Aggiorna Password
            </button>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>

function showAlert(msg, type = 'success') {
    const box = document.getElementById('alertBox');
    box.className = `alert alert-${type}`;
    box.innerHTML = `<i class="fa-solid fa-${type === 'success' ? 'check' : 'triangle-exclamation'} me-2"></i>${msg}`;
    box.classList.remove('d-none');
    setTimeout(() => box.classList.add('d-none'), 4000);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function togglePwd(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}

// Indicatore forza password
document.getElementById('pwdNuova').addEventListener('input', function() {
    const val = this.value;
    const bar = document.getElementById('pwdStrength');
    const label = document.getElementById('pwdStrengthLabel');
    let strength = 0;
    if (val.length >= 8) strength++;
    if (/[A-Z]/.test(val)) strength++;
    if (/[0-9]/.test(val)) strength++;
    if (/[^A-Za-z0-9]/.test(val)) strength++;

    const levels = [
        { w: '0%',   cls: '',          txt: '' },
        { w: '25%',  cls: 'bg-danger', txt: 'Debole' },
        { w: '50%',  cls: 'bg-warning', txt: 'Discreta' },
        { w: '75%',  cls: 'bg-info',   txt: 'Buona' },
        { w: '100%', cls: 'bg-success', txt: 'Ottima' },
    ];
    bar.style.width = levels[strength].w;
    bar.className = `progress-bar ${levels[strength].cls}`;
    label.textContent = levels[strength].txt;
});

async function salvaProfilo() {
    const payload = {
        nome:  document.getElementById('nome').value.trim(),
        email: document.getElementById('email').value.trim()
    };

    try {
        const res = await fetch('api/aggiorna_profilo.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'profilo', ...payload })
        });
        const data = await res.json();
        if (data.success) {
            showAlert('Profilo aggiornato con successo!');
        } else {
            showAlert(data.error || 'Errore durante il salvataggio.', 'danger');
        }
    } catch (e) {
        showAlert('Errore di connessione.', 'danger');
    }
}

async function cambiaPassword() {
    const attuale  = document.getElementById('pwdAttuale').value;
    const nuova    = document.getElementById('pwdNuova').value;
    const conferma = document.getElementById('pwdConferma').value;

    if (!attuale || !nuova || !conferma) {
        showAlert('Compila tutti i campi password.', 'warning');
        return;
    }
    if (nuova.length < 8) {
        showAlert('La nuova password deve essere di almeno 8 caratteri.', 'warning');
        return;
    }
    if (nuova !== conferma) {
        showAlert('Le password non coincidono.', 'danger');
        return;
    }

    try {
        const res = await fetch('api/aggiorna_profilo.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'password', pwd_attuale: attuale, pwd_nuova: nuova })
        });
        const data = await res.json();
        if (data.success) {
            showAlert('Password aggiornata con successo!');
            document.getElementById('formPassword').reset();
            document.getElementById('pwdStrength').style.width = '0%';
            document.getElementById('pwdStrengthLabel').textContent = '';
        } else {
            showAlert(data.error || 'Errore durante l\'aggiornamento.', 'danger');
        }
    } catch (e) {
        showAlert('Errore di connessione.', 'danger');
    }
}
</script>
</body>
</html>