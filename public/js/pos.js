/* ============================================================
   pos.js — Logika Kasir POS (Terhubung Database)
   Referensi pola dari pelanggan.js, disesuaikan untuk kasir
============================================================ */

// ============================================================
//  STATE KERANJANG
// ============================================================
let cartItems = [];
let nextCartId = 1;

// State untuk batalkan order dari antrian
let cancelTargetOrderId  = null;
let cancelTargetOrderCode = null;

// State payment method yang dipilih
let selectedPaymentMethod = null; // 'qris' | 'cash'

// Ambil CSRF token
const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content;

// ============================================================
//  FORMAT HELPERS
// ============================================================
function formatRp(amount) {
    return 'Rp ' + parseInt(amount).toLocaleString('id-ID');
}

function nowLabel() {
    const now = new Date();
    return now.getHours().toString().padStart(2, '0') + '.' +
           now.getMinutes().toString().padStart(2, '0') + ' WIB';
}

// ============================================================
//  RENDER KERANJANG
// ============================================================
function renderCart() {
    const container = document.getElementById('cart-items-container');
    const emptyMsg  = document.getElementById('cart-empty-msg');
    if (!container) return;

    // Hapus item lama kecuali empty msg
    Array.from(container.children).forEach(child => {
        if (child.id !== 'cart-empty-msg') child.remove();
    });

    if (cartItems.length === 0) {
        if (emptyMsg) emptyMsg.style.display = 'flex';
        updateSummary();
        return;
    }
    if (emptyMsg) emptyMsg.style.display = 'none';

    cartItems.forEach(item => {
        const card = document.createElement('div');
        card.className = 'item-card-mini';
        card.style.cssText = 'display:flex; gap:10px; margin-bottom:12px; padding-bottom:12px; border-bottom:1px solid #f0f0f0;';
        card.innerHTML = `
            <img src="${item.image}" class="mini-img" alt="${item.name}" style="width:56px; height:56px; border-radius:8px; object-fit:cover; flex-shrink:0;">
            <div class="mini-info" style="flex:1; min-width:0;">
                <div class="mini-header" style="display:flex; justify-content:space-between; align-items:flex-start; gap:4px;">
                    <p class="mini-name" style="font-size:12px; font-weight:600; color:#222; margin:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${item.name}</p>
                    <button class="delete-item" data-cart-id="${item.cartId}" style="background:none; border:none; cursor:pointer; color:#ef4444; padding:0; flex-shrink:0;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                    </button>
                </div>
                <div style="display:flex; flex-wrap:wrap; gap:4px; margin:3px 0;">
                    ${item.level !== null && item.level !== undefined ? `<div style="font-size:10px; color:#1e40af; background:#dbeafe; padding:2px 6px; border-radius:4px; display:inline-flex; align-items:center; gap:3px;">🌶 Lv.${item.level}</div>` : ''}
                    ${item.catatan ? `<div class="badge-note" style="font-size:10px; color:#854d0e; background:#fef3c7; padding:2px 6px; border-radius:4px; display:inline-flex; align-items:center; gap:3px;">📝 ${item.catatan}</div>` : ''}
                </div>
                <div class="mini-price-row" style="display:flex; justify-content:space-between; align-items:center; margin-top:5px;">
                    <p class="mini-price" style="font-size:12px; font-weight:700; color:#efb100; margin:0;">${formatRp(item.price * item.qty)}</p>
                    <div class="mini-qty" style="display:flex; align-items:center; gap:8px; background:#f4f4f4; padding:3px 8px; border-radius:16px;">
                        <button class="qty-btn minus" data-cart-id="${item.cartId}" style="background:none; border:none; font-weight:bold; cursor:pointer; font-size:14px; color:#555; line-height:1;">-</button>
                        <span class="qty-val" style="font-size:12px; font-weight:600; width:16px; text-align:center;">${item.qty}</span>
                        <button class="qty-btn plus" data-cart-id="${item.cartId}" style="background:none; border:none; font-weight:bold; cursor:pointer; font-size:14px; color:#555; line-height:1;">+</button>
                    </div>
                </div>
            </div>`;
        container.appendChild(card);
    });

    updateSummary();
}

function updateSummary() {
    let subtotal = 0;
    cartItems.forEach(item => { subtotal += item.price * item.qty; });
    const tax   = subtotal * 0.10;
    const total = subtotal + tax;

    const elSub   = document.getElementById('cart-subtotal');
    const elTax   = document.getElementById('cart-tax');
    const elTotal = document.getElementById('cart-total-price');
    if (elSub)   elSub.innerText   = formatRp(subtotal);
    if (elTax)   elTax.innerText   = formatRp(tax);
    if (elTotal) elTotal.innerText = formatRp(total);

    return { subtotal, tax, total };
}

function addToCart(menuData) {
    const level   = menuData.level !== undefined ? menuData.level : null;
    const catatan = menuData.catatan || '';

    // Item dianggap sama hanya jika id, level pedas, DAN catatan identik
    const existing = cartItems.find(i =>
        i.id      === menuData.id &&
        i.level   === level &&
        i.catatan === catatan
    );

    if (existing) {
        existing.qty += menuData.qty || 1;
    } else {
        cartItems.push({
            cartId:  nextCartId++,
            id:      menuData.id,
            name:    menuData.name,
            price:   menuData.price,
            image:   menuData.image,
            qty:     menuData.qty || 1,
            level,
            catatan,
        });
    }
    renderCart();
    showAddedToast(menuData.name);
}

// ============================================================
//  TOAST NOTIFIKASI
// ============================================================
function showAddedToast(name) {
    let toast = document.getElementById('add-toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'add-toast';
        toast.style.cssText = 'position:fixed; bottom:90px; left:50%; transform:translateX(-50%); background:#22c55e; color:#fff; padding:10px 20px; border-radius:30px; font-size:13px; font-weight:600; z-index:9999; opacity:0; transition:opacity 0.3s; display:flex; align-items:center; gap:8px; box-shadow:0 4px 12px rgba(34,197,94,.3); pointer-events:none;';
        document.body.appendChild(toast);
    }
    toast.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" fill="#fff"/><path d="M8 12L11 15L16 9" stroke="#22c55e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg> ${name} ditambahkan`;
    toast.style.opacity = '1';
    clearTimeout(toast._t);
    toast._t = setTimeout(() => (toast.style.opacity = '0'), 2000);
}

// ============================================================
//  SUBMIT ORDER KE DATABASE
// ============================================================
function submitOrder(paymentMethod, cashReceived = 0) {
    const customerName = document.getElementById('customer-name-input')?.value?.trim();
    if (!customerName) { alert('Isi nama pelanggan dulu!'); return; }
    if (cartItems.length === 0) { alert('Keranjang kosong!'); return; }

    // Kasir selalu Take Away — tidak ada pilihan meja/tipe
    const payload = {
        customer_name:  customerName,
        table_id:       null,
        order_type:     'take_away',
        payment_method: paymentMethod,
        cash_received:  cashReceived,
        items: cartItems.map(i => ({
            id:      parseInt(i.id),
            qty:     i.qty,
            catatan: i.catatan,
            level:   i.level,
        })),
    };

    fetch('/kasir/checkout', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
            'Accept': 'application/json',
        },
        body: JSON.stringify(payload),
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) { alert('Gagal: ' + data.message); return; }

        // Isi popup sukses dengan data dari response
        document.getElementById('success-order-code').innerText = data.order_code;
        document.getElementById('success-order-type').innerText =
            data.order_type === 'dine_in' ? 'Dine In' : 'Take Away';
        document.getElementById('success-waktu').innerText = nowLabel();
        document.getElementById('success-subtotal').innerText = formatRp(data.subtotal);
        document.getElementById('success-tax').innerText      = formatRp(data.tax);
        document.getElementById('success-total').innerText    = formatRp(data.total);

        const tunaiRow = document.getElementById('success-tunai-row');
        const kembalianRow = document.getElementById('success-kembalian-row');
        if (paymentMethod === 'cash' && cashReceived > 0) {
            tunaiRow.style.display      = 'flex';
            kembalianRow.style.display  = 'flex';
            document.getElementById('success-tunai').innerText     = formatRp(cashReceived);
            document.getElementById('success-kembalian').innerText = formatRp(data.kembalian);
        } else {
            tunaiRow.style.display     = 'none';
            kembalianRow.style.display = 'none';
        }

        window._openPopup('popup-success');
    })
    .catch(err => {
        console.error(err);
        alert('Terjadi gangguan koneksi. Coba lagi.');
    });
}

// ============================================================
//  SELESAIKAN ORDER (dari popup antrian)
// ============================================================
function selesaikanOrder(orderId) {
    fetch(`/kasir/order/${orderId}/selesaikan`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken(),
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            // Hapus kartu antrian dari DOM
            const card = document.querySelector(`.queue-card[data-order-id="${orderId}"]`);
            if (card) card.remove();

            const gridCard = document.querySelector(`.antrian-card[data-order-id="${orderId}"]`);
            if (gridCard) gridCard.remove();

            showAddedToast('Order ditandai selesai ✅');
        } else {
            alert('Gagal: ' + d.message);
        }
    })
    .catch(() => alert('Gagal koneksi ke server.'));
}

// ============================================================
//  BATALKAN ORDER (dari popup antrian)
// ============================================================
function batalkanOrder(orderId, alasan) {
    fetch(`/kasir/order/${orderId}/batalkan`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken(),
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ alasan }),
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            const card = document.querySelector(`.queue-card[data-order-id="${orderId}"]`);
            if (card) card.remove();
            const gridCard = document.querySelector(`.antrian-card[data-order-id="${orderId}"]`);
            if (gridCard) gridCard.remove();
            showAddedToast('Pesanan dibatalkan');
        } else {
            alert('Gagal: ' + d.message);
        }
    })
    .catch(() => alert('Gagal koneksi ke server.'));
}

// ============================================================
//  FILTER MENU (Kategori + Tag)
// ============================================================
let activeKategori = 'all';
let activeTag      = 'semua';
let searchQuery    = '';

function applyMenuFilter() {
    document.querySelectorAll('.menu-card').forEach(card => {
        const kategori = card.dataset.kategori || '';
        const tag      = card.dataset.tag || '';
        const judul    = card.querySelector('.card-judul')?.innerText.toLowerCase() || '';

        const matchKategori = activeKategori === 'all' || kategori === activeKategori;
        const matchTag      = activeTag === 'semua' || tag.includes(activeTag);
        const matchSearch   = searchQuery === '' || judul.includes(searchQuery);

        card.style.display = (matchKategori && matchTag && matchSearch) ? 'block' : 'none';
    });
}

// ============================================================
//  FILTER ANTRIAN (Tab di section antrian)
// ============================================================
function applyAntrianFilter(filter) {
    document.querySelectorAll('.queue-card').forEach(card => {
        if (filter === 'all') {
            card.style.display = '';
        } else if (filter === 'pending') {
            const orderCode = card.querySelector('.order-id')?.innerText || '';
            // Tampilkan semua; status pending ditandai data-type di card
            card.style.display = card.dataset.type === 'pending' ? '' : 'none';
        } else {
            card.style.display = card.dataset.type === filter ? '' : 'none';
        }
    });
}

// ============================================================
//  TUNAI — HITUNG KEMBALIAN
// ============================================================
function hitungKembalian() {
    const { total } = updateSummary();
    const diterima  = parseFloat(document.getElementById('tunai-diterima')?.value) || 0;
    const kembalian = diterima - total;

    const elDisplay = document.getElementById('tunai-kembalian-display');
    if (elDisplay) {
        elDisplay.innerText = kembalian >= 0 ? formatRp(kembalian) : '⚠ Kurang ' + formatRp(Math.abs(kembalian));
        elDisplay.style.color = kembalian >= 0 ? '#22c55e' : '#ef4444';
    }
}

// ============================================================
//  BUKA POPUP DETAIL ANTRIAN — Dipakai oleh kartu & row See All
// ============================================================
function openAntrianDetail(dataset) {
    const orderId     = dataset.orderId;
    const orderCode   = dataset.orderCode;
    const primaryId   = dataset.primaryId;
    const secondaryId = dataset.secondaryId;
    const tipe        = dataset.tipe;
    const total       = parseInt(dataset.total) || 0;
    const createdAt   = dataset.createdAt;
    const items       = JSON.parse(dataset.items || '[]');

    // Tentukan status dari data-status jika ada (fallback dari urgency warna)
    const statusRaw   = (dataset.status || '').toUpperCase();
    const statusMap   = {
        PENDING: { label: 'Menunggu',  cls: 'pill-pending' },
        COOKING: { label: 'Memasak',   cls: 'pill-cooking' },
        READY:   { label: 'Siap Saji', cls: 'pill-ready' },
    };
    const statusInfo = statusMap[statusRaw] || { label: statusRaw, cls: '' };

    // Header popup
    document.getElementById('popup-antrian-title').innerText    = `Detail — ${primaryId}`;
    document.getElementById('popup-antrian-order-id').innerText = primaryId;

    const masukTime = createdAt
        ? new Date(createdAt).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
        : '—';
    document.getElementById('popup-antrian-meta').innerHTML =
        `<strong>${secondaryId}</strong> &nbsp;·&nbsp; ${tipe} &nbsp;·&nbsp; Masuk ${masukTime}`;

    // Status pill
    const pillEl = document.getElementById('popup-antrian-status-pill');
    if (pillEl) {
        pillEl.textContent  = statusInfo.label;
        pillEl.className    = `status-pill ${statusInfo.cls}`;
        pillEl.style.cssText = 'font-size:11px; padding:4px 12px; margin-top:4px; display:inline-block; border-radius:20px; font-weight:600;';
    }

    // Total
    document.getElementById('popup-antrian-total').innerText = formatRp(total);

    // Render item list — tabel bergaya koki
    const itemsEl = document.getElementById('popup-antrian-items');
    itemsEl.innerHTML = '';

    if (items.length === 0) {
        itemsEl.innerHTML = '<div style="padding:16px; text-align:center; color:#bbb; font-size:12px;">Tidak ada item</div>';
    } else {
        items.forEach((item, idx) => {
            const isLast  = idx === items.length - 1;
            const row     = document.createElement('div');
            row.style.cssText = `
                display:flex; align-items:flex-start; gap:12px;
                padding:11px 14px;
                background:${idx % 2 === 0 ? '#fff' : '#fafafa'};
                border-bottom:${isLast ? 'none' : '1px solid #f3f4f6'};
            `;

            // Badge qty
            const harga = item.harga ? formatRp(item.harga * item.qty) : '';
            row.innerHTML = `
                <div style="min-width:28px; height:28px; border-radius:8px; background:#fef3c7;
                            display:flex; align-items:center; justify-content:center;
                            font-size:12px; font-weight:700; color:#92400e; flex-shrink:0;">
                    ${item.qty}×
                </div>
                <div style="flex:1; min-width:0;">
                    <div style="font-size:13px; font-weight:600; color:#1a1a1a;">${item.nama}</div>
                    ${item.catatan ? `
                    <div style="display:flex; align-items:center; gap:4px; margin-top:4px;
                                font-size:11px; color:#92400e; background:#fef3c7;
                                padding:3px 8px; border-radius:6px; width:fit-content;">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none">
                            <rect x="4" y="2" width="16" height="20" rx="2" stroke="#92400e" stroke-width="1.8"/>
                            <path d="M8 7H16M8 11H16M8 15H12" stroke="#92400e" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        ${item.catatan}
                    </div>` : ''}
                </div>
                ${harga ? `<div style="font-size:12px; font-weight:700; color:#efb100; flex-shrink:0;">${harga}</div>` : ''}
            `;
            itemsEl.appendChild(row);
        });
    }

    // Tombol aksi
    document.getElementById('popup-antrian-btn-selesai').dataset.orderId = orderId;
    const btnBatal = document.getElementById('popup-antrian-btn-batal');
    btnBatal.dataset.orderId   = orderId;
    btnBatal.dataset.orderCode = orderCode;
    btnBatal.dataset.table     = secondaryId;

    window._openPopup('popup-detail-antrian');
}

// ============================================================
//  DOMContentLoaded — Pasang semua event listener
// ============================================================
document.addEventListener('DOMContentLoaded', () => {
    renderCart();

    // ============================================================
    //  REALTIME TIMER — Update semua .timer-val tiap 10 detik
    // ============================================================
    function urgencyFromSec(sec) {
        if (sec >= 20 * 60) return 'card-urgent';
        if (sec >= 10 * 60) return 'card-warning';
        return 'card-fresh';
    }
    function urgencyColor(cls, part) {
        const map = {
            'card-fresh':   { dot: '#22c55e', text: '#16a34a' },
            'card-warning': { dot: '#f59e0b', text: '#d97706' },
            'card-urgent':  { dot: '#ef4444', text: '#dc2626' },
            'card-done':    { dot: '#9ca3af', text: '#6b7280' },
        };
        return (map[cls] || map['card-fresh'])[part];
    }

    function tickTimers() {
        const now = Date.now();
        document.querySelectorAll('.timer-val[data-created-at]').forEach(el => {
            const createdMs = new Date(el.dataset.createdAt).getTime();
            if (isNaN(createdMs)) return;
            const sec = Math.max(0, Math.floor((now - createdMs) / 1000));
            const min = Math.floor(sec / 60);

            // Teks waktu
            el.textContent = min < 60
                ? `${min} mnt`
                : `${Math.floor(min / 60)} j ${min % 60} mnt`;

            // Hanya update urgency untuk kartu yang bukan READY/DONE
            const card = el.closest('.queue-card, .sa-row');
            if (!card || card.dataset.status === 'READY' || card.classList.contains('card-done')) return;

            const newCls = urgencyFromSec(sec);

            // Update warna dot di kartu
            const dot = card.querySelector('.timer-dot');
            if (dot) dot.style.background = urgencyColor(newCls, 'dot');

            // Update warna teks timer
            el.style.color = urgencyColor(newCls, 'text');

            // Update class urgency card jika berubah
            const urgencyClasses = ['card-fresh', 'card-warning', 'card-urgent'];
            const current = urgencyClasses.find(c => card.classList.contains(c));
            if (current && current !== newCls) {
                card.classList.remove(...urgencyClasses);
                card.classList.add(newCls);
            }
        });
    }

    tickTimers();                      // jalankan sekali saat load
    setInterval(tickTimers, 10_000);   // lalu tiap 10 detik

    // ============================================================
    //  ARROW SCROLL — Kiri & Kanan queue-container
    // ============================================================
    const qContainer = document.getElementById('queue-container');
    const btnPrev    = document.getElementById('queue-prev');
    const btnNext    = document.getElementById('queue-next');

    function scrollStep() {
        const card = qContainer?.querySelector('.queue-card');
        return card ? card.offsetWidth + 12 : 200;
    }

    function syncArrows() {
        if (!qContainer || !btnPrev || !btnNext) return;
        const { scrollLeft, scrollWidth, clientWidth } = qContainer;
        btnPrev.disabled = scrollLeft <= 2;
        btnNext.disabled = scrollLeft + clientWidth >= scrollWidth - 2;
    }

    if (qContainer && btnPrev && btnNext) {
        btnPrev.addEventListener('click', () => {
            qContainer.scrollBy({ left: -scrollStep(), behavior: 'smooth' });
        });
        btnNext.addEventListener('click', () => {
            qContainer.scrollBy({ left: scrollStep(), behavior: 'smooth' });
        });
        qContainer.addEventListener('scroll', syncArrows, { passive: true });
        // scrollend untuk browser yang support (Chrome 109+)
        qContainer.addEventListener('scrollend', syncArrows, { passive: true });
        // Fallback: re-check setelah 400ms (durasi smooth scroll ≈ 300ms)
        const origPrev = btnPrev.onclick;
        const origNext = btnNext.onclick;
        btnPrev.addEventListener('click', () => setTimeout(syncArrows, 400));
        btnNext.addEventListener('click', () => setTimeout(syncArrows, 400));

        syncArrows(); // set state awal
    }

    // ---- SEARCH BAR ----
    document.querySelectorAll('.search-bar').forEach(input => {
        input.addEventListener('input', e => {
            searchQuery = e.target.value.toLowerCase().trim();
            document.querySelectorAll('.search-bar').forEach(si => {
                if (si !== e.target) si.value = e.target.value;
            });
            applyMenuFilter();
        });
    });

    // ---- TAB KATEGORI MENU ----
    document.querySelectorAll('.menu-tab').forEach(tab => {
        tab.addEventListener('click', function () {
            document.querySelectorAll('.menu-tab').forEach(t => t.classList.remove('active-tab'));
            this.classList.add('active-tab');
            activeKategori = this.dataset.filter;
            applyMenuFilter();
        });
    });

    // ---- MOOD / TAG FILTER ----
    document.querySelectorAll('.mood').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.mood').forEach(m => m.classList.remove('active-mood'));
            this.classList.add('active-mood');
            activeTag = this.dataset.tag;
            applyMenuFilter();
        });
    });

    // ---- KLIK KARTU ANTRIAN → Buka popup dinamis ----
    document.getElementById('queue-container')?.addEventListener('click', function (e) {
        const card = e.target.closest('.queue-card[data-order-id]');
        if (!card) return;
        openAntrianDetail(card.dataset);
    });

    // ---- KLIK ROW DI POPUP SEE ALL → Buka popup detail ----
    document.getElementById('sa-list')?.addEventListener('click', function (e) {
        const row = e.target.closest('.sa-row[data-order-id]');
        if (!row) return;
        openAntrianDetail(row.dataset);
    });

    // ============================================================
    //  SEE ALL ANTRIAN — Polling realtime dari /kasir/api/antrian
    // ============================================================

    // State aktif filter di popup see-all
    let saActiveFilter = 'all';
    let saAntrianData  = [];   // cache data terakhir dari API
    let saPollingTimer = null;

    const SA_URGENCY_MAP = {
        READY:   'card-done',
        _20plus: 'card-urgent',
        _10plus: 'card-warning',
        _fresh:  'card-fresh',
    };

    function saUrgencyClass(status, createdAt) {
        if (status === 'READY') return 'card-done';
        const sec = Math.max(0, (Date.now() - new Date(createdAt).getTime()) / 1000);
        if (sec >= 20 * 60) return 'card-urgent';
        if (sec >= 10 * 60) return 'card-warning';
        return 'card-fresh';
    }

    function saUrgencyDotColor(cls) {
        return { 'card-fresh':'#22c55e','card-warning':'#f59e0b','card-urgent':'#ef4444','card-done':'#9ca3af' }[cls] || '#9ca3af';
    }

    function saTimerText(createdAt) {
        const sec = Math.max(0, Math.floor((Date.now() - new Date(createdAt).getTime()) / 1000));
        const min = Math.floor(sec / 60);
        return min < 60 ? `${min} mnt` : `${Math.floor(min/60)} j ${min%60} mnt`;
    }

    function saTimerColor(cls) {
        return { 'card-fresh':'#16a34a','card-warning':'#d97706','card-urgent':'#dc2626','card-done':'#9ca3af' }[cls] || '#9ca3af';
    }

    function saBadgeInfo(order) {
        if (order.is_open_bill)                                              return { cls:'badge-openbill', label:'Open Bill' };
        if (order.filter_type === 'takeaway')                                return { cls:'badge-takeaway', label:'Take Away' };
        return { cls:'badge-dine', label:'Dine In' };
    }

    function saStatusInfo(status) {
        return {
            PENDING: { label:'Menunggu',  cls:'pill-pending' },
            COOKING: { label:'Memasak',   cls:'pill-cooking' },
            READY:   { label:'Siap Saji', cls:'pill-ready'   },
        }[status] || { label: status, cls: '' };
    }

    /** Render satu baris sa-row sebagai element DOM */
    function buildSaRow(order) {
        const primaryId   = '#' + (order.order_code || order.id);
        const secondaryId = order.table_number
            ? 'Meja ' + String(order.table_number).padStart(2, '0')
            : 'Take Away';
        const urgency  = saUrgencyClass(order.status, order.created_at);
        const badge    = saBadgeInfo(order);
        const statusI  = saStatusInfo(order.status);
        const timerTxt = saTimerText(order.created_at);
        const timerClr = saTimerColor(urgency);

        // Preview item (maks 2)
        const itemsArr  = order.items || [];
        const preview   = itemsArr.slice(0, 2).map(i => `${i.quantity}× ${i.menu_name}`).join(', ');
        const extraItem = itemsArr.length > 2 ? ` <span style="color:#bbb">+${itemsArr.length - 2} lagi</span>` : '';

        // Items JSON untuk popup detail
        const itemsJson = JSON.stringify(itemsArr.map(i => ({
            nama: i.menu_name, qty: i.quantity, catatan: i.note || '', harga: i.price || 0,
        })));

        const row = document.createElement('div');
        row.className = `sa-row ${urgency}`;
        row.dataset.saType      = order.filter_type;
        row.dataset.orderId     = order.id;
        row.dataset.orderCode   = order.order_code || order.id;
        row.dataset.primaryId   = primaryId;
        row.dataset.secondaryId = secondaryId;
        row.dataset.tipe        = badge.label;
        row.dataset.total       = order.total;
        row.dataset.createdAt   = order.created_at;
        row.dataset.status      = order.status;
        row.dataset.items       = itemsJson;
        row.setAttribute('role', 'button');
        row.setAttribute('tabindex', '0');
        row.style.cssText = 'display:flex; align-items:center; gap:12px; padding:12px 14px; border-radius:12px; cursor:pointer; transition:box-shadow .15s;';

        row.innerHTML = `
            <div class="sa-dot" style="width:10px;height:10px;border-radius:50%;flex-shrink:0;background:${saUrgencyDotColor(urgency)};"></div>

            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:center;gap:6px;margin-bottom:3px;flex-wrap:wrap;">
                    <span style="font-size:13px;font-weight:700;color:#1a1a1a;">${primaryId}</span>
                    <span class="badge ${badge.cls}" style="font-size:10px;padding:1px 7px;">${badge.label}</span>
                    <span class="status-pill ${statusI.cls}" style="font-size:10px;padding:1px 8px;">${statusI.label}</span>
                </div>
                <div style="font-size:11px;color:#888;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    ${secondaryId} &nbsp;·&nbsp; ${preview}${extraItem}
                </div>
            </div>

            <div style="text-align:right;flex-shrink:0;">
                <div class="timer-val" data-created-at="${order.created_at}"
                     style="font-size:11px;font-weight:600;color:${timerClr};">${timerTxt}</div>
                <div style="font-size:12px;font-weight:700;color:#efb100;margin-top:2px;">
                    ${formatRp(order.total)}
                </div>
            </div>

            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" style="flex-shrink:0;">
                <path d="M9 18l6-6-6-6" stroke="#d1d5db" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>`;

        return row;
    }

    /** Render ulang #sa-list sesuai data + filter aktif */
    function renderSaList(data) {
        const listEl  = document.getElementById('sa-list');
        const emptyEl = document.getElementById('sa-empty');
        if (!listEl) return;

        listEl.innerHTML = '';

        const filtered = saActiveFilter === 'all'
            ? data
            : data.filter(o => o.filter_type === saActiveFilter);

        if (filtered.length === 0) {
            if (emptyEl) emptyEl.style.display = 'block';
            return;
        }
        if (emptyEl) emptyEl.style.display = 'none';

        filtered.forEach(order => listEl.appendChild(buildSaRow(order)));
    }

    /** Update badge count di tab filter */
    function updateSaBadges(counts) {
        ['all','pending','dine','takeaway','openbill'].forEach(key => {
            const el = document.getElementById(`sa-badge-${key}`);
            if (el) el.textContent = counts[key] ?? 0;
        });

        // Update juga badge di tab antrian utama (queue-header)
        const tabMap = { all:'all', pending:'pending', dine:'dine', takeaway:'takeaway', openbill:'openbill' };
        document.querySelectorAll('.antrian-tab').forEach(tab => {
            const f = tab.dataset.filter;
            const badge = tab.querySelector('.tab-badge');
            if (badge && counts[f] !== undefined) badge.textContent = counts[f];
        });
    }

    /** Fetch data antrian dari API, render popup see-all */
    async function fetchAntrianData() {
        const apiUrl = (window.ROUTES && window.ROUTES.apiAntrian) || '/kasir/api/antrian';
        try {
            const res  = await fetch(apiUrl, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const json = await res.json();

            saAntrianData = json.antrian || [];

            renderSaList(saAntrianData);
            updateSaBadges(json.counts || {});
            tickTimers(); // perbarui timer setelah re-render

            // Timestamp
            const tsEl = document.getElementById('sa-last-update');
            if (tsEl) {
                const now = new Date();
                tsEl.textContent = 'Diperbarui ' + now.toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit', second:'2-digit' });
            }
        } catch (err) {
            console.warn('[See All] Gagal fetch antrian:', err.message);
        }
    }

    // ---- TOMBOL SEE ALL (desktop & mobile) — fetch saat popup dibuka ----
    function openSeeAll() {
        window._openPopup('popup-see-all-antrian');
        fetchAntrianData();             // fetch langsung saat dibuka
        // Mulai polling 15 detik selama popup terbuka
        if (saPollingTimer) clearInterval(saPollingTimer);
        saPollingTimer = setInterval(fetchAntrianData, 15_000);
    }
    function closeSeeAll() {
        if (saPollingTimer) { clearInterval(saPollingTimer); saPollingTimer = null; }
    }

    document.getElementById('btn-see-all-antrian')?.addEventListener('click', openSeeAll);
    document.getElementById('btn-see-all-antrian-mobile')?.addEventListener('click', openSeeAll);

    // Hentikan polling saat popup see-all ditutup
    document.getElementById('popup-see-all-antrian')?.addEventListener('click', e => {
        if (e.target.closest('[data-close]')) closeSeeAll();
    });

    // ---- FILTER TABS DI POPUP SEE ALL ----
    document.querySelectorAll('.sa-tab').forEach(tab => {
        tab.addEventListener('click', function () {
            saActiveFilter = this.dataset.saFilter;

            document.querySelectorAll('.sa-tab').forEach(t => {
                t.style.background = '#fff';
                t.style.color      = '#555';
                t.style.border     = '1.5px solid #e5e7eb';
            });
            this.style.background = '#efb100';
            this.style.color      = '#fff';
            this.style.border     = 'none';

            renderSaList(saAntrianData);
        });
    });

    // ---- TAB ANTRIAN ----
    document.querySelectorAll('.antrian-tab').forEach(tab => {
        tab.addEventListener('click', function () {
            document.querySelectorAll('.antrian-tab').forEach(t => {
                t.classList.remove('antrian-tab-active');
                const badge = t.querySelector('.tab-badge');
                if (badge) badge.className = 'tab-badge tab-badge-outline';
            });
            this.classList.add('antrian-tab-active');
            const activeBadge = this.querySelector('.tab-badge');
            if (activeBadge) activeBadge.className = 'tab-badge tab-badge-yellow';
            applyAntrianFilter(this.dataset.filter);
        });
    });

    // ---- DELEGASI KLIK GLOBAL ----
    document.addEventListener('click', function (e) {

        // Tombol +/- qty di keranjang
        const btnPlus = e.target.closest('.qty-btn.plus[data-cart-id]');
        if (btnPlus) {
            const item = cartItems.find(i => i.cartId === parseInt(btnPlus.dataset.cartId));
            if (item) { item.qty++; renderCart(); } return;
        }

        const btnMinus = e.target.closest('.qty-btn.minus[data-cart-id]');
        if (btnMinus) {
            const cartId = parseInt(btnMinus.dataset.cartId);
            const item = cartItems.find(i => i.cartId === cartId);
            if (item) {
                item.qty--;
                if (item.qty <= 0) cartItems = cartItems.filter(i => i.cartId !== cartId);
                renderCart();
            } return;
        }

        // Tombol hapus item
        const btnDel = e.target.closest('.delete-item');
        if (btnDel) {
            cartItems = cartItems.filter(i => i.cartId !== parseInt(btnDel.dataset.cartId));
            renderCart(); return;
        }

        // Tombol tambah dari kartu menu langsung (tanpa popup)
        const btnAddCard = e.target.closest('.qty-card');
        if (btnAddCard) {
            e.stopPropagation();
            const card = btnAddCard.closest('.menu-card');
            if (card) {
                const menuId = card.dataset.id;
                const popup  = document.getElementById(`popup-detail-menu-${menuId}`);
                if (popup) {
                    // Buka popup detail dulu agar kasir bisa atur qty & catatan
                    window._openPopup(`popup-detail-menu-${menuId}`);
                }
            } return;
        }

        // Tombol "Tambahkan ke Keranjang" dari popup detail menu
        const btnAddDetail = e.target.closest('.btn-add-from-detail');
        if (btnAddDetail) {
            const menuId   = btnAddDetail.dataset.id;
            const popup    = btnAddDetail.closest('.popup-menu-detail');
            const qtyEl    = popup?.querySelector('.pd-qty-val');
            const qty      = qtyEl ? parseInt(qtyEl.innerText) || 1 : 1;
            const catatan  = document.getElementById(`catatan-${menuId}`)?.value || '';

            // Ambil level pedas yang dipilih (null jika menu tidak punya pilihan level)
            const levelInput = popup?.querySelector(`input[name="level-pedas-${menuId}"]:checked`);
            const level = levelInput ? parseInt(levelInput.value) : null;

            addToCart({
                id:      menuId,
                name:    btnAddDetail.dataset.name,
                price:   parseInt(btnAddDetail.dataset.price),
                image:   btnAddDetail.dataset.image,
                qty,
                level,
                catatan,
            });
            window._closePopup(); return;
        }

        // +/- qty di popup detail menu
        const pdPlus = e.target.closest('.pd-btn-plus');
        if (pdPlus) {
            const val = pdPlus.previousElementSibling;
            if (val) val.innerText = parseInt(val.innerText) + 1;
            return;
        }
        const pdMin = e.target.closest('.pd-btn-min');
        if (pdMin) {
            const val = pdMin.nextElementSibling;
            if (val && parseInt(val.innerText) > 1) val.innerText = parseInt(val.innerText) - 1;
            return;
        }

        // Tombol "Order Now"
        const btnOrderNow = e.target.closest('#btn-order-now');
        if (btnOrderNow) {
            if (cartItems.length === 0) { alert('Keranjang kosong!'); return; }
            if (!document.getElementById('customer-name-input')?.value?.trim()) {
                alert('Isi nama pelanggan dulu!'); return;
            }
            const { total } = updateSummary();
            document.getElementById('popup-total-display').innerText = formatRp(total);
            window._openPopup('popup-payment'); return;
        }

        // Pilih QRIS di popup payment
        const btnPayQris = e.target.closest('#btn-pay-qris');
        if (btnPayQris) {
            selectedPaymentMethod = 'qris';
            window._openPopup('popup-qris-confirm'); return;
        }

        // Konfirmasi QRIS lanjut → tampilkan QR code
        const btnKonfirQris = e.target.closest('#btn-konfir-qris-lanjut');
        if (btnKonfirQris) {
            const { total } = updateSummary();
            document.getElementById('popup-qris-total-display').innerText = formatRp(total);
            const qrImg = document.getElementById('qris-img');
            if (qrImg) qrImg.src = `https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=TskuyPOS${total}`;
            window._openPopup('popup-qris-code'); return;
        }

        // Konfirmasi QRIS sukses
        const btnQrisSukses = e.target.closest('#btn-qris-sukses');
        if (btnQrisSukses) {
            submitOrder('qris'); return;
        }

        // Pilih TUNAI di popup payment
        const btnPayTunai = e.target.closest('#btn-pay-tunai');
        if (btnPayTunai) {
            selectedPaymentMethod = 'cash';
            const { total } = updateSummary();
            document.getElementById('popup-tunai-total-display').innerText = formatRp(total);
            document.getElementById('tunai-diterima').value = '';
            document.getElementById('tunai-kembalian-display').innerText = 'Rp 0';
            window._openPopup('popup-tunai'); return;
        }

        // Preset nominal tunai
        const btnPreset = e.target.closest('.preset-btn');
        if (btnPreset) {
            document.getElementById('tunai-diterima').value = btnPreset.dataset.preset;
            hitungKembalian(); return;
        }

        // Tombol "Bayar" di popup tunai
        const btnBayarTunai = e.target.closest('#btn-bayar-tunai');
        if (btnBayarTunai) {
            const diterima = parseFloat(document.getElementById('tunai-diterima')?.value) || 0;
            const { total } = updateSummary();
            if (diterima < total) { alert('Uang yang diterima kurang!'); return; }
            submitOrder('cash', diterima); return;
        }

        // Tombol "Selesai" di popup success → reset keranjang + reload antrian
        const btnSelesai = e.target.closest('#btn-selesai-transaksi');
        if (btnSelesai) {
            cartItems = [];
            renderCart();
            document.getElementById('customer-name-input').value = '';
            window._closePopup();
            // Reload halaman agar antrian ter-refresh
            setTimeout(() => location.reload(), 300); return;
        }

        // Konfirmasi hapus keranjang
        const btnKonfirHapus = e.target.closest('#btn-confirm-hapus-keranjang');
        if (btnKonfirHapus) {
            cartItems = [];
            renderCart();
            window._closePopup(); return;
        }

        // Tombol "Selesaikan Order" di popup detail antrian
        const btnSelesaikanOrder = e.target.closest('.btn-selesaikan-order');
        if (btnSelesaikanOrder) {
            const orderId = btnSelesaikanOrder.dataset.orderId;
            window._closePopup();
            selesaikanOrder(orderId); return;
        }

        // Tombol "Batalkan Pesanan" di popup detail antrian → buka konfirmasi
        const btnBatalOrder = e.target.closest('.btn-batal-order');
        if (btnBatalOrder) {
            cancelTargetOrderId   = btnBatalOrder.dataset.orderId;
            cancelTargetOrderCode = btnBatalOrder.dataset.orderCode;
            const tableLabel      = btnBatalOrder.dataset.table || 'Take Away';

            document.getElementById('alasan-batal-intro').innerHTML =
                `Pesanan <strong>${cancelTargetOrderCode} (${tableLabel})</strong> akan dibatalkan sepenuhnya. Mohon pilih alasan:`;

            window._openPopup('popup-alasan-batal'); return;
        }

        // Submit batalkan order
        const btnSubmitBatal = e.target.closest('#btn-submit-batal-order');
        if (btnSubmitBatal) {
            if (!cancelTargetOrderId) return;
            const selectedAlasan = document.querySelector('input[name="alasan"]:checked')?.value || 'lain';
            const teksAlasan     = document.getElementById('alasan-input-teks')?.value || '';
            const alasan         = selectedAlasan === 'lain' ? teksAlasan : selectedAlasan;
            window._closePopup();
            batalkanOrder(cancelTargetOrderId, alasan);
            cancelTargetOrderId = null; return;
        }
    });

    // ---- INPUT TUNAI — hitung kembalian real-time ----
    document.getElementById('tunai-diterima')?.addEventListener('input', hitungKembalian);
});