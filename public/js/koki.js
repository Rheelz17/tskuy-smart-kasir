/**
 * koki.js — Kitchen Console Logic
 * ════════════════════════════════════════════════════════════════
 * Fitur:
 *   1. Live clock (detik)
 *   2. Timer per kartu — update setiap 10 detik
 *   3. Auto-detect urgent: elapsed >= 20 mnt → tambah class .urgent
 *   4. Counter badge filter (Semua / Menunggu / Selesai) — dinamis
 *   5. Filter tab: show/hide kartu + empty state
 *   6. Modal detail: buka, render item, toggle ✓ per item
 *   7. Progress bar realtime di dalam modal
 *   8. Tombol "Selesaikan Pesanan" aktif hanya saat semua item ✓
 *   9. Success modal + "Kembali ke Antrian" → ubah status kartu
 *  10. AJAX ke server untuk update status DB
 *  11. Toast notifikasi
 * ════════════════════════════════════════════════════════════════
 */

/* ─── Konstanta ──────────────────────────────────────────────── */
const URGENT_MS   = 20 * 60 * 1000;   // 20 menit dalam milidetik
const TIMER_TICK  = 10 * 1000;         // update timer setiap 10 detik

/* ─── State ──────────────────────────────────────────────────── */
let activeOrderId = null;
let itemStates    = {};   // { itemIndex: true | false }

/* ─── DOM Shortcuts ──────────────────────────────────────────── */
const overlay      = document.getElementById('modalOverlay');
const detailModal  = document.getElementById('detailModal');
const successModal = document.getElementById('successModal');
const modalTitle   = document.getElementById('modalTitle');
const modalInfo    = document.getElementById('modalInfoRow');
const itemsWrap    = document.getElementById('modalItemsWrap');
const progDone     = document.getElementById('progDone');
const progTotal    = document.getElementById('progTotal');
const progFill     = document.getElementById('progFill');
const btnComplete  = document.getElementById('btnComplete');
const btnLabel     = document.getElementById('btnCompleteLabel');
const successDetail = document.getElementById('successDetail');
const toastWrap    = document.getElementById('toastWrap');
const clock        = document.getElementById('liveClock');

/* ══════════════════════════════════════════════════════════════
   1. LIVE CLOCK
══════════════════════════════════════════════════════════════ */
function startClock() {
    function tick() {
        const d = new Date();
        clock.textContent = [d.getHours(), d.getMinutes(), d.getSeconds()]
            .map(n => String(n).padStart(2, '0')).join(':');
    }
    tick();
    setInterval(tick, 1000);
}

/* ══════════════════════════════════════════════════════════════
   2. TIMER PER KARTU + DETEKSI URGENT OTOMATIS
   Cek setiap 10 detik; kartu otomatis merah jika >= 20 menit.
══════════════════════════════════════════════════════════════ */
function startTimers() {
    const pendingCards = Array.from(
        document.querySelectorAll('.order-card[data-status="pending"]')
    );
    if (!pendingCards.length) return;

    function update() {
        const now = Date.now();
        pendingCards.forEach(card => {
            const createdAt  = new Date(card.dataset.createdAt).getTime();
            const elapsed    = now - createdAt;
            const elapsedMin = Math.floor(elapsed / 60000);

            // Perbarui teks timer di kartu
            const timerVal = card.querySelector('.timer-val');
            if (timerVal) timerVal.textContent = `${elapsedMin} mnt`;

            // Cek urgent: >= 20 menit → tambah class .urgent
            const wasUrgent = card.classList.contains('urgent');
            const isUrgent  = elapsed >= URGENT_MS;

            if (isUrgent && !wasUrgent) {
                card.classList.add('urgent');

                // Update status pill jadi merah
                const pill = card.querySelector('[data-status-pill]');
                if (pill) {
                    pill.classList.remove('status-pending');
                    pill.classList.add('status-urgent');
                    pill.textContent = '⚠ >20 mnt';
                }

                updateCounters();
                showToast(`⚠ Pesanan #${card.dataset.orderId} sudah ${elapsedMin} menit menunggu!`);
            }
        });
    }

    update();
    setInterval(update, TIMER_TICK);
}

/* ══════════════════════════════════════════════════════════════
   3. COUNTER BADGE FILTER — dihitung dari DOM, selalu akurat
══════════════════════════════════════════════════════════════ */
function updateCounters() {
    const allCards = document.querySelectorAll('.order-card');
    let semua = 0, menunggu = 0, selesai = 0;

    allCards.forEach(card => {
        semua++;
        const status = card.dataset.status;
        if (status === 'pending')   menunggu++;
        if (status === 'completed') selesai++;
    });

    const set = (id, val) => {
        const el = document.getElementById(id);
        if (el) el.textContent = val;
    };

    set('badge-semua',    semua);
    set('badge-menunggu', menunggu);
    set('badge-selesai',  selesai);
}

/* ══════════════════════════════════════════════════════════════
   4. FILTER TAB — tampilkan/sembunyikan kartu
══════════════════════════════════════════════════════════════ */
function applyFilter(filter) {
    const cards = document.querySelectorAll('.order-card');

    cards.forEach(card => {
        const status = card.dataset.status;
        let show;

        switch (filter) {
            case 'semua':    show = true; break;
            case 'menunggu': show = status === 'pending'; break;
            case 'selesai':  show = status === 'completed'; break;
            default:         show = true;
        }

        card.style.display = show ? '' : 'none';
    });

    // Empty state jika tidak ada kartu yang tampil
    const grid    = document.getElementById('ordersGrid');
    const visible = [...cards].filter(c => c.style.display !== 'none').length;
    let emptyEl   = document.getElementById('jsEmptyState');

    if (!visible && !emptyEl) {
        emptyEl = document.createElement('div');
        emptyEl.id        = 'jsEmptyState';
        emptyEl.className = 'empty-state';
        emptyEl.style.gridColumn = '1/-1';
        emptyEl.innerHTML = `
            <div class="empty-icon">🍽️</div>
            <div class="empty-title">Tidak ada pesanan di kategori ini</div>
            <div class="empty-desc">Pesanan akan muncul di sini saat ada yang masuk.</div>
        `;
        grid.appendChild(emptyEl);
    } else if (visible && emptyEl) {
        emptyEl.remove();
    }
}

function initFilterTabs() {
    const tabs = document.querySelectorAll('.filter-tab');

    tabs.forEach(tab => {
        tab.addEventListener('click', (e) => {
            e.preventDefault();
            tabs.forEach(t => {
                t.classList.remove('active');
                t.setAttribute('aria-selected', 'false');
            });
            tab.classList.add('active');
            tab.setAttribute('aria-selected', 'true');
            applyFilter(tab.dataset.filter);
        });
    });

    // Default: terapkan filter "semua" saat load
    applyFilter('semua');
}

/* ══════════════════════════════════════════════════════════════
   5. BUKA DETAIL MODAL — render item dari data-items kartu
══════════════════════════════════════════════════════════════ */
function openDetailModal(card) {
    activeOrderId = card.dataset.orderId;

    const tipe    = card.dataset.tipe;
    const meja    = card.dataset.meja;
    const nota    = card.dataset.nota;
    const nama    = card.dataset.pelanggan;
    const items   = JSON.parse(card.dataset.items);

    // Reset state item (semua belum done)
    itemStates = {};
    items.forEach((_, i) => { itemStates[i] = false; });

    // Isi header modal
    const isDineIn    = tipe === 'Dine In';
    const identifier  = isDineIn
        ? `MEJA ${String(meja).padStart(2, '0')}`
        : `Order #${nota}`;

    modalTitle.textContent = identifier;

    const badgeClass = isDineIn ? 'badge-dine-in' : 'badge-takeaway';
    let infoHTML = `<span class="type-badge ${badgeClass}">${tipe}</span>`;
    if (isDineIn && meja) infoHTML += `<strong>Meja ${meja}</strong>`;
    infoHTML += `<span>👤 ${nama}</span>`;
    modalInfo.innerHTML = infoHTML;

    // Render daftar item
    renderModalItems(items);
    updateProgress(items.length);

    // Buka modal
    overlay.classList.add('active');
    detailModal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

/* ── Render baris item di dalam modal ─────────────────────── */
function renderModalItems(items) {
    itemsWrap.innerHTML = '';

    items.forEach((item, idx) => {
        const div       = document.createElement('div');
        div.className   = 'modal-item' + (itemStates[idx] ? ' done' : '');
        div.dataset.idx = idx;
        div.setAttribute('role', 'checkbox');
        div.setAttribute('aria-checked', itemStates[idx] ? 'true' : 'false');
        div.setAttribute('tabindex', '0');

        div.innerHTML = `
            <div class="item-check" aria-hidden="true">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                    <path d="M5 13l4 4L19 7" stroke="#fff" stroke-width="2.5"
                          stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="item-text">
                <div class="item-name">${item.nama}</div>
                ${item.catatan ? `<div class="item-note">📝 ${item.catatan}</div>` : ''}
            </div>
            <div class="item-qty">×${item.qty}</div>
        `;

        // Klik atau Enter untuk toggle
        div.addEventListener('click', () => toggleItem(idx, items.length));
        div.addEventListener('keydown', e => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                toggleItem(idx, items.length);
            }
        });

        itemsWrap.appendChild(div);
    });
}

/* ══════════════════════════════════════════════════════════════
   6. TOGGLE ITEM ✓ — koki centang satu per satu
══════════════════════════════════════════════════════════════ */
function toggleItem(idx, total) {
    itemStates[idx] = !itemStates[idx];

    const el = itemsWrap.querySelector(`[data-idx="${idx}"]`);
    if (!el) return;

    el.classList.toggle('done', itemStates[idx]);
    el.setAttribute('aria-checked', itemStates[idx] ? 'true' : 'false');

    updateProgress(total);
}

/* ── Update progress bar dan tombol selesai ───────────────── */
function updateProgress(total) {
    const done = Object.values(itemStates).filter(Boolean).length;
    const pct  = total > 0 ? (done / total * 100) : 0;

    progDone.textContent  = done;
    progTotal.textContent = total;
    progFill.style.width  = pct + '%';
    btnLabel.textContent  = `Selesaikan Pesanan (${done}/${total})`;

    const canFinish = done >= total && total > 0;
    btnComplete.disabled = !canFinish;
    btnComplete.classList.toggle('can-complete',  canFinish);
    btnComplete.classList.toggle('cant-complete', !canFinish);
}

/* ══════════════════════════════════════════════════════════════
   7. TUTUP DETAIL MODAL
══════════════════════════════════════════════════════════════ */
function closeDetailModal() {
    detailModal.classList.remove('active');
    overlay.classList.remove('active');
    document.body.style.overflow = '';
    activeOrderId = null;
}

/* ══════════════════════════════════════════════════════════════
   8. SELESAIKAN ORDER:
      a. Tutup detail modal
      b. Buka success modal
      c. Ubah status kartu di DOM
      d. Kirim AJAX ke server
      e. Perbarui counter filter
══════════════════════════════════════════════════════════════ */
function completeOrder() {
    const card = document.getElementById(`card-${activeOrderId}`);
    if (!card) return;

    // Ambil data untuk success modal
    const tipe    = card.dataset.tipe;
    const meja    = card.dataset.meja;
    const nota    = card.dataset.nota;
    const nama    = card.dataset.pelanggan;
    const items   = JSON.parse(card.dataset.items);
    const orderId = activeOrderId;
    const isDineIn = tipe === 'Dine In';

    // a. Tutup detail modal
    closeDetailModal();

    // b. Isi & buka success modal
    const identifier = isDineIn
        ? `MEJA ${String(meja).padStart(2, '0')}`
        : `Order #${nota}`;

    successDetail.innerHTML = `
        <div class="success-row"><span>Order</span><span>#${orderId}</span></div>
        <div class="success-row"><span>Tipe</span><span>${tipe}</span></div>
        ${isDineIn && meja ? `<div class="success-row"><span>Meja</span><span>Meja ${meja}</span></div>` : ''}
        <div class="success-row"><span>Pelanggan</span><span>${nama}</span></div>
        <div class="success-row"><span>Total Item</span><span>${items.length} jenis</span></div>
    `;

    overlay.classList.add('active');
    successModal.classList.add('active');
    document.body.style.overflow = 'hidden';

    showToast(`✅ Pesanan ${identifier} berhasil diselesaikan!`);

    // c. Ubah status kartu di DOM — dilakukan di btnBackToQueue
    //    (sesuai spesifikasi: saat klik "Kembali ke Antrian")
    //    Simpan orderId untuk dipakai saat kembali
    successModal.dataset.completedOrderId = orderId;

    // d. AJAX ke server Laravel
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    fetch(`/koki/${orderId}/selesaikan`, {
        method: 'POST',
        headers: {
            'Content-Type':     'application/json',
            'X-CSRF-TOKEN':     csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept':           'application/json',
        },
        body: JSON.stringify({ id: orderId }),
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) console.warn('Server: gagal update status', data);
    })
    .catch(err => console.error('AJAX error:', err));
}

/* ══════════════════════════════════════════════════════════════
   9. KEMBALI KE ANTRIAN — mutasi DOM + perbarui counter
══════════════════════════════════════════════════════════════ */
function returnToQueue() {
    const orderId = successModal.dataset.completedOrderId;

    if (orderId) {
        const card = document.getElementById(`card-${orderId}`);
        if (card) {
            // Ubah status kartu jadi completed
            card.dataset.status = 'completed';
            card.classList.remove('dine-in', 'takeaway', 'urgent');
            card.classList.add('completed');

            // Update status pill
            const pill = card.querySelector('[data-status-pill]');
            if (pill) {
                pill.className   = 'status-pill status-completed';
                pill.textContent = '✓ Selesai';
            }

            // Hapus chevron
            const chevron = card.querySelector('.card-chevron');
            if (chevron) chevron.remove();

            // Nonaktifkan klik pada kartu yang sudah selesai
            card.setAttribute('tabindex', '-1');
        }
    }

    // Tutup success modal
    successModal.classList.remove('active');
    overlay.classList.remove('active');
    document.body.style.overflow = '';

    // Perbarui counter badge
    updateCounters();

    // Terapkan ulang filter aktif (kartu selesai mungkin perlu disembunyikan)
    const activeTab = document.querySelector('.filter-tab.active');
    if (activeTab) applyFilter(activeTab.dataset.filter);
}

/* ══════════════════════════════════════════════════════════════
   10. TOAST NOTIFIKASI
══════════════════════════════════════════════════════════════ */
function showToast(msg) {
    if (!toastWrap) return;

    const toast     = document.createElement('div');
    toast.className = 'toast';
    toast.textContent = msg;
    toastWrap.prepend(toast);

    setTimeout(() => {
        toast.classList.add('out');
        toast.addEventListener('animationend', () => toast.remove(), { once: true });
    }, 4500);
}

/* ══════════════════════════════════════════════════════════════
   11. EVENT LISTENERS
══════════════════════════════════════════════════════════════ */
function initEvents() {
    // Klik kartu → buka modal (hanya jika bukan completed)
    document.querySelectorAll('.order-card').forEach(card => {
        card.addEventListener('click', () => {
            if (card.dataset.status === 'completed') return;
            openDetailModal(card);
        });
        card.addEventListener('keydown', e => {
            if ((e.key === 'Enter' || e.key === ' ') && card.dataset.status !== 'completed') {
                e.preventDefault();
                openDetailModal(card);
            }
        });
    });

    // Tutup detail modal (tombol X)
    document.getElementById('modalCloseBtn')
        .addEventListener('click', closeDetailModal);

    // Klik overlay → tutup modal yang aktif
    overlay.addEventListener('click', () => {
        if (detailModal.classList.contains('active'))  closeDetailModal();
        // Success modal TIDAK ditutup lewat overlay (harus pakai tombol)
    });

    // Tombol "Selesaikan Pesanan"
    btnComplete.addEventListener('click', () => {
        if (!btnComplete.disabled) completeOrder();
    });

    // Tombol "Kembali ke Antrian"
    document.getElementById('btnBackToQueue')
        .addEventListener('click', returnToQueue);

    // Escape key → tutup modal aktif
    document.addEventListener('keydown', e => {
        if (e.key !== 'Escape') return;
        if (detailModal.classList.contains('active'))  closeDetailModal();
        // Success modal tidak tutup via Escape (user harus klik tombol)
    });
}

/* ══════════════════════════════════════════════════════════════
   INIT — jalankan semua fungsi saat DOM siap
══════════════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
    startClock();       // ① Jam realtime
    startTimers();      // ② Timer + urgent detection
    updateCounters();   // ③ Counter badge awal
    initFilterTabs();   // ④ Setup filter tab
    initEvents();       // ⑤ Event listeners
});