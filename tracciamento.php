<?php
require_once __DIR__ . '/backend/auth.php';
require_once __DIR__ . '/includes/auth_admin.php';
include 'includes/header.php';


function calcolaDistanza($lat1, $lon1, $lat2, $lon2) {
    $earth_radius = 6371000; // Raggio della terra in metri
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    return $earth_radius * $c; // Distanza in metri
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        background: #f4f6f9;
        margin: 0;
    }

    header {
        background: #0056D2;
        color: white;
        padding: 15px 25px;
        font-size: 1.4rem;
        font-weight: bold;
        margin-bottom: 20px;
    }

    .header-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    h2 {
        color: #0056D2;
        margin: 0;
    }

    button {
        background: #0056D2;
        color: white;
        border: none;
        padding: 10px 18px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: bold;
        transition: .2s;
    }

    button:hover {
        background: #003f9e;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        animation: fadeIn 0.3s ease;
    }
    .modal.show {
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .modal-content {
        background-color: #fff;
        padding: 30px;
        border-radius: 12px;
        width: 90%;
        max-width: 700px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        animation: slideDown 0.3s ease;
    }
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }
    .modal-header h3 {
        color: #0056D2;
        margin: 0;
    }
    .close-btn {
        background: none;
        color: #999;
        font-size: 28px;
        padding: 0;
        width: 30px;
        height: 30px;
        cursor: pointer;
        border: none;
    }
    .close-btn:hover {
        color: #333;
    }
    .btn-cancel {
        background: #6c757d;
    }
    .btn-cancel:hover {
        background: #5a6268;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes slideDown {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
</style>

<div class="layout">
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <header>📍 Gestione Tracciamenti</header>

        <div class="header-actions">
            <h2>Registro Attività</h2>
            <button id="openModalBtn">➕ Aggiungi Tracciamento</button>
        </div>

        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Dipendente</th>
                    <th>Cantiere</th>
                    <th>Mezzo</th>
                    <th>Latitudine</th>
                    <th>Longitudine</th>
                    <th>Data/Ora</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody id="tracciamentiTable"></tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Nuovo Tracciamento</h3>
            <button class="close-btn" id="closeModalBtn">&times;</button>
        </div>
        
        <form id="addForm" class="row g-3">
            <div class="col-md-12">
                <label class="form-label">Dipendente *</label>
                <select id="dipendente_id" class="form-control" required>
                    <option value="">Seleziona dipendente</option>
                </select>
            </div>

            <div class="col-md-12">
                <label class="form-label">Cantiere *</label>
                <select id="cantiere_id" class="form-control" required>
                    <option value="">Seleziona cantiere</option>
                </select>
            </div>

            <div class="col-md-12">
                <label class="form-label">Mezzo</label>
                <select id="mezzo_id" class="form-control">
                    <option value="">Seleziona mezzo</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Latitudine</label>
            </div>

            <div class="col-md-6">
                <label class="form-label">Longitudine</label>
            </div>

            <div class="col-md-12">
                <label class="form-label">Data e Ora *</label>
                <input type="datetime-local" id="data_attivita" class="form-control" required>
            </div>

            <div class="col-12 text-end">
                <button type="button" class="btn-cancel" id="cancelBtn">Annulla</button>
                <button type="submit" class="ms-2">✓ Salva Tracciamento</button>
            </div>
        </form>
    </div>
</div>

<script>
const modal = document.getElementById('addModal');
const openBtn = document.getElementById('openModalBtn');
const closeBtn = document.getElementById('closeModalBtn');
const cancelBtn = document.getElementById('cancelBtn');

// Apri/Chiudi modal
openBtn.addEventListener('click', () => modal.classList.add('show'));
closeBtn.addEventListener('click', () => modal.classList.remove('show'));
cancelBtn.addEventListener('click', () => modal.classList.remove('show'));
modal.addEventListener('click', (e) => {
    if (e.target === modal) modal.classList.remove('show');
});

// Carica dropdown
async function caricaDropdown() {
    try {
        // --- DIPENDENTI ---
        const dipRes = await fetch('api/get_dipendenti.php');
        const dipJson = await dipRes.json();
        const dipendenti = dipJson.data || dipJson || [];

        const dipSelect = document.getElementById('dipendente_id');
        dipSelect.innerHTML = '<option value="">Seleziona dipendente</option>';

        dipendenti.forEach(d => {
            dipSelect.innerHTML += `<option value="${d.id}">${d.nome} ${d.cognome}</option>`;
        });

        // --- CANTIERI ---
        const cantRes = await fetch('api/get_cantieri.php');
        const cantJson = await cantRes.json();
        const cantieri = cantJson.data || cantJson || [];

        const cantSelect = document.getElementById('cantiere_id');
        cantSelect.innerHTML = '<option value="">Seleziona cantiere</option>';

        cantieri.forEach(c => {
            cantSelect.innerHTML += `<option value="${c.id}">${c.nome}</option>`;
        });

        // --- MEZZI ---
        const mezziRes = await fetch('api/get_mezzi.php');
        const mezziJson = await mezziRes.json();
        const mezzi = mezziJson.data || mezziJson || [];

        const mezziSelect = document.getElementById('mezzo_id');
        mezziSelect.innerHTML = '<option value="">Seleziona mezzo</option>';

        mezzi.forEach(m => {
            mezziSelect.innerHTML += `<option value="${m.id}">${m.nome_mezzo}</option>`;
        });
    } catch (error) {
        console.error('Errore caricamento dropdown:', error);
        alert('Errore nel caricamento dei dati. Verifica la connessione.');
    }
}

// Carica tracciamenti - USA IL NUOVO ENDPOINT
async function caricaTracciamenti() {
    try {
        // ⚠️ IMPORTANTE: Usa get_tracciamenti.php invece di dati_combinati.php
        const res = await fetch('api/get_tracciamenti.php?t=' + Date.now());
        const response = await res.json();
        
        console.log('✅ Risposta get_tracciamenti.php:', response);
        
        if (!response.success) {
            throw new Error(response.error || 'Errore API');
        }

        const data = response.data || [];
        const tbody = document.getElementById('tracciamentiTable');
        tbody.innerHTML = '';

        if (data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center">Nessun tracciamento registrato</td></tr>';
            return;
        }

        console.log(`📊 Caricati ${data.length} tracciamenti`);

        // I dati sono già ordinati per ID DESC dalla query SQL
        data.forEach(t => {
            const dataFormatted = t.data_attivita || t.data || '-';
            tbody.innerHTML += `
                <tr>
                    <td>${t.id}</td>
                    <td>${t.dipendente || '-'}</td>
                    <td>${t.cantiere || '-'}</td>
                    <td>${t.mezzo || 'Nessuno'}</td>
                    <td>${t.lat || t.latitudine || '-'}</td>
                    <td>${t.lng || t.longitudine || '-'}</td>
                    <td>${dataFormatted}</td>
                    <td>
                        <button class="btn btn-sm btn-danger" onclick="elimina(${t.id})">🗑️</button>
                    </td>
                </tr>
            `;
        });
    } catch (error) {
        console.error('❌ Errore caricamento tracciamenti:', error);
        document.getElementById('tracciamentiTable').innerHTML = 
            '<tr><td colspan="8" class="text-center text-danger">Errore nel caricamento: ' + error.message + '</td></tr>';
    }
}

// Submit form
document.getElementById('addForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const payload = {
        dipendente_id: document.getElementById('dipendente_id').value,
        cantiere_id: document.getElementById('cantiere_id').value,
        mezzo_id: document.getElementById('mezzo_id').value || null,
        data_attivita: document.getElementById('data_attivita').value
    };

    console.log('📤 Invio payload:', payload);

    try {
        const res = await fetch('api/add_tracciamento.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const data = await res.json();
        console.log('📥 Risposta add_tracciamento:', data);

        if (data.success) {
            alert('✅ Tracciamento aggiunto con successo!');
            document.getElementById('addForm').reset();
            modal.classList.remove('show');
            
            // Imposta di nuovo la data corrente dopo il reset
            document.getElementById('data_attivita').value = new Date().toISOString().slice(0, 16);
            
            // Ricarica i tracciamenti
            await caricaTracciamenti();
        } else {
            alert('❌ Errore: ' + (data.error || 'Errore sconosciuto'));
            console.error('Dettagli errore:', data);
        }
    } catch (error) {
        console.error('❌ Errore durante l\'invio:', error);
        alert('Errore di connessione. Riprova.');
    }
});

async function elimina(id) {
    if (!confirm('Eliminare questo tracciamento?')) return;

    try {
        const res = await fetch('api/delete_tracciamento.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        });

        const data = await res.json();
        
        if (data.success) {
            alert('✅ Tracciamento eliminato');
            await caricaTracciamenti();
        } else {
            alert('❌ Errore eliminazione: ' + (data.error || 'Errore sconosciuto'));
        }
    } catch (error) {
        console.error('❌ Errore eliminazione:', error);
        alert('Errore di connessione. Riprova.');
    }
}

// Imposta data/ora corrente di default
document.getElementById('data_attivita').value = new Date().toISOString().slice(0, 16);

// Inizializza
console.log('🚀 Inizializzazione pagina tracciamenti...');
caricaDropdown();
caricaTracciamenti();
</script>

<?php include 'includes/footer.php'; ?>