<div class="popup-overlay" id="popup-overlay"></div>

<div class="notif-panel" id="popup-notif-panel">
    <div class="notif-panel-header">
    <h3>Notifikasi</h3>
    <button class="notif-read-link" data-close>Tandai sudah dibaca</button>
    </div>
    <div class="notif-panel-list">
    <div class="notif-panel-item" data-close>
        <div class="notif-panel-icon-wrap"><svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M6 10C6 6.686 8.686 4 12 4C15.314 4 18 6.686 18 10V14L20 16V17H4V16L6 14V10Z" fill="#EFB100"/><path d="M10 17C10 18.105 10.895 19 12 19C13.105 19 14 18.105 14 17" stroke="#EFB100" stroke-width="1.8"/></svg></div>
        <div class="notif-panel-content">
        <p class="notif-panel-title">Pesanan Baru!</p>
        <p class="notif-panel-desc">MEJA 4 baru saja membuat pesanan</p>
        <p class="notif-panel-order">#T0987</p>
        <p class="notif-panel-time">Baru saja</p>
        </div>
    </div>
    <div class="notif-panel-item" data-close>
        <div class="notif-panel-icon-wrap"><svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M6 10C6 6.686 8.686 4 12 4C15.314 4 18 6.686 18 10V14L20 16V17H4V16L6 14V10Z" fill="#EFB100"/><path d="M10 17C10 18.105 10.895 19 12 19C13.105 19 14 18.105 14 17" stroke="#EFB100" stroke-width="1.8"/></svg></div>
        <div class="notif-panel-content">
        <p class="notif-panel-title">Pesanan Baru!</p>
        <p class="notif-panel-desc">MEJA 7 baru saja membuat pesanan</p>
        <p class="notif-panel-order">#T0988</p>
        <p class="notif-panel-time">2 menit lalu</p>
        </div>
    </div>
    </div>
    <button class="notif-panel-footer" data-close>Lihat Semua Notifikasi</button>
</div>

<div class="popup popup-profil" id="popup-profil">
    <div class="popup-header popup-header-yellow">
    <span>Profil Saya</span>
    <button class="popup-close popup-close-white" data-close>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="#fff" stroke-width="1.8"/><path d="M15 9L9 15M9 9L15 15" stroke="#fff" stroke-width="1.8" stroke-linecap="round"/></svg>
    </button>
    </div>
    <div class="popup-body popup-body-profil">
    <div class="profil-top">
        <div class="profil-avatar">
        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=eab308&color=fff" alt="{{ Auth::user()->name }}">
        </div>
        <div class="profil-top-info">
        <p class="profil-name">{{ Auth::user()->name }}</p>
        <p class="profil-role">Cashier</p>
        </div>
    </div>
    <div class="profil-field-wrap"><label class="profil-label">Nama lengkap</label><div class="profil-field">{{ Auth::user()->name }}</div></div>
    <div class="profil-field-wrap"><label class="profil-label">ID Karyawan</label><div class="profil-field">{{ Auth::user()->employee_id }}</div></div>
    <div class="profil-field-wrap"><label class="profil-label">Jabatan</label><div class="profil-field">Kasir</div></div>
    <div class="profil-field-wrap"><label class="profil-label">No Telepon</label><div class="profil-field">{{ Auth::user()->phone ?? 'Belum diatur' }}</div></div>
    <div class="profil-field-wrap"><label class="profil-label">Alamat</label><div class="profil-field">Jl. Raya Cileunyi No. 123, RT 03/RW 05, Bandung, Jawa Barat</div></div>
    <div class="profil-actions">
        <button class="popup-btn profil-btn-close" data-close>Tutup</button>
    </div>
    </div>
</div>