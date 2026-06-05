const URGENT_MS   = 20 * 60 * 1000;   
const TIMER_TICK  = 10 * 1000;         

let activeOrderId = null;
let itemStates    = {};   

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

function startClock() {
    function tick() {
        const d = new Date();
        clock.textContent = [d.getHours(), d.getMinutes(), d.getSeconds()]
            .map(n => String(n).padStart(2, '0')).join(':');
    }
    tick();
    setInterval(tick, 1000);
}

function startTimers() {
    const pendingCards = Array.from(document.querySelectorAll('.order-card[data-status="pending"]'));
    if (!pendingCards.length) return;

    function update() {
        const now = Date.now();
        pendingCards.forEach(card => {
            const createdAt  = new Date(card.dataset.createdAt).getTime();
            const elapsed    = now - createdAt;
            const elapsedMin = Math.floor(elapsed / 60000);

            const timerVal = card.querySelector('.timer-val');
            if (timerVal) timerVal.textContent = `${elapsedMin} mnt`;

            const wasUrgent = card.classList.contains('urgent');
            const isUrgent  = elapsed >= URGENT_MS;

            if (isUrgent && !wasUrgent) {
                card.classList.add('urgent');
                const pill = card.querySelector('[data-status-pill]');
                if (pill) {
                    pill.className = 'status-pill status-urgent';
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

function applyFilter(filter) {
    const cards = document.querySelectorAll('.order-card');
    cards.forEach(card => {
        const status = card.dataset.status;
        let show = (filter === 'semua') || (filter === 'menunggu' && status === 'pending') || (filter === 'selesai' && status === 'completed');
        card.style.display = show ? '' : 'none';
    });

    const grid    = document.getElementById('ordersGrid');
    const visible = [...cards].filter(c => c.style.display !== 'none').length;
    let emptyEl   = document.getElementById('jsEmptyState');

    if (!visible && !emptyEl) {
        emptyEl = document.createElement('div');
        emptyEl.id        = 'jsEmptyState';
        emptyEl.className = 'empty-state';
        emptyEl.style.gridColumn = '1/-1';
        emptyEl.innerHTML = `<div class="empty-icon">🍽️</div><div class="empty-title">Tidak ada pesanan</div>`;
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
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            applyFilter(tab.dataset.filter);
        });
    });
    applyFilter('semua');
}

function openDetailModal(card) {
    activeOrderId = card.dataset.orderId;
    const tipe    = card.dataset.tipe;
    const meja    = card.dataset.meja;
    const nota    = card.dataset.nota;
    const nama    = card.dataset.pelanggan;
    const items   = JSON.parse(card.dataset.items);

    itemStates = {};
    items.forEach((_, i) => { itemStates[i] = false; });

    const isDineIn    = tipe === 'Dine In';
    modalTitle.textContent = isDineIn ? `MEJA ${String(meja).padStart(2, '0')}` : `Order #${nota}`;
    modalInfo.innerHTML = `<span class="type-badge ${isDineIn ? 'badge-dine-in' : 'badge-takeaway'}">${tipe}</span>👤 ${nama}`;

    renderModalItems(items);
    updateProgress(items.length);
    overlay.classList.add('active');
    detailModal.classList.add('active');
}

function renderModalItems(items) {
    itemsWrap.innerHTML = '';
    items.forEach((item, idx) => {
        const div       = document.createElement('div');
        div.className   = 'modal-item';
        div.innerHTML   = `<div class="item-check">✓</div><div class="item-text"><div class="item-name">${item.nama}</div></div><div class="item-qty">×${item.qty}</div>`;
        div.addEventListener('click', () => {
            itemStates[idx] = !itemStates[idx];
            div.classList.toggle('done', itemStates[idx]);
            updateProgress(items.length);
        });
        itemsWrap.appendChild(div);
    });
}

function updateProgress(total) {
    const done = Object.values(itemStates).filter(Boolean).length;
    const pct  = total > 0 ? (done / total * 100) : 0;
    progDone.textContent  = done;
    progTotal.textContent = total;
    progFill.style.width  = pct + '%';
    btnLabel.textContent  = `Selesaikan Pesanan (${done}/${total})`;
    btnComplete.disabled  = done < total;
}

function closeDetailModal() {
    detailModal.classList.remove('active');
    overlay.classList.remove('active');
    activeOrderId = null;
}

function completeOrder() {
    const card = document.getElementById(`card-${activeOrderId}`);
    if (!card) return;
    const orderId = activeOrderId;

    closeDetailModal();
    successDetail.innerHTML = `<p>Pesanan #${orderId} sukses diproses dan dikirim!</p>`;
    successModal.dataset.completedOrderId = orderId;
    overlay.classList.add('active');
    successModal.classList.add('active');

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    fetch(`/koki/${orderId}/selesaikan`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }
    });
}

function returnToQueue() {
    const orderId = successModal.dataset.completedOrderId;
    if (orderId) {
        const card = document.getElementById(`card-${orderId}`);
        if (card) {
            card.dataset.status = 'completed';
            card.className = 'order-card completed';
            card.querySelector('[data-status-pill]').textContent = '✓ Selesai';
            card.querySelector('.card-chevron')?.remove();
        }
    }
    successModal.classList.remove('active');
    overlay.classList.remove('active');
    updateCounters();
}

function showToast(msg) {
    if (!toastWrap) return;
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.textContent = msg;
    toastWrap.prepend(toast);
    setTimeout(() => toast.remove(), 4000);
}

function initEvents() {
    document.querySelectorAll('.order-card').forEach(card => {
        card.addEventListener('click', () => {
            if (card.dataset.status !== 'completed') openDetailModal(card);
        });
    });
    document.getElementById('modalCloseBtn').addEventListener('click', closeDetailModal);
    overlay.addEventListener('click', () => { if (detailModal.classList.contains('active')) closeDetailModal(); });
    btnComplete.addEventListener('click', completeOrder);
    document.getElementById('btnBackToQueue').addEventListener('click', returnToQueue);
}

document.addEventListener('DOMContentLoaded', () => {
    startClock();
    startTimers();
    updateCounters();
    initFilterTabs();
    initEvents();
});