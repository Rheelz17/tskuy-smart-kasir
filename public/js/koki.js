/**
 * koki.js — Kitchen Display System · Tskuy Smart Kasir
 * ════════════════════════════════════════════════════════════
 *
 * FITUR UTAMA:
 *   1.  Jam digital live
 *   2.  Timer waktu tunggu per kartu (Hijau / Kuning / Merah)
 *   3.  Filter tab  (Semua / Menunggu / Memasak / Siap Saji)
 *   4.  Modal detail pesanan dengan checklist item
 *   5.  STATE PERSISTENCE centang item (tidak reset saat modal ditutup)
 *       → Disimpan di orderItemCache (object global dalam-memory)
 *   6.  Tombol "Mulai Masak"   → POST /koki/{id}/mulai-masak
 *   7.  Tombol "Selesaikan"    → POST /koki/{id}/selesaikan
 *       → Hanya aktif jika SEMUA item sudah dicentang
 *   8.  Tombol "Batalkan"      → POST /koki/{id}/batalkan
 *   9.  Auto-refresh polling   → GET  /koki/api/orders (30 detik)
 *   10. Toast notification     → mengikuti struktur kasir
 *   11. Notif panel            → mengikuti struktur kasir
 *
 * ARSITEKTUR URL:
 *   URL di-hardcode langsung di fungsi helper (sesuai instruksi),
 *   TIDAK menggunakan KOKI_ROUTES dinamis agar tidak ada race-condition.
 *   window.ROUTES.csrfToken tetap dibaca untuk keamanan CSRF.
 * ════════════════════════════════════════════════════════════
 */

'use strict';

(function () {

  /* ══════════════════════════════════════════════════════════
     GLOBAL STATE
  ══════════════════════════════════════════════════════════ */

  let activeOrderId   = null;   // ID order yang sedang dibuka di popup
  let activeOrderData = null;   // Data order aktif (items, status, dll)
  let currentFilter   = 'semua';

  /**
   * orderItemCache — State persistence centang per item.
   * Format: { [orderId]: { [itemId_or_idx]: boolean } }
   *
   * Saat modal ditutup dan dibuka kembali, status centang
   * TIDAK hilang — dibaca dari cache ini.
   */
  const orderItemCache = {};

  /* ══════════════════════════════════════════════════════════
     THRESHOLD WAKTU (detik)
  ══════════════════════════════════════════════════════════ */
  const T_FRESH   = 5  * 60;    // 0–5 mnt   → hijau
  const T_WARN    = 15 * 60;    // 5–15 mnt  → kuning
  // > 15 mnt → merah

  /* ══════════════════════════════════════════════════════════
     HELPER URL — hardcoded, no ROUTES object needed
  ══════════════════════════════════════════════════════════ */
  const url = {
    mulaiMasak  : (id) => `/koki/${id}/mulai-masak`,
    selesaikan  : (id) => `/koki/${id}/selesaikan`,
    batalkan    : (id) => `/koki/${id}/batalkan`,
    apiOrders   : `/koki/api/orders`,
  };

  function getCsrf() {
    return (window.ROUTES && window.ROUTES.csrfToken)
      || document.querySelector('meta[name="csrf-token"]')?.content
      || '';
  }

  /* ══════════════════════════════════════════════════════════
     1. JAM DIGITAL
  ══════════════════════════════════════════════════════════ */
  function startClock() {
    const el = document.getElementById('liveClock');
    if (!el) return;
    const tick = () => {
      el.textContent = new Date().toLocaleTimeString('id-ID', {
        hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit',
      });
    };
    tick();
    setInterval(tick, 1000);
  }

  /* ══════════════════════════════════════════════════════════
     2. TIMER KARTU (update tiap detik)
  ══════════════════════════════════════════════════════════ */
  function startCardTimers() {
    const tick = () => {
      document.querySelectorAll('.order-card:not(.card-done)').forEach(card => {
        const raw = card.dataset.createdAt;
        if (!raw) return;

        const elapsed = Math.max(0, Math.floor((Date.now() - new Date(raw).getTime()) / 1000));
        const m = Math.floor(elapsed / 60);
        const s = elapsed % 60;

        const valEl = card.querySelector('.timer-val');
        if (valEl) {
          valEl.textContent = elapsed < 60
            ? `${elapsed} dtk`
            : `${m} mnt ${String(s).padStart(2, '0')} dtk`;
        }

        const newClass = elapsed >= T_WARN ? 'card-urgent'
                       : elapsed >= T_FRESH ? 'card-warning'
                       : 'card-fresh';

        card.classList.remove('card-fresh', 'card-warning', 'card-urgent');
        card.classList.add(newClass);

        // Sinkronkan class timer badge dalam kartu
        const timerEl = card.querySelector('.card-timer');
        if (timerEl) {
          timerEl.classList.remove('card-fresh', 'card-warning', 'card-urgent', 'card-done');
          timerEl.classList.add(newClass);
        }
      });
    };
    tick();
    setInterval(tick, 1000);
  }

  /* ══════════════════════════════════════════════════════════
     3. TOAST NOTIFICATION (mengikuti struktur kasir)
  ══════════════════════════════════════════════════════════ */
  function showToast(message, type = 'default') {
    const wrap = document.getElementById('toastWrap');
    if (!wrap) return;

    // SVG icons per type
    const icons = {
      default : '<circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5" fill="none"/><path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>',
      success : '<path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>',
      error   : '<circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5" fill="none"/><path d="M15 9l-6 6M9 9l6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>',
      info    : '<circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5" fill="none"/><path d="M12 16v-4M12 8h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>',
    };

    const toast = document.createElement('div');
    toast.className = `koki-toast${type !== 'default' ? ` toast-${type}` : ''}`;
    toast.innerHTML = `
      <svg class="koki-toast-icon" viewBox="0 0 24 24" fill="none">${icons[type] || icons.default}</svg>
      <span>${escHtml(message)}</span>
    `;
    wrap.prepend(toast);

    setTimeout(() => {
      toast.classList.add('hiding');
      toast.addEventListener('animationend', () => toast.remove(), { once: true });
    }, 4500);
  }

  /* ══════════════════════════════════════════════════════════
     4. FILTER TAB
  ══════════════════════════════════════════════════════════ */
  function applyFilter(filter) {
    currentFilter = filter;
    document.querySelectorAll('.order-card').forEach(card => {
      const s = card.dataset.status;
      const show = {
        semua   : true,
        pending : s === 'pending',
        cooking : s === 'cooking',
        ready   : s === 'ready',
      }[filter] ?? true;
      card.style.display = show ? '' : 'none';
    });
  }

  function initFilterTabs() {
    const tabs = document.querySelectorAll('.koki-filter-tab');
    tabs.forEach(tab => {
      tab.addEventListener('click', function () {
        tabs.forEach(t => { t.classList.remove('active'); t.setAttribute('aria-selected', 'false'); });
        this.classList.add('active');
        this.setAttribute('aria-selected', 'true');
        applyFilter(this.dataset.filter);
      });
    });
    const active = document.querySelector('.koki-filter-tab.active');
    if (active) applyFilter(active.dataset.filter);
  }

  function updateCounters() {
    let semua = 0, pending = 0, cooking = 0, ready = 0;
    document.querySelectorAll('.order-card').forEach(card => {
      const s = card.dataset.status;
      semua++;
      if (s === 'pending') pending++;
      if (s === 'cooking') cooking++;
      if (s === 'ready')   ready++;
    });
    const set = (id, n) => { const el = document.getElementById(id); if (el) el.textContent = n; };
    set('badge-semua',   semua);
    set('badge-pending', pending);
    set('badge-cooking', cooking);
    set('badge-ready',   ready);
  }

  /* ══════════════════════════════════════════════════════════
     5. FETCH API HELPER
  ══════════════════════════════════════════════════════════ */
  async function post(endpoint, data = {}) {
    const res = await fetch(endpoint, {
      method  : 'POST',
      headers : {
        'Content-Type'     : 'application/json',
        'Accept'           : 'application/json',
        'X-CSRF-TOKEN'     : getCsrf(),
        'X-Requested-With' : 'XMLHttpRequest',
      },
      body: JSON.stringify(data),
    });

    const json = await res.json().catch(() => ({}));
    if (!res.ok) throw new Error(json.message || `HTTP ${res.status}`);
    return json;
  }

  /* ══════════════════════════════════════════════════════════
     6. POPUP DETAIL — OPEN / CLOSE
  ══════════════════════════════════════════════════════════ */
  function openPopup(card) {
    const id     = card.dataset.orderId;
    const status = card.dataset.status;

    if (['completed', 'cancelled', 'ready'].includes(status)) return;

    // Parse items
    let items = [];
    try { items = JSON.parse(card.dataset.items || '[]'); } catch (_) { /* noop */ }

    activeOrderId   = id;
    activeOrderData = {
      id         : id,
      orderCode  : card.dataset.orderCode,
      primaryId  : card.dataset.primaryId,
      secondaryId: card.dataset.secondaryId,
      pelanggan  : card.dataset.pelanggan,
      tipe       : card.dataset.tipe,
      meja       : card.dataset.meja,
      status     : status,
      createdAt  : card.dataset.createdAt,
      items      : items,
    };

    // Inisialisasi cache untuk order ini jika belum ada
    if (!orderItemCache[id]) {
      orderItemCache[id] = {};
      // Pre-populate dari status DB
      items.forEach((item, idx) => {
        const key = item.item_id != null ? String(item.item_id) : String(idx);
        const dbDone = item.item_status === 'READY' || item.item_status === 'ready';
        orderItemCache[id][key] = dbDone;
      });
    }

    // Isi header popup
    const titleEl = document.getElementById('popupDetailTitle');
    const idEl    = document.getElementById('popupOrderId');
    const metaEl  = document.getElementById('popupMeta');

    if (titleEl) titleEl.textContent = 'Detail Pesanan';
    if (idEl)    idEl.textContent    = `Detail Pesanan: ${activeOrderData.secondaryId}`;
    if (metaEl) {
      const tipeKelas = activeOrderData.tipe === 'Dine In' ? 'badge-dine' : 'badge-takeaway';
      metaEl.innerHTML =
        `<span style="font-size:11px;color:#888">Id: ${escHtml(activeOrderData.primaryId)}</span>
         &nbsp;|&nbsp;
         <span class="badge ${tipeKelas}">${escHtml(activeOrderData.tipe)}</span>
         &nbsp;|&nbsp;
         <span style="font-size:11px;color:#888">Masuk: ${formatTime(activeOrderData.createdAt)}</span>`;
    }

    renderItems();
    syncActionButtons();

    document.getElementById('globalOverlay')?.classList.add('is-open');
    document.getElementById('popupDetail')?.classList.add('is-open');
    document.body.style.overflow = 'hidden';
  }

  function closePopup() {
    document.getElementById('globalOverlay')?.classList.remove('is-open');
    document.getElementById('popupDetail')?.classList.remove('is-open');
    document.getElementById('popupSuccess')?.classList.remove('is-open');
    document.body.style.overflow = '';
  }

  /* ══════════════════════════════════════════════════════════
     7. RENDER ITEMS — membaca dari orderItemCache
        Centang TIDAK hilang saat modal dibuka ulang!
  ══════════════════════════════════════════════════════════ */
  function renderItems() {
    const list = document.getElementById('popupItemsList');
    if (!list || !activeOrderData) return;

    list.innerHTML = '';
    const items  = activeOrderData.items;
    const cache  = orderItemCache[activeOrderId] || {};

    items.forEach((item, idx) => {
      const key    = item.item_id != null ? String(item.item_id) : String(idx);
      const isDone = !!cache[key];

      const nama    = item.nama    || item.name || '';
      const qty     = item.qty     || item.quantity || 1;
      const catatan = item.catatan || item.note || '';

      const row = document.createElement('div');
      row.className = `koki-item-row${isDone ? ' done' : ''}`;
      row.dataset.key = key;

      row.innerHTML = `
        <div class="koki-item-check">&#10003;</div>
        <div class="koki-item-info">
          <div class="koki-item-name">${escHtml(nama)}</div>
          ${catatan
            ? `<div class="badge-note">${escHtml(catatan)}</div>`
            : ''}
        </div>
        <div class="koki-item-qty">[${qty}x]</div>
      `;

      row.addEventListener('click', () => toggleItem(key, row));
      list.appendChild(row);
    });

    updateProgressBar();
  }

  /* ══════════════════════════════════════════════════════════
     8. TOGGLE CENTANG ITEM — menyimpan ke cache
  ══════════════════════════════════════════════════════════ */
  function toggleItem(key, row) {
    if (!activeOrderId) return;

    // Inisialisasi cache jika perlu
    if (!orderItemCache[activeOrderId]) orderItemCache[activeOrderId] = {};

    // Toggle
    orderItemCache[activeOrderId][key] = !orderItemCache[activeOrderId][key];

    // Update DOM
    row.classList.toggle('done', orderItemCache[activeOrderId][key]);

    updateProgressBar();
  }

  /* ══════════════════════════════════════════════════════════
     9. UPDATE PROGRESS BAR + validasi tombol Selesaikan
  ══════════════════════════════════════════════════════════ */
  function updateProgressBar() {
    if (!activeOrderId || !activeOrderData) return;

    const items   = activeOrderData.items;
    const cache   = orderItemCache[activeOrderId] || {};
    const total   = items.length;
    const done    = items.filter((item, idx) => {
      const key = item.item_id != null ? String(item.item_id) : String(idx);
      return !!cache[key];
    }).length;
    const pct     = total ? Math.round((done / total) * 100) : 0;
    const allDone = done === total && total > 0;

    // Update progress
    const doneEl = document.getElementById('progDone');
    const totEl  = document.getElementById('progTotal');
    const fillEl = document.getElementById('progFill');
    if (doneEl)  doneEl.textContent  = done;
    if (totEl)   totEl.textContent   = total;
    if (fillEl)  fillEl.style.width  = pct + '%';

    // Update label tombol Selesaikan
    const lblEl = document.getElementById('btnSelesaikanLabel');
    if (lblEl)   lblEl.textContent   = `Tandai Sebagai Selesai (${done}/${total})`;

    // Aktifkan/nonaktifkan tombol Selesaikan
    const btnSelesai = document.getElementById('btnSelesaikan');
    if (btnSelesai) {
      const canComplete = allDone && ['pending', 'cooking'].includes(activeOrderData.status);
      btnSelesai.disabled = !canComplete;
    }
  }

  /* ══════════════════════════════════════════════════════════
     10. SINKRON TOMBOL AKSI SESUAI STATUS ORDER
  ══════════════════════════════════════════════════════════ */
  function syncActionButtons() {
    if (!activeOrderData) return;
    const status    = activeOrderData.status;
    const btnMulai  = document.getElementById('btnMulaiMasak');
    const btnBatal  = document.getElementById('btnBatalkan');

    // Tombol "Mulai Masak" → hanya muncul saat PENDING
    if (btnMulai) btnMulai.style.display = status === 'pending' ? '' : 'none';

    // Tombol "Batalkan" → hanya muncul saat PENDING
    if (btnBatal) btnBatal.style.display = status === 'pending' ? '' : 'none';
  }

  /* ══════════════════════════════════════════════════════════
     11. AKSI: MULAI MASAK  (PENDING → COOKING)
  ══════════════════════════════════════════════════════════ */
  async function handleMulaiMasak() {
    if (!activeOrderId) return;

    const btn = document.getElementById('btnMulaiMasak');
    const origText = btn?.textContent || 'Mulai Masak';
    if (btn) { btn.disabled = true; btn.classList.add('btn-loading'); btn.textContent = 'Memproses'; }

    try {
      const data = await post(url.mulaiMasak(activeOrderId));

      if (data.success) {
        if (activeOrderData) activeOrderData.status = 'cooking';
        updateCardStatusUI(activeOrderId, 'cooking');

        // Sembunyikan tombol mulai + batalkan
        if (btn) btn.style.display = 'none';
        const btnBatal = document.getElementById('btnBatalkan');
        if (btnBatal) btnBatal.style.display = 'none';

        // Re-evaluasi tombol selesaikan
        updateProgressBar();
        showToast(`Pesanan ${activeOrderData?.secondaryId} mulai dimasak`, 'info');
        updateCounters();
      } else {
        showToast(data.message || 'Gagal memulai masak', 'error');
      }
    } catch (err) {
      showToast(`Error: ${err.message}`, 'error');
    } finally {
      if (btn) { btn.disabled = false; btn.classList.remove('btn-loading'); btn.textContent = origText; }
    }
  }

  /* ══════════════════════════════════════════════════════════
     12. AKSI: SELESAIKAN  (PENDING/COOKING → READY)
         Hanya bisa dieksekusi jika semua item sudah dicentang
  ══════════════════════════════════════════════════════════ */
  async function handleSelesaikan() {
    if (!activeOrderId) return;

    const btn = document.getElementById('btnSelesaikan');
    if (btn?.disabled) return;   // guard extra

    if (btn) { btn.disabled = true; btn.classList.add('btn-loading'); }

    try {
      const data = await post(url.selesaikan(activeOrderId));

      if (data.success) {
        updateCardStatusUI(activeOrderId, 'ready');

        // Tampilkan success popup
        document.getElementById('popupDetail')?.classList.remove('is-open');
        const detailEl = document.getElementById('successDetail');
        if (detailEl) {
          detailEl.textContent = `${activeOrderData?.primaryId} — ${activeOrderData?.secondaryId}`;
        }
        document.getElementById('popupSuccess')?.classList.add('is-open');

        showToast(`Pesanan ${activeOrderData?.secondaryId} siap saji`, 'success');
        updateCounters();
        applyFilter(currentFilter);

        // Bersihkan cache untuk order ini
        delete orderItemCache[activeOrderId];
      } else {
        showToast(data.message || 'Gagal menyelesaikan', 'error');
      }
    } catch (err) {
      showToast(`Error: ${err.message}`, 'error');
    } finally {
      if (btn) { btn.disabled = false; btn.classList.remove('btn-loading'); }
    }
  }

  /* ══════════════════════════════════════════════════════════
     13. AKSI: BATALKAN  (PENDING → CANCELLED)
  ══════════════════════════════════════════════════════════ */
  async function handleBatalkan() {
    if (!activeOrderId) return;

    const konfirm = window.confirm(
      `Batalkan pesanan ${activeOrderData?.primaryId}?\nTindakan ini tidak dapat dibatalkan.`
    );
    if (!konfirm) return;

    const btn = document.getElementById('btnBatalkan');
    if (btn) { btn.disabled = true; btn.classList.add('btn-loading'); }

    try {
      const data = await post(url.batalkan(activeOrderId));
      if (data.success) {
        closePopup();
        // Hapus kartu dari DOM
        document.getElementById(`card-${activeOrderId}`)?.remove();
        showToast(`Pesanan ${activeOrderData?.primaryId} dibatalkan`, 'error');
        updateCounters();
        delete orderItemCache[activeOrderId];
      } else {
        showToast(data.message || 'Gagal membatalkan', 'error');
      }
    } catch (err) {
      showToast(`Error: ${err.message}`, 'error');
    } finally {
      if (btn) { btn.disabled = false; btn.classList.remove('btn-loading'); }
    }
  }

  /* ══════════════════════════════════════════════════════════
     14. UPDATE VISUAL KARTU DI GRID
  ══════════════════════════════════════════════════════════ */
  function updateCardStatusUI(orderId, newStatus) {
    const card = document.getElementById(`card-${orderId}`);
    if (!card) return;

    card.dataset.status = newStatus;

    // Update status pill
    const pill = card.querySelector('[data-status-pill]');
    if (pill) {
      const labelMap = { pending: 'Menunggu', cooking: 'Memasak', ready: 'Siap Saji', completed: 'Selesai' };
      const classMap = { pending: 'pill-pending', cooking: 'pill-cooking', ready: 'pill-ready', completed: 'pill-done' };
      pill.textContent = labelMap[newStatus] || newStatus;
      pill.className   = `status-pill ${classMap[newStatus] || ''}`;
    }

    // Card selesai / ready → class done
    if (['ready', 'completed', 'cancelled'].includes(newStatus)) {
      card.classList.remove('card-fresh', 'card-warning', 'card-urgent');
      card.classList.add('card-done');
      card.removeAttribute('role');
      card.removeAttribute('tabindex');
    }

    // Cooking → reset ke fresh (timer mulai dari awal)
    if (newStatus === 'cooking') {
      card.classList.remove('card-warning', 'card-urgent');
      card.classList.add('card-fresh');
    }
  }

  /* ══════════════════════════════════════════════════════════
     15. POLLING AUTO-REFRESH
  ══════════════════════════════════════════════════════════ */
  function startPolling() {
    setInterval(async () => {
      try {
        const res  = await fetch(url.apiOrders, { headers: { Accept: 'application/json' } });
        const data = await res.json();

        if (data.success && data.counts) {
          const c = data.counts;
          Object.entries(c).forEach(([key, n]) => {
            const el = document.getElementById(`badge-${key}`);
            if (el) el.textContent = n;
          });

          // Deteksi pesanan baru
          const badgeSemua = document.getElementById('badge-semua');
          const prev = parseInt(badgeSemua?.dataset.prevCount || '0');
          if (c.semua > prev && prev > 0) {
            showToast(`${c.semua - prev} pesanan baru masuk`, 'info');
            const notifBadge = document.getElementById('notifBadge');
            if (notifBadge) { notifBadge.style.display = 'flex'; }
          }
          if (badgeSemua) badgeSemua.dataset.prevCount = c.semua;
        }
      } catch (_) { /* silent fail */ }
    }, 30_000);
  }

  /* ══════════════════════════════════════════════════════════
     16. NOTIF PANEL
  ══════════════════════════════════════════════════════════ */
  function initNotifPanel() {
    const btn    = document.getElementById('notifButton');
    const panel  = document.getElementById('notifPanel');
    const readBtn = document.getElementById('btnReadAll');
    const footer = document.getElementById('btnNotifFooter');

    if (btn && panel) {
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        panel.classList.toggle('is-open');
        // Hapus badge
        const badge = document.getElementById('notifBadge');
        if (badge) badge.style.display = 'none';
      });
    }

    if (readBtn) {
      readBtn.addEventListener('click', () => {
        const badge = document.getElementById('notifBadge');
        if (badge) badge.style.display = 'none';
        showToast('Semua notifikasi ditandai dibaca', 'info');
      });
    }

    if (footer) {
      footer.addEventListener('click', () => {
        panel?.classList.remove('is-open');
        showToast('Fitur riwayat notifikasi akan segera tersedia', 'info');
      });
    }

    // Klik di luar → tutup panel
    document.addEventListener('click', (e) => {
      if (panel && !panel.contains(e.target) && e.target !== btn) {
        panel.classList.remove('is-open');
      }
    });
  }

  /* ══════════════════════════════════════════════════════════
     17. INIT EVENT LISTENERS
  ══════════════════════════════════════════════════════════ */
  function init() {
    // Klik kartu → buka popup
    document.querySelectorAll('.order-card[role="button"]').forEach(card => {
      card.addEventListener('click', () => openPopup(card));
      card.addEventListener('keydown', e => {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); openPopup(card); }
      });
    });

    // Tutup popup detail
    document.getElementById('btnCloseDetail')?.addEventListener('click', closePopup);

    // Tutup popup success
    document.getElementById('btnCloseSuccess')?.addEventListener('click', closePopup);

    // Tombol Mulai Masak
    document.getElementById('btnMulaiMasak')?.addEventListener('click', handleMulaiMasak);

    // Tombol Selesaikan
    document.getElementById('btnSelesaikan')?.addEventListener('click', handleSelesaikan);

    // Tombol Batalkan
    document.getElementById('btnBatalkan')?.addEventListener('click', handleBatalkan);

    // Kembali ke Antrian (dari success)
    document.getElementById('btnBackToQueue')?.addEventListener('click', () => {
      closePopup();
      applyFilter(currentFilter);
    });

    // Overlay → tutup popup
    document.getElementById('globalOverlay')?.addEventListener('click', closePopup);

    // Escape → tutup popup
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closePopup(); });
  }

  /* ══════════════════════════════════════════════════════════
     UTILITY
  ══════════════════════════════════════════════════════════ */
  function escHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function formatTime(raw) {
    if (!raw) return '--:--';
    try {
      return new Date(raw).toLocaleTimeString('id-ID', {
        hour: '2-digit', minute: '2-digit', hour12: false,
      });
    } catch (_) { return '--:--'; }
  }

  /* ══════════════════════════════════════════════════════════
     BOOTSTRAP
  ══════════════════════════════════════════════════════════ */
  document.addEventListener('DOMContentLoaded', () => {
    startClock();
    startCardTimers();
    initFilterTabs();
    initNotifPanel();
    init();
    startPolling();
  });

})();