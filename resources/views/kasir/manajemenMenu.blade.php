@extends('layouts.kasir')

@section('title', 'Manajemen Menu – Warkop Tskuy')

@section('header_title', 'Manajemen Menu')
@section('header_subtitle', 'Kelola ketersediaan menu')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/kasir-pages.css') }}" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    /* ============================================================
           TAMBAHAN STYLE — Tabel Menu Kasir
           ============================================================ */

    /* Scroll area utama */
    .scroll-area {
        flex: 1;
        overflow-y: auto;
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    /* Action bar atas */
    .action-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    /* Tab kategori */
    .category-tabs {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .tab {
        padding: 6px 14px;
        border-radius: 20px;
        border: 1.5px solid var(--border);
        background: #fff;
        font-size: 12px;
        font-weight: 600;
        color: #555;
        cursor: pointer;
        transition: all .2s;
        white-space: nowrap;
    }

    .tab:hover {
        border-color: var(--kuning);
        color: #92400e;
    }

    .tab.active {
        background: var(--kuning);
        border-color: var(--kuning);
        color: #fff;
    }

    /* Search */
    .action-right {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .search-wrapper {
        position: relative;
    }

    .search-wrapper input {
        height: 38px;
        padding: 0 36px 0 14px;
        border: 1.5px solid var(--border);
        border-radius: 20px;
        font-size: 13px;
        outline: none;
        width: 200px;
        transition: border-color .2s;
        font-family: "Poppins", sans-serif;
        color: var(--teks);
        background: var(--bg-page);
    }

    .search-wrapper input:focus {
        border-color: var(--kuning);
        background: #fff;
    }

    .search-wrapper .search-icon {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
    }

    /* Table container */
    .table-container {
        background: #fff;
        border-radius: var(--radius-md);
        overflow: hidden;
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
    }

    #tabel-menu {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    #tabel-menu thead tr {
        background: #fafafa;
        border-bottom: 1.5px solid var(--border);
    }

    #tabel-menu th {
        padding: 12px 14px;
        text-align: center;
        font-size: 12px;
        font-weight: 700;
        color: #888;
        white-space: nowrap;
    }

    #tabel-menu th.col-left,
    #tabel-menu td.col-left {
        text-align: left;
    }

    #tabel-menu tbody tr {
        border-bottom: 1px solid #f3f4f6;
        transition: background .15s;
    }

    #tabel-menu tbody tr:last-child {
        border-bottom: none;
    }

    #tabel-menu tbody tr:hover {
        background: #fffbeb;
    }

    #tabel-menu td {
        padding: 12px 14px;
        text-align: center;
        color: var(--teks);
        vertical-align: middle;
    }

    .text-bold {
        font-weight: 600;
    }

    .stok-badge {
        display: inline-block;
        background: #f3f4f6;
        color: #555;
        font-size: 11px;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 20px;
    }

    /* Status badge */
    .status-avail {
        display: inline-block;
        background: #dcfce7;
        color: #166534;
        font-size: 11px;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 20px;
    }

    .status-habis {
        display: inline-block;
        background: #fee2e2;
        color: #991b1b;
        font-size: 11px;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 20px;
    }

    /* Toggle switch — sesuai style admin */
    .popup-form-toggle {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .popup-toggle-switch {
        position: relative;
        width: 44px;
        height: 24px;
        cursor: pointer;
        display: block;
    }

    .popup-toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
        position: absolute;
    }

    .popup-toggle-track {
        position: absolute;
        inset: 0;
        background: #d1d5db;
        border-radius: 999px;
        transition: background .25s;
    }

    .popup-toggle-thumb {
        position: absolute;
        top: 3px;
        left: 3px;
        width: 18px;
        height: 18px;
        background: #fff;
        border-radius: 50%;
        transition: transform .25s;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .18);
    }

    .popup-toggle-switch input:checked+.popup-toggle-track {
        background: #22c55e;
    }

    .popup-toggle-switch input:checked+.popup-toggle-track .popup-toggle-thumb {
        transform: translateX(20px);
    }

    /* Kategori badge */
    .kat-badge {
        display: inline-block;
        font-size: 11px;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 20px;
    }

    .kat-makanan {
        background: #fef3c7;
        color: #92400e;
    }

    .kat-minuman {
        background: #dbeafe;
        color: #1e40af;
    }

    .kat-snack {
        background: #fce7f3;
        color: #9d174d;
    }

    /* Footer info */
    .content-footer {
        padding: 8px 0 2px;
        font-size: 12px;
        color: #aaa;
    }

    /* Empty state */
    .empty-row td {
        padding: 48px 20px !important;
        text-align: center !important;
        color: #bbb;
        font-size: 13px;
    }

    /* Toast notifikasi */
    #menu-toast {
        position: fixed;
        bottom: 24px;
        left: 50%;
        transform: translateX(-50%) translateY(20px);
        background: #1a1a1a;
        color: #fff;
        padding: 10px 20px;
        border-radius: 30px;
        font-size: 13px;
        font-weight: 600;
        z-index: 9999;
        opacity: 0;
        transition: opacity .3s, transform .3s;
        pointer-events: none;
        white-space: nowrap;
    }

    #menu-toast.show {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }

    /* Responsive */
    @media (max-width: 767px) {
        .action-bar {
            flex-direction: column;
            align-items: flex-start;
        }

        .search-wrapper input {
            width: 100%;
        }

        .action-right {
            width: 100%;
        }

        #tabel-menu th:nth-child(3),
        #tabel-menu td:nth-child(3),
        #tabel-menu th:nth-child(4),
        #tabel-menu td:nth-child(4),
        #tabel-menu th:nth-child(6),
        #tabel-menu td:nth-child(6) {
            display: none;
        }

        .table-container {
            overflow-x: auto;
        }
    }
</style>
@endsection

@section('content')
<main class="scroll-area">

    {{-- ===== ACTION BAR ===== --}}
    <div class="action-bar">
        {{-- Tab Kategori --}}
        {{-- <div class="category-tabs">
                <button class="tab active" data-kategori="all">Semua</button>
                @foreach ($categories as $cat)
                    <button class="tab" data-kategori="{{ strtolower($cat->name) }}">
        {{ ucfirst($cat->name) }}
        </button>
        @endforeach
    </div> --}}

    {{-- Search --}}
    <div class="action-right">
        <div class="search-wrapper">
            <input type="text" id="search-menu-input" placeholder="Cari menu...">
            <span class="search-icon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                    <circle cx="11" cy="11" r="7" stroke="#fbbf24" stroke-width="2" />
                    <path d="M16.5 16.5L21 21" stroke="#fbbf24" stroke-width="2" stroke-linecap="round" />
                </svg>
            </span>
        </div>
    </div>
    </div>

    {{-- ===== TABEL MENU ===== --}}
    @include('components.menu-tabel', ['menus' => $menus])

    {{-- Footer info --}}
    <footer class="content-footer">
        <p class="data-info">Memuat data...</p>
        <div class="pagination">
        </div>
    </footer>

</main>

{{-- Toast --}}
<div id="menu-toast"></div>

@endsection

@section('scripts')
<script src="{{ asset('js/menu.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // ============================================================
        // HELPER: Toast notifikasi
        // ============================================================
        function showToast(msg, isError = false) {
            const toast = document.getElementById('menu-toast');
            if (!toast) return;
            toast.textContent = msg;
            toast.style.background = isError ? '#ef4444' : '#1a1a1a';
            toast.classList.add('show');
            clearTimeout(toast._t);
            toast._t = setTimeout(() => toast.classList.remove('show'), 2500);
        }

        // ============================================================
        // FILTER: Tab Kategori
        // ============================================================
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                filterTable();
            });
        });

        // ============================================================
        // FILTER: Search
        // ============================================================
        document.getElementById('search-menu-input')?.addEventListener('input', filterTable);

        function filterTable() {
            const activeKat = document.querySelector('.tab.active')?.dataset.kategori || 'all';
            const query = document.getElementById('search-menu-input')?.value.toLowerCase().trim() || '';
            let count = 0;

            document.querySelectorAll('#menu-tbody tr[data-kategori]').forEach(row => {
                const kat = row.dataset.kategori || '';
                const nama = row.dataset.nama || '';

                const matchKat = activeKat === 'all' || kat === activeKat;
                const matchCari = query === '' || nama.includes(query);

                row.style.display = (matchKat && matchCari) ? '' : 'none';
                if (matchKat && matchCari) count++;
            });

            document.getElementById('data-info-label').textContent =
                `Menampilkan ${count} menu`;
        }

        // ============================================================
        // TOGGLE STATUS MENU (AJAX)
        // ============================================================
        document.querySelectorAll('.toggle-status-menu').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const menuId = this.dataset.id;
                const menuNama = this.dataset.nama;
                const isChecked = this.checked;
                const self = this;

                // Kirim ke endpoint toggle kasir
                fetch(`/kasir/menu/${menuId}/toggle`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                    })
                    .then(r => r.json())
                    .then(d => {
                        if (d.success) {
                            const label = isChecked ? 'Tersedia' : 'Habis';
                            showToast(`${menuNama} → ${label}`);
                        } else {
                            // Kembalikan toggle ke posisi sebelumnya
                            self.checked = !isChecked;
                            showToast('Gagal mengubah status menu.', true);
                        }
                    })
                    .catch(() => {
                        self.checked = !isChecked;
                        showToast('Gagal koneksi ke server.', true);
                    });
            });
        });

    });
</script>
@endsection