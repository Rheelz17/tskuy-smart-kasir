@extends('layouts.kasir')

@section('title', 'Manajemen Menu – Warkop Tskuy')

@section('header_title', 'Manajemen Menu')
@section('header_subtitle', 'Kelola ketersediaan menu')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/kasir-pages.css') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .scroll-area { flex:1; overflow-y:auto; padding:16px; display:flex; flex-direction:column; gap:14px; }
        .action-bar  { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; }
        .category-tabs { display:flex; gap:6px; flex-wrap:wrap; }
        .tab { padding:6px 14px; border-radius:20px; border:1.5px solid var(--border); background:#fff; font-size:12px; font-weight:600; color:#555; cursor:pointer; transition:all .2s; white-space:nowrap; }
        .tab:hover  { border-color:var(--kuning); color:#92400e; }
        .tab.active { background:var(--kuning); border-color:var(--kuning); color:#fff; }
        .action-right { display:flex; align-items:center; gap:10px; }
        .search-wrapper { position:relative; }
        .search-wrapper input { height:38px; padding:0 36px 0 14px; border:1.5px solid var(--border); border-radius:20px; font-size:13px; outline:none; width:220px; transition:border-color .2s; font-family:"Poppins",sans-serif; color:var(--teks); background:var(--bg-page); }
        .search-wrapper input:focus { border-color:var(--kuning); background:#fff; }
        .search-wrapper .search-icon { position:absolute; right:12px; top:50%; transform:translateY(-50%); pointer-events:none; }
        .table-container { background:#fff; border-radius:var(--radius-md); overflow:hidden; border:1px solid var(--border); box-shadow:var(--shadow-sm); }
        #tabel-menu { width:100%; border-collapse:collapse; font-size:13px; }
        #tabel-menu thead tr { background:#fafafa; border-bottom:1.5px solid var(--border); }
        #tabel-menu th { padding:12px 14px; text-align:center; font-size:12px; font-weight:700; color:#888; white-space:nowrap; }
        #tabel-menu th.col-left, #tabel-menu td.col-left { text-align:left; }
        #tabel-menu tbody tr { border-bottom:1px solid #f3f4f6; transition:background .15s; }
        #tabel-menu tbody tr:last-child { border-bottom:none; }
        #tabel-menu tbody tr:hover { background:#fffbeb; }
        #tabel-menu td { padding:12px 14px; text-align:center; color:var(--teks); vertical-align:middle; }
        .text-bold { font-weight:600; }
        .kat-badge { display:inline-block; font-size:11px; font-weight:600; padding:3px 10px; border-radius:20px; }
        .kat-makanan { background:#fef3c7; color:#92400e; }
        .kat-minuman { background:#dbeafe; color:#1e40af; }
        .kat-snack   { background:#fce7f3; color:#9d174d; }
        .popup-form-toggle { display:flex; align-items:center; justify-content:center; }
        .popup-toggle-switch { position:relative; width:44px; height:24px; cursor:pointer; display:block; }
        .popup-toggle-switch input { opacity:0; width:0; height:0; position:absolute; }
        .popup-toggle-track { position:absolute; inset:0; background:#d1d5db; border-radius:999px; transition:background .25s; }
        .popup-toggle-thumb { position:absolute; top:3px; left:3px; width:18px; height:18px; background:#fff; border-radius:50%; transition:transform .25s; box-shadow:0 1px 4px rgba(0,0,0,.18); }
        .popup-toggle-switch input:checked + .popup-toggle-track { background:#22c55e; }
        .popup-toggle-switch input:checked + .popup-toggle-track .popup-toggle-thumb { transform:translateX(20px); }
        .content-footer { display:flex; align-items:center; justify-content:space-between; padding:10px 4px; font-size:12px; color:#aaa; flex-wrap:wrap; gap:8px; }
        .pagination { display:flex; gap:4px; }
        .page-link { padding:6px 12px; border-radius:8px; border:1.5px solid var(--border); font-size:12px; font-weight:600; cursor:pointer; background:#fff; color:#555; transition:all .2s; }
        .page-link:hover:not([disabled]) { border-color:var(--kuning); color:#92400e; }
        .page-link[disabled] { opacity:.4; cursor:not-allowed; }
        .page-number { width:32px; height:32px; border-radius:8px; border:1.5px solid var(--border); font-size:12px; font-weight:600; cursor:pointer; background:#fff; color:#555; transition:all .2s; }
        .page-number:hover { border-color:var(--kuning); }
        .page-number.active { background:var(--kuning); border-color:var(--kuning); color:#fff; }
        #menu-toast { position:fixed; bottom:24px; left:50%; transform:translateX(-50%) translateY(20px); background:#1a1a1a; color:#fff; padding:10px 20px; border-radius:30px; font-size:13px; font-weight:600; z-index:9999; opacity:0; transition:opacity .3s, transform .3s; pointer-events:none; white-space:nowrap; }
        #menu-toast.show { opacity:1; transform:translateX(-50%) translateY(0); }
        @media (max-width:767px) {
            .action-bar { flex-direction:column; align-items:flex-start; }
            .search-wrapper input { width:100%; }
            .action-right { width:100%; }
            #tabel-menu th:nth-child(4), #tabel-menu td:nth-child(4) { display:none; }
            .table-container { overflow-x:auto; }
        }
    </style>
@endsection

@section('content')
<main class="scroll-area">

    {{-- ACTION BAR --}}
    <div class="action-bar">
        <div class="category-tabs">
            <button class="tab active" data-kategori="all">Semua</button>
            @foreach($categories as $cat)
                <button class="tab" data-kategori="{{ strtolower($cat->name) }}">
                    {{ ucfirst($cat->name) }}
                </button>
            @endforeach
        </div>
        <div class="action-right">
            <div class="search-wrapper">
                <input type="text" id="search-menu-input" placeholder="Cari menu...">
                <span class="search-icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                        <circle cx="11" cy="11" r="7" stroke="#fbbf24" stroke-width="2"/>
                        <path d="M16.5 16.5L21 21" stroke="#fbbf24" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </span>
            </div>
        </div>
    </div>

    {{-- TABEL --}}
    <div class="table-container">
        <table id="tabel-menu">
            <thead>
                <tr>
                    <th class="col-foto">Foto</th>
                    <th class="col-left">Nama</th>
                    <th>ID Menu</th>
                    <th>Deskripsi</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th>Harga</th>
                    <th>Tersedia</th>
                </tr>
            </thead>
            <tbody id="menu-tbody">
                @forelse($menus as $menu)
                <tr data-kategori="{{ strtolower($menu->category->name ?? '') }}"
                    data-nama="{{ strtolower($menu->name) }}"
                    data-id="{{ $menu->id }}">

                    {{-- Foto --}}
                    <td>
                        @if($menu->image)
                            <img src="{{ asset('storage/' . $menu->image) }}"
                                 style="width:48px;height:48px;border-radius:8px;object-fit:cover;display:block;margin:0 auto;"
                                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                            <div style="display:none;width:48px;height:48px;border-radius:8px;background:#e5e7eb;margin:0 auto;align-items:center;justify-content:center;font-size:13px;color:#555;font-weight:700;">
                                {{ strtoupper(substr($menu->name, 0, 2)) }}
                            </div>
                        @else
                            <div style="width:48px;height:48px;border-radius:8px;background:#e5e7eb;margin:0 auto;display:flex;align-items:center;justify-content:center;font-size:13px;color:#555;font-weight:700;">
                                {{ strtoupper(substr($menu->name, 0, 2)) }}
                            </div>
                        @endif
                    </td>

                    {{-- Nama --}}
                    <td class="col-left text-bold">{{ $menu->name }}</td>

                    {{-- ID --}}
                    <td style="color:#aaa;font-size:11px;">MNU{{ str_pad($menu->id, 3, '0', STR_PAD_LEFT) }}</td>

                    {{-- Deskripsi --}}
                    <td style="font-size:12px;color:#666;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        {{ $menu->description ?? '-' }}
                    </td>

                    {{-- Kategori --}}
                    <td>
                        @php
                            $katName  = strtolower($menu->category->name ?? '');
                            $katClass = match(true) {
                                str_contains($katName, 'makan') => 'kat-makanan',
                                str_contains($katName, 'minum') => 'kat-minuman',
                                default                         => 'kat-snack',
                            };
                        @endphp
                        <span class="kat-badge {{ $katClass }}">
                            {{ ucfirst($menu->category->name ?? '-') }}
                        </span>
                    </td>

                    {{-- Stok --}}
                    <td>
                        <span style="display:inline-block;background:{{ $menu->stock > 0 ? '#f3f4f6' : '#fee2e2' }};color:{{ $menu->stock > 0 ? '#555' : '#991b1b' }};font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;">
                            {{ $menu->stock }} Porsi
                        </span>
                    </td>

                    {{-- Harga --}}
                    <td style="font-weight:700;color:#e07b2a;">
                        Rp {{ number_format($menu->price, 0, ',', '.') }}
                    </td>

                    {{-- Toggle Tersedia — AJAX ke /menu/{id}/toggle-status --}}
                    <td>
                        <div class="popup-form-toggle" style="padding:0;margin:0;justify-content:center;border:none;">
                            <label class="popup-toggle-switch">
                                <input type="checkbox"
                                       class="toggle-status-menu"
                                       data-id="{{ $menu->id }}"
                                       data-nama="{{ $menu->name }}"
                                       {{ $menu->is_available ? 'checked' : '' }}>
                                <div class="popup-toggle-track">
                                    <div class="popup-toggle-thumb"></div>
                                </div>
                            </label>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="padding:48px 20px;text-align:center;color:#bbb;font-size:13px;">
                        Belum ada menu. Tambahkan menu melalui halaman Admin.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Footer + Pagination --}}
    <footer class="content-footer">
        <p class="data-info" id="data-info-label">Menampilkan {{ $menus->count() }} menu</p>
        <div class="pagination" id="pagination-container"></div>
    </footer>

</main>

<div id="menu-toast"></div>
@endsection

@section('scripts')
<script src="{{ asset('js/menu-kasir.js') }}"></script>
@endsection