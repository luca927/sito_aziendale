<?php
require_once __DIR__ . '/backend/auth.php';
include 'includes/header.php';
?>

<div class="d-flex">
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <header>🏗️ Gestione Cantieri</header>

        <div class="header-actions d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold">Cantieri Operativi</h2>
            </div>
            <div class="btn-group shadow-sm">
                <button id="openModalBtn" class="btn btn-primary">
                    <i class="fa-solid fa-plus me-1"></i> Aggiungi
                </button>
                <button onclick="exportCantieri('csv')" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-file-csv"></i> CSV
                </button>
                <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-file-pdf"></i> PDF
                </button>
            </div>
        </div>

        <!-- Sezione Filtri -->
        <div class="filtri-section">
            <h4>FILTRI</h4>
            <div class="filtri-grid">
                <div class="filtro-group">
                    <label>Nome Cantiere</label>
                    <input type="text" id="filtroNome" placeholder="Cerca per nome...">
                </div>
                <div class="filtro-group">
                    <label>Indirizzo</label>
                    <input type="text" id="filtroIndirizzo" placeholder="Cerca per indirizzo...">
                </div>
                <div class="filtro-group">
                    <label>Nome o Cognome Referente</label>
                    <input type="text" id="filtroReferente" placeholder="Cerca referente...">
                </div>
            </div>
        </div>

        <!-- TABELLA CANTIERI -->
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Indirizzo</th>
                        <th>Referente</th>
                        <th>Data Inizio</th>
                        <th>Data Fine</th>
                        <th>Giorni Lavoro</th>
                        <th>Stato</th>
                        <th>Operai</th>
                        <th>Mappa</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody id="cantieriTable"></tbody>
            </table>
        </div>

    </div>
</div>

<!-- MODALE CANTIERE -->
<div id="cantiereModal" class="modal">
    <div class="modal-content">

        <div class="modal-header">
            <h3 id="cantiereModalTitle">Nuovo Cantiere</h3>
            <button class="close-btn" id="closeCantiereModal">&times;</button>
        </div>

        <!-- NAV TABS -->
        <ul id="cantiereTabs" class="nav nav-tabs custom-tabs">
            <li class="nav-item">
                <button class="nav-link active" data-tab="dati" type="button">Dati Cantiere</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-tab="operai" type="button">Operai Assegnati</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-tab="mezzi" type="button">Mezzi Assegnati</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-tab="stato" type="button">Stato</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-tab="note" type="button">Note</button>
            </li>
        </ul>

       <form id="cantiereForm">
    <input type="hidden" id="cantiere_id">

    <!-- TAB DATI -->
    <div id="tab-dati" class="tab-pane active">
        <div class="row g-3 mt-2">
            <div class="col-md-6">
                <label class="form-label fw-bold">Nome *</label>
                <input type="text" id="nome_cantiere" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Indirizzo</label>
                <input type="text" id="indirizzo" class="form-control">
            </div>

            <div class="col-md-6">
                <label class="form-label">Referente</label>
                <input type="text" id="referente" class="form-control">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold">Giorni Lavoro</label>
                <div class="d-flex flex-wrap gap-2" id="giorniBox">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="LUN" id="lun">
                        <label class="form-check-label" for="lun">Lunedì</label>    
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="MAR" id="mar">
                        <label class="form-check-label" for="mar">Martedì</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="MER" id="mer">
                        <label class="form-check-label" for="mer">Mercoledì</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="GIO" id="gio">
                        <label class="form-check-label" for="gio">Giovedì</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="VEN" id="ven">
                        <label class="form-check-label" for="ven">Venerdì</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="SAB" id="sab">
                        <label class="form-check-label" for="sab">Sabato</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="DOM" id="dom">
                        <label class="form-check-label" for="dom">Domenica</label>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Data Inizio</label>
                <input type="date" id="data_inizio" class="form-control">
            </div>

            <div class="col-md-6">
                <label class="form-label">Data Fine</label>
                <input type="date" id="data_fine" class="form-control">
            </div>

            <div class="col-md-6">
                <label class="form-label">Latitudine</label>
                <input type="text" id="lat" class="form-control">
            </div>

            <div class="col-md-6">
                <label class="form-label">Longitudine</label>
                <input type="text" id="lng" class="form-control">
            </div>

            <div class="col-12 mt-2">
                <button type="button" class="btn btn-success w-100" onclick="rilevaPosizioneGPS()">
                    <i class="fa-solid fa-location-crosshairs me-1"></i> Rileva Posizione GPS Automatica
                </button>
            </div>

            <div class="col-md-6">
                <label class="form-label">Coordinatore Sicurezza</label>
                <input type="text" id="coordinatore_sicurezza" class="form-control">
            </div>

            <div class="col-md-6">
                <label class="form-label">Piano Sicurezza e Coordinamento</label>
                <input type="text" id="piano_sicurezza" class="form-control">
            </div>

        </div> 
    </div>

    <!-- TAB OPERAI -->
    <div id="tab-operai" class="tab-pane">
        <div class="row mt-3">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5>Operai Assegnati</h5>
                    <button type="button" class="btn btn-sm btn-success" id="aggiungiOperaio">
                        <i class="fa-solid fa-plus me-1"></i> Assegna Operaio
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nome</th>
                                <th>Cognome</th>
                                <th>Ruolo</th>
                                <th>Inizio</th>
                                <th>Fine</th>
                                <th>Ore Previste</th>
                                <th>Azioni</th>
                            </tr>
                        </thead>
                        <tbody id="operaiBody">
                            <tr><td colspan="7" class="text-center text-muted">Nessun operaio assegnato</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB MEZZI -->
    <div id="tab-mezzi" class="tab-pane">
        <div class="row mt-3">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5>Mezzi Assegnati</h5>
                    <button type="button" class="btn btn-sm btn-success" id="aggiungiMezzo">
                        <i class="fa-solid fa-plus me-1"></i> Assegna Mezzo
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Mezzo</th>
                                <th>Targa</th>
                                <th>Operatore</th>
                                <th>Inizio</th>
                                <th>Fine</th>
                                <th>Km</th>
                                <th>Azioni</th>
                            </tr>
                        </thead>
                        <tbody id="mezziBody">
                            <tr><td colspan="7" class="text-center text-muted">Nessun mezzo assegnato</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB STATO -->
    <div id="tab-stato" class="tab-pane">
        <div class="mt-3">
            <label class="form-label">Stato</label>
            <select id="stato_cantiere" class="form-select">
                <option value="attivo">Attivo</option>
                <option value="completato">Completato</option>
                <option value="sospeso">Sospeso</option>
            </select>
        </div>
    </div>

    <!-- TAB NOTE -->
    <div id="tab-note" class="tab-pane">
        <div class="mt-3">
            <label class="form-label">Note</label>
            <textarea id="note_cantiere" class="form-control" rows="4"></textarea>
        </div>
    </div>

    <div class="modal-footer mt-4">
        <button type="button" class="btn btn-secondary" id="cancelCantiereModal">Annulla</button>
        <button type="submit" class="btn btn-primary">Salva Cantiere</button>
    </div>
</form>
    </div>
</div>

<!-- MODALE AGGIUNGI OPERAIO -->
<div id="modaleOperaio" class="modal-nested" style="display: none;">
    <div class="modal-nested-content">
        <h6 class="mb-3">Assegna Operaio</h6>
        
        <form id="formOperaio">
            <div class="row g-2">
                
                <div class="col-md-12">
                    <label class="form-label">Operaio *</label>
                    <select id="selezionaOperaio" class="form-select form-select-sm" required>
                        <option value="">-- Seleziona operaio --</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Ruolo *</label>
                    <input type="text" id="ruoloOperaio" class="form-control form-control-sm" 
                           placeholder="Es. Operaio, Supervisore" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Ore Previste</label>
                    <input type="number" id="oreOperaio" class="form-control form-control-sm">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Data Inizio</label>
                    <input type="date" id="dataInizioOp" class="form-control form-control-sm">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Data Fine</label>
                    <input type="date" id="dataFineOp" class="form-control form-control-sm">
                </div>

            </div>

            <div class="modal-nested-footer mt-3">
                <button type="button" class="btn btn-sm btn-secondary" onclick="chiudiModaleOperaio()">
                    Annulla
                </button>
                <button type="submit" class="btn btn-sm btn-primary">
                    Assegna
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODALE AGGIUNGI MEZZO -->
<div id="modaleMezzo" class="modal-nested" style="display: none;">
    <div class="modal-nested-content">
        <h6 class="mb-3">Assegna Mezzo</h6>
        
        <form id="formMezzo">
            <div class="row g-2">
                
                <div class="col-md-12">
                    <label class="form-label">Mezzo *</label>
                    <select id="selezionaMezzo" class="form-select form-select-sm" required>
                        <option value="">-- Seleziona mezzo --</option>
                    </select>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Operatore (opzionale)</label>
                    <select id="selezionaOperatoreMezzo" class="form-select form-select-sm">
                        <option value="">-- Nessuno --</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Inizio Utilizzo</label>
                    <input type="datetime-local" id="dataInizioMezzo" class="form-control form-control-sm">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Km Inizio</label>
                    <input type="number" id="kmInizioMezzo" class="form-control form-control-sm">
                </div>

                <div class="col-12">
                    <label class="form-label">Note</label>
                    <textarea id="noteMezzo" class="form-control form-control-sm" rows="2"></textarea>
                </div>

            </div>

            <div class="modal-nested-footer mt-3">
                <button type="button" class="btn btn-sm btn-secondary" onclick="chiudiModaleMezzo()">
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
.modal-nested {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    z-index: 1001;
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
let cantieriGlobali = [];
let tuttiDipendenti = [];
let tuttiMezzi = [];

const cantiereModal = document.getElementById("cantiereModal");
const cantiereForm  = document.getElementById("cantiereForm");

function rilevaPosizioneGPS() {
    if (!navigator.geolocation) {
        alert("Spiacente, il tuo browser non supporta la geolocalizzazione.");
        return;
    }

    const btn = event.currentTarget;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Acquisizione in corso...';
    btn.disabled = true;

    navigator.geolocation.getCurrentPosition(
        (position) => {
            document.getElementById("lat").value = position.coords.latitude.toFixed(7);
            document.getElementById("lng").value = position.coords.longitude.toFixed(7);
            
            btn.innerHTML = originalText;
            btn.disabled = false;
            alert("Posizione acquisita correttamente!");
        },
        (error) => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            
            let messaggio = "Errore sconosciuto";
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    messaggio = "Permesso negato. Devi abilitare la posizione nel browser.";
                    break;
                case error.POSITION_UNAVAILABLE:
                    messaggio = "Informazioni posizione non disponibili.";
                    break;
                case error.TIMEOUT:
                    messaggio = "Tempo scaduto per il rilevamento.";
                    break;
            }
            alert("Errore: " + messaggio);
        },
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        }
    );
}  

// --- CARICA CANTIERI ---
async function caricaCantieri() {
    const res = await fetch("api/get_cantieri.php");
    cantieriGlobali = await res.json();
    mostraCantieri(cantieriGlobali);
}

// --- CARICA DIPENDENTI ---
async function caricaDipendenti() {
    try {
        const res = await fetch("api/get_dipendenti.php");
        const data = await res.json();
        if (data.success) {
            tuttiDipendenti = data.data;
            popolareDipendenti();
        }
    } catch (error) {
        console.error("Errore caricamento dipendenti:", error);
    }
}

// --- CARICA MEZZI ---
async function caricaMezzi() {
    try {
        const res = await fetch("api/get_mezzi.php");
        tuttiMezzi = await res.json();
        popolareMezzi();
    } catch (error) {
        console.error("Errore caricamento mezzi:", error);
    }
}

function popolareDipendenti() {
    const select = document.getElementById('selezionaOperaio');
    const selectMezzo = document.getElementById('selezionaOperatoreMezzo');
    
    select.innerHTML = '<option value="">-- Seleziona operaio --</option>';
    selectMezzo.innerHTML = '<option value="">-- Nessuno --</option>';
    
    tuttiDipendenti.forEach(d => {
        const option = document.createElement('option');
        option.value = d.id;
        option.textContent = d.nome + ' ' + d.cognome;
        select.appendChild(option);
        
        const option2 = option.cloneNode(true);
        selectMezzo.appendChild(option2);
    });
}

function popolareMezzi() {
    const select = document.getElementById('selezionaMezzo');
    select.innerHTML = '<option value="">-- Seleziona mezzo --</option>';
    
    tuttiMezzi.forEach(m => {
        const option = document.createElement('option');
        option.value = m.id;
        option.textContent = m.nome_mezzo + ' (' + m.targa + ')';
        select.appendChild(option);
    });
}

// --- MOSTRA TABELLA CANTIERI ---
function mostraCantieri(lista) {
    const tbody = document.getElementById("cantieriTable");
    tbody.innerHTML = "";

    lista.forEach(c => {
        const linkMappa = (c.lat && c.lng)
            ? `<a href="https://www.google.com/maps?q=${c.lat},${c.lng}" 
                 target="_blank" 
                 class="btn btn-outline-primary btn-sm">
                <i class="fa-solid fa-location-dot"></i>
             </a>`
            : `<span class="text-muted small">N/D</span>`;

        tbody.innerHTML += `
            <tr>
                <td>${c.id}</td>
                <td><strong>${c.nome}</strong></td>
                <td>${c.indirizzo || '-'}</td>
                <td>${c.referente || '-'}</td>
                <td>${c.data_inizio || '-'}</td>
                <td>${c.data_fine || '-'}</td>
                <td>${c.giorni_lavoro && c.giorni_lavoro.trim() !== "" ? c.giorni_lavoro : '-'}</td>
                <td><span class="badge bg-primary">${c.stato}</span></td>
                <td><span class="badge bg-info">${c.numero_operai || 0}</span></td>
                <td class="text-center">${linkMappa}</td>

                <td class="text-nowrap">
                    <button class="btn btn-warning btn-sm me-1" onclick="apriModificaCantiere(${c.id})">
                        <i class="fa-solid fa-pen"></i>
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="deleteCantiere(${c.id})">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
            </tr>`;
    });
}

// --- FILTRI ---
function applicaFiltri() {
    const nome      = document.getElementById("filtroNome").value.toLowerCase();
    const indirizzo = document.getElementById("filtroIndirizzo").value.toLowerCase();
    const referente = document.getElementById("filtroReferente").value.toLowerCase();

    const filtrati = cantieriGlobali.filter(c =>
        c.nome.toLowerCase().includes(nome) &&
        (c.indirizzo || '').toLowerCase().includes(indirizzo) &&
        (c.referente || '').toLowerCase().includes(referente)
    );

    mostraCantieri(filtrati);
}

document.getElementById("filtroNome").addEventListener("input", applicaFiltri);
document.getElementById("filtroIndirizzo").addEventListener("input", applicaFiltri);
document.getElementById("filtroReferente").addEventListener("input", applicaFiltri);

// --- TAB SWITCH ---
document.querySelectorAll("#cantiereTabs .nav-link").forEach(btn => {
    btn.addEventListener("click", () => {
        document.querySelectorAll("#cantiereTabs .nav-link")
            .forEach(b => b.classList.remove("active"));

        btn.classList.add("active");

        document.querySelectorAll(".tab-pane")
            .forEach(p => p.classList.remove("active"));

        const tab = btn.getAttribute("data-tab");
        document.getElementById("tab-" + tab).classList.add("active");
    });
});

// --- APRI MODALE NUOVO ---
document.getElementById("openModalBtn").addEventListener("click", () => {
    cantiereForm.reset();
    document.getElementById("cantiere_id").value = "";
    document.getElementById("cantiereModalTitle").innerText = "Nuovo Cantiere";

    document.querySelector("#cantiereTabs .nav-link.active").classList.remove("active");
    document.querySelector("#cantiereTabs .nav-link[data-tab='dati']").classList.add("active");

    document.querySelectorAll(".tab-pane").forEach(p => p.classList.remove("active"));
    document.getElementById("tab-dati").classList.add("active");

    cantiereModal.classList.add("show");
});

// --- APRI MODALE MODIFICA ---
function apriModificaCantiere(id) {
    const c = cantieriGlobali.find(x => x.id == id);
    if (!c) return;

    document.getElementById("cantiere_id").value = c.id;
    document.getElementById("nome_cantiere").value = c.nome;
    document.getElementById("indirizzo").value = c.indirizzo;
    document.getElementById("referente").value = c.referente;
    document.getElementById("data_inizio").value = c.data_inizio;
    document.getElementById("data_fine").value = c.data_fine;
    document.getElementById("note_cantiere").value = c.note;
    document.getElementById("lat").value = c.lat;
    document.getElementById("lng").value = c.lng;
    document.getElementById("stato_cantiere").value = c.stato;
    document.getElementById("coordinatore_sicurezza").value = c.coordinatore_sicurezza || '';
    document.getElementById("piano_sicurezza").value = c.piano_sicurezza || '';

    document.querySelectorAll('#giorniBox input[type="checkbox"]').forEach(cb => {
        cb.checked = false;
    });
    
    if (c.giorni_lavoro) {
        c.giorni_lavoro.split(',').forEach(g => {
            const checkbox = document.querySelector(`#giorniBox input[value="${g.trim()}"]`);
            if (checkbox) checkbox.checked = true;
        });
    }

    document.getElementById("cantiereModalTitle").innerText = "Modifica Cantiere: " + c.nome;
    
    // Carica operai e mezzi
    caricaOperaiCantiere(id);
    caricaMezziCantiere(id);

    cantiereModal.classList.add("show");
}

// --- OPERAI CANTIERE ---
async function caricaOperaiCantiere(id_cantiere) {
    try {
        const res = await fetch(`api/get_assegnazioni_operai_cantiere.php?id_cantiere=${id_cantiere}`);
        const data = await res.json();
        
        const tbody = document.getElementById('operaiBody');
        tbody.innerHTML = '';
        
        if (!data.success || data.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Nessun operaio</td></tr>';
            return;
        }
        
        data.data.forEach(op => {
            const inizio = new Date(op.data_inizio).toLocaleDateString('it-IT');
            const fine = op.data_fine ? new Date(op.data_fine).toLocaleDateString('it-IT') : '-';
            
            tbody.innerHTML += `
                <tr>
                    <td>${op.nome_dipendente}</td>
                    <td>${op.cognome_dipendente || '-'}</td>
                    <td>${op.ruolo_cantiere || '-'}</td>
                    <td>${inizio}</td>
                    <td>${fine}</td>
                    <td>${op.ore_previste || '-'}</td>
                    <td>
                        <button class="btn btn-xs btn-danger" onclick="eliminaOperaioCantiere(${op.id})">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    } catch (error) {
        console.error("Errore:", error);
    }
}

document.getElementById('aggiungiOperaio').addEventListener('click', function() {
    document.getElementById('modaleOperaio').style.display = 'block';
});

function chiudiModaleOperaio() {
    document.getElementById('modaleOperaio').style.display = 'none';
    document.getElementById('formOperaio').reset();
}

document.getElementById('formOperaio').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const idCantiere = document.getElementById('cantiere_id').value;
    const idDip = document.getElementById('selezionaOperaio').value;
    
    if (!idDip) {
        alert('Seleziona un operaio');
        return;
    }
    
    const payload = {
        id_dipendente: idDip,
        id_cantiere: idCantiere,
        ruolo_cantiere: document.getElementById('ruoloOperaio').value,
        ore_previste: document.getElementById('oreOperaio').value || null,
        data_inizio: document.getElementById('dataInizioOp').value || null,
        data_fine: document.getElementById('dataFineOp').value || null
    };
    
    try {
        const res = await fetch('api/add_dipendente_cantiere.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(payload)
        });
        
        const result = await res.json();
        
        if (result.success) {
            chiudiModaleOperaio();
            caricaOperaiCantiere(idCantiere);
            alert('Operaio assegnato!');
        } else {
            alert('Errore: ' + result.error);
        }
    } catch (error) {
        console.error("Errore:", error);
    }
});

async function eliminaOperaioCantiere(id) {
    if (!confirm('Rimuovere operaio?')) return;
    
    const idCantiere = document.getElementById('cantiere_id').value;
    
    try {
        const res = await fetch('api/delete_dipendente_cantiere.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ id: id })
        });
        
        const result = await res.json();
        if (result.success) {
            caricaOperaiCantiere(idCantiere);
            alert('Operaio rimosso!');
        }
    } catch (error) {
        console.error("Errore:", error);
    }
}

// --- MEZZI CANTIERE ---
async function caricaMezziCantiere(id_cantiere) {
    try {
        const res = await fetch(`api/get_assegnazioni_mezzo_cantiere.php?id_mezzo=0&id_cantiere=${id_cantiere}`);
        const data = await res.json();
        
        const tbody = document.getElementById('mezziBody');
        tbody.innerHTML = '';
        
        if (!data.success || data.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Nessun mezzo</td></tr>';
            return;
        }
        
        data.data.forEach(m => {
            const inizio = new Date(m.ora_inizio).toLocaleDateString('it-IT');
            const fine = m.ora_fine ? new Date(m.ora_fine).toLocaleDateString('it-IT') : '-';
            const km = m.km_fine && m.km_inizio ? (m.km_fine - m.km_inizio) + ' km' : '-';
            
            tbody.innerHTML += `
                <tr>
                    <td>${m.nome_mezzo || '-'}</td>
                    <td>${m.targa || '-'}</td>
                    <td>${m.nome_dipendente ? m.nome_dipendente + ' ' + (m.cognome_dipendente || '') : '-'}</td>
                    <td>${inizio}</td>
                    <td>${fine}</td>
                    <td>${km}</td>
                    <td>
                        <button class="btn btn-xs btn-danger" onclick="eliminaMezzoCantiere(${m.id})">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    } catch (error) {
        console.error("Errore:", error);
    }
}

document.getElementById('aggiungiMezzo').addEventListener('click', function() {
    document.getElementById('modaleMezzo').style.display = 'block';
});

function chiudiModaleMezzo() {
    document.getElementById('modaleMezzo').style.display = 'none';
    document.getElementById('formMezzo').reset();
}

document.getElementById('formMezzo').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const idCantiere = document.getElementById('cantiere_id').value;
    const idMezzo = document.getElementById('selezionaMezzo').value;
    
    if (!idMezzo) {
        alert('Seleziona un mezzo');
        return;
    }
    
    const payload = {
        id_mezzo: idMezzo,
        id_cantiere: idCantiere,
        id_dipendente: document.getElementById('selezionaOperatoreMezzo').value || null,
        ora_inizio: document.getElementById('dataInizioMezzo').value || new Date().toISOString().slice(0, 16),
        km_inizio: document.getElementById('kmInizioMezzo').value || null,
        note: document.getElementById('noteMezzo').value || null
    };
    
    try {
        const res = await fetch('api/add_assegnazioni_mezzo_cantiere.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(payload)
        });
        
        const result = await res.json();
        
        if (result.success) {
            chiudiModaleMezzo();
            caricaMezziCantiere(idCantiere);
            alert('Mezzo assegnato!');
        } else {
            alert('Errore: ' + result.error);
        }
    } catch (error) {
        console.error("Errore:", error);
    }
});

async function eliminaMezzoCantiere(id) {
    if (!confirm('Rimuovere mezzo?')) return;
    
    const idCantiere = document.getElementById('cantiere_id').value;
    
    try {
        const res = await fetch('api/delete_mezzo_cantiere.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ id: id })
        });
        
        const result = await res.json();
        if (result.success) {
            caricaMezziCantiere(idCantiere);
            alert('Mezzo rimosso!');
        }
    } catch (error) {
        console.error("Errore:", error);
    }
}

// --- CHIUDI MODALE ---
document.getElementById("closeCantiereModal").onclick =
document.getElementById("cancelCantiereModal").onclick = () => {
    cantiereModal.classList.remove("show");
    document.getElementById('modaleOperaio').style.display = 'none';
    document.getElementById('modaleMezzo').style.display = 'none';
};

// --- SALVA CANTIERE ---
cantiereForm.onsubmit = async (e) => {
    e.preventDefault();

    const giorniSelezionati = [...document.querySelectorAll("#giorniBox input:checked")]
        .map(cb => cb.value)
        .join(",");

    const payload = {
        id: document.getElementById("cantiere_id").value,
        nome: document.getElementById("nome_cantiere").value,
        indirizzo: document.getElementById("indirizzo").value,
        referente: document.getElementById("referente").value,
        giorni_lavoro: giorniSelezionati,
        data_inizio: document.getElementById("data_inizio").value,
        data_fine: document.getElementById("data_fine").value,
        note: document.getElementById("note_cantiere").value,
        lat: document.getElementById("lat").value,
        lng: document.getElementById("lng").value,
        stato: document.getElementById("stato_cantiere").value,
        coordinatore_sicurezza: document.getElementById("coordinatore_sicurezza").value,
        piano_sicurezza: document.getElementById("piano_sicurezza").value
    };

    const url = payload.id ? "api/update_cantiere.php" : "api/add_cantiere.php";

    const res = await fetch(url, {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify(payload)
    });

    const result = await res.json();

   if (result.success) {
    cantiereModal.classList.remove("show");
    caricaCantieri();
} else {
    // Mostra error o message se error non esiste
    alert("Errore: " + (result.error || result.message || "Errore sconosciuto"));
}
};

// --- ELIMINA CANTIERE ---
function deleteCantiere(id) {
    if (!confirm("Sei sicuro di voler eliminare questo cantiere?")) return;

    fetch('api/delete_cantiere.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            caricaCantieri();
        } else {
            alert("Errore durante l'eliminazione");
        }
    })
    .catch(err => console.error("Errore:", err));
}

// --- EXPORT CSV ---
function exportCantieri(format) {
    if (format === 'csv') {
        let csv = "ID,Nome,Indirizzo,Referente,Data Inizio,Data Fine,Stato\n";
        cantieriGlobali.forEach(c => {
            csv += `${c.id},"${c.nome}","${c.indirizzo}","${c.referente}","${c.data_inizio}","${c.data_fine}","${c.stato}"\n`;
        });
        
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'cantieri_' + new Date().toISOString().slice(0,10) + '.csv';
        a.click();
    }
}

// --- AVVIO ---
document.addEventListener("DOMContentLoaded", () => {
    caricaCantieri();
    caricaDipendenti();
    caricaMezzi();
});
</script>


<?php include 'includes/footer.php'; ?>