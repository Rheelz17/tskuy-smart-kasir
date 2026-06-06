@extends('layouts.admin')

@section('title', 'Manajemen Menu — Tskuy Admin')

@section('header_title', 'Manajemen Menu')
@section('header_subtitle', 'Mengatur hak akses dan data karyawan warkop.')

@section('content')
  <div class="page-title-section">
    <h1 class="page-title">Manajemen Menu</h1>
    <p class="page-subtitle">Mengatur hak akses dan data karyawan warkop.</p>
  </div>

  <main class="scroll-area">
    <div class="action-bar-mobile" style="margin-bottom:10px;">
        <div class="search-wrapper">
            <input type="text" placeholder="Apa yang kamu mau coba?">
            <span class="search-icon"><svg width="18" height="18" viewBox="0 0 26 26" fill="none"><path d="M25.103 22.071L19.66 16.628A10.721 10.721 0 1010.721 21.443c2.182 0 4.212-.662 5.907-1.786l5.443 5.443a2.143 2.143 0 003.032-3.03zM3.216 10.721a7.505 7.505 0 1115.01 0 7.505 7.505 0 01-15.01 0z" fill="#F8B602"/></svg></span>
        </div>
        <!-- <div class="category-tabs" style="margin-bottom:14px;">
            <button class="tab active" data-kategori="all">All</button>
            <button class="tab" data-kategori="makanan">Makanan</button>
            <button class="tab" data-kategori="minuman">Minuman</button>
            <button class="tab" data-kategori="cemilan">Cemilan</button>
        </div> -->
        <div class="category-tabs" style="margin-bottom:14px;">
            <button class="tab active filter-tab" data-kategori="all">All</button>
            @foreach($categories as $cat)
                <button class="tab filter-tab" data-kategori="{{ $cat->id }}">{{ $cat->name }}</button>
            @endforeach
        </div>
        <div class="action-buttons-row">
            <button class="btn-outline" id="btnExport">
            <svg width="16" height="16" viewBox="0 0 30 30" fill="none"><path d="M15.603 14.095V5.284M13.132 7.549l1.375-1.742a2.32 2.32 0 013.563 0l1.375 1.742M21.639 19.836H9.567M11.768 10.893C5.511 12.51 6.009 17.071 6.009 17.071s-.498 4.577 5.759 6.174a13.124 13.124 0 007.663 0c6.255-1.616 5.759-6.174 5.759-6.174s.496-4.578-5.759-6.178z" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Export
            </button>
        </div>
    </div>

    <!-- Tombol Tambah -->
    <div class="action-bar-top">
        <button class="btn-tambah" id="btn-tambah-menu">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10.4019 9.23002V3.356M8.75497 4.86578L9.67177 3.70427C9.7571 3.59604 9.86739 3.50829 9.9939 3.44787C10.1204 3.38744 10.2598 3.35596 10.401 3.35596C10.5423 3.35596 10.6816 3.38744 10.8081 3.44787C10.9346 3.50829 11.0448 3.59604 11.1302 3.70427L12.0471 4.86578M14.4261 13.0575H6.37782M7.84554 7.0955C3.6739 8.17264 4.00628 11.2138 4.00628 11.2138C4.00628 11.2138 3.6739 14.2657 7.84554 15.3302C9.52076 15.7485 11.2793 15.7485 12.9546 15.3302C17.1243 14.2531 16.7938 11.2138 16.7938 11.2138C16.7938 11.2138 17.1243 8.16187 12.9546 7.0955" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Tambah Menu
        </button>
    </div>
    
    <!-- Action bar desktop (search + filter + export) -->
    <div class="action-bar">
        <div class="category-tabs" style="margin-bottom:14px;">
            <button class="tab active" data-kategori="all">All</button>
            <button class="tab" data-kategori="makanan">Makanan</button>
            <button class="tab" data-kategori="minuman">Minuman</button>
            <button class="tab" data-kategori="snack">Snack</button>
        </div>
        <div class="action-right">
            <div class="search-wrapper">
                <input type="text" placeholder="Cari Menu...">
                <span class="search-icon"><svg width="16" height="16" viewBox="0 0 26 26" fill="none"><path d="M25.103 22.071L19.66 16.628A10.721 10.721 0 1010.721 21.443c2.182 0 4.212-.662 5.907-1.786l5.443 5.443a2.143 2.143 0 003.032-3.03zM3.216 10.721a7.505 7.505 0 1115.01 0 7.505 7.505 0 01-15.01 0z" fill="#F8B602"/></svg></span>
            </div>
            <button class="btn-outline" id="btnExport">
                <svg width="16" height="16" viewBox="0 0 30 30" fill="none"><path d="M15.603 14.095V5.284M13.132 7.549l1.375-1.742a2.32 2.32 0 013.563 0l1.375 1.742M21.639 19.836H9.567M11.768 10.893C5.511 12.51 6.009 17.071 6.009 17.071s-.498 4.577 5.759 6.174a13.124 13.124 0 007.663 0c6.255-1.616 5.759-6.174 5.759-6.174s.496-4.578-5.759-6.178z" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Export
            </button>
        </div>
    </div>

    <!-- CARD LIST — mobile only -->
    <div class="menu-card-list">
        @foreach($menus as $item)
        <div class="menu-card" data-kategori="{{ $item->category_id }}">
            <img src="{{ $item->image ? asset('storage/' . $item->image) : asset('images/default-menu.jpg') }}" 
                alt="{{ $item->name }}" 
                class="menu-thumb">
            
            <div class="menu-card-info">
                <div class="menu-card-top">
                    <p class="menu-card-name">{{ $item->name }}</p>
                    <label class="switch-toggle-container">
                        <input type="checkbox" 
                            class="toggle-status-menu" 
                            data-id="{{ $item->id }}" 
                            data-nama="{{ $item->name }}" 
                            {{ $item->is_available ? 'checked' : '' }}>
                        <span class="switch-slider"></span>
                    </label>
                </div>
                
                <p class="menu-card-meta">
                    MN-{{ sprintf('%03d', $item->id) }} | {{ $item->category->name ?? 'Kategori' }}
                </p>
                
                <div class="menu-card-bottom">
                    <div class="menu-card-price-stock">
                        <span class="menu-price">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                        <span class="menu-stock">(Stok: {{ $item->stock }})</span>
                    </div>
                    
                    <div class="tindakan-col">
                        <span style="font-size:11px;color:#94a3b8;">Tindakan:</span>
                        
                        <a href="#" class="btn-tindakan btn-edit-menu" 
                        data-id="{{ $item->id }}"
                        data-nama="{{ $item->name }}"
                        data-deskripsi="{{ $item->description }}"
                        data-kategori="{{ $item->category_id }}"
                        data-stok="{{ $item->stock }}"
                        data-harga="{{ $item->price }}"
                        data-foto="{{ $item->image }}">
                            <svg width="28" height="28" viewBox="0 0 40 40" fill="none">
                                <rect width="40" height="40" rx="10" fill="#EFB100" fill-opacity="0.2"/>
                                <path d="M28 12L16 24c-1.1 1.1-4 1.5-4.5 1s.4-3.4 1.5-4.5L25 8.5c1.2-1.2 3-.3 3.5 1 .5.7.7 2.1-.5 2.5z" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M15 8H10a2 2 0 00-2 2v20a2 2 0 002 2h20a2 2 0 002-2v-5" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                        
                        <a href="#" class="btn-tindakan btn-hapus-menu" data-id="{{ $item->id }}" data-nama="{{ $item->name }}">
                            <svg width="28" height="28" viewBox="0 0 40 40" fill="none">
                                <rect width="40" height="40" rx="10" fill="#FB2C36" fill-opacity="0.15"/>
                                <path d="M14 16v12a2 2 0 002 2h8a2 2 0 002-2V16M12 13h16M17 13v-2a1 1 0 011-1h4a1 1 0 011 1v2" stroke="#FB2C36" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <footer class="content-footer-mobile">
        <p class="data-info">Memmuat data...</p>
        <div class="pagination">
        </div>
    </footer>

    <!-- Tabel desktop -->
    @include('components.menu-tabel', ['menus' => $menus])

    <footer class="content-footer">
        <p class="data-info">Memuat data...</p>
        <div class="pagination">
        </div>
    </footer>
  </main>
@endsection


@section('page_popups')
    <div class="popup popup-form-karyawan" id="popup-tambah-menu">
        <form id="form-tambah-menu" action="{{ route('admin.menu.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="popup-form-header">
                <div>
                    <h2 class="popup-form-title">Tambah Menu Baru</h2>
                    <p class="popup-form-subtitle">Isi kolom di bawah untuk menambahkan menu kuliner baru</p>
                </div>
                <button type="button" class="popup-close-merah" data-close aria-label="Tutup">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" fill="#fee2e2"/>
                        <path d="M15 9L9 15M9 9L15 15" stroke="#ef4444" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>

            <div class="popup-form-body">
                <div class="popup-form-foto-col">
                    <div class="popup-upload-area" id="popup-tambah-upload-area">
                        <input type="file" class="popup-upload-input" id="popup-tambah-input-foto" name="image" accept="image/jpg,image/jpeg,image/png">
                        <svg width="40" height="40" viewBox="0 0 48 48" fill="none">
                            <path d="M32 32L24 24L16 32" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M24 24V40" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M40.82 36.82A10 10 0 0034 18h-2.52A16 16 0 108 36.92" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <p class="popup-upload-label">Unggah Foto Menu</p>
                        <p class="popup-upload-hint">Klik atau seret foto produk ke sini (JPG / PNG).</p>
                        <img src="" alt="Preview" class="popup-upload-preview" id="popup-tambah-preview">
                    </div>
                </div>

                <div class="popup-form-fields-col">
                    <div class="popup-form-group">
                        <label class="popup-form-label" for="popup-tambah-id">ID Menu</label>
                        <input type="text" class="popup-form-input" id="popup-tambah-id" value="Otomatis" readonly style="background-color: #f3f4f6; color: #9ca3af;">
                    </div>

                    <div class="popup-form-group">
                        <label class="popup-form-label" for="popup-tambah-nama">Nama Menu</label>
                        <input type="text" class="popup-form-input" id="popup-tambah-nama" name="name" placeholder="Masukkan Nama Menu..." required>
                    </div>

                    <div class="popup-form-group">
                        <label class="popup-form-label" for="popup-tambah-deskripsi">Deskripsi</label>
                        <textarea class="popup-form-input" id="popup-tambah-deskripsi" name="description" rows="2" placeholder="Masukkan Deskripsi singkat rasa atau porsi menu..." style="resize: none; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; font-family: inherit;"></textarea>
                    </div>

                    <div class="popup-form-group">
                        <label class="popup-form-label" for="popup-tambah-kategori">Kategori</label>
                        <div class="popup-select-wrapper">
                            <select class="popup-form-select" id="popup-tambah-kategori" name="category_id" required>
                                <option value="" disabled selected>Pilih Kategori</option>
                                @if(isset($categories))
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ ucfirst($category->name) }}</option>
                                    @endforeach
                                @else
                                    <option value="1">Makanan</option>
                                    <option value="2">Minuman</option>
                                    <option value="3">Cemilan</option>
                                @endif
                            </select>
                            <div class="popup-select-chevron">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                                    <path d="M6 9L12 15L18 9" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="popup-form-group">
                        <label class="popup-form-label" for="popup-tambah-stok">Stok Porsi</label>
                        <input type="number" class="popup-form-input" id="popup-tambah-stok" name="stock" min="0" placeholder="Contoh: 50" required>
                    </div>

                    <div class="popup-form-group">
                        <label class="popup-form-label" for="popup-tambah-harga">Harga Jual (Rp)</label>
                        <input type="number" class="popup-form-input" id="popup-tambah-harga" name="price" min="0" placeholder="Contoh: 15000" required>
                    </div>

                    <div class="popup-form-toggle">
                        <span class="popup-toggle-label">Tersedia (Aktif)</span>
                        <label class="popup-toggle-switch">
                            <input type="checkbox" id="popup-tambah-status" name="is_available" value="1" checked>
                            <div class="popup-toggle-track">
                                <div class="popup-toggle-thumb"></div>
                            </div>
                        </label>
                    </div>

                    <div class="popup-form-actions">
                        <button type="button" class="popup-btn-batal" data-close>Batal</button>
                        <button type="submit" class="popup-btn-simpan" id="popup-btn-tambah-simpan">Simpan Menu</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="popup popup-form-karyawan" id="popup-edit-menu">
        <form id="form-edit-menu" enctype="multipart/form-data">
            @csrf
            <div class="popup-form-header">
                <div>
                    <h2 class="popup-form-title">Edit Data Menu</h2>
                    <p class="popup-form-subtitle">Silahkan sesuaikan info menu kuliner yang ingin diubah.</p>
                </div>
                <button class="popup-close-merah" data-close aria-label="Tutup">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" fill="#fee2e2"/>
                        <path d="M15 9L9 15M9 9L15 15" stroke="#ef4444" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>

            <div class="popup-form-body">
                <div class="popup-form-foto-col">
                    <div class="popup-foto-edit-wrap">
                        <img src="" alt="Foto Menu" class="popup-foto-edit-img" id="popup-edit-foto-img">
                        
                        <div class="popup-foto-edit-actions">
                            <button type="button" class="popup-btn-foto-edit" id="popup-edit-btn-ganti-foto" title="Ganti Foto">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" stroke="#efb100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" stroke="#efb100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <button type="button" class="popup-btn-foto-hapus" id="popup-edit-btn-hapus-foto" title="Hapus Foto">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                    <path d="M3 6h18M8 6V4h8v2M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                        <input type="file" id="popup-edit-input-foto" accept="image/jpg,image/jpeg,image/png" style="display:none;">
                    </div>
                </div>

                <div class="popup-form-fields-col">
                    <div class="popup-form-group">
                        <label class="popup-form-label" for="popup-edit-id">ID Menu</label>
                        <input type="text" class="popup-form-input" id="popup-edit-id" readonly>
                    </div>

                    <div class="popup-form-group">
                        <label class="popup-form-label" for="popup-edit-nama">Nama Menu</label>
                        <input type="text" class="popup-form-input" id="popup-edit-nama" placeholder="Nama Menu">
                    </div>

                    <div class="popup-form-group">
                        <label class="popup-form-label" for="popup-edit-deskripsi">Deskripsi Menu</label>
                        <textarea class="popup-form-input" id="popup-edit-deskripsi" rows="2" placeholder="Deskripsi singkat menu..."></textarea>
                    </div>

                    <div class="popup-form-group">
                        <label class="popup-form-label" for="popup-edit-kategori">Kategori</label>
                        <div class="popup-select-wrapper">
                            <select class="popup-form-select" id="popup-edit-kategori">
                                <option value="" disabled selected>Pilih Kategori</option>
                                <option value="1">Makanan</option>
                                <option value="2">Minuman</option>
                                <option value="3">Cemilan</option>
                            </select>
                            <div class="popup-select-chevron">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                                    <path d="M6 9L12 15L18 9" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="popup-form-group">
                        <label class="popup-form-label" for="popup-edit-stok">Stok Porsi</label>
                        <input type="number" class="popup-form-input" id="popup-edit-stok" min="0">
                    </div>

                    <div class="popup-form-group">
                        <label class="popup-form-label" for="popup-edit-harga">Harga Jual (Rp)</label>
                        <input type="number" class="popup-form-input" id="popup-edit-harga" min="0">
                    </div>

                    <div class="popup-form-actions">
                        <button class="popup-btn-batal" data-close>Batal</button>
                        <button class="popup-btn-simpan" id="popup-btn-edit-simpan">Simpan Perubahan</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <div class="popup popup-warning" id="popup-hapus-menu">
        <input type="hidden" id="popup-hapus-db-id">

        <button class="popup-close popup-close-float" data-close>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10" stroke="#555" stroke-width="1.8" />
                <path d="M15 9L9 15M9 9L15 15" stroke="#555" stroke-width="1.8" stroke-linecap="round" />
            </svg>
        </button>
        <div class="popup-body popup-body-warning">
            <div class="warning-icon-wrap">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none">
                    <path d="M12 9V13M12 17H12.01" stroke="#fff" stroke-width="2.5" stroke-linecap="round" />
                </svg>
            </div>
            <h3 class="warning-title">
                Hapus menu <span id="popup-hapus-nama-menu" style="color: #ef4444;">[Nama Menu]</span>?
            </h3>
            <p class="warning-desc">
                Data menu kuliner ini akan dihapus permanen dari katalog kasir dan tidak bisa dikembalikan.
            </p>
            <div class="warning-actions">
                <button class="btn-batal-warning" data-close>Batal</button>
                <button class="btn-confirm-warning" id="btn-confirm-hapus" data-close>Ya, Hapus</button>
            </div>
        </div>
    </div>

    <div class="popup popup-export" id="popupExport">
        <div class="popup-export-header">
        <h3>Unduh Data Menu</h3>
        <button class="popup-close-white" id="closeExportBtn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="18" y1="6" x2="6" y2="18" />
            <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
        </button>
        </div>
        <div class="popup-export-body">
        <div class="export-format-title">Pilih Format Data</div>
        <div class="export-format-options">
            <div class="export-option selected" data-fmt="xlsx">
            <div class="export-icon xlsx">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                <polyline points="14 2 14 8 20 8" />
                <line x1="16" y1="13" x2="8" y2="13" />
                <line x1="16" y1="17" x2="8" y2="17" />
                <polyline points="10 9 9 9 8 9" />
                </svg>
            </div>
            <span class="export-option-name">Excel</span>
            </div>
            <div class="expofrt-option" data-fmt="pdf">
            <div class="export-icon pdf">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#991b1b" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                <polyline points="14 2 14 8 20 8" />
                <line x1="16" y1="13" x2="8" y2="13" />
                <line x1="16" y1="17" x2="8" y2="17" />
                <polyline points="10 9 9 9 8 9" />
                </svg>
            </div>
            <span class="export-option-name">PDF</span>
            </div>
        </div>

        <div>
            <div class="export-range-title">Rentang Data</div>
            <div class="export-range-list" id="exportRangeList">
            <label class="export-range-item">
                <input type="radio" name="exportRange" value="all">
                <span>Semua Data Menu <span>(123 Item)</span></span>
            </label>
            <label class="export-range-item">
                <input type="radio" name="exportRange" value="current" checked>
                <span>Hanya Menu Yang Sedang Sditampilkan <span id="exportRangeCount">(8 Menu)</span></span>
            </label>
            <label class="export-range-item">
                <input type="radio" name="exportRange" value="none">
                <span>Tidak Tersedia</span>
            </label>
            </div>
        </div>

        <button class="btn-unduh" id="btnUnduhData">Unduh Data</button>
        </div>
    </div>
@endsection


@section('scripts')
  <script src="{{ asset('js/menu.js') }}"></script>
@endsection