<?php
require_once __DIR__ . '/backend/auth.php'; // Protegge la pagina
require_once __DIR__ . '/includes/auth_admin.php';
?>
<?php include 'includes/header.php'; ?>

<div class="d-flex">
  <?php include 'includes/sidebar.php'; ?>
  
  <div class="main-content container-fluid">
    <div class="header-actions d-flex justify-content-between align-items-center mb-4">
      <h2 class="text-primary fw-bold mb-0">Dipendenti</h2>
      <button id="openModalBtn" class="btn btn-primary">
        <i class="fa-solid fa-plus me-2"></i>Aggiungi Dipendente
      </button>
    </div>
  
  <!-- Sezione Filtri -->
    <div class="filtri-section">
      <h4>FILTRI</h4>
      <div class="filtri-grid">
        <div class="filtro-group">
          <label>Nome Dipendente</label>
          <input type="text" id="filtroNome" placeholder="Cerca per nome...">
        </div>
        <div class="filtro-group">
          <label>Indirizzo</label>
          <input type="text" id="filtroIndirizzo" placeholder="Cerca per indirizzo...">
        </div>
        <div class="filtro-group">
          <label>Codice Fiscale</label>
          <input type="text" id="filtroCF" placeholder="Cerca per codice fiscale...">
        </div>
      </div>
    </div>

    <div class="card shadow-sm">
      <div class="card-body">
        <div class="table-responsive">
          <table id="dipTable" class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>Nome</th>
                <th>Cognome</th>
                <th>Telefono</th>
                <th>C. Fiscale</th>
                <th>Assunzione</th>
                <th class="text-end">Azioni</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- MODALE DIPENDENTE -->
<div id="addModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3 id="modalTitle">Gestione Dipendente</h3>
      <button class="close-btn" id="closeModalBtn" onclick="closeDipModal()">&times;</button>
    </div>

    <form id="addForm">
      <input type="hidden" id="dipendente_id" name="id">

      <!-- NAV TABS -->
      <ul class="nav nav-tabs" id="dipTabs">
        <li class="nav-item">
          <button class="nav-link active" data-tab="anagrafica" type="button">Anagrafica</button>
        </li>
        <li class="nav-item">
          <button class="nav-link" data-tab="contatti" type="button">Contatti</button>
        </li>
        <li class="nav-item">
          <button class="nav-link" data-tab="documenti" type="button">Documenti</button>
        </li>
        <li class="nav-item">
          <button class="nav-link" data-tab="formazione" type="button">Formazione</button>
        </li>
        <li class="nav-item">
          <button class="nav-link" data-tab="assegnazioni" type="button">Assegnazioni</button>
        </li>
      </ul>

      <!-- TAB CONTENT -->
      <div class="tab-content mt-3">

        <!-- TAB ANAGRAFICA -->
        <div class="tab-pane active" id="tab-anagrafica">
          <div class="row">

            <div class="col-md-6 mb-3">
              <label class="fw-bold">Nome *</label>
              <input type="text" id="dipendente_nome" name="nome" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
              <label class="fw-bold">Cognome *</label>
              <input type="text" id="dipendente_cognome" name="cognome" class="form-control" required>
            </div>

            <div class="col-md-4 mb-3">
              <label>Data di Nascita</label>
              <input type="date" id="data_nascita" name="data_nascita" class="form-control">
            </div>

            <div class="col-md-4 mb-3">
              <label class="fw-bold">Data Assunzione</label>
              <input type="date" id="data_assunzione" name="data_assunzione" class="form-control">
            </div>

            <div class="col-md-4 mb-3">
              <label>Sesso</label>
              <select id="sesso" name="sesso" class="form-select">
                <option value="">Seleziona</option>
                <option value="M">Maschio</option>
                <option value="F">Femmina</option>
              </select>
            </div>

            <div class="col-md-4 mb-3">
              <label>Stato Civile</label>
              <input type="text" id="stato_civile" name="stato_civile" class="form-control">
            </div>

            <div class="col-md-6 mb-3">
              <label>Esperienze</label>
              <textarea id="esperienze" name="esperienze" class="form-control" rows="2" 
                        placeholder="Descrivi l'esperienza professionale..."></textarea>
            </div>

            <div class="col-md-6 mb-3">
              <label>Competenze</label>
              <textarea id="competenze" name="competenze" class="form-control" rows="2" 
                        placeholder="Elenca le competenze tecniche..."></textarea>
            </div>

            <div class="col-md-6 mb-3">
              <label>Livello Esperienza</label>
              <select id="livello_esperienza" name="livello_esperienza" class="form-select">
                <option value="">Seleziona</option>
                <option value="1">1 - Junior</option>
                <option value="2">2 - Middle</option>
                <option value="3">3 - Senior</option>
                <option value="4">4 - Expert</option>
              </select>
            </div>

          </div>
        </div>

        <!-- TAB CONTATTI -->
        <div class="tab-pane" id="tab-contatti">
          <div class="row">

            <div class="col-md-6 mb-3">
              <label>Telefono</label>
              <input type="tel" id="telefono" name="telefono" class="form-control">
            </div>

            <div class="col-md-6 mb-3">
              <label>Email</label>
              <input type="email" id="email" name="email" class="form-control">
            </div>

            <div class="col-md-12 mb-3">
              <label>Indirizzo Residenza</label>
              <input type="text" id="residenza" name="residenza" class="form-control"
                     placeholder="Via, Civico, Città, CAP">
            </div>

          </div>
        </div>

        <!-- TAB DOCUMENTI -->
        <div class="tab-pane" id="tab-documenti">
          <div class="row mt-3">

            <div class="col-12">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>Documenti di Identità</h5>
                <button type="button" class="btn btn-sm btn-success" id="aggiungiDocumento">
                  <i class="fa-solid fa-plus me-1"></i> Aggiungi
                </button>
              </div>
              
              <div class="table-responsive">
                <table class="table table-sm table-hover">
                  <thead class="table-light">
                    <tr>
                      <th>Tipo</th>
                      <th>Numero</th>
                      <th>Rilasciato</th>
                      <th>Scadenza</th>
                      <th>Ente</th>
                      <th>Azioni</th>
                    </tr>
                  </thead>
                  <tbody id="documentiBody">
                    <tr><td colspan="6" class="text-center text-muted">Nessun documento</td></tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="col-12">
              <label class="fw-bold mt-3">Dati nel form (non sincronizzati con tabella sopra)</label>
              <input type="text" id="codice_fiscale" name="codice_fiscale" class="form-control" placeholder="Codice Fiscale">
            </div>

          </div>
        </div>

        <!-- TAB FORMAZIONE -->
        <div class="tab-pane" id="tab-formazione">
          <div class="row mt-3">

            <div class="col-12">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>Corsi e Formazione</h5>
                <button type="button" class="btn btn-sm btn-success" id="aggiungiCorso">
                  <i class="fa-solid fa-plus me-1"></i> Aggiungi Corso
                </button>
              </div>
              
              <div class="table-responsive">
                <table class="table table-sm table-hover">
                  <thead class="table-light">
                    <tr>
                      <th>Nome Corso</th>
                      <th>Ente</th>
                      <th>Data</th>
                      <th>Ore</th>
                      <th>Costo</th>
                      <th>Certificazione</th>
                      <th>Azioni</th>
                    </tr>
                  </thead>
                  <tbody id="corsiBody">
                    <tr><td colspan="7" class="text-center text-muted">Nessun corso</td></tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="col-12">
              <label class="fw-bold mt-3">Note aggiuntive</label>
              <textarea id="formazione" name="formazione" class="form-control" rows="2"></textarea>
            </div>

          </div>
        </div>

        <!-- TAB ASSEGNAZIONI -->
        <div class="tab-pane" id="tab-assegnazioni">
          <div class="row mt-3">

            <div class="col-12">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>Assegnazioni ai Cantieri</h5>
                <button type="button" class="btn btn-sm btn-success" id="aggiungiAssegnamento">
                  <i class="fa-solid fa-plus me-1"></i> Assegna Cantiere
                </button>
              </div>
              
              <div class="table-responsive">
                <table class="table table-sm table-hover">
                  <thead class="table-light">
                    <tr>
                      <th>Cantiere</th>
                      <th>Indirizzo</th>
                      <th>Ruolo</th>
                      <th>Inizio</th>
                      <th>Fine</th>
                      <th>Ore Previste</th>
                      <th>Azioni</th>
                    </tr>
                  </thead>
                  <tbody id="assegnamentiBody">
                    <tr><td colspan="7" class="text-center text-muted">Nessuna assegnazione</td></tr>
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </div>

      </div>

      <div class="modal-footer mt-4">
        <button type="button" class="btn btn-secondary" onclick="closeDipModal()">Annulla</button>
        <button type="submit" class="btn btn-primary">
          <i class="fa-solid fa-floppy-disk me-1"></i> Salva Dipendente
        </button>
      </div>

    </form>
  </div>
</div>

<!-- MODALE AGGIUNGI DOCUMENTO -->
<div id="modaleDocumento" class="modal-nested">
  <div class="modal-nested-content">
    <h6 class="mb-3">Aggiungi Documento</h6>
    
    <form id="formDocumento">
      <div class="row g-2">
        
        <div class="col-md-6">
          <label class="form-label">Tipo *</label>
          <input type="text" id="tipoDoc" class="form-control form-control-sm" 
                 placeholder="Es. Patente, Carta Identità" required>
        </div>

        <div class="col-md-6">
          <label class="form-label">Numero *</label>
          <input type="text" id="numeroDoc" class="form-control form-control-sm" required>
        </div>

        <div class="col-md-6">
          <label class="form-label">Rilasciato</label>
          <input type="date" id="dataRilascio" class="form-control form-control-sm">
        </div>

        <div class="col-md-6">
          <label class="form-label">Scadenza</label>
          <input type="date" id="dataScadenza" class="form-control form-control-sm">
        </div>

        <div class="col-12">
          <label class="form-label">Rilasciato da</label>
          <input type="text" id="rilasciatoDa" class="form-control form-control-sm">
        </div>

      </div>

      <div class="modal-nested-footer mt-3">
        <button type="button" class="btn btn-sm btn-secondary" onclick="chiudiModaleDocumento()">
          Annulla
        </button>
        <button type="submit" class="btn btn-sm btn-primary">
          Salva Documento
        </button>
      </div>
    </form>
  </div>
</div>

<!-- MODALE AGGIUNGI CORSO -->
<div id="modaleCorso" class="modal-nested">
  <div class="modal-nested-content">
    <h6 class="mb-3">Aggiungi Corso</h6>
    
    <form id="formCorso">
      <div class="row g-2">
        
        <div class="col-md-6">
          <label class="form-label">Nome Corso *</label>
          <input type="text" id="nomeCorso" class="form-control form-control-sm" required>
        </div>

        <div class="col-md-6">
          <label class="form-label">Ente Erogante</label>
          <input type="text" id="enteCorso" class="form-control form-control-sm">
        </div>

        <div class="col-md-6">
          <label class="form-label">Data Inizio</label>
          <input type="date" id="dataInizioCorso" class="form-control form-control-sm">
        </div>

        <div class="col-md-6">
          <label class="form-label">Data Completamento</label>
          <input type="date" id="dataCompCorso" class="form-control form-control-sm">
        </div>

        <div class="col-md-6">
          <label class="form-label">Ore</label>
          <input type="number" id="oreCorso" class="form-control form-control-sm">
        </div>

        <div class="col-md-6">
          <label class="form-label">Costo (€)</label>
          <input type="number" id="costoCorso" class="form-control form-control-sm" step="0.01">
        </div>

        <div class="col-12">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="certCorso">
            <label class="form-check-label" for="certCorso">
              Certificazione rilasciata
            </label>
          </div>
        </div>

      </div>

      <div class="modal-nested-footer mt-3">
        <button type="button" class="btn btn-sm btn-secondary" onclick="chiudiModaleCorso()">
          Annulla
        </button>
        <button type="submit" class="btn btn-sm btn-primary">
          Salva Corso
        </button>
      </div>
    </form>
  </div>
</div>

<!-- MODALE AGGIUNGI ASSEGNAZIONE -->
<div id="modaleAssegnamento" class="modal-nested">
  <div class="modal-nested-content">
    <h6 class="mb-3">Assegna a Cantiere</h6>
    
    <form id="formAssegnamento">
      <div class="row g-2">
        
        <div class="col-md-6">
          <label class="form-label">Cantiere *</label>
          <select id="selezionaCantiere" class="form-select form-select-sm" required>
            <option value="">-- Seleziona cantiere --</option>
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label">Ruolo *</label>
          <input type="text" id="ruoloAssegn" class="form-control form-control-sm" 
                 placeholder="Es. Operaio, Supervisore" required>
        </div>

        <div class="col-md-6">
          <label class="form-label">Data Inizio</label>
          <input type="date" id="dataInizioAssegn" class="form-control form-control-sm">
        </div>

        <div class="col-md-6">
          <label class="form-label">Data Fine</label>
          <input type="date" id="dataFineAssegn" class="form-control form-control-sm">
        </div>

        <div class="col-12">
          <label class="form-label">Ore Previste</label>
          <input type="number" id="oreAssegn" class="form-control form-control-sm">
        </div>

      </div>

      <div class="modal-nested-footer mt-3">
        <button type="button" class="btn btn-sm btn-secondary" onclick="chiudiModaleAssegnamento()">
          Annulla
        </button>
        <button type="submit" class="btn btn-sm btn-primary">
          Assegna
        </button>
      </div>
    </form>
  </div>
</div>

<!-- CSS MODALI NIDIFICATE -->
<style>
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal.show {
    display: flex !important;
}

.modal-content {
    background: white;
    border-radius: 8px;
    max-width: 800px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
    z-index: 1050;
}

/* MODAL ANNIDATI - OPERAI E MEZZI */
.modal-nested {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 4px 25px rgba(0,0,0,0.4);
    z-index: 2050;
    max-width: 600px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
    display: none;
}

.modal-nested.visible {
    display: block !important;
}

.modal-nested::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.3);
    z-index: 2049;
    display: none;
}

.modal-nested.visible::before {
    display: block;
}

.modal-nested-content {
    width: 100%;
    position: relative;
    z-index: 2051;
}

.modal-nested-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    border-top: 1px solid #e9ecef;
    padding-top: 15px;
}
</style>


<script>
const USER_ROLE = "<?php echo $_SESSION['ruolo'] ?? 'guest'; ?>";
const modal = document.getElementById('addModal');
const form = document.getElementById('addForm');

let allData = [];
let tuttiCantieri = [];

// --- CARICA DIPENDENTI ---
function loadDipendenti() {
  fetch("api/get_dipendenti.php")
    .then(res => res.json())
    .then(response => {
      if (!response.success) {
        console.error("Errore backend:", response.error);
        return;
      }
      allData = response.data;
      mostraDipendenti(allData);
    })
    .catch(err => console.error("Errore fetch:", err));
}

function mostraDipendenti(lista) {
    const tbody = document.querySelector('#dipTable tbody');
    tbody.innerHTML = '';

    lista.forEach(d => {
        // 1. GESTIONE DATA: Definiamo la variabile qui, visibile per tutta l'iterazione del ciclo
        let dataDisplay = 'Non inserita';
        
        if (d.data_assunzione && d.data_assunzione !== "0000-00-00") {
            const dateObj = new Date(d.data_assunzione);
            if (!isNaN(dateObj.getTime())) {
                dataDisplay = dateObj.toLocaleDateString('it-IT');
            }
        }

        // 2. GESTIONE RUOLO: Verifichiamo il ruolo per i permessi
        let btnElimina = '';
        if (typeof USER_ROLE !== 'undefined' && USER_ROLE === 'admin') {
            btnElimina = `
                <button class="btn btn-sm btn-danger" onclick="eliminaDipendente(${d.id})">
                    <i class="fa-solid fa-trash"></i>
                </button>`;
        }

        // 3. CREAZIONE RIGA: Usiamo dataDisplay che è stata definita sopra
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${d.nome || '-'}</td>
            <td>${d.cognome || '-'}</td>
            <td>${d.telefono || '-'}</td>
            <td><code>${d.codice_fiscale || '-'}</code></td>
            <td>${dataDisplay}</td>
            <td class="text-end">
                <button class="btn btn-sm btn-warning me-1" onclick="apriModifica(${d.id})">
                    <i class="fa-solid fa-pen"></i>
                </button>
                ${btnElimina}
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// --- FILTRI ---
document.getElementById("filtroNome").addEventListener("input", applicaFiltri);
document.getElementById("filtroIndirizzo").addEventListener("input", applicaFiltri);
document.getElementById("filtroCF").addEventListener("input", applicaFiltri);

function applicaFiltri() {
  const nome = document.getElementById("filtroNome").value.toLowerCase();
  const indirizzo = document.getElementById("filtroIndirizzo").value.toLowerCase();
  const cf = document.getElementById("filtroCF").value.toLowerCase();

  const filtrati = allData.filter(d => {
    const nomeCampo = (d.nome || "").toLowerCase();
    const indirizzoCampo = (d.residenza || "").toLowerCase();
    const cfCampo = (d.codice_fiscale || "").toLowerCase();

    return nomeCampo.includes(nome)
      && indirizzoCampo.includes(indirizzo)
      && cfCampo.includes(cf);
  });

  mostraDipendenti(filtrati);
}

// --- CARICA CANTIERI ---
async function caricaCantieri() {
  try {
    const res = await fetch("api/get_cantieri.php");
    const data = await res.json();
    
    if (Array.isArray(data)) {
      tuttiCantieri = data.filter(c => c.stato === 'attivo' || c.stato === 'in corso');
      
      const select = document.getElementById('selezionaCantiere');
      select.innerHTML = '<option value="">-- Seleziona cantiere --</option>';
      
      tuttiCantieri.forEach(cantiere => {
        const option = document.createElement('option');
        option.value = cantiere.id;
        option.textContent = cantiere.nome + ' (' + (cantiere.indirizzo || 'N/D') + ')';
        select.appendChild(option);
      });
    }
  } catch (error) {
    console.error("Errore caricamento cantieri:", error);
  }
}

// --- APRI MODIFICA ---
function apriModifica(id) {
  const item = allData.find(d => d.id == id);
  
  if (item) {
    document.getElementById('modalTitle').innerText = "Modifica Dipendente: " + item.nome + " " + item.cognome;
    document.getElementById('dipendente_id').value = item.id;
    
    // Anagrafica
    document.getElementById('dipendente_nome').value = item.nome || '';
    document.getElementById('dipendente_cognome').value = item.cognome || '';
    const pulisciData = (data) => (data && data !== "0000-00-00") ? data : "";
    document.getElementById('data_nascita').value = item.dataDiNascita || '';  // Colonna DB: dataDiNascita
    document.getElementById('data_assunzione').value = item.data_assunzione || '';
    document.getElementById('sesso').value = item.sesso || '';
    document.getElementById('stato_civile').value = item.stato_civile || '';
    document.getElementById('esperienze').value = item.Esperienze || '';
    document.getElementById('competenze').value = item.Competenze || '';
    document.getElementById('livello_esperienza').value = item.livello_esperienza || '';
    
    // Contatti
    document.getElementById('telefono').value = item.telefono || '';
    document.getElementById('email').value = item.recapitieMail || '';  // Colonna DB: recapitieMail
    document.getElementById('residenza').value = item.indirizzoResidenza || '';  // Colonna DB: indirizzoResidenza
    
    // Documenti
    document.getElementById('codice_fiscale').value = item.codice_fiscale || '';
    
    // Formazione
    document.getElementById('formazione').value = item.Corsi_e_Formazione || '';  // Colonna DB: Corsi_e_Formazione
    
    // Carica dati dalle tabelle di relazione
    caricaDocumenti(item.id);
    caricaCorsi(item.id);
    caricaAssegnazioni(item.id);
    
    modal.classList.add('show');
  }
}

// --- DOCUMENTI ---
async function caricaDocumenti(id_dipendente) {
  try {
    const res = await fetch(`api/get_documenti.php?id_dipendente=${id_dipendente}`);
    const data = await res.json();
    
    const tbody = document.getElementById('documentiBody');
    tbody.innerHTML = '';
    
    if (!data.success || data.data.length === 0) {
      tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Nessun documento</td></tr>';
      return;
    }
    
    data.data.forEach(doc => {
      const scadenza = doc.data_scadenza ? new Date(doc.data_scadenza).toLocaleDateString('it-IT') : '-';
      const rilascio = doc.data_rilascio ? new Date(doc.data_rilascio).toLocaleDateString('it-IT') : '-';
      
      tbody.innerHTML += `
        <tr>
          <td>${doc.tipo_documento}</td>
          <td>${doc.numero_documento}</td>
          <td>${rilascio}</td>
          <td>${scadenza}</td>
          <td>${doc.rilasciato_da || '-'}</td>
          <td>
            <button class="btn btn-xs btn-danger" onclick="eliminaDocumento(${doc.id})">
              <i class="fa-solid fa-trash"></i>
            </button>
          </td>
        </tr>
      `;
    });
  } catch (error) {
    console.error("Errore caricamento documenti:", error);
  }
}

document.getElementById('aggiungiDocumento').addEventListener('click', function() {
  document.getElementById('modaleDocumento').classList.add('visible');
});

function chiudiModaleDocumento() {
  document.getElementById('modaleDocumento').classList.remove('visible');
  document.getElementById('formDocumento').reset();
}

document.getElementById('formDocumento').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const idDipRaw = document.getElementById('dipendente_id').value;
    
    // Controllo fondamentale: il dipendente deve avere un ID
    if (!idDipRaw || idDipRaw === "") {
        alert("Attenzione: Devi prima salvare l'anagrafica del dipendente (clicca su 'Salva Dipendente') prima di poter aggiungere documenti o corsi.");
        return;
    }

    const payload = {
        id_dipendente: parseInt(idDipRaw),
        tipo_documento: document.getElementById('tipoDoc').value.trim(),
        numero_documento: document.getElementById('numeroDoc').value.trim(),
        descrizione: null,
        data_rilascio: document.getElementById('dataRilascio').value || null,
        data_scadenza: document.getElementById('dataScadenza').value || null,
        rilasciato_da: document.getElementById('rilasciatoDa').value || null
    };

    try {
        const res = await fetch('api/add_documenti.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(payload)
        });
        
        const result = await res.json();
        
        if (result.success) {
            chiudiModaleDocumento();
            caricaDocumenti(idDipRaw); // Ricarica la tabella
            alert('Documento aggiunto!');
        } else {
            // Qui vedrai l'errore "Dipendente non trovato" se l'ID è sbagliato
            alert('Errore: ' + result.error);
        }
    } catch (error) {
        console.error("Errore:", error);
        alert('Errore durante il salvataggio');
    }
});

async function eliminaDocumento(id) {
  if (!confirm('Eliminare il documento?')) return;
  
  const idDip = document.getElementById('dipendente_id').value;
  
  try {
    const res = await fetch('api/delete_documenti.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ id: id })
    });
    
    const result = await res.json();
    
    if (result.success) {
      caricaDocumenti(idDip);
      alert('Documento eliminato!');
    }
  } catch (error) {
    console.error("Errore:", error);
  }
}

// --- CORSI ---
async function caricaCorsi(id_dipendente) {
  try {
    const res = await fetch(`api/get_corsi.php?id_dipendente=${id_dipendente}`);
    const data = await res.json();
    
    const tbody = document.getElementById('corsiBody');
    tbody.innerHTML = '';
    
    if (!data.success || data.data.length === 0) {
      tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Nessun corso</td></tr>';
      return;
    }
    
    data.data.forEach(corso => {
      const dataComp = corso.data_completamento ? new Date(corso.data_completamento).toLocaleDateString('it-IT') : '-';
      const cert = corso.certificazione_rilasciata ? '<i class="fa-solid fa-check text-success"></i>' : '-';
      
      tbody.innerHTML += `
        <tr>
          <td>${corso.nome_corso}</td>
          <td>${corso.ente_erogante || '-'}</td>
          <td>${dataComp}</td>
          <td>${corso.ore_corso || '-'}</td>
          <td>€ ${parseFloat(corso.costo || 0).toFixed(2)}</td>
          <td>${cert}</td>
          <td>
            <button class="btn btn-xs btn-danger" onclick="eliminaCorso(${corso.id})">
              <i class="fa-solid fa-trash"></i>
            </button>
          </td>
        </tr>
      `;
    });
  } catch (error) {
    console.error("Errore caricamento corsi:", error);
  }
}

document.getElementById('aggiungiCorso').addEventListener('click', function() {
  document.getElementById('modaleCorso').classList.add('visible');
});

function chiudiModaleCorso() {
  document.getElementById('modaleCorso').classList.remove('visible');
  document.getElementById('formCorso').reset();
}

document.getElementById('formCorso').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // 1. Recupera l'ID e trasformalo in numero
    const idDipRaw = document.getElementById('dipendente_id').value;
    const idDip = idDipRaw ? parseInt(idDipRaw) : null;

    // 2. Controllo di sicurezza: se non c'è l'ID, non inviare la richiesta
    if (!idDip) {
        alert("Attenzione: Devi prima salvare il dipendente o caricarne uno esistente prima di aggiungere un corso.");
        return;
    }
    
    // 3. Costruisci il payload con i tipi di dato corretti
    const payload = {
        id_dipendente: idDip,
        nome_corso: document.getElementById('nomeCorso').value.trim(),
        descrizione: null,
        ente_erogante: document.getElementById('enteCorso').value || null,
        data_inizio_corso: document.getElementById('dataInizioCorso').value || null,
        data_completamento: document.getElementById('dataCompCorso').value || null,
        // Converti in numero se c'è un valore, altrimenti null
        ore_corso: document.getElementById('oreCorso').value ? parseInt(document.getElementById('oreCorso').value) : null,
        costo: document.getElementById('costoCorso').value ? parseFloat(document.getElementById('costoCorso').value) : null,
        certificazione_numero: null,
        certificazione_rilasciata: document.getElementById('certCorso').checked ? 1 : 0,
        voto_finale: null
    };

    // 4. Controllo nome corso (obbligatorio nel tuo PHP)
    if (!payload.nome_corso) {
        alert("Il nome del corso è obbligatorio");
        return;
    }
    
    try {
        const res = await fetch('api/add_corsi.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(payload)
        });
        
        // Debug veloce: se non è 200, fammi vedere cosa dice il server
        if (!res.ok) {
            const errorText = await res.text();
            console.error("Errore del server:", errorText);
            alert("Errore 400: Il server ha rifiutato i dati. Controlla la console.");
            return;
        }

        const result = await res.json();
        
        if (result.success) {
            chiudiModaleCorso();
            caricaCorsi(idDip);
            alert('Corso aggiunto con successo!');
        } else {
            alert('Errore: ' + result.error);
        }
    } catch (error) {
        console.error("Errore di rete:", error);
        alert('Impossibile comunicare con il server.');
    }
});

async function eliminaCorso(id) {
  if (!confirm('Eliminare il corso?')) return;
  
  const idDip = document.getElementById('dipendente_id').value;
  
  try {
    const res = await fetch('api/delete_corsi.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ id: id })
    });
    
    const result = await res.json();
    
    if (result.success) {
      caricaCorsi(idDip);
      alert('Corso eliminato!');
    }
  } catch (error) {
    console.error("Errore:", error);
  }
}

// --- ASSEGNAZIONI ---
async function caricaAssegnazioni(id_dipendente) {
  try {
    const res = await fetch(`api/get_dipendenti_cantieri.php?id_dipendente=${id_dipendente}`);
    const data = await res.json();
    
    const tbody = document.getElementById('assegnamentiBody');
    tbody.innerHTML = '';
    
    if (!data.success || data.data.length === 0) {
      tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Nessuna assegnazione</td></tr>';
      return;
    }
    
    data.data.forEach(ass => {
      const inizio = new Date(ass.data_inizio).toLocaleDateString('it-IT');
      const fine = ass.data_fine ? new Date(ass.data_fine).toLocaleDateString('it-IT') : '-';
      
      tbody.innerHTML += `
        <tr>
          <td><strong>${ass.nome_cantiere}</strong></td>
          <td>${ass.indirizzo_cantiere || '-'}</td>
          <td>${ass.ruolo_cantiere || '-'}</td>
          <td>${inizio}</td>
          <td>${fine}</td>
          <td>${ass.ore_previste || '-'}</td>
          <td>
            <button class="btn btn-xs btn-danger" onclick="eliminaAssegnazione(${ass.id})">
              <i class="fa-solid fa-trash"></i>
            </button>
          </td>
        </tr>
      `;
    });
  } catch (error) {
    console.error("Errore caricamento assegnazioni:", error);
  }
}

document.getElementById('aggiungiAssegnamento').addEventListener('click', function() {
  caricaCantieri();
  document.getElementById('modaleAssegnamento').classList.add('visible');
});

function chiudiModaleAssegnamento() {
  document.getElementById('modaleAssegnamento').classList.remove('visible');
  document.getElementById('formAssegnamento').reset();
}

document.getElementById('formAssegnamento').addEventListener('submit', async function(e) {
  e.preventDefault();
  
  const idDip = document.getElementById('dipendente_id').value;
  const payload = {
    id_dipendente: idDip,
    id_cantiere: document.getElementById('selezionaCantiere').value,
    ruolo_cantiere: document.getElementById('ruoloAssegn').value,
    ore_previste: document.getElementById('oreAssegn').value || null,
    data_inizio: document.getElementById('dataInizioAssegn').value || null,
    data_fine: document.getElementById('dataFineAssegn').value || null
  };
  
  try {
    const res = await fetch('api/add_dipendente_cantiere.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify(payload)
    });
    
    const result = await res.json();
    
    if (result.success) {
      chiudiModaleAssegnamento();
      caricaAssegnazioni(idDip);
      alert('Assegnazione creata!');
    } else {
      alert('Errore: ' + result.error);
    }
  } catch (error) {
    console.error("Errore:", error);
    alert('Errore durante il salvataggio');
  }
});

async function eliminaAssegnazione(id) {
  if (!confirm('Rimuovere questa assegnazione?')) return;
  
  const idDip = document.getElementById('dipendente_id').value;
  
  try {
    const res = await fetch('api/delete_dipendente_cantiere.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ id: id })
    });
    
    const result = await res.json();
    
    if (result.success) {
      caricaAssegnazioni(idDip);
      alert('Assegnazione rimossa!');
    }
  } catch (error) {
    console.error("Errore:", error);
  }
}

// --- SALVA DIPENDENTE ---
form.onsubmit = async (e) => {
  e.preventDefault();
  
  const idValue = document.getElementById('dipendente_id').value;
  const payload = {
    id: idValue,
    nome: document.getElementById('dipendente_nome').value,
    cognome: document.getElementById('dipendente_cognome').value,
    data_nascita: document.getElementById('data_nascita').value || null,
    data_assunzione: document.getElementById('data_assunzione').value || null,
    sesso: document.getElementById('sesso').value || null,
    stato_civile: document.getElementById('stato_civile').value || null,
    esperienze: document.getElementById('esperienze').value || null,
    competenze: document.getElementById('competenze').value || null,
    livello_esperienza: document.getElementById('livello_esperienza').value || null,
    telefono: document.getElementById('telefono').value || null,
    email: document.getElementById('email').value || null,
    residenza: document.getElementById('residenza').value || null,
    codice_fiscale: document.getElementById('codice_fiscale').value || null,
    formazione: document.getElementById('formazione').value || null
  };

  const urlDestinazione = idValue ? 'api/update_dipendente.php' : 'api/add_dipendente.php';

  try {
    const res = await fetch(urlDestinazione, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });

    const text = await res.text(); // Prendi il testo, non JSON
    console.log("RISPOSTA RAW:", text); // LOG l'errore
    
    try {
      const result = JSON.parse(text);
      if (result.success) {
        alert(idValue ? "Aggiornato!" : "Aggiunto!");
        closeDipModal();
        loadDipendenti();
      } else {
        alert("Errore: " + result.error);
      }
    } catch (e) {
      alert("Errore server (vedi console): " + text);
    }
  } catch (err) {
    console.error("Errore invio:", err);
  }
};

// --- FUNZIONI UTILITY ---
function closeDipModal() {
  modal.classList.remove('show');
  form.reset();
  document.getElementById('dipendente_id').value = '';
  
  // Chiudi e pulisci anche le modali nidificate
  document.getElementById('modaleDocumento').classList.remove('visible');
  document.getElementById('modaleCorso').classList.remove('visible');
  document.getElementById('modaleAssegnamento').classList.remove('visible');
  
  // Pulisci i form
  document.getElementById('formDocumento').reset();
  document.getElementById('formCorso').reset();
  document.getElementById('formAssegnamento').reset();
  
  // Pulisci le tabelle
  document.getElementById('documentiBody').innerHTML = '<tr><td colspan="6" class="text-center text-muted">Nessun documento</td></tr>';
  document.getElementById('corsiBody').innerHTML = '<tr><td colspan="7" class="text-center text-muted">Nessun corso</td></tr>';
  document.getElementById('assegnamentiBody').innerHTML = '<tr><td colspan="7" class="text-center text-muted">Nessuna assegnazione</td></tr>';
}

async function eliminaDipendente(id) {
  if (confirm("Eliminare definitivamente questo dipendente?")) {
    try {
      const res = await fetch('api/delete_dipendente.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id })
      });
      const result = await res.json();
      if (result.success) loadDipendenti();
    } catch (e) { console.error(e); }
  }
}

// --- GESTIONE TAB ---
document.querySelectorAll('#dipTabs .nav-link').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('#dipTabs .nav-link')
      .forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    document.querySelectorAll('.tab-pane')
      .forEach(p => p.classList.remove('active'));

    const tabName = btn.getAttribute('data-tab');
    document.getElementById('tab-' + tabName).classList.add('active');
  });
});

// --- EVENTI ---
document.getElementById('openModalBtn').onclick = () => {
  document.getElementById('modalTitle').innerText = "Aggiungi Nuovo Dipendente";
  closeDipModal();
  modal.classList.add('show');
};

window.onclick = (event) => { 
  if (event.target == modal) closeDipModal(); 
};

// --- CARICAMENTO INIZIALE ---
document.addEventListener('DOMContentLoaded', loadDipendenti);
</script>


<?php include 'includes/footer.php'; ?>