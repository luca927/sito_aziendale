// assets/js/app.js
document.addEventListener('DOMContentLoaded', () => {
  const tblBody = document.querySelector('#tbl tbody');
  const pagination = document.getElementById('pagination');
  const searchInput = document.getElementById('search');
  const limitSelect = document.getElementById('limit');
  const downloadBtn = document.getElementById('downloadReport');

  let page = 1;

  // Funzione principale per caricare i dati nella tabella
  function fetchData() {
    const limit = parseInt(limitSelect.value, 10);
    const offset = (page - 1) * limit;
    const s = encodeURIComponent(searchInput.value.trim());

    fetch(`api/get_dashboard_data.php?limit=${limit}&offset=${offset}&search=${s}`)
      .then((r) => r.json())
      .then((json) => {
        tblBody.innerHTML = '';
        json.data.forEach((row) => {
          let dataFormattata = 'N/D';
          if (row.data_registrazione) {
            const dataObj = new Date(row.data_registrazione);
            const giorno = String(dataObj.getDate()).padStart(2, '0');
            const mese = String(dataObj.getMonth() + 1).padStart(2, '0');
            const anno = dataObj.getFullYear();
            dataFormattata = `${giorno}/${mese}/${anno}`;
          }

          const tr = document.createElement('tr');
          tr.innerHTML = `
          <td>${escapeHtml(row.id)}</td>
          <td>${escapeHtml(row.dipendente_nome || 'Sconosciuto')}</td>
          <td>${escapeHtml(row.cantiere_nome || 'Sconosciuto')}</td>
          <td>${escapeHtml(row.mezzo_identificativo || 'Nessuno')}</td>
          <td>${escapeHtml(row.latitudine || 'N/D')}</td>
          <td>${escapeHtml(row.longitudine || 'N/D')}</td>
          <td>${escapeHtml(dataFormattata)}</td>
          `;
          tblBody.appendChild(tr);
        });
        renderPagination(json.total, limit);
      })
      .catch((e) => console.error('Errore nel caricamento dati:', e));
  }

  // Funzione per creare i pulsanti di paginazione
  function renderPagination(total, limit) {
    const pages = Math.max(1, Math.ceil(total / limit));
    pagination.innerHTML = '';
    for (let i = 1; i <= pages; i++) {
      const li = document.createElement('li');
      li.className = 'page-item' + (i === page ? ' active' : '');
      li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
      li.addEventListener('click', (ev) => {
        ev.preventDefault();
        page = i;
        fetchData();
      });
      pagination.appendChild(li);
    }
  }

  // Escape per evitare XSS nelle celle della tabella
  function escapeHtml(str) {
    if (!str) return '';
    return str
      .toString()
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  // Event listeners
  searchInput.addEventListener('keyup', () => {
    page = 1;
    fetchData();
  });

  limitSelect.addEventListener('change', () => {
    page = 1;
    fetchData();
  });

  downloadBtn.addEventListener('click', (e) => {
    e.preventDefault();
    const s = encodeURIComponent(searchInput.value.trim());
    window.location.href = `api/export_report.php?search=${s}`;
  });

  // Caricamento iniziale
  fetchData();
});

document.getElementById('menuSearch').addEventListener('input', function(e) {
    let filter = e.target.value.toLowerCase();
    let menuItems = document.querySelectorAll('#sidebar .nav-item');

    menuItems.forEach(item => {
        let text = item.innerText.toLowerCase();
        if (text.includes(filter)) {
            item.style.display = "block";
            // Se cerchiamo una sottovoce, espandiamo il padre
            let collapse = item.querySelector('.collapse');
            if(collapse && filter !== "") {
                let bsCollapse = new bootstrap.Collapse(collapse, { show: true });
            }
        } else {
            item.style.display = "none";
        }
    });
});