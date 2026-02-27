<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['ruolo'] !== 'admin') {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/backend/db.php';

$utenti = [];
$result = $conn->query("
    SELECT u.id, u.username, u.nome, u.email, u.ruolo, u.created_at,
           d.data_assunzione, d.livello_esperienza
    FROM users u
    LEFT JOIN dipendenti d ON d.id = u.dipendente_id
    ORDER BY u.created_at DESC
");

if (!$result) {
    die('<pre>ERRORE QUERY: ' . $conn->error . '</pre>');
}

while ($row = $result->fetch_assoc()) {
    $utenti[] = $row;
}
?>
<!doctype html>
<html lang="it">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gestione Utenti - Arsnet</title>
  <style>
    .stat-card {
      border: none; border-radius: 14px;
      box-shadow: 0 2px 12px rgba(0,0,0,0.07);
      padding: 1.2rem 1.5rem;
      display: flex; align-items: center; gap: 1rem;
    }
    .stat-icon {
      width: 50px; height: 50px; border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.3rem; color: #fff; flex-shrink: 0;
    }
    .stat-label { font-size: 0.78rem; color: #6c757d; font-weight: 500; text-transform: uppercase; letter-spacing: .04em; }
    .stat-value { font-size: 1.6rem; font-weight: 700; color: var(--navy, #1a2a4a); line-height: 1; }
    .table-card {
      border: none; border-radius: 16px;
      box-shadow: 0 2px 16px rgba(0,0,0,0.08);
      overflow: hidden;
    }
    .table-card .card-header {
      background: var(--navy, #1a2a4a); color: #fff;
      padding: 1rem 1.5rem;
      display: flex; align-items: center; justify-content: space-between;
    }
    .table thead th {
      background: #f0f3f8; font-size: 0.78rem;
      font-weight: 600; text-transform: uppercase;
      letter-spacing: .05em; color: #6c757d;
      border-bottom: none; padding: 0.9rem 1rem;
    }
    .table tbody tr { transition: background .15s; }
    .table tbody tr:hover { background: #f8f9ff; }
    .table tbody td { padding: 0.85rem 1rem; vertical-align: middle; font-size: 0.88rem; }
    .avatar-sm {
      width: 36px; height: 36px; border-radius: 50%;
      display: inline-flex; align-items: center; justify-content: center;
      font-size: 0.9rem; font-weight: 600; color: #fff; flex-shrink: 0;
    }
    .badge-ruolo { font-size: 0.72rem; padding: 4px 10px; border-radius: 20px; }
    .modal-content { border: none; border-radius: 16px; }
    .modal-header { background: var(--navy, #1a2a4a); color: #fff; border-radius: 16px 16px 0 0; }
    .modal-header .btn-close { filter: invert(1); }
    .form-label { font-size: 0.83rem; font-weight: 500; color: #495057; }
    .section-divider { font-size: 0.75rem; font-weight: 600; text-transform: uppercase;
      letter-spacing: .06em; color: #adb5bd; margin: 1rem 0 0.5rem; }
    .pwd-bar { height: 5px; border-radius: 10px; transition: all .3s; }
    .btn-action { width: 30px; height: 30px; padding: 0; border-radius: 8px;
      display: inline-flex; align-items: center; justify-content: center; font-size: 0.8rem; }
    .search-box { max-width: 260px; }
    .filter-pills .btn { border-radius: 20px; font-size: 0.8rem; }
    @media(max-width:576px) {
      .stat-card { flex-direction: column; text-align: center; }
      .search-box { max-width: 100%; }
    }
  </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="d-flex">
  <?php include 'includes/sidebar.php'; ?>

  <div class="main-content">
    <header>👥 Gestione Utenti</header>

    <div id="alertBox" class="d-none mb-3"></div>

    <div class="d-flex align-items-center justify-content-between mb-4">
      <div>
        <h4 class="fw-bold mb-0">Gestione Utenti</h4>
        <small class="text-muted">Crea, modifica ed elimina gli account dei dipendenti</small>
      </div>
      <button class="btn btn-primary rounded-pill px-4" onclick="apriModaleCrea()">
        <i class="fa-solid fa-user-plus me-2"></i>Nuovo Utente
      </button>
    </div>

    <!-- Stat cards -->
    <div class="row g-3 mb-4" id="statCards">
      <?php
        $totale  = count($utenti);
        $admin   = count(array_filter($utenti, fn($u) => $u['ruolo'] === 'admin'));
        $manager = count(array_filter($utenti, fn($u) => $u['ruolo'] === 'manager'));
        $dip     = count(array_filter($utenti, fn($u) => $u['ruolo'] === 'dipendente'));
      ?>
      <div class="col-6 col-md-3">
        <div class="stat-card bg-white">
          <div class="stat-icon" style="background:linear-gradient(135deg,#0d6efd,#6610f2)"><i class="fa-solid fa-users"></i></div>
          <div><div class="stat-label">Totale</div><div class="stat-value"><?= $totale ?></div></div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-card bg-white">
          <div class="stat-icon" style="background:linear-gradient(135deg,#dc3545,#b02a37)"><i class="fa-solid fa-shield-halved"></i></div>
          <div><div class="stat-label">Admin</div><div class="stat-value"><?= $admin ?></div></div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-card bg-white">
          <div class="stat-icon" style="background:linear-gradient(135deg,#ffc107,#e0a800)"><i class="fa-solid fa-user-tie"></i></div>
          <div><div class="stat-label">Manager</div><div class="stat-value"><?= $manager ?></div></div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-card bg-white">
          <div class="stat-icon" style="background:linear-gradient(135deg,#198754,#157347)"><i class="fa-solid fa-user"></i></div>
          <div><div class="stat-label">Dipendenti</div><div class="stat-value"><?= $dip ?></div></div>
        </div>
      </div>
    </div>

    <!-- Tabella -->
    <div class="card table-card mb-5">
      <div class="card-header">
        <span class="fw-semibold"><i class="fa-solid fa-list me-2"></i>Elenco Utenti</span>
        <div class="d-flex align-items-center gap-2">
          <input type="text" id="searchInput" class="form-control form-control-sm search-box"
                 placeholder="Cerca..." oninput="filtraTabella()">
        </div>
      </div>
      <div class="px-3 pt-3 pb-1 filter-pills d-flex gap-2 flex-wrap">
        <button class="btn btn-sm btn-primary active" onclick="setFiltro('tutti', this)">Tutti</button>
        <button class="btn btn-sm btn-outline-secondary" onclick="setFiltro('admin', this)">Admin</button>
        <button class="btn btn-sm btn-outline-secondary" onclick="setFiltro('manager', this)">Manager</button>
        <button class="btn btn-sm btn-outline-secondary" onclick="setFiltro('dipendente', this)">Dipendenti</button>
      </div>
      <div class="table-responsive">
        <table class="table mb-0" id="tabellaUtenti">
          <thead>
            <tr>
              <th>Utente</th>
              <th>Email</th>
              <th>Ruolo</th>
              <th>Livello</th>
              <th>Assunto il</th>
              <th>Creato il</th>
              <th class="text-center">Azioni</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($utenti as $u): ?>
            <?php
              $colori = ['admin' => '#dc3545', 'manager' => '#ffc107', 'dipendente' => '#0d6efd'];
              $col = $colori[$u['ruolo']] ?? '#6c757d';
              $iniziale = strtoupper(substr($u['username'], 0, 1));
              $livelli = [1 => 'Junior', 2 => 'Middle', 3 => 'Senior'];
              $livello_label = $livelli[$u['livello_esperienza']] ?? '—';
            ?>
            <tr data-ruolo="<?= $u['ruolo'] ?>" data-search="<?= strtolower($u['username'].' '.$u['nome'].' '.$u['email']) ?>">
              <td>
                <div class="d-flex align-items-center gap-2">
                  <span class="avatar-sm" style="background:<?= $col ?>;"><?= $iniziale ?></span>
                  <div>
                    <div class="fw-semibold" style="color:var(--navy)"><?= htmlspecialchars($u['nome'] ?: $u['username']) ?></div>
                    <div class="text-muted" style="font-size:0.78rem">@<?= htmlspecialchars($u['username']) ?></div>
                  </div>
                </div>
              </td>
              <td class="text-muted"><?= htmlspecialchars($u['email'] ?: '—') ?></td>
              <td>
                <?php $bc = match($u['ruolo']) { 'admin' => 'bg-danger', 'manager' => 'bg-warning text-dark', default => 'bg-primary' }; ?>
                <span class="badge <?= $bc ?> badge-ruolo"><?= $u['ruolo'] ?></span>
              </td>
              <td class="text-muted"><?= $livello_label ?></td>
              <td class="text-muted"><?= $u['data_assunzione'] ? date('d/m/Y', strtotime($u['data_assunzione'])) : '—' ?></td>
              <td class="text-muted"><?= $u['created_at'] ? date('d/m/Y', strtotime($u['created_at'])) : '—' ?></td>
              <td class="text-center">
                <button class="btn btn-sm btn-outline-primary btn-action me-1" title="Modifica"
                        onclick='apriModifica(<?= json_encode($u) ?>)'>
                  <i class="fa-solid fa-pen"></i>
                </button>
                <button class="btn btn-sm btn-outline-warning btn-action me-1" title="Reset Password"
                        onclick="apriResetPwd(<?= $u['id'] ?>, '<?= htmlspecialchars($u['username']) ?>')">
                  <i class="fa-solid fa-key"></i>
                </button>
                <?php if ($u['id'] != $_SESSION['user_id']): ?>
                <button class="btn btn-sm btn-outline-danger btn-action" title="Elimina"
                        onclick="confermaElimina(<?= $u['id'] ?>, '<?= htmlspecialchars($u['username']) ?>')">
                  <i class="fa-solid fa-trash"></i>
                </button>
                <?php else: ?>
                <button class="btn btn-sm btn-outline-secondary btn-action" disabled title="Non puoi eliminare te stesso">
                  <i class="fa-solid fa-trash"></i>
                </button>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="px-3 py-2 text-muted" style="font-size:0.8rem">
        <span id="countLabel"><?= count($utenti) ?> utenti mostrati</span>
      </div>
    </div>
  </div>
</div>


<!-- ==================== MODAL CREA ==================== -->
<div class="modal fade" id="modalCrea" tabindex="-1" aria-modal="true" role="dialog">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa-solid fa-user-plus me-2"></i>Nuovo Utente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <p class="section-divider">Dati Account</p>
        <div class="mb-3">
          <label class="form-label">Username *</label>
          <input type="text" id="c_username" class="form-control" placeholder="Es. mario.rossi">
        </div>
        <div class="mb-3">
          <label class="form-label">Ruolo *</label>
          <select id="c_ruolo" class="form-select">
            <option value="dipendente">Dipendente</option>
            <option value="manager">Manager</option>
            <option value="admin">Admin</option>
          </select>
        </div>
        <p class="section-divider">Dati Personali</p>
        <div class="mb-3">
          <label class="form-label">Nome Completo</label>
          <input type="text" id="c_nome" class="form-control" placeholder="Es. Mario Rossi">
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" id="c_email" class="form-control" placeholder="mario@azienda.it">
        </div>
        <div class="mb-3">
          <label class="form-label">Data Assunzione</label>
          <input type="date" id="c_data_assunzione" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">Livello Esperienza</label>
          <select id="c_livello_esperienza" class="form-select">
            <option value="">— Non specificato —</option>
            <option value="1">Junior</option>
            <option value="2">Middle</option>
            <option value="3">Senior</option>
          </select>
        </div>
        <p class="section-divider">Password</p>
        <div class="mb-2">
          <label class="form-label">Password Temporanea *</label>
          <div class="input-group">
            <input type="password" id="c_password" class="form-control" placeholder="Minimo 8 caratteri" oninput="strengthCheck('c_password','c_bar','c_lbl')">
            <button class="btn btn-outline-secondary" type="button" onclick="togglePwd('c_password')"><i class="fa-regular fa-eye"></i></button>
            <button class="btn btn-outline-secondary" type="button" onclick="generaPwd('c_password','c_bar','c_lbl')"><i class="fa-solid fa-wand-magic-sparkles"></i></button>
          </div>
        </div>
        <div class="progress mb-1" style="height:5px;border-radius:10px;">
          <div id="c_bar" class="progress-bar pwd-bar" style="width:0%"></div>
        </div>
        <small id="c_lbl" class="text-muted"></small>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Annulla</button>
        <button class="btn btn-primary rounded-pill px-4" onclick="creaUtente()">
          <i class="fa-solid fa-floppy-disk me-2"></i>Crea Utente
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ==================== MODAL MODIFICA ==================== -->
<div class="modal fade" id="modalModifica" tabindex="-1" aria-modal="true" role="dialog">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa-solid fa-pen me-2"></i>Modifica Utente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <input type="hidden" id="m_id">
        <div class="mb-3">
          <label class="form-label">Username</label>
          <input type="text" id="m_username" class="form-control" disabled>
          <small class="text-muted">Lo username non è modificabile</small>
        </div>
        <div class="mb-3">
          <label class="form-label">Ruolo</label>
          <select id="m_ruolo" class="form-select">
            <option value="dipendente">Dipendente</option>
            <option value="manager">Manager</option>
            <option value="admin">Admin</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Nome Completo</label>
          <input type="text" id="m_nome" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" id="m_email" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">Data Assunzione</label>
          <input type="date" id="m_data_assunzione" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">Livello Esperienza</label>
          <select id="m_livello_esperienza" class="form-select">
            <option value="">— Non specificato —</option>
            <option value="1">Junior</option>
            <option value="2">Middle</option>
            <option value="3">Senior</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Annulla</button>
        <button class="btn btn-primary rounded-pill px-4" onclick="salvaModifica()">
          <i class="fa-solid fa-floppy-disk me-2"></i>Salva
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ==================== MODAL RESET PASSWORD ==================== -->
<div class="modal fade" id="modalReset" tabindex="-1" aria-modal="true" role="dialog">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa-solid fa-key me-2"></i>Reset Password</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <input type="hidden" id="r_id">
        <p class="mb-3">Reset password per: <strong id="r_username"></strong></p>
        <div class="mb-2">
          <label class="form-label">Nuova Password *</label>
          <div class="input-group">
            <input type="password" id="r_password" class="form-control" placeholder="Minimo 8 caratteri" oninput="strengthCheck('r_password','r_bar','r_lbl')">
            <button class="btn btn-outline-secondary" type="button" onclick="togglePwd('r_password')"><i class="fa-regular fa-eye"></i></button>
            <button class="btn btn-outline-secondary" type="button" onclick="generaPwd('r_password','r_bar','r_lbl')"><i class="fa-solid fa-wand-magic-sparkles"></i></button>
          </div>
        </div>
        <div class="progress mb-1" style="height:5px;border-radius:10px;">
          <div id="r_bar" class="progress-bar pwd-bar" style="width:0%"></div>
        </div>
        <small id="r_lbl" class="text-muted"></small>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Annulla</button>
        <button class="btn btn-warning rounded-pill px-4" onclick="resetPassword()">
          <i class="fa-solid fa-key me-2"></i>Aggiorna Password
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ==================== MODAL ELIMINA ==================== -->
<div class="modal fade" id="modalElimina" tabindex="-1" aria-modal="true" role="dialog">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white" style="border-radius:16px 16px 0 0">
        <h5 class="modal-title"><i class="fa-solid fa-triangle-exclamation me-2"></i>Conferma</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:invert(1)"></button>
      </div>
      <div class="modal-body text-center p-4">
        <input type="hidden" id="e_id">
        <p>Stai per eliminare l'utente <strong id="e_username"></strong>.<br>L'operazione è irreversibile.</p>
      </div>
      <div class="modal-footer justify-content-center">
        <button class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Annulla</button>
        <button class="btn btn-danger rounded-pill px-4" onclick="eliminaUtente()">
          <i class="fa-solid fa-trash me-2"></i>Elimina
        </button>
      </div>
    </div>
  </div>
</div>


<script>
// ================================================================
// ISTANZE MODALI — create una volta sola al caricamento della pagina
// ================================================================
let modalCrea, modalModifica, modalReset, modalElimina;

document.addEventListener('DOMContentLoaded', () => {
    modalCrea     = new bootstrap.Modal(document.getElementById('modalCrea'),     { backdrop: true, keyboard: true });
    modalModifica = new bootstrap.Modal(document.getElementById('modalModifica'), { backdrop: true, keyboard: true });
    modalReset    = new bootstrap.Modal(document.getElementById('modalReset'),    { backdrop: true, keyboard: true });
    modalElimina  = new bootstrap.Modal(document.getElementById('modalElimina'),  { backdrop: true, keyboard: true });

    // Quando una modale si chiude, rimuovi il focus dal pulsante per evitare il warning aria-hidden
    document.getElementById('modalCrea').addEventListener('hidden.bs.modal',     () => document.activeElement?.blur());
    document.getElementById('modalModifica').addEventListener('hidden.bs.modal', () => document.activeElement?.blur());
    document.getElementById('modalReset').addEventListener('hidden.bs.modal',    () => document.activeElement?.blur());
    document.getElementById('modalElimina').addEventListener('hidden.bs.modal',  () => document.activeElement?.blur());
});

// ================================================================
// UTILITY
// ================================================================
function showAlert(msg, type = 'success') {
    const box = document.getElementById('alertBox');
    const icon = type === 'success' ? 'check-circle' : type === 'warning' ? 'triangle-exclamation' : 'circle-xmark';
    box.className = `alert alert-${type}`;
    box.innerHTML = `<i class="fa-solid fa-${icon} me-2"></i>${msg}`;
    box.classList.remove('d-none');
    setTimeout(() => box.classList.add('d-none'), 5000);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function togglePwd(id) {
    const inp = document.getElementById(id);
    inp.type = inp.type === 'password' ? 'text' : 'password';
}

function strengthCheck(inputId, barId, lblId) {
    const val = document.getElementById(inputId).value;
    const bar = document.getElementById(barId);
    const lbl = document.getElementById(lblId);
    let s = 0;
    if (val.length >= 8) s++;
    if (/[A-Z]/.test(val)) s++;
    if (/[0-9]/.test(val)) s++;
    if (/[^A-Za-z0-9]/.test(val)) s++;
    const lvl = [
        { w:'0%',   cls:'',           txt:'' },
        { w:'25%',  cls:'bg-danger',  txt:'Debole' },
        { w:'50%',  cls:'bg-warning', txt:'Discreta' },
        { w:'75%',  cls:'bg-info',    txt:'Buona' },
        { w:'100%', cls:'bg-success', txt:'Ottima' },
    ];
    bar.style.width = lvl[s].w;
    bar.className = `progress-bar pwd-bar ${lvl[s].cls}`;
    lbl.textContent = lvl[s].txt;
}

// Funzione generica per generare password (usata sia in Crea che in Reset)
function generaPwd(inputId, barId, lblId) {
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789!@#$%';
    let pwd = '';
    for (let i = 0; i < 12; i++) pwd += chars[Math.floor(Math.random() * chars.length)];
    const inp = document.getElementById(inputId);
    inp.type = 'text';
    inp.value = pwd;
    strengthCheck(inputId, barId, lblId);
}

// ================================================================
// FILTRI TABELLA
// ================================================================
let filtroAttivo = 'tutti';

function setFiltro(ruolo, btn) {
    filtroAttivo = ruolo;
    document.querySelectorAll('.filter-pills .btn').forEach(b => {
        b.classList.remove('btn-primary', 'active');
        b.classList.add('btn-outline-secondary');
    });
    btn.classList.remove('btn-outline-secondary');
    btn.classList.add('btn-primary', 'active');
    filtraTabella();
}

function filtraTabella() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    const righe = document.querySelectorAll('#tabellaUtenti tbody tr');
    let visibili = 0;
    righe.forEach(tr => {
        const matchRuolo  = filtroAttivo === 'tutti' || tr.dataset.ruolo === filtroAttivo;
        const matchSearch = tr.dataset.search.includes(q);
        tr.style.display = (matchRuolo && matchSearch) ? '' : 'none';
        if (matchRuolo && matchSearch) visibili++;
    });
    document.getElementById('countLabel').textContent = `${visibili} utenti mostrati`;
}

// ================================================================
// MODAL CREA
// ================================================================
function apriModaleCrea() {
    document.getElementById('c_username').value          = '';
    document.getElementById('c_nome').value              = '';
    document.getElementById('c_email').value             = '';
    document.getElementById('c_password').value          = '';
    document.getElementById('c_ruolo').value             = 'dipendente';
    document.getElementById('c_data_assunzione').value   = '';
    document.getElementById('c_livello_esperienza').value = '';
    document.getElementById('c_bar').style.width         = '0%';
    document.getElementById('c_lbl').textContent         = '';
    modalCrea.show();
}

async function creaUtente() {
    const payload = {
        action:             'crea',
        username:           document.getElementById('c_username').value.trim(),
        nome:               document.getElementById('c_nome').value.trim(),
        email:              document.getElementById('c_email').value.trim(),
        ruolo:              document.getElementById('c_ruolo').value,
        password:           document.getElementById('c_password').value,
        data_assunzione:    document.getElementById('c_data_assunzione').value,
        livello_esperienza: document.getElementById('c_livello_esperienza').value
    };
    if (!payload.username || !payload.password) {
        showAlert('Username e password sono obbligatori.', 'warning'); return;
    }
    if (payload.password.length < 8) {
        showAlert('La password deve essere di almeno 8 caratteri.', 'warning'); return;
    }
    try {
        const res  = await fetch('api/get_utenti.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload) });
        const data = await res.json();
        if (data.success) {
            modalCrea.hide();
            showAlert('Utente creato con successo! Ricarico la pagina...');
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert(data.error || 'Errore durante la creazione.', 'danger');
        }
    } catch(e) { showAlert('Errore di connessione.', 'danger'); }
}

// ================================================================
// MODAL MODIFICA
// ================================================================
function apriModifica(u) {
    document.getElementById('m_id').value                = u.id;
    document.getElementById('m_username').value          = u.username;
    document.getElementById('m_nome').value              = u.nome              || '';
    document.getElementById('m_email').value             = u.email             || '';
    document.getElementById('m_ruolo').value             = u.ruolo;
    document.getElementById('m_data_assunzione').value   = u.data_assunzione   || '';
    document.getElementById('m_livello_esperienza').value = u.livello_esperienza || '';
    modalModifica.show();
}

async function salvaModifica() {
    const payload = {
        action:             'modifica',
        id:                 document.getElementById('m_id').value,
        nome:               document.getElementById('m_nome').value.trim(),
        email:              document.getElementById('m_email').value.trim(),
        ruolo:              document.getElementById('m_ruolo').value,
        data_assunzione:    document.getElementById('m_data_assunzione').value,
        livello_esperienza: document.getElementById('m_livello_esperienza').value
    };
    try {
        const res  = await fetch('api/get_utenti.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload) });
        const data = await res.json();
        if (data.success) {
            modalModifica.hide();
            showAlert('Utente aggiornato! Ricarico...');
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert(data.error || 'Errore.', 'danger');
        }
    } catch(e) { showAlert('Errore di connessione.', 'danger'); }
}

// ================================================================
// MODAL RESET PASSWORD
// ================================================================
function apriResetPwd(id, username) {
    document.getElementById('r_id').value       = id;
    document.getElementById('r_username').textContent = username;
    document.getElementById('r_password').value = '';
    document.getElementById('r_bar').style.width = '0%';
    document.getElementById('r_lbl').textContent = '';
    modalReset.show();
}

async function resetPassword() {
    const pwd = document.getElementById('r_password').value;
    if (!pwd || pwd.length < 8) {
        showAlert('Inserisci una password di almeno 8 caratteri.', 'warning'); return;
    }
    try {
        const res  = await fetch('api/get_utenti.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ action:'reset_pwd', id: document.getElementById('r_id').value, password: pwd }) });
        const data = await res.json();
        if (data.success) {
            modalReset.hide();
            showAlert('Password aggiornata con successo!');
        } else {
            showAlert(data.error || 'Errore.', 'danger');
        }
    } catch(e) { showAlert('Errore di connessione.', 'danger'); }
}

// ================================================================
// MODAL ELIMINA
// ================================================================
function confermaElimina(id, username) {
    document.getElementById('e_id').value = id;
    document.getElementById('e_username').textContent = username;
    modalElimina.show();
}

async function eliminaUtente() {
    try {
        const res  = await fetch('api/get_utenti.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ action:'elimina', id: document.getElementById('e_id').value }) });
        const data = await res.json();
        if (data.success) {
            modalElimina.hide();
            showAlert('Utente eliminato. Ricarico...');
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert(data.error || 'Errore.', 'danger');
        }
    } catch(e) { showAlert('Errore di connessione.', 'danger'); }
}
</script>

<?php include 'includes/footer.php'; ?>