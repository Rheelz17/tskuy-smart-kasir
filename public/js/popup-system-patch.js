/**
 * popup-system-patch.js
 * 
 * FIX 1: Double overlay saat popup buka dari drawer hamburger
 *         → Drawer otomatis tutup dulu sebelum popup buka
 *         → body.popup-open class untuk sembunyikan drawer overlay via CSS
 * 
 * FIX 2: Konfirmasi hapus item di keranjang (orders page)
 *         → Tombol delete item cart buka popup warning dulu
 *         → Baru hapus setelah user konfirmasi "Ya, Hapus Pesanan"
 * 
 * CARA PAKAI: Include script ini di bawah script utama orders page
 * Bisa dimasukkan ke layouts/pelanggan.blade.php sebagai partial script
 */

document.addEventListener('DOMContentLoaded', function () {

    /* ==========================================================
       FIX 1: POPUP SYSTEM — TUTUP DRAWER DULU SEBELUM POPUP
    ========================================================== */
    const drawer = document.getElementById('drawer');
    const drawerOverlay = document.getElementById('drawer-overlay');
    const hamburgerBtn = document.getElementById('hamburger-btn');

    function closeDrawer() {
        if (!drawer) return;
        if (drawer.classList.contains('is-open')) {
            drawer.classList.remove('is-open');
            if (drawerOverlay) drawerOverlay.classList.remove('is-open');
            if (hamburgerBtn) hamburgerBtn.classList.remove('is-open');
            document.body.style.overflow = '';
        }
    }

    // ===== OVERRIDE: openPopup global =====
    // Intercept semua klik [data-open] supaya drawer tutup dulu
    document.querySelectorAll('[data-open]').forEach(function (el) {
        // Hapus listener lama tidak bisa, tapi kita tambahkan handler baru di capture phase
        // (capture=true artinya jalan SEBELUM listener lain)
        el.addEventListener('click', function (e) {
            // Kalau drawer lagi buka, tutup dulu
            if (drawer && drawer.classList.contains('is-open')) {
                closeDrawer();
                // Delay buka popup sedikit biar transisi drawer kelar
                const targetId = this.dataset.open;
                setTimeout(function () {
                    openPopupGlobal(targetId);
                }, 120);
                e.stopImmediatePropagation(); // Hentikan handler lain agar tidak dobel-buka
            }
            // Kalau drawer sudah tutup, biarkan handler biasa jalan
        }, true); // capture phase
    });

    function openPopupGlobal(id) {
        const overlay = document.getElementById('popup-overlay');
        const popup = document.getElementById(id);
        if (overlay) overlay.classList.add('is-open');
        if (popup) popup.classList.add('is-open');
        document.body.classList.add('popup-open');
        document.body.style.overflow = 'hidden';
    }

    // Kalau popup ditutup, hapus class popup-open dari body
    const originalCloseHandlers = document.querySelectorAll('[data-close]');
    originalCloseHandlers.forEach(function (el) {
        el.addEventListener('click', function () {
            document.body.classList.remove('popup-open');
        });
    });

    // Klik overlay popup juga trigger remove popup-open
    const popupOverlay = document.getElementById('popup-overlay');
    if (popupOverlay) {
        popupOverlay.addEventListener('click', function () {
            document.body.classList.remove('popup-open');
        });
    }

    /* ==========================================================
       FIX 2: DELETE ITEM KERANJANG — POPUP KONFIRMASI
       (Untuk halaman ordersPelanggan.blade.php)
    ========================================================== */

    // Buat popup warning kalau belum ada di DOM
    function ensureDeletePopup() {
        if (document.getElementById('popup-delete-confirm')) return;

        const popup = document.createElement('div');
        popup.className = 'popup popup-warning';
        popup.id = 'popup-delete-confirm';
        popup.innerHTML = `
            <div class="popup-body popup-body-warning">
                <div class="warning-icon-wrap">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none">
                        <path d="M12 9v4M12 17h.01" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
                    </svg>
                </div>
                <p class="warning-title">Apakah kamu yakin ingin menghapus item dari keranjang?</p>
                <p class="warning-desc">Item akan dihapus</p>
                <div class="warning-actions">
                    <button class="btn-batal-warning" data-close>Tidak</button>
                    <button class="btn-confirm-warning" id="btn-confirm-delete">Ya, Hapus Pesanan</button>
                </div>
            </div>
        `;
        document.body.appendChild(popup);

        // Bind close untuk tombol yang baru dibuat
        popup.querySelectorAll('[data-close]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                popup.classList.remove('is-open');
                if (popupOverlay) popupOverlay.classList.remove('is-open');
                document.body.classList.remove('popup-open');
                document.body.style.overflow = '';
            });
        });
    }

    ensureDeletePopup();

    // Track index item yang mau dihapus
    let pendingDeleteCartKey = null;

    // ===== INTERCEPT: Tombol hapus item di cart (.delete-item) =====
    // Delegate ke container karena item dibuat dinamis
    document.addEventListener('click', function (e) {
        const deleteBtn = e.target.closest('.delete-item');
        if (!deleteBtn) return;

        // Cegah hapus langsung
        e.preventDefault();
        e.stopImmediatePropagation();

        // Simpan referensi ke item yang mau dihapus
        pendingDeleteCartKey = deleteBtn.closest('[data-cart-key]') 
            ? deleteBtn.closest('[data-cart-key]').dataset.cartKey 
            : deleteBtn.dataset.cartKey || deleteBtn.dataset.id || null;

        // Simpan referensi elemen tombol supaya bisa panggil aksi aslinya
        pendingDeleteBtn = deleteBtn;

        // Buka popup
        openPopupGlobal('popup-delete-confirm');
    }, true);

    let pendingDeleteBtn = null;

    // Konfirmasi hapus
    document.addEventListener('click', function (e) {
        if (e.target.id !== 'btn-confirm-delete') return;

        // Tutup popup
        const popup = document.getElementById('popup-delete-confirm');
        if (popup) popup.classList.remove('is-open');
        if (popupOverlay) popupOverlay.classList.remove('is-open');
        document.body.classList.remove('popup-open');
        document.body.style.overflow = '';

        // Eksekusi hapus item: panggil fungsi hapus dari cart JS yang sudah ada
        if (typeof window.removeCartItem === 'function' && pendingDeleteCartKey !== null) {
            window.removeCartItem(pendingDeleteCartKey);
        } else if (pendingDeleteBtn) {
            // Fallback: trigger klik langsung ke tombol asli tapi lewati listener kita
            // dengan dispatch event custom
            const realClick = new CustomEvent('real-delete-click', { bubbles: true });
            pendingDeleteBtn.dispatchEvent(realClick);
        }

        pendingDeleteBtn = null;
        pendingDeleteCartKey = null;
    });

    // ===== SUPPORT: cart JS expose fungsi removeCartItem supaya bisa dipanggil di atas =====
    // Kalau cart JS-mu sudah ada fungsi removeCartItem, expose ke window.removeCartItem
    // Kalau belum, script ini akan fallback ke pendingDeleteBtn click event di atas

    /* ==========================================================
       CLEAR CART BUTTON — Juga butuh konfirmasi
    ========================================================== */
    const clearCartBtn = document.getElementById('btn-clear-cart');
    if (clearCartBtn) {
        clearCartBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            // Modifikasi teks popup untuk clear semua
            const popup = document.getElementById('popup-delete-confirm');
            if (popup) {
                const title = popup.querySelector('.warning-title');
                const desc = popup.querySelector('.warning-desc');
                if (title) title.textContent = 'Hapus semua item dari keranjang?';
                if (desc) desc.textContent = 'Seluruh pesanan di keranjang akan dikosongkan';
            }

            pendingDeleteBtn = clearCartBtn;
            pendingDeleteCartKey = '__clear_all__';
            openPopupGlobal('popup-delete-confirm');
        }, true);
    }

});