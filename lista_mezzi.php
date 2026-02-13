<?php
require_once __DIR__ . '/backend/auth.php';
require_once __DIR__ . '/backend/db.php';
include 'includes/header.php'; 
?>

<div class="d-flex">
  <?php include 'includes/sidebar.php'; ?>
  
  <div class="main-content flex-grow-1" style="background-color: #f8f9fa; min-height: 100vh;">
    <div class="container-fluid px-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold m-0"><i class="fa-solid fa-truck-pickup me-2"></i>Gestione Parco Mezzi</h2>
        <button class="btn btn-primary shadow-sm" id="openModalBtn">
          <i class="fa-solid fa-plus me-2"></i>Aggiungi Mezzo
        </button>
      </div>

    <!-- Sezione Filtri -->
    <div class="filtri-section">
      <h4>FILTRI</h4>
      <div class="filtri-grid">
        <div class="filtro-group">
          <label>Nome Mezzo</label>
          <input type="text" id="filtroNome" placeholder="Cerca per nome...">
        </div>
        <div class="filtro-group">
          <label>Targa</label>
          <input type="text" id="filtroTarga" placeholder="Cerca per targa...">
        </div>
        <div class="filtro-group">
          <label>Anno</label>
          <input type="text" id="filtroAnno" placeholder="Cerca per anno...">
        </div>
      </div>
    </div>

      <div class="card shadow-sm border-0">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th class="ps-4">MEZZO / MODELLO</th>
                  <th>TARGA</th>
                  <th>ANNO</th>
                  <th>MANUTENZIONE</th>
                  <th>STATO</th>
                  <th class="text-end pe-4">AZIONI</th>
                </tr>
              </thead>
              <tbody id="mezziTable">
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- MODALE MEZZO -->
<div id="mezzoModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3 id="modalTitle">Nuovo Mezzo</h3>
      <button class="close-btn" id="closeModalBtn" onclick="closeMezzoModal(true)">&times;</button>
    </div>

    <input type="hidden" id="mezzo_id" name="id">

    <form id="mezzoForm" novalidate>

      <!-- NAV TABS -->
      <ul class="nav nav-tabs" id="mezzoTabs">
        <li class="nav-item">
          <button class="nav-link active" data-tab="info" type="button">Dati Mezzo</button>
        </li>
        <li class="nav-item">
          <button class="nav-link" data-tab="manutenzione" type="button">Manutenzione</button>
        </li>
        <li class="nav-item">
          <button class="nav-link" data-tab="stato" type="button">Stato</button>
        </li>
        <li class="nav-item">
          <button class="nav-link" data-tab="note" type="button">Note</button>
        </li>
      </ul>

      <!-- TAB CONTENT -->
      <div class="tab-content mt-3">

        <!-- TAB DATI MEZZO -->
        <div class="tab-pane fade show active" id="tab-info">
          <div class="row g-3">

            <div class="col-md-6">
              <label class="form-label fw-bold">Nome Mezzo *</label>
              <input type="text" id="nome_mezzo" name="nome_mezzo" class="form-control" required placeholder="Es. Fiat Ducato">
            </div>

            <div class="col-md-6">
              <label class="form-label fw-bold">Targa *</label>
              <input type="text" id="targa" name="targa" class="form-control" required placeholder="AA000BB">
            </div>

            <div class="col-md-6">
              <label class="form-label">Tipo / Modello</label>
              <input type="text" id="tipo" name="tipo" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="form-label">Anno</label>
              <input type="number" id="anno" name="anno" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="form-label">Portata (kg)</label>
              <input type="number" id="portata_kg" name="portata_kg" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="form-label">Centro di Costo</label>
              <input type="text" id="centro_costo" name="centro_costo" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="form-label">Pneumatici Attuali</label>
              <input type="text" id="pneumatici" name="pneumatici_attuali" class="form-control" placeholder="Es. Michelin 175/75R16">
            </div>

            <div class="col-md-6">
              <label class="form-label">Dotazioni Specifiche</label>
              <input type="text" id="dotazioni" name="dotazioni" class="form-control" placeholder="Es. Gancio, Impianto idraulico">
            </div>

          </div>
        </div>

        <!-- TAB MANUTENZIONE -->
        <div class="tab-pane" id="tab-manutenzione">
          <div class="row g-3 mt-2">

            <div class="col-md-6">
              <label class="form-label text-warning fw-bold">Ultima Manutenzione</label>
              <input type="date" id="ultima_manutenzione" name="ultima_manutenzione" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="form-label text-danger fw-bold">Prossima Scadenza</label>
              <input type="date" id="prossima_manutenzione" name="prossima_manutenzione" class="form-control">
            </div>

            <div class="col-12">
              <hr>
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h6>Storico Manutenzioni</h6>
                <button type="button" class="btn btn-sm btn-primary" id="aggiungiManutenzione">
                  <i class="fa-solid fa-plus me-1"></i> Nuova Manutenzione
                </button>
              </div>

              <div class="table-responsive">
                <table class="table table-sm table-hover">
                  <thead class="table-light">
                    <tr>
                      <th>Data</th>
                      <th>Tipo</th>
                      <th>Costo</th>
                      <th>Fornitore</th>
                      <th>Prossima</th>
                      <th>Azioni</th>
                    </tr>
                  </thead>
                  <tbody id="manutenzioniBody">
                    <tr><td colspan="6" class="text-center text-muted">Nessuna manutenzione registrata</td></tr>
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </div>

        <!-- TAB STATO -->
        <div class="tab-pane" id="tab-stato">
          <div class="row g-3 mt-2">

            <div class="col-md-12">
              <label class="form-label">Stato</label>
              <select id="stato" name="stato" class="form-select">
                <option value="attivo">Attivo</option>
                <option value="in manutenzione">In Manutenzione</option>
                <option value="fuori uso">Fuori Uso</option>
              </select>
            </div>

          </div>
        </div>

        <!-- TAB NOTE -->
        <div class="tab-pane" id="tab-note">
          <div class="row g-3 mt-2">

            <div class="col-12">
              <label class="form-label">Note</label>
              <textarea id="note" name="note" class="form-control" rows="3"></textarea>
            </div>

          </div>
        </div>

      </div>

      <!-- FOOTER -->
      <div class="modal-footer mt-4">
        <button type="button" class="btn btn-secondary" onclick="closeMezzoModal(true)">Annulla</button>
        <button type="submit" class="btn btn-primary">Salva Mezzo</button>
      </div>

    </form>
  </div>
</div>

<!-- MODALE AGGIUNGI MANUTENZIONE -->
<div id="modaleManutenzione" class="modal-nested" style="display: none;">
  <div class="modal-nested-content">
    <h6 class="mb-3">Registra Manutenzione</h6>
    
    <form id="formManutenzione">
      <div class="row g-2">
        
        <div class="col-md-6">
          <label class="form-label">Data Manutenzione *</label>
          <input type="date" id="dataManutenzione" class="form-control form-control-sm" required>
        </div>

        <div class="col-md-6">
          <label class="form-label">Tipo *</label>
          <select id="tipoManutenzione" class="form-select form-select-sm" required>
            <option value="">-- Seleziona --</option>
            <option value="Ordinaria">Ordinaria</option>
            <option value="Straordinaria">Straordinaria</option>
            <option value="Cambio Pneumatici">Cambio Pneumatici</option>
            <option value="Ispezione">Ispezione</option>
            <option value="Riparazione">Riparazione</option>
          </select>
        </div>

        <div class="col-12">
          <label class="form-label">Descrizione</label>
          <textarea id="descrizioneManutenzione" class="form-control form-control-sm" 
                    rows="2" placeholder="Dettagli della manutenzione..."></textarea>
        </div>

        <div class="col-md-6">
          <label class="form-label">Costo (€)</label>
          <input type="number" id="costoManutenzione" class="form-control form-control-sm" 
                 step="0.01">
        </div>

        <div class="col-md-6">
          <label class="form-label">Ore di Lavoro</label>
          <input type="number" id="oreLavoro" class="form-control form-control-sm" 
                 step="0.5">
        </div>

        <div class="col-md-6">
          <label class="form-label">Fornitore</label>
          <input type="text" id="fornitore" class="form-control form-control-sm">
        </div>

        <div class="col-md-6">
          <label class="form-label">Prossima Scadenza</label>
          <input type="date" id="prossimaManutenzioneNew" class="form-control form-control-sm">
        </div>

        <div class="col-12">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="completataCheck" checked>
            <label class="form-check-label" for="completataCheck">
              Manutenzione completata
            </label>
          </div>
        </div>

      </div>

      <div class="modal-nested-footer mt-3">
        <button type="button" class="btn btn-sm btn-secondary" onclick="chiudiModaleManutenzione()">
          Annulla
        </button>
        <button type="submit" class="btn btn-sm btn-primary">
          Salva Manutenzione
        </button>
      </div>
    </form>
  </div>
</div>

<!-- CSS MODALI NIDIFICATE -->
<style>
.modal-nested {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: white;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.3);
  z-index: 1050;
  max-width: 600px;
  width: 90%;
  max-height: 80vh;
  overflow-y: auto;
}

.modal-nested-content {
  width: 100%;
}

.modal-nested-footer {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  border-top: 1px solid #e9ecef;
  padding-top: 15px;
}

.modal-nested::before {
  content: '';
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.5);
  z-index: -1;
}
</style>


<script>

// 1. Definiamo le variabili globali all'inizio
const USER_ROLE = "<?= $_SESSION['ruolo'] ?? 'guest' ?>";
const modal = document.getElementById('mezzoModal');
const form = document.getElementById('mezzoForm');
let mezziGlobali = []; // Qui salviamo i dati per la modifica

// Gestione Filtri
document.getElementById("filtroNome").addEventListener("input", applicaFiltri);
document.getElementById("filtroTarga").addEventListener("input", applicaFiltri);
document.getElementById("filtroAnno").addEventListener("input", applicaFiltri);

// Funzione Filtra
function applicaFiltri() {
  const nome = document.getElementById("filtroNome").value.toLowerCase();
  const targa = document.getElementById("filtroTarga").value.toLowerCase();
  const anno = document.getElementById("filtroAnno").value.toLowerCase();

  const filtrati = mezziGlobali.filter(m => {
    const nomeCampo = (m.nome_mezzo || "").toLowerCase();
    const targaCampo = (m.targa || "").toLowerCase();
    const annoCampo = (m.anno || "").toString().toLowerCase();

    return nomeCampo.includes(nome)
      && targaCampo.includes(targa)
      && annoCampo.includes(anno);
  });

  mostraMezzi(filtrati);
}

function mostraMezzi(lista) {
  const tbody = document.getElementById("mezziTable");
  tbody.innerHTML = "";

  lista.forEach(m => {
    let statoClass = m.stato === 'attivo' ? 'badge-attivo' :
                     (m.stato === 'in manutenzione' ? 'badge-manutenzione' : 'badge-fuori-uso');

    let prossima = m.prossima_manutenzione
      ? new Date(m.prossima_manutenzione).toLocaleDateString('it-IT')
      : '-';

    let btnElimina = USER_ROLE === 'admin'
      ? `<button class="btn btn-sm btn-danger" onclick="elimina(${m.id})"><i class="fa-solid fa-trash"></i></button>`
      : '';

    tbody.innerHTML += `
      <tr>
        <td class="ps-4">
          <div class="fw-bold">${m.nome_mezzo}</div>
          <small class="text-muted">${m.tipo || '-'}</small>
        </td>
        <td><span class="badge bg-secondary">${m.targa}</span></td>
        <td>${m.anno || '-'}</td>
        <td><small>Prossima: <b class="text-danger">${prossima}</b></small></td>
        <td><span class="badge ${statoClass}">${m.stato}</span></td>
        <td class="text-end pe-4">
          <button class="btn btn-sm btn-warning" onclick="apriModifica(${m.id})">
            <i class="fa-solid fa-pen"></i>
          </button>
          ${btnElimina}
        </td>
      </tr>`;
  });
}

// 2. Funzione per caricare i mezzi nella tabella
async function caricaMezzi() {
  try {
    const res = await fetch("api/get_mezzi.php");
    mezziGlobali = await res.json();
    mostraMezzi(mezziGlobali);
  } catch (error) {
    console.error("Errore nel caricamento mezzi:", error);
  }
}

// 3. FUNZIONE PER APRIRE LA MODALE IN MODALITÀ MODIFICA
function apriModifica(id) {
  // Cerchiamo i dati del mezzo dentro l'array mezziGlobali usando l'ID
  const m = mezziGlobali.find(mezzo => mezzo.id == id);

  if (m) {
    // Riordiniamo i campi del form con i dati del database
    document.getElementById('mezzo_id').value = m.id;
    document.getElementById('nome_mezzo').value = m.nome_mezzo;
    document.getElementById('targa').value = m.targa;
    document.getElementById('tipo').value = m.tipo || '';
    document.getElementById('anno').value = m.anno || '';
    
    // Nota: gli input date HTML richiedono il formato YYYY-MM-DD
    document.getElementById('ultima_manutenzione').value = m.ultima_manutenzione || '';
    document.getElementById('prossima_manutenzione').value = m.prossima_manutenzione || '';
    
    // Nuovi campi
    document.getElementById('portata_kg').value = m.portata_kg || '';
    document.getElementById('centro_costo').value = m.centro_costo || '';
    document.getElementById('pneumatici').value = m.pneumatici_attuali || '';
    document.getElementById('dotazioni').value = m.dotazioni || '';
    
    document.getElementById('stato').value = m.stato;
    document.getElementById('note').value = m.note || '';

    // Carica manutenzioni
    caricaManutenzioni(m.id);

    // Cambiamo il titolo della modale e la mostriamo
    document.getElementById('modalTitle').innerText = 'Modifica Mezzo: ' + m.nome_mezzo;
    modal.classList.add('show');
  }
}

// 4. Gestione apertura modale per NUOVO MEZZO (Reset form)
document.getElementById('openModalBtn').onclick = () => {
  form.reset();
  document.getElementById('mezzo_id').value = ''; // ID vuoto = Nuovo inserimento
  document.getElementById('modalTitle').innerText = 'Nuovo Mezzo';
  modal.classList.add('show');
};


// 6. Invio Form (Salvataggio o Aggiornamento)
form.onsubmit = async (e) => {
  e.preventDefault();
  const id = document.getElementById('mezzo_id').value;
  
  const payload = {
    id: id,
    nome_mezzo: document.getElementById('nome_mezzo').value,
    targa: document.getElementById('targa').value,
    tipo: document.getElementById('tipo').value,
    anno: document.getElementById('anno').value,
    portata_kg: document.getElementById('portata_kg').value || null,
    centro_costo: document.getElementById('centro_costo').value || null,
    pneumatici_attuali: document.getElementById('pneumatici').value || null,
    dotazioni: document.getElementById('dotazioni').value || null,
    ultima_manutenzione: document.getElementById('ultima_manutenzione').value,
    prossima_manutenzione: document.getElementById('prossima_manutenzione').value,
    note: document.getElementById('note').value,
    stato: document.getElementById('stato').value
  };

  // Se l'ID esiste usiamo update, altrimenti add
  const url = id ? 'api/update_mezzo.php' : 'api/add_mezzo.php';
  
  try {
    const res = await fetch(url, {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify(payload)
    });
    const result = await res.json();
    
    if(result.success) {
      modal.classList.remove('show');
      caricaMezzi(); // Ricarica la tabella aggiornata
    } else {
      alert("Errore durante il salvataggio: " + result.error);
    }
  } catch (err) {
    alert("Errore di connessione al server");
  }
};

// --- MANUTENZIONI ---
async function caricaManutenzioni(id_mezzo) {
  try {
    const res = await fetch(`api/get_manutenzioni.php?id_mezzo=${id_mezzo}`);
    const data = await res.json();
    
    const tbody = document.getElementById('manutenzioniBody');
    tbody.innerHTML = '';
    
    if (!data.success || data.data.length === 0) {
      tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Nessuna manutenzione registrata</td></tr>';
      return;
    }
    
    data.data.forEach(m => {
      const dataFmt = new Date(m.data_manutenzione).toLocaleDateString('it-IT');
      const prossima = m.prossima_scadenza ? new Date(m.prossima_scadenza).toLocaleDateString('it-IT') : '-';
      
      tbody.innerHTML += `
        <tr>
          <td>${dataFmt}</td>
          <td>${m.tipo_manutenzione || '-'}</td>
          <td>€ ${parseFloat(m.costo || 0).toFixed(2)}</td>
          <td>${m.fornitore || '-'}</td>
          <td>${prossima}</td>
          <td>
            <button class="btn btn-xs btn-danger" onclick="eliminaManutenzione(${m.id})">
              <i class="fa-solid fa-trash"></i>
            </button>
          </td>
        </tr>
      `;
    });
  } catch (error) {
    console.error("Errore caricamento manutenzioni:", error);
  }
}

document.getElementById('aggiungiManutenzione').addEventListener('click', function() {
  closeMezzoModal(false); // Chiudi la modale principale per evitare sovrapposizioni
  document.getElementById('modaleManutenzione').style.display = 'block';
  document.getElementById('dataManutenzione').valueAsDate = new Date();
});

// Quando chiudi dalla X o Annulla, resetta il form
document.getElementById('closeModalBtn').onclick = () => {
  closeMezzoModal(true); // true = resetta il form
};

function chiudiModaleManutenzione() {
  document.getElementById('modaleManutenzione').style.display = 'none';
  document.getElementById('formManutenzione').reset();

  //riapri la modale principale con i dati del mezzo
  modal.classList.add('show');
}

document.getElementById('formManutenzione').addEventListener('submit', async function(e) {
  e.preventDefault();
  
  const idMezzo = document.getElementById('mezzo_id').value;
  const data = document.getElementById('dataManutenzione').value;
  const tipo = document.getElementById('tipoManutenzione').value;
  const descrizione = document.getElementById('descrizioneManutenzione').value;
  const costo = document.getElementById('costoManutenzione').value;
  const ore = document.getElementById('oreLavoro').value;
  const fornitore = document.getElementById('fornitore').value;
  const prossima = document.getElementById('prossimaManutenzioneNew').value;
  const completata = document.getElementById('completataCheck').checked ? 1 : 0;
  
  if (!idMezzo || !data || !tipo) {
    alert('Compila i campi obbligatori');
    return;
  }
  
  try {
    const res = await fetch('api/add_manutenzioni.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({
        id_mezzo: idMezzo,
        data_manutenzione: data,
        tipo_manutenzione: tipo,
        descrizione: descrizione,
        costo: costo || null,
        ore_lavoro: ore || null,
        fornitore: fornitore,
        prossima_scadenza: prossima,
        completata: completata
      })
    });
    
    const result = await res.json();
    
    if (result.success) {
      chiudiModaleManutenzione();
      caricaManutenzioni(idMezzo); // Ricarica
      alert('Manutenzione registrata!');
    } else {
      alert('Errore: ' + result.error);
    }
  } catch (error) {
    console.error("Errore:", error);
    alert('Errore durante il salvataggio');
  }
});

async function eliminaManutenzione(id) {
  if (!confirm('Eliminare questa manutenzione?')) return;
  
  const idMezzo = document.getElementById('mezzo_id').value;
  
  try {
    const res = await fetch('api/delete_manutenzione.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ id: id })
    });
    
    const result = await res.json();
    
    if (result.success) {
      caricaManutenzioni(idMezzo); // Ricarica
      alert('Manutenzione eliminata!');
    }
  } catch (error) {
    console.error("Errore:", error);
  }
}

// Gestione Tabs nella Modale
document.querySelectorAll('#mezzoTabs .nav-link').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('#mezzoTabs .nav-link')
      .forEach(b => b.classList.remove('active'));

    btn.classList.add('active');

    document.querySelectorAll('#mezzoModal .tab-pane')
      .forEach(p => p.classList.remove('active'));

    const tabName = btn.getAttribute('data-tab');
    document.getElementById('tab-' + tabName).classList.add('active');
  });
});

// 7. Funzione Elimina
async function elimina(id) {
  if(!confirm("Sei sicuro di voler eliminare questo mezzo?")) return;
  try {
    const res = await fetch("api/delete_mezzo.php", {
      method: "POST",
      headers: {"Content-Type": "application/json"},
      body: JSON.stringify({ id })
    });
    const result = await res.json();
    if(result.success) caricaMezzi();
  } catch (err) {
    alert("Errore durante l'eliminazione");
  }
}

function closeMezzoModal(resetForm) {
  modal.classList.remove('show');
  //form.reset();
  document.getElementById('modaleManutenzione').style.display = 'none';

  if (resetForm) {
    form.reset();
    document.getElementById('mezzo_id').value = '';
  }
}

// Avvio iniziale
document.addEventListener('DOMContentLoaded', caricaMezzi);
</script>

<?php include 'includes/footer.php'; ?>