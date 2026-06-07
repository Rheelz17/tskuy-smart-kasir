/* ============================================================
   menu-kasir.js — Logika Manajemen Menu untuk Kasir
   Fitur:
   1. Toggle status tersedia (AJAX ke /menu/{id}/toggle-status  PATCH)
   2. Filter tab kategori
   3. Search real-time
   4. Paginasi dinamis
   Route: PATCH /menu/{id}/toggle-status → AdminMenuController@toggleStatus
============================================================ */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    //  CONFIG
    // ============================================================
    const ITEMS_PER_PAGE = 10;
    let   currentPage    = 1;

    const csrfToken = () =>
        document.querySelector('meta[name="csrf-token"]')?.content || '';

    // ============================================================
    //  TOAST NOTIFIKASI
    // ============================================================
    function showToast(msg, isError = false) {
        const toast = document.getElementById('menu-toast');
        if (!toast) return;
        toast.textContent      = msg;
        toast.style.background = isError ? '#ef4444' : '#22c55e';
        toast.classList.add('show');
        clearTimeout(toast._t);
        toast._t = setTimeout(() => toast.classList.remove('show'), 2800);
    }

    // ============================================================
    //  1. TOGGLE STATUS TERSEDIA — AJAX PATCH
    //     Route: PATCH /menu/{id}/toggle-status
    //     Body:  { is_available: 0 | 1 }
    // ============================================================
    document.getElementById('tabel-menu')?.addEventListener('change', function (e) {
        const toggle = e.target.closest('.toggle-status-menu');
        if (!toggle) return;

        const menuId    = toggle.dataset.id;
        const namaMenu  = toggle.dataset.nama || `Menu #${menuId}`;
        const isChecked = toggle.checked;
        const prevState = !isChecked; // untuk rollback jika gagal

        fetch(`/menu/${menuId}/toggle-status`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN':  csrfToken(),
                'Content-Type':  'application/json',
                'Accept':        'application/json',
            },
            body: JSON.stringify({ is_available: isChecked ? 1 : 0 }),
        })
        .then(res => {
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            return res.json();
        })
        .then(data => {
            if (data.success) {
                const statusTeks = isChecked ? 'Tersedia ✓' : 'Tidak Tersedia';
                showToast(`${namaMenu} → ${statusTeks}`);
            } else {
                toggle.checked = prevState; // rollback
                showToast(data.message || 'Gagal mengubah status.', true);
            }
        })
        .catch(err => {
            console.error('Toggle error:', err);
            toggle.checked = prevState; // rollback
            showToast('Gagal koneksi ke server.', true);
        });
    });

    // ============================================================
    //  2. FILTER TAB KATEGORI
    // ============================================================
    document.querySelectorAll('.category-tabs .tab').forEach(tab => {
        tab.addEventListener('click', function () {
            document.querySelectorAll('.category-tabs .tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            currentPage = 1;
            renderTable();
        });
    });

    // ============================================================
    //  3. SEARCH REAL-TIME
    // ============================================================
    document.getElementById('search-menu-input')?.addEventListener('input', function () {
        currentPage = 1;
        renderTable();
    });

    // ============================================================
    //  4. RENDER TABEL + FILTER + PAGINASI
    // ============================================================
    function getFilteredRows() {
        const activeTab = document.querySelector('.category-tabs .tab.active');
        const kategori  = activeTab?.dataset.kategori || 'all';
        const keyword   = (document.getElementById('search-menu-input')?.value || '')
                            .toLowerCase().trim();

        const allRows = Array.from(
            document.querySelectorAll('#menu-tbody tr[data-id]')
        );

        return allRows.filter(row => {
            const rowKat  = row.dataset.kategori || '';
            const rowNama = row.dataset.nama || '';

            const matchKat  = kategori === 'all' || rowKat === kategori;
            const matchCari = keyword === '' || rowNama.includes(keyword) ||
                              row.textContent.toLowerCase().includes(keyword);

            return matchKat && matchCari;
        });
    }

    function renderTable() {
        const filtered  = getFilteredRows();
        const total     = filtered.length;
        const totalPage = Math.ceil(total / ITEMS_PER_PAGE) || 1;

        if (currentPage > totalPage) currentPage = totalPage;

        const startIdx = (currentPage - 1) * ITEMS_PER_PAGE;
        const endIdx   = startIdx + ITEMS_PER_PAGE;

        // Sembunyikan SEMUA baris dulu
        document.querySelectorAll('#menu-tbody tr').forEach(r => r.style.display = 'none');

        // Tampilkan hanya yang masuk halaman ini
        filtered.forEach((row, i) => {
            row.style.display = (i >= startIdx && i < endIdx) ? '' : 'none';
        });

        // Update info label
        const infoEl = document.getElementById('data-info-label');
        if (infoEl) {
            const dari  = total === 0 ? 0 : startIdx + 1;
            const ke    = Math.min(endIdx, total);
            infoEl.textContent = total === 0
                ? 'Tidak ada menu yang cocok'
                : `Menampilkan ${dari}–${ke} dari ${total} menu`;
        }

        // Render tombol paginasi
        renderPagination(totalPage);
    }

    function renderPagination(totalPage) {
        const container = document.getElementById('pagination-container');
        if (!container) return;
        container.innerHTML = '';

        // Tombol Sebelumnya
        const prev = document.createElement('button');
        prev.className   = 'page-link';
        prev.textContent = 'Sebelumnya';
        prev.disabled    = currentPage === 1;
        prev.addEventListener('click', () => {
            if (currentPage > 1) { currentPage--; renderTable(); }
        });
        container.appendChild(prev);

        // Angka halaman — tampilkan maksimal 5 angka di sekitar currentPage
        const range = pagRange(currentPage, totalPage, 5);
        range.forEach(page => {
            if (page === '...') {
                const dots = document.createElement('span');
                dots.textContent = '…';
                dots.style.cssText = 'padding:0 4px; line-height:32px; color:#aaa; font-size:12px;';
                container.appendChild(dots);
            } else {
                const btn = document.createElement('button');
                btn.className   = `page-number${page === currentPage ? ' active' : ''}`;
                btn.textContent = page;
                btn.addEventListener('click', () => { currentPage = page; renderTable(); });
                container.appendChild(btn);
            }
        });

        // Tombol Selanjutnya
        const next = document.createElement('button');
        next.className   = 'page-link';
        next.textContent = 'Selanjutnya';
        next.disabled    = currentPage === totalPage;
        next.addEventListener('click', () => {
            if (currentPage < totalPage) { currentPage++; renderTable(); }
        });
        container.appendChild(next);
    }

    // Helper: hasilkan array nomor halaman dengan ellipsis
    function pagRange(current, total, maxVisible) {
        if (total <= maxVisible) return Array.from({length: total}, (_, i) => i + 1);

        const half = Math.floor(maxVisible / 2);
        let start  = Math.max(2, current - half);
        let end    = Math.min(total - 1, current + half);

        if (current - half <= 1) end = Math.min(total - 1, maxVisible - 1);
        if (current + half >= total) start = Math.max(2, total - maxVisible + 2);

        const pages = [1];
        if (start > 2) pages.push('...');
        for (let i = start; i <= end; i++) pages.push(i);
        if (end < total - 1) pages.push('...');
        pages.push(total);
        return pages;
    }

    // Jalankan render pertama kali
    renderTable();
});