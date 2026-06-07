/* ============================================================
   KOKI.JS — Logic for Kitchen Console Tskuy POS
   ============================================================ */

const KokiApp = (function() {

    let currentActiveOrderId = null;
    
    // --- 0. SIMULATED DATA ---
    const orderData = {
        // Status: 'waiting', 'cooking', 'done'
        "101": { 
            identifier: "MEJA 05", type: "Makan Di Tempat", status: "waiting", items: [
                { id: 1, name: "Kopi Susu Gula Aren", qty: 2, note: "Kurangi gula", done: false },
                { id: 2, name: "Roti Bakar Keju", qty: 1, note: "Panggang kering", done: false },
                { id: 3, name: "Nasi Goreng Ayam", qty: 1, note: "Pedas sedang", done: false },
                { id: 4, name: "Es Teh Manis", qty: 2, note: "", done: false },
                { id: 5, name: "Tahu Isi", qty: 3, note: "Jangan terlalu asin", done: false },
            ] 
        },
        "102": { 
            identifier: "DELIVERY 12", type: "Bawa Pulang", status: "cooking", items: [
                { id: 6, name: "Es Teh Manis Jumbo", qty: 3, note: "", done: false },
                { id: 7, name: "Mie Instan Kari Ayam", qty: 1, note: "Tambahkan telur mata sapi", done: false },
            ] 
        },
        "103": { 
            identifier: "BILL #24", type: "Open Bill", status: "cooking", items: [
                { id: 8, name: "Ayam Geprek Sambal Matah", qty: 1, note: "Sambal dipisah", done: true },
                { id: 9, name: "Lemon Tea Dingin", qty: 1, note: "Hangat saja", done: true },
            ] 
        },
        "104": { 
            identifier: "MEJA 01", type: "Makan Di Tempat", status: "done", items: [
                { id: 10, name: "Kentang Goreng", qty: 2, note: "", done: true },
                { id: 11, name: "Teh Hangat", qty: 2, note: "", done: true },
            ] 
        },
    };

    // Helper function for time formatting
    function formatTime(date) {
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        const seconds = String(date.getSeconds()).padStart(2, '0');
        return `${hours}:${minutes}:${seconds}`;
    }

    function formatElapsed(seconds) {
        const minutes = Math.floor(seconds / 60);
        return `${minutes} mnt`;
    }

    // --- 1. CLOCK AND TIMER LOGIC ---
    function startClock() {
        const clockElement = document.getElementById('liveClock');
        if (!clockElement) return;
        function updateClock() {
            const now = new Date();
            clockElement.textContent = formatTime(now);
        }
        updateClock();
        setInterval(updateClock, 1000);
    }

    function startCardTimers() {
        const orderCards = document.querySelectorAll('.order-card');
        if (orderCards.length === 0) return;
        function updateCardTimers() {
            const now = new Date().getTime();
            orderCards.forEach(card => {
                // Stop if card is done 
                if (card.getAttribute('data-status') === 'done') return; 
                
                const acceptedAtISO = card.getAttribute('data-accepted-at');
                const timerValueElement = card.querySelector('.timer-value');

                if (!acceptedAtISO || !timerValueElement) return;

                const acceptedAt = new Date(acceptedAtISO).getTime();
                let elapsedSeconds = Math.floor((now - acceptedAt) / 1000);

                if (elapsedSeconds < 0) elapsedSeconds = 0; 
                timerValueElement.textContent = formatElapsed(elapsedSeconds);
                
                // Logika perubahan warna (urgency)
                const warningThreshold = 10 * 60; 
                const urgentThreshold  = 15 * 60; 
                
                card.classList.remove('card-fresh', 'card-warning', 'card-urgent');
                
                if (elapsedSeconds >= urgentThreshold) {
                    card.classList.add('card-urgent');
                } else if (elapsedSeconds >= warningThreshold) {
                    card.classList.add('card-warning');
                } else {
                    card.classList.add('card-fresh');
                }
                
            });
        }
        updateCardTimers();
        setInterval(updateCardTimers, 1000);
    }

    // --- 2. NOTIFICATION AND POLLING LOGIC ---
    function showToast(message) {
        const container = document.getElementById('toastContainer');
        if (!container) return;

        const initialToast = container.querySelector('.toast');
        if(initialToast && initialToast.textContent.includes('Simulasi JS aktif')) {
             initialToast.remove();
        }

        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.innerHTML = `<span class="toast-icon">🔔</span>${message}`;
        
        container.prepend(toast);

        setTimeout(() => {
            toast.classList.add('hiding');
            toast.addEventListener('animationend', () => {
                toast.remove();
            }, { once: true });
        }, 5000);
    }
    
    function startPolling(apiUrl, currentMenungguCount) {
        const pollingInterval = 30 * 1000; 
        
        setInterval(() => {
            // SIMULASI: Pesanan baru masuk (Notifikasi Pelanggan)
            const newOrderIdentifier = `MEJA ${Math.floor(Math.random() * 20) + 1}`;
            showToast(`Pesanan baru masuk: ${newOrderIdentifier}`);
            document.getElementById('notifBadge').classList.add('active'); 
        }, pollingInterval);
    }

    // --- 3. MODAL INTERACTION LOGIC ---
    
    function updateCardStatus(orderId) {
        const cardElement = document.getElementById(`card-${orderId}`);
        if (!cardElement) return;

        const order = orderData[orderId];
        if (order.status === 'done') {
            cardElement.classList.remove('card-fresh', 'card-warning', 'card-urgent');
            cardElement.classList.add('card-done');
            cardElement.setAttribute('data-status', 'done');
            
            // Update footer status pill
            const statusPill = cardElement.querySelector('.card-status-pill');
            statusPill.classList.remove('status-waiting', 'status-cooking');
            statusPill.classList.add('status-done');
            statusPill.textContent = '✓ Selesai';

            // Stop the timer
            const timerDot = cardElement.querySelector('.timer-dot');
            if (timerDot) timerDot.style.animation = 'none';

            showToast(`Pesanan ${order.identifier} berhasil diselesaikan!`);
        }
    }
    
    function updateCompletionButton(orderId) {
        const order = orderData[orderId];
        const totalItems = order.items.length;
        const doneItems = order.items.filter(item => item.done).length;
        const btn = document.getElementById('completeOrderBtn');

        btn.innerHTML = `<i class="fas fa-check"></i> Selesaikan Pesanan (${doneItems}/${totalItems})`;

        if (doneItems === totalItems) {
            btn.classList.remove('btn-disabled');
            btn.classList.add('btn-active');
            btn.disabled = false;
        } else {
            btn.classList.add('btn-disabled');
            btn.classList.remove('btn-active');
            btn.disabled = true;
        }
    }

    function toggleItemDone(itemIndex) {
        if (!currentActiveOrderId) return;

        const order = orderData[currentActiveOrderId];
        const item = order.items[itemIndex];

        // Toggle status in data
        item.done = !item.done;

        // Toggle status visually
        const itemRow = document.getElementById(`modal-item-${itemIndex}`);
        if (item.done) {
            itemRow.classList.add('done');
        } else {
            itemRow.classList.remove('done');
        }

        // Update button status
        updateCompletionButton(currentActiveOrderId);
    }

    function renderModalItems(orderId) {
        const order = orderData[orderId];
        const listContainer = document.getElementById('modalItemsList');
        listContainer.innerHTML = ''; 

        order.items.forEach((item, index) => {
            const row = document.createElement('div');
            row.className = `modal-item-row ${item.done ? 'done' : ''}`;
            row.id = `modal-item-${index}`;
            row.setAttribute('data-item-index', index);
            
            row.innerHTML = `
                <div class="item-check-icon">
                    <i class="fas fa-check"></i>
                </div>
                <div class="modal-item-info">
                    <div class="modal-item-name">${item.name}</div>
                    ${item.note ? `<div class="modal-item-note">Catatan: ${item.note}</div>` : ''}
                </div>
                <div class="modal-item-qty">×${item.qty}</div>
            `;

            row.addEventListener('click', () => {
                toggleItemDone(index);
            });

            listContainer.appendChild(row);
        });
        updateCompletionButton(orderId);
    }

    function openModal(orderId, identifier, type) {
        currentActiveOrderId = orderId;
        const detailModal = document.getElementById('detailModal');
        const overlay = document.getElementById('modalOverlay');

        document.getElementById('modalIdentifier').textContent = identifier;
        document.getElementById('modalType').textContent = type;
        document.getElementById('modalOrderId').textContent = `#${orderId}`;
        
        renderModalItems(orderId);

        overlay.classList.add('active');
        detailModal.classList.add('active');
        document.body.style.overflow = 'hidden'; 
    }

    function closeModal(modalElement) {
        const overlay = document.getElementById('modalOverlay');
        overlay.classList.remove('active');
        modalElement.classList.remove('active');
        document.body.style.overflow = 'auto';
        currentActiveOrderId = null; 
    }
    
    function openSuccessModal() {
         const detailModal = document.getElementById('detailModal');
         const successModal = document.getElementById('successModal');
         
         closeModal(detailModal); // Tutup detail modal
         successModal.classList.add('active'); // Buka success modal
         document.getElementById('modalOverlay').classList.add('active'); // Pastikan overlay tetap aktif
    }

    function initInteractions() {
        const orderCards = document.querySelectorAll('.order-card');
        const filterTabs = document.querySelectorAll('.filter-tab');
        
        // --- FILTER TAB FUNCTIONALITY ---
        function applyFilter(filter) {
            orderCards.forEach(card => {
                const status = card.getAttribute('data-status');
                
                let shouldShow = false;
                if (filter === 'semua') {
                    shouldShow = true;
                } else if (filter === 'menunggu') {
                    // Tampilkan 'waiting' dan 'cooking'
                    shouldShow = (status === 'waiting' || status === 'cooking');
                } else if (filter === 'selesai') {
                    // Tampilkan 'done'
                    shouldShow = (status === 'done');
                }

                if (shouldShow) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        filterTabs.forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Visual activation
                filterTabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');

                // Apply functional filter
                const filter = this.getAttribute('data-filter');
                applyFilter(filter);
            });
        });
        // Apply default filter on load
        applyFilter(document.querySelector('.filter-tab.active').getAttribute('data-filter'));

        // --- MODAL AND BUTTON INTERACTIONS ---

        // Handle Notification Button
        document.getElementById('notifButton').addEventListener('click', function() {
            document.getElementById('notifBadge').classList.remove('active');
            showToast("Notifikasi dibuka. Semua notifikasi telah ditandai dibaca.");
        });

        // Handle Order Cards (Open Detail Modal)
        orderCards.forEach(card => {
            card.addEventListener('click', function(e) {
                e.preventDefault();
                // Check if card is done (redundant due to CSS, but safe)
                if (this.getAttribute('data-status') === 'done') return;
                
                const orderId = this.getAttribute('data-order-id');
                const identifier = this.querySelector('.card-identifier').textContent;
                const type = this.querySelector('.type-badge').textContent.trim();
                
                openModal(orderId, identifier, type);
            });
        });

        // Handle 'X' close button on Detail Modal
        document.getElementById('closeDetailBtn').addEventListener('click', function() {
            closeModal(document.getElementById('detailModal'));
        });

        // Handle "Selesaikan Pesanan" button inside the Detail Modal
        document.getElementById('completeOrderBtn').addEventListener('click', function() {
            if (this.classList.contains('btn-active')) {
                // 1. Mark order as done in data
                orderData[currentActiveOrderId].status = 'done';
                
                // 2. Update card appearance on main grid
                updateCardStatus(currentActiveOrderId);
                
                // 3. Close detail modal and open success modal
                openSuccessModal();
            }
        });

        // Handle "Kembali ke Antrian" button inside the Success Modal
        document.getElementById('returnToQueueBtn').addEventListener('click', function() {
            closeModal(document.getElementById('successModal'));
            // Setelah kembali, terapkan filter yang sedang aktif
            applyFilter(document.querySelector('.filter-tab.active').getAttribute('data-filter'));
        });

        // Close Modal via Overlay click (Hanya jika klik tepat di overlay, bukan di modal)
        document.getElementById('modalOverlay').addEventListener('click', function(e) {
            const detailModal = document.getElementById('detailModal');
            const successModal = document.getElementById('successModal');
            
            if (e.target === this) {
                if (detailModal.classList.contains('active')) {
                    closeModal(detailModal);
                }
                if (successModal.classList.contains('active')) {
                    closeModal(successModal);
                }
            }
        });
    }


    // ============================================================
    // Public API
    // ============================================================
    return {
        startClock: startClock,
        startCardTimers: startCardTimers,
        startPolling: startPolling,
        initInteractions: initInteractions
    };

})();

// Inisialisasi KokiApp setelah DOM siap
document.addEventListener('DOMContentLoaded', () => {
    KokiApp.startClock();
    KokiApp.startCardTimers();
    // Simulasi Polling (mengganti route laravel dengan string statis)
    KokiApp.startPolling('SIMULATED_API_ROUTE', 5);
    KokiApp.initInteractions();
});