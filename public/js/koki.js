/**
 * koki.js — Kitchen Display System · Tskuy Smart Kasir
 * ============================================================
 * Mengelola semua interaksi di halaman dapur:
 *   1. Jam digital live
 *   2. Timer waktu tunggu per kartu (Hijau/Kuning/Merah)
 *   3. Filter tab (Semua / Menunggu / Memasak / Siap Saji)
 *   4. Modal detail pesanan + centang item
 *   5. Tombol "Mulai Masak" → AJAX POST ke /koki/{id}/mulai-masak
 *   6. Tombol "Selesaikan"  → AJAX POST ke /koki/{id}/selesaikan
 *   7. Auto-refresh antrian via polling (setiap 30 detik)
 *   8. Toast notification
 *
 * Cara kerja komunikasi dengan Laravel:
 *   - Route URL diambil dari window.KOKI_ROUTES (didefinisikan di blade)
 *   - CSRF token diambil dari window.KOKI_ROUTES.csrfToken
 *   - Semua POST request menggunakan Fetch API + JSON
 * ============================================================
 */

'use strict';

const KokiApp = (() => {

  // ── State internal ───────────────────────────────────────
  let activeOrderId      = null;  // ID order yang sedang dibuka di modal
  let activeOrderData    = null;  // Data order aktif (item list, status, dll)
  let timerInterval      = null;  // setInterval untuk update timer kartu
  let pollingInterval    = null;  // setInterval untuk auto-refresh antrian
  let currentFilter      = 'semua';
const ROUTES           = window.KOKI_ROUTES || window.ROUTES || {};

  // ── Threshold waktu (dalam detik) ───────────────────────
  const THRESHOLD_FRESH   =  5 * 60;   // 0–5 mnt   → Hijau
  const THRESHOLD_WARNING = 15 * 60;   // 5–15 mnt  → Kuning
  // > 15 mnt → Merah

  // ── Interval polling (ms) ────────────────────────────────
  const POLLING_MS = 30_000; // 30 detik

  // ============================================================
  // 1. JAM DIGITAL
  // ============================================================
  function startClock() {
    const el = document.getElementById('liveClock');
    if (!el) return;

    function tick() {
      const now = new Date();
      el.textContent = now.toLocaleTimeString('id-ID', {
        hour12: false,
        hour: '2-digit', minute: '2-digit', second: '2-digit',
      });
    }
    tick();
    setInterval(tick, 1000);
  }

  // ============================================================
  // 2. TIMER WAKTU TUNGGU PER KARTU
  //    Setiap detik, semua kartu aktif dihitung ulang durasinya
  //    dan diberi class css sesuai threshold.
  // ============================================================
  function startCardTimers() {
    function tick() {
      document.querySelectorAll('.order-card:not(.card-done)').forEach(card => {
        const createdAtStr = card.dataset.createdAt;
        if (!createdAtStr) return;

        const createdAt  = new Date(createdAtStr).getTime();
        const elapsed    = Math.max(0, Math.floor((Date.now() - createdAt) / 1000));
        const elapsedMin = Math.floor(elapsed / 60);
        const elapsedSec = elapsed % 60;

        // Perbarui teks timer
        const timerEl = card.querySelector('.timer-val');
        if (timerEl) {
          timerEl.textContent = elapsed < 60
            ? `${elapsed} dtk`
            : `${elapsedMin} mnt ${String(elapsedSec).padStart(2,'0')} dtk`;
        }

        // Tentukan kelas warna dan perbarui card
        const newClass = elapsed >= THRESHOLD_WARNING
          ? 'card-urgent'
          : elapsed >= THRESHOLD_FRESH
          ? 'card-warning'
          : 'card-fresh';

        card.classList.remove('card-fresh', 'card-warning', 'card-urgent');
        card.classList.add(newClass);
      });
    }

    tick();
    timerInterval = setInterval(tick, 1000);
  }

  // ============================================================
  // 3. TOAST NOTIFICATION
  // ============================================================
  function showToast(message, type = 'default') {
    const container = document.getElementById('toastContainer');
    if (!container) return;

    const icons = { default: '🔔', success: '✅', error: '❌', info: 'ℹ️' };

    const toast = document.createElement('div');
    toast.className = `toast${type !== 'default' ? ` toast-${type}` : ''}`;
    toast.innerHTML = `<span class="toast-icon">${icons[type] || '🔔'}</span>${message}`;
    container.prepend(toast);

    // Hapus otomatis setelah 5 detik
    setTimeout(() => {
      toast.classList.add('hiding');
      toast.addEventListener('animationend', () => toast.remove(), { once: true });
    }, 5000);
  }

  // ============================================================
  // 4. FILTER TAB
  // ============================================================
  function applyFilter(filter) {
    currentFilter = filter;

    document.querySelectorAll('.order-card').forEach(card => {
      const status = card.dataset.status;

      const visible = {
        'semua'   : true,
        'pending' : status === 'pending',
        'cooking' : status === 'cooking',
        'ready'   : status === 'ready',
      }[filter] ?? true;

      card.style.display = visible ? '' : 'none';
    });

    // Cek apakah semua kartu tersembunyi → tampilkan empty hint
    const visibleCards = document.querySelectorAll('.order-card[style=""]').length
                       + document.querySelectorAll('.order-card:not([style])').length;
  }

  function initFilterTabs() {
    const tabs = document.querySelectorAll('.filter-tab');

    tabs.forEach(tab => {
      tab.addEventListener('click', function () {
        tabs.forEach(t => {
          t.classList.remove('active');
          t.setAttribute('aria-selected', 'false');
        });
        this.classList.add('active');
        this.setAttribute('aria-selected', 'true');
        applyFilter(this.dataset.filter);
      });
    });

    // Apply default filter
    const activeTab = document.querySelector('.filter-tab.active');
    if (activeTab) applyFilter(activeTab.dataset.filter);
  }

  function updateCounterBadges() {
    const cards = document.querySelectorAll('.order-card');
    let semua = 0, pending = 0, cooking = 0, ready = 0;

    cards.forEach(card => {
      const s = card.dataset.status;
      semua++;
      if (s === 'pending') pending++;
      if (s === 'cooking') cooking++;
      if (s === 'ready')   ready++;
    });

    const set = (id, n) => {
      const el = document.getElementById(id);
      if (el) el.textContent = n;
    };
    set('badge-semua',   semua);
    set('badge-pending', pending);
    set('badge-cooking', cooking);
    set('badge-ready',   ready);
  }

  // ============================================================
  // 5. HELPER FETCH API (semua request ke Laravel)
  // ============================================================
  async function postToLaravel(url, data = {}) {
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type'  : 'application/json',
        'X-CSRF-TOKEN'  : ROUTES.csrfToken || document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute('content') || '',
        'Accept'        : 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: JSON.stringify(data),
    });

    if (!response.ok) {
      const errJson = await response.json().catch(() => ({}));
      throw new Error(errJson.message || `HTTP ${response.status}`);
    }

    return response.json();
  }

  // ============================================================
  // 6. MODAL DETAIL PESANAN
  // ============================================================

  /** Render semua item dalam list modal */
  function renderModalItems(items, orderStatus) {
    const list = document.getElementById('modalItemsList');
    if (!list) return;

    list.innerHTML = '';

    items.forEach((item, idx) => {
      const isDone = item.item_status === 'READY' || item.item_status === 'ready';

      const row = document.createElement('div');
      row.className = `modal-item-row${isDone ? ' done' : ''}`;
      row.id = `modal-item-${idx}`;
      row.dataset.itemId = item.item_id;
      row.dataset.idx    = idx;

      row.innerHTML = `
        <div class="item-check-icon">✓</div>
        <div class="modal-item-info">
          <div class="modal-item-name">${escHtml(item.nama)}</div>
          ${item.catatan ? `<div class="modal-item-note">📝 ${escHtml(item.catatan)}</div>` : ''}
        </div>
        <div class="modal-item-qty">×${item.qty}</div>
      `;

      // Klik item → toggle centang (visual saja, tidak wajib semua done untuk selesaikan)
      row.addEventListener('click', () => toggleItemVisual(row, idx));

      list.appendChild(row);
    });

    updateProgress(items);
  }

  /** Toggle centang visual item di modal */
  function toggleItemVisual(row, idx) {
    row.classList.toggle('done');
    // Perbarui progress bar
    const allRows  = document.querySelectorAll('.modal-item-row');
    const doneRows = document.querySelectorAll('.modal-item-row.done');

    const total = allRows.length;
    const done  = doneRows.length;

    document.getElementById('progDone').textContent  = done;
    document.getElementById('progTotal').textContent = total;
    const pct = total ? Math.round((done / total) * 100) : 0;
    document.getElementById('progFill').style.width  = pct + '%';

    const label   = document.getElementById('btnCompleteLabel');
    const btnSelesaikan = document.getElementById('btnComplete');
    if (label) label.textContent = `Selesaikan Pesanan (${done}/${total})`;

    // Aktifkan tombol selesaikan (tidak perlu semua done — koki bisa selesaikan kapan saja)
    // Tapi setidaknya ≥1 item done agar tidak di-klik sembarangan
    if (btnSelesaikan) {
      const canComplete = activeOrderData &&
        ['pending', 'cooking'].includes(activeOrderData.status);

      if (canComplete) {
        btnSelesaikan.disabled = false;
        btnSelesaikan.classList.remove('cant-complete');
      }
    }
  }

  /** Update progress bar saat modal dibuka */
  function updateProgress(items) {
    const total = items.length;
    const done  = items.filter(i =>
      i.item_status === 'READY' || i.item_status === 'ready'
    ).length;

    document.getElementById('progDone').textContent  = done;
    document.getElementById('progTotal').textContent = total;
    const pct = total ? Math.round((done / total) * 100) : 0;
    document.getElementById('progFill').style.width  = pct + '%';

    const label = document.getElementById('btnCompleteLabel');
    if (label) label.textContent = `Selesaikan Pesanan (${done}/${total})`;
  }

  /** Buka detail modal dari data kartu */
  function openModal(card) {
    const orderId = card.dataset.orderId;
    const status  = card.dataset.status;

    if (['completed', 'cancelled'].includes(status)) return;

    // Parse data item dari data-attribute
    let items = [];
    try { items = JSON.parse(card.dataset.items || '[]'); } catch (_) {}

    activeOrderId   = orderId;
    activeOrderData = {
      id         : orderId,
      order_code : card.dataset.orderCode,
      identifier : card.dataset.identifier,
      pelanggan  : card.dataset.pelanggan,
      tipe       : card.dataset.tipe,
      status     : status,
      meja       : card.dataset.meja,
      createdAt  : card.dataset.createdAt,
      items      : items,
    };

    // Isi header modal
    const modalTitle  = document.getElementById('modalTitle');
    const modalInfo   = document.getElementById('modalInfoRow');
    if (modalTitle) modalTitle.textContent = activeOrderData.identifier;
    if (modalInfo) {
      modalInfo.innerHTML = `
        <span class="type-badge ${activeOrderData.tipe === 'Dine In' ? 'badge-dine-in' : 'badge-take-away'}"
              style="margin-right:6px">${escHtml(activeOrderData.tipe)}</span>
        👤 ${escHtml(activeOrderData.pelanggan)}
        ${activeOrderData.meja ? `&nbsp;·&nbsp; Meja ${escHtml(activeOrderData.meja)}` : ''}
      `;
    }

    // Tampilkan/sembunyikan tombol aksi sesuai status
    const btnMulai    = document.getElementById('btnMulaiMasak');
    const btnSelesai  = document.getElementById('btnComplete');

    if (btnMulai) {
      btnMulai.style.display = status === 'pending' ? '' : 'none';
    }
    if (btnSelesai) {
      const canComplete = ['pending', 'cooking'].includes(status);
      btnSelesai.disabled = !canComplete;
      btnSelesai.classList.toggle('cant-complete', !canComplete);
    }

    renderModalItems(items, status);

    // Tampilkan overlay + modal
    document.getElementById('modalOverlay').classList.add('active');
    document.getElementById('detailModal').classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function closeModal() {
    document.getElementById('modalOverlay')?.classList.remove('active');
    document.getElementById('detailModal')?.classList.remove('active');
    document.getElementById('successModal')?.classList.remove('active');
    document.body.style.overflow = '';
    activeOrderId   = null;
    activeOrderData = null;
  }

  // ============================================================
  // 7. AKSI: MULAI MASAK  (PENDING → COOKING)
  // ============================================================
    async function handleMulaiMasak() {
        if (!activeOrderId) return;

        const btn = document.getElementById('btnMulaiMasak');
        if (btn) {
        btn.disabled = true;
        btn.classList.add('btn-loading');
        btn.textContent = 'Memproses…';
        }

        try {
        // ⬇️ GANTI BARIS INI: Pakai window.KOKI_ROUTES, jangan ROUTES biasa!
        const url  = window.KOKI_ROUTES.mulaiMasak(activeOrderId);
        const data = await postToLaravel(url);

        if (data.success) {
            // Update tampilan kartu di grid
            updateCardStatusUI(activeOrderId, 'cooking');   

        // Update state aktif
        if (activeOrderData) activeOrderData.status = 'cooking';

        // Sembunyikan tombol mulai, tampilkan selesaikan
        if (btn) btn.style.display = 'none';
        const btnSelesai = document.getElementById('btnComplete');
        if (btnSelesai) {
          btnSelesai.disabled = false;
          btnSelesai.classList.remove('cant-complete');
        }

        showToast(`Pesanan ${activeOrderData?.identifier} mulai dimasak!`, 'info');
        updateCounterBadges();
      } else {
        showToast(data.message || 'Gagal memulai masak.', 'error');
      }
    } catch (err) {
      showToast(`Error: ${err.message}`, 'error');
    } finally {
      if (btn) {
        btn.disabled = false;
        btn.classList.remove('btn-loading');
        btn.textContent = 'Mulai Masak';
      }
    }
  }

  // ============================================================
  // 8. AKSI: SELESAIKAN  (→ READY)
  // ============================================================
  async function handleSelesaikan() {
    if (!activeOrderId) return;

    const btn = document.getElementById('btnComplete');
    if (btn) {
      btn.disabled = true;
      btn.classList.add('btn-loading');
    }

    try {
      const url  = window.KOKI_ROUTES.selesaikan(activeOrderId);
      const data = await postToLaravel(url);

      if (data.success) {
        const identifier = activeOrderData?.identifier || `#${activeOrderId}`;
        const orderCode  = data.order_code || activeOrderData?.order_code || activeOrderId;

        // Update kartu di grid → status READY (done visually)
        updateCardStatusUI(activeOrderId, 'ready');

        // Tutup detail modal, buka success modal
        document.getElementById('detailModal')?.classList.remove('active');

        const detail = document.getElementById('successDetail');
        if (detail) detail.textContent = `${identifier} · ${orderCode}`;

        document.getElementById('successModal')?.classList.add('active');

        showToast(`✅ ${identifier} siap saji!`, 'success');
        updateCounterBadges();
        applyFilter(currentFilter);
      } else {
        showToast(data.message || 'Gagal menyelesaikan pesanan.', 'error');
      }
    } catch (err) {
      showToast(`Error: ${err.message}`, 'error');
    } finally {
      if (btn) {
        btn.disabled = false;
        btn.classList.remove('btn-loading');
      }
    }
  }

  // ============================================================
  // 9. UPDATE VISUAL KARTU DI GRID SETELAH AKSI
  // ============================================================
  function updateCardStatusUI(orderId, newStatus) {
    const card = document.getElementById(`card-${orderId}`);
    if (!card) return;

    card.dataset.status = newStatus;

    // Update status pill
    const pill = card.querySelector('[data-status-pill]');
    if (pill) {
      const labels = {
        pending   : '🕐 Menunggu',
        cooking   : '🔥 Memasak',
        ready     : '✅ Siap Saji',
        completed : '☑ Selesai',
      };
      const classes = {
        pending   : 'status-pending',
        cooking   : 'status-cooking',
        ready     : 'status-ready',
        completed : 'status-done',
      };
      pill.textContent = labels[newStatus] || newStatus;
      pill.className   = `card-status-pill ${classes[newStatus] || ''}`;
    }

    // Jika READY atau lebih → jadikan "done" (tidak bisa diklik)
    if (['ready', 'completed', 'cancelled'].includes(newStatus)) {
      card.classList.remove('card-fresh', 'card-warning', 'card-urgent');
      card.classList.add('card-done');
      card.removeAttribute('role');
      card.removeAttribute('tabindex');
    }

    // Jika COOKING → update class timer ke fresh (warna reset ke hijau)
    if (newStatus === 'cooking') {
      card.classList.add('card-fresh');
    }
  }

  // ============================================================
  // 10. AUTO-REFRESH ANTRIAN (Polling)
  //     Setiap 30 detik, fetch JSON dari /koki/api/orders
  //     dan perbarui counter badge.
  //     (Untuk full render ulang → reload halaman)
  // ============================================================
  function startPolling() {
    if (!ROUTES.apiOrders) return;

    pollingInterval = setInterval(async () => {
      try {
        const res  = await fetch(ROUTES.apiOrders, {
          headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();

        if (data.success && data.counts) {
          const c = data.counts;
          ['semua','pending','cooking','ready'].forEach(key => {
            const el = document.getElementById(`badge-${key}`);
            if (el && c[key] !== undefined) el.textContent = c[key];
          });

          // Jika ada order baru (count bertambah), tampilkan notif
          const badgeSemua = document.getElementById('badge-semua');
          const prevCount  = parseInt(badgeSemua?.dataset.prevCount || '0');
          if (c.semua > prevCount && prevCount > 0) {
            showToast(`🔔 ${c.semua - prevCount} pesanan baru masuk!`);
            document.getElementById('notifBadge')?.classList.add('active');
          }
          if (badgeSemua) badgeSemua.dataset.prevCount = c.semua;
        }
      } catch (_) {
        // Polling gagal — diam saja, tidak perlu alert
      }
    }, POLLING_MS);
  }

  // ============================================================
  // 11. INIT SEMUA EVENT LISTENER
  // ============================================================
  function init() {
    // ── Klik kartu → buka modal ──────────────────────────
    document.querySelectorAll('.order-card[role="button"]').forEach(card => {
      card.addEventListener('click', () => openModal(card));
      // Keyboard accessibility
      card.addEventListener('keydown', e => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          openModal(card);
        }
      });
    });

    // ── Tombol X tutup detail modal ──────────────────────
    document.getElementById('closeDetailBtn')?.addEventListener('click', closeModal);

    // ── Tombol "Mulai Masak" ─────────────────────────────
    document.getElementById('btnMulaiMasak')?.addEventListener('click', handleMulaiMasak);

    // ── Tombol "Selesaikan Pesanan" ──────────────────────
    document.getElementById('btnComplete')?.addEventListener('click', () => {
      if (!document.getElementById('btnComplete').disabled) {
        handleSelesaikan();
      }
    });

    // ── Tombol "Kembali ke Antrian" (success modal) ──────
    document.getElementById('btnBackToQueue')?.addEventListener('click', () => {
      closeModal();
      applyFilter(currentFilter);
    });

    // ── Klik overlay → tutup modal ───────────────────────
    document.getElementById('modalOverlay')?.addEventListener('click', function (e) {
      if (e.target === this) closeModal();
    });

    // ── Escape key → tutup modal ─────────────────────────
    document.addEventListener('keydown', e => {
      if (e.key === 'Escape') closeModal();
    });

    // ── Tombol notif → hapus badge ───────────────────────
    document.getElementById('notifButton')?.addEventListener('click', () => {
      document.getElementById('notifBadge')?.classList.remove('active');
      showToast('Semua notifikasi telah dibaca.', 'info');
    });
  }

  // ============================================================
  // UTILITY
  // ============================================================
  function escHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  // ============================================================
  // PUBLIC API — dipanggil setelah DOMContentLoaded
  // ============================================================
  return { startClock, startCardTimers, initFilterTabs, init, startPolling };

})();

// ── Bootstrap ──────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  KokiApp.startClock();
  KokiApp.startCardTimers();
  KokiApp.initFilterTabs();
  KokiApp.init();
  KokiApp.startPolling();
});