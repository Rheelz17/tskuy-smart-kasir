@extends('layouts.admin')

@section('title', 'Manajemen Karyawan — Tskuy Admin')

@section('header_title', 'Manajemen Karyawan')
@section('header_subtitle', 'Mengatur hak akses dan data karyawan warkop.')

@section('content')
  <div class="page-title-section">
    <h1 class="page-title">Manajemen Karyawan</h1>
    <p class="page-subtitle">Mengatur hak akses dan data karyawan warkop.</p>
  </div>

  <main class="scroll-area">
    <div class="action-bar-mobile" style="margin-bottom:10px;">
        <div class="search-wrapper">
            <input type="text" placeholder="Apa yang kamu mau coba?">
            <span class="search-icon"><svg width="18" height="18" viewBox="0 0 26 26" fill="none"><path d="M25.103 22.071L19.66 16.628A10.721 10.721 0 1010.721 21.443c2.182 0 4.212-.662 5.907-1.786l5.443 5.443a2.143 2.143 0 003.032-3.03zM3.216 10.721a7.505 7.505 0 1115.01 0 7.505 7.505 0 01-15.01 0z" fill="#F8B602"/></svg></span>
        </div>
        <div class="category-tabs" style="margin-top: 10px; margin-bottom: 10px; display: flex; gap: 8px; overflow-x: auto; padding-bottom: 4px;">
            <button class="tab active" data-jabatan="all">All</button>
            <button class="tab" data-jabatan="admin">Admin</button>
            <button class="tab" data-jabatan="kasir">Kasir</button>
            <button class="tab" data-jabatan="koki">Koki</button>
        </div>
        <div class="action-buttons-row">
            <button class="btn-outline">
            <svg width="16" height="16" viewBox="0 0 30 30" fill="none"><path d="M15.603 14.095V5.284M13.132 7.549l1.375-1.742a2.32 2.32 0 013.563 0l1.375 1.742M21.639 19.836H9.567M11.768 10.893C5.511 12.51 6.009 17.071 6.009 17.071s-.498 4.577 5.759 6.174a13.124 13.124 0 007.663 0c6.255-1.616 5.759-6.174 5.759-6.174s.496-4.578-5.759-6.178z" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Export
            </button>
        </div>
    </div>

    <!-- Tombol Tambah -->
    <div class="action-bar-top">
        <button class="btn-tambah" id="btn-tambah-karyawan">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10.4019 9.23002V3.356M8.75497 4.86578L9.67177 3.70427C9.7571 3.59604 9.86739 3.50829 9.9939 3.44787C10.1204 3.38744 10.2598 3.35596 10.401 3.35596C10.5423 3.35596 10.6816 3.38744 10.8081 3.44787C10.9346 3.50829 11.0448 3.59604 11.1302 3.70427L12.0471 4.86578M14.4261 13.0575H6.37782M7.84554 7.0955C3.6739 8.17264 4.00628 11.2138 4.00628 11.2138C4.00628 11.2138 3.6739 14.2657 7.84554 15.3302C9.52076 15.7485 11.2793 15.7485 12.9546 15.3302C17.1243 14.2531 16.7938 11.2138 16.7938 11.2138C16.7938 11.2138 17.1243 8.16187 12.9546 7.0955" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Tambah Karyawan
        </button>
    </div>

    <!-- Action bar desktop (search + filter + export) -->
    <div class="action-bar">
        <div class="category-tabs" style="margin-bottom:14px;">
        <button class="tab active" data-jabatan="all">All</button>
        <button class="tab" data-jabatan="admin">Admin</button>
        <button class="tab" data-jabatan="kasir">Kasir</button>
        <button class="tab" data-jabatan="koki">Koki</button>
        </div>
        <div class="action-right">
            <div class="search-wrapper">
            <input type="text" placeholder="Cari Karyawan...">
            <span class="search-icon"><svg width="16" height="16" viewBox="0 0 26 26" fill="none"><path d="M25.103 22.071L19.66 16.628A10.721 10.721 0 1010.721 21.443c2.182 0 4.212-.662 5.907-1.786l5.443 5.443a2.143 2.143 0 003.032-3.03zM3.216 10.721a7.505 7.505 0 1115.01 0 7.505 7.505 0 01-15.01 0z" fill="#F8B602"/></svg></span>
            </div>
            <button class="btn-outline" id="btnExport">
            <svg width="16" height="16" viewBox="0 0 30 30" fill="none"><path d="M15.603 14.095V5.284M13.132 7.549l1.375-1.742a2.32 2.32 0 013.563 0l1.375 1.742M21.639 19.836H9.567M11.768 10.893C5.511 12.51 6.009 17.071 6.009 17.071s-.498 4.577 5.759 6.174a13.124 13.124 0 007.663 0c6.255-1.616 5.759-6.174 5.759-6.174s.496-4.578-5.759-6.178z" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Export
            </button>
        </div>
    </div>

    <!-- CARD LIST — mobile only -->
    <div class="karyawan-card-list">
        @foreach($karyawan as $k)
        @php
            $jabatanString = $k->role_id == 1 ? 'admin' : ($k->role_id == 2 ? 'kasir' : 'koki');
            $jabatanLabel = $k->role_id == 1 ? 'Admin' : ($k->role_id == 2 ? 'Kasir' : 'Koki');
        @endphp
        <div class="karyawan-card" data-jabatan="{{ $jabatanString }}">
            @if($k->photo)
                <img src="{{ asset('storage/' . $k->photo) }}" alt="{{ $k->name }}" class="karyawan-thumb">
            @else
                <div class="karyawan-thumb" style="background:#e5e7eb; display:flex; align-items:center; justify-content:center; font-size:14px; color:#4b5563; font-weight:600; border-radius:50%;">
                    {{ strtoupper(substr($k->name, 0, 2)) }}
                </div>
            @endif
            <div class="karyawan-card-info">
                <div class="karyawan-card-top">
                    <p class="karyawan-card-name">{{ $k->name }}</p>
                    @if($k->is_active == 1)
                        <span class="karyawan-card-status" style="background:#d1fae5; color:#065f46; padding:2px 8px; border-radius:4px; font-size:11px;">Aktif</span>
                    @else
                        <span class="karyawan-card-status" style="background:#fee2e2; color:#065f46; padding:2px 8px; border-radius:4px; font-size:11px; color:#ef4444;">Nonaktif</span>
                    @endif
                </div>
                <p class="karyawan-card-meta">{{ $k->employee_id }} | {{ $jabatanLabel }}</p>
                <div class="karyawan-card-bottom">
                    <span class="karyawan-card-phone">{{ $k->phone ?? '-' }}</span>
                    <div class="tindakan-col">
                        <span style="font-size:11px;color:#94a3b8;">Tindakan:</span>
                        <a href="#" class="btn-tindakan btn-edit-karyawan" 
                            data-id="{{ $k->id }}"
                            data-employee-id="{{ $k->employee_id }}"
                            data-nama="{{ $k->name }}"
                            data-jabatan="{{ $jabatanString }}"
                            data-telp="{{ $k->phone }}"
                            data-email="{{ $k->email }}"
                            data-alamat="{{ $k->alamat ?? '' }}"
                            data-photo="{{ $k->photo ?? '' }}"
                            data-status="{{ $k->is_active }}">
                                <svg width="28" height="28" viewBox="0 0 40 40" fill="none">
                                    <rect width="40" height="40" rx="10" fill="#EFB100" fill-opacity="0.2"/>
                                    <path d="M28 12L16 24c-1.1 1.1-4 1.5-4.5 1s.4-3.4 1.5-4.5L25 8.5c1.2-1.2 3-.3 3.5 1 .5.7.7 2.1-.5 2.5z" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M15 8H10a2 2 0 00-2 2v20a2 2 0 002 2h20a2 2 0 002-2v-5" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                        </a>

                        <a href="#" class="btn-tindakan btn-hapus-karyawan" 
                            data-id="{{ $k->id }}" 
                            data-nama="{{ $k->name }}">
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
        <p class="data-info">Memuat data...</p>
        <div class="pagination">
            </div>
    </footer>

    <!-- Tabel desktop -->
    <div class="table-container">
        <table id="tabel-karyawan">
            <thead>
            <tr>
                <th class="col-foto">Foto</th>
                <th class="col-left">Nama Lengkap</th>
                <th>ID Karyawan</th>
                <th>Jabatan</th>
                <th>No Telp</th>
                <th>Email</th>
                <th>Alamat</th>
                <th>Status</th>
                <th>Tindakan</th>
            </tr>
            </thead>
            <tbody>
                @foreach($karyawan as $k)
                <tr data-jabatan="{{ $k->role_id == 1 ? 'admin' : ($k->role_id == 2 ? 'kasir' : 'koki') }}">
                    <td>
                        @if($k->photo)
                            <img src="{{ asset('storage/' . $k->photo) }}" style="width:48px;height:48px;border-radius:8px;object-fit:cover;display:block;margin:0 auto;">
                        @else
                            <div style="width:48px;height:48px;border-radius:8px;background:#e5e7eb;margin:0 auto;display:flex;align-items:center;justify-content:center;font-size:14px;color:#4b5563;font-weight:600;">
                                {{ strtoupper(substr($k->name, 0, 2)) }}
                            </div>
                        @endif
                    </td>
                    <td class="col-left text-bold">{{ $k->name }}</td>
                    <td>{{ $k->employee_id }}</td>
                    <td>{{ $k->role_id == 1 ? 'Admin' : ($k->role_id == 2 ? 'Kasir' : 'Koki') }}</td>
                    <td><span class="notelp-badge">{{ $k->phone ?? '-' }}</span></td>
                    <td>{{ $k->email }}</td>
                    <td>{{ $k->alamat ?? '-' }}</td>
                    <td>
                        @if($k->is_active == 1)
                            <span class="status-badge success">Aktif</span>
                        @else
                            <span class="status-badge danger" style="background:#fee2e2; color:#ef4444;">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <div class="tindakan-col">
                            <a href="#" class="btn-tindakan btn-edit-karyawan" 
                            data-id="{{ $k->id }}"
                            data-employee-id="{{ $k->employee_id }}"
                            data-nama="{{ $k->name }}"
                            data-jabatan="{{ $k->role_id == 1 ? 'admin' : ($k->role_id == 2 ? 'kasir' : 'koki') }}"
                            data-telp="{{ $k->phone }}"
                            data-email="{{ $k->email }}"
                            data-alamat="{{ $k->alamat ?? '' }}"
                            data-photo="{{ $k->photo ?? '' }}"
                            data-status="{{ $k->is_active }}">
                                <svg width="28" height="28" viewBox="0 0 40 40" fill="none">
                                    <rect width="40" height="40" rx="10" fill="#EFB100" fill-opacity="0.2"/>
                                    <path d="M28 12L16 24c-1.1 1.1-4 1.5-4.5 1s.4-3.4 1.5-4.5L25 8.5c1.2-1.2 3-.3 3.5 1 .5.7.7 2.1-.5 2.5z" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M15 8H10a2 2 0 00-2 2v20a2 2 0 002 2h20a2 2 0 002-2v-5" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>

                            <a href="#" class="btn-tindakan btn-hapus-karyawan" 
                            data-id="{{ $k->id }}" 
                            data-nama="{{ $k->name }}">
                                <svg width="28" height="28" viewBox="0 0 40 40" fill="none">
                                    <rect width="40" height="40" rx="10" fill="#FB2C36" fill-opacity="0.15"/>
                                    <path d="M14 16v12a2 2 0 002 2h8a2 2 0 002-2V16M12 13h16M17 13v-2a1 1 0 011-1h4a1 1 0 011 1v2" stroke="#FB2C36" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            <!-- <tr>
                <td><div style="width:48px;height:48px;border-radius:8px;background:#e5e7eb;margin:0 auto;"></div></td>
                <td class="col-left text-bold">Fahri Eka Pratama</td>
                <td>EMP001</td>
                <td>Kasir</td>
                <td><span class="notelp-badge">0821-8422-9314</span></td>
                <td>fahri@gmail.com</td>
                <td>Jl. Ahmad Yani No. 10</td>
                <td><span class="status-badge success">Aktif</span></td>
                <td><div class="tindakan-col"><a href="#" class="btn-tindakan btn-edit-karyawan" data-id="EMP001"
                    data-nama="Fahri Eka Pratama"
                    data-jabatan="kasir"
                    data-telp="0821-8422-9314"
                    data-email="fahri@gmail.com"
                    data-alamat="Jl. Ahmad Yani No. 10"><svg width="28" height="28" viewBox="0 0 40 40" fill="none"><rect width="40" height="40" rx="10" fill="#EFB100" fill-opacity="0.2"/><path d="M28 12L16 24c-1.1 1.1-4 1.5-4.5 1s.4-3.4 1.5-4.5L25 8.5c1.2-1.2 3-.3 3.5 1 .5.7.7 2.1-.5 2.5z" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 8H10a2 2 0 00-2 2v20a2 2 0 002 2h20a2 2 0 002-2v-5" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></a><a href="#" class="btn-tindakan btn-hapus-karyawan" data-nama="Fahri Eka Pratama"><svg width="28" height="28" viewBox="0 0 40 40" fill="none"><rect width="40" height="40" rx="10" fill="#FB2C36" fill-opacity="0.15"/><path d="M14 16v12a2 2 0 002 2h8a2 2 0 002-2V16M12 13h16M17 13v-2a1 1 0 011-1h4a1 1 0 011 1v2" stroke="#FB2C36" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></a></div>
                </td>
            </tr> -->
            </tbody>
        </table>
    </div>

    <footer class="content-footer">
        <p class="data-info">Memuat data...</p>
        <div class="pagination">
        </div>
    </footer>
    </main>
@endsection


@section('page_popups')
    <div class="popup popup-form-karyawan" id="popup-tambah-karyawan">
        <div class="popup-form-header">
            <div>
            <h2 class="popup-form-title">Tambah Karyawan Baru</h2>
            <p class="popup-form-subtitle">Isi kolom di bawah untuk menambahkan karyawan baru</p>
            </div>
            <button class="popup-close-merah" data-close aria-label="Tutup">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10" fill="#fee2e2"/>
                <path d="M15 9L9 15M9 9L15 15" stroke="#ef4444" stroke-width="2" stroke-linecap="round"/>
            </svg>
            </button>
        </div>

        <div class="popup-form-body">
            <!-- Kolom kiri: upload foto -->
            <div class="popup-form-foto-col">
                <div class="popup-upload-area" id="popup-tambah-upload-area">
                    <input
                    type="file"
                    class="popup-upload-input"
                    id="popup-tambah-input-foto"
                    accept="image/jpg,image/jpeg,image/png"
                    >
                    <svg width="40" height="40" viewBox="0 0 48 48" fill="none">
                    <path d="M32 32L24 24L16 32" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M24 24V40" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M40.82 36.82A10 10 0 0034 18h-2.52A16 16 0 108 36.92" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <p class="popup-upload-label">Unggah Foto Wajah</p>
                    <p class="popup-upload-hint">Klik atau seret fotomu ke sini (JPG or PNG).</p>
                    <img src="" alt="Preview" class="popup-upload-preview" id="popup-tambah-preview">
                </div>
            </div>

            <!-- Kolom kanan: form fields -->
            <div class="popup-form-fields-col">
                <!-- ID — auto-generated, readonly -->
                <div class="popup-form-group">
                    <label class="popup-form-label" for="popup-tambah-id">ID Karyawan</label>
                    <input
                    type="text"
                    class="popup-form-input"
                    id="popup-tambah-id"
                    placeholder="Otomatis setelah disimpan..." readonly>
                </div>

                <!-- Nama Lengkap -->
                <div class="popup-form-group">
                    <label class="popup-form-label" for="popup-tambah-nama">Nama Lengkap</label>
                    <input
                    type="text"
                    class="popup-form-input"
                    id="popup-tambah-nama"
                    name="name"
                    placeholder="Masukkan Nama Lengkap..."
                    >
                </div>

                <!-- Jabatan -->
                <div class="popup-form-group">
                    <label class="popup-form-label" for="popup-tambah-jabatan">Jabatan</label>
                    <div class="popup-select-wrapper">
                    <select class="popup-form-select" id="popup-tambah-jabatan" name="jabatan">
                        <option value="" disabled selected>Pilih Jabatan</option>
                        <option value="1">Admin</option>
                        <option value="2">Kasir</option>
                        <option value="4">Koki</option>
                    </select>
                    <div class="popup-select-chevron">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                        <path d="M6 9L12 15L18 9" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    </div>
                </div>

                <!-- Nomor Telepon -->
                <div class="popup-form-group">
                    <label class="popup-form-label" for="popup-tambah-telp">Nomor Telepon</label>
                    <input
                    type="tel"
                    class="popup-form-input"
                    id="popup-tambah-telp"
                    name="telp"
                    placeholder="Masukkan Nomor Telepon..."
                    >
                </div>

                <!-- Email -->
                <div class="popup-form-group">
                    <label class="popup-form-label" for="popup-tambah-email">Email</label>
                    <input
                    type="email"
                    class="popup-form-input"
                    id="popup-tambah-email"
                    name="email"
                    placeholder="Masukkan Email Baru..."
                    >
                </div>

                <div class="popup-form-group">
                    <label class="popup-form-label" for="popup-tambah-alamat">Alamat</label>
                    <textarea 
                        class="popup-form-input" 
                        id="popup-tambah-alamat" 
                        name="alamat" 
                        placeholder="Masukkan Alamat Lengkap..."
                        rows="3"></textarea>
                </div>

                <!-- Toggle Akses Login — di bawah dua kolom, full width -->
                <div class="popup-form-toggle">
                    <span class="popup-toggle-label">Akses Login (Aktif)</span>
                    <label class="popup-toggle-switch">
                        <input type="checkbox" id="popup-tambah-akses">
                        <div class="popup-toggle-track">
                            <div class="popup-toggle-thumb"></div>
                        </div>
                    </label>
                </div>

                <!-- Tombol Batal + Simpan (inline, sesuai desain desktop) -->
                <div class="popup-form-actions">
                    <button class="popup-btn-batal" data-close>Batal</button>
                    <button class="popup-btn-simpan" id="popup-btn-tambah-simpan">Simpan</button>
                </div>
            </div><!-- end popup-form-fields-col -->    

        </div><!-- end popup-form-body -->
    </div>

    <div class="popup popup-form-karyawan" id="popup-edit-karyawan">
        <input type="hidden" id="popup-edit-db-id">
        <div class="popup-form-header">
            <div>
            <h2 class="popup-form-title">Edit Data Karyawan</h2>
            <p class="popup-form-subtitle">Silahkan sesuaikan perubahan yang Anda inginkan.</p>
            </div>
            <button class="popup-close-merah" data-close aria-label="Tutup">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="10" fill="#fee2e2"/>
                    <path d="M15 9L9 15M9 9L15 15" stroke="#ef4444" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
        </div>

        <div class="popup-form-body">
            <!-- Kolom kiri: foto existing + tombol edit/hapus overlay -->
            <div class="popup-form-foto-col">
                <div class="popup-foto-edit-wrap">
                    <img
                    src="https://i.pravatar.cc/300?img=5"
                    alt="Foto Karyawan"
                    class="popup-foto-edit-img"
                    id="popup-edit-foto-img"
                    >
                    <!-- Tombol edit & hapus foto, overlay di pojok kanan atas foto -->
                    <div class="popup-foto-edit-actions">
                        <button class="popup-btn-foto-edit" id="popup-edit-btn-ganti-foto" title="Ganti Foto">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" stroke="#efb100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" stroke="#efb100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <button class="popup-btn-foto-hapus" id="popup-edit-btn-hapus-foto" title="Hapus Foto">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M3 6h18M8 6V4h8v2M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                    <!-- Input file tersembunyi untuk ganti foto -->
                    <input
                    type="file"
                    id="popup-edit-input-foto"
                    accept="image/jpg,image/jpeg,image/png"
                    style="display:none;"
                    >
                </div>
            </div>

            <!-- Kolom kanan: form fields pre-filled -->
            <div class="popup-form-fields-col">
                <!-- ID — readonly, diisi JS -->
                <div class="popup-form-group">
                    <label class="popup-form-label" for="popup-edit-id">ID Karyawan</label>
                    <input
                    type="text"
                    class="popup-form-input"
                    id="popup-edit-id"
                    readonly
                    >
                </div>
                <!-- Nama Lengkap — diisi JS -->
                <div class="popup-form-group">
                    <label class="popup-form-label" for="popup-edit-nama">Nama Lengkap</label>
                    <input
                    type="text"
                    class="popup-form-input"
                    id="popup-edit-nama"
                    name="nama"
                    placeholder="Nama Lengkap"
                    >
                </div>

                <!-- Jabatan — diisi JS (option selected) -->
                <div class="popup-form-group">
                    <label class="popup-form-label" for="popup-edit-jabatan">Jabatan</label>
                    <div class="popup-select-wrapper">
                        <select class="popup-form-select" id="popup-edit-jabatan" name="jabatan">
                            <option value="" disabled>Pilih Jabatan</option>
                            <option value="1">Admin</option>
                            <option value="2">Kasir</option>
                            <option value="4">Koki</option>
                        </select>
                        <div class="popup-select-chevron">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                                <path d="M6 9L12 15L18 9" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Nomor Telepon — diisi JS -->
                <div class="popup-form-group">
                    <label class="popup-form-label" for="popup-edit-telp">Nomor Telepon</label>
                    <input
                    type="tel"
                    class="popup-form-input"
                    id="popup-edit-telp"
                    name="telp"
                    placeholder="Nomor Telepon"
                    >
                </div>

                <!-- Email — diisi JS -->
                <div class="popup-form-group">
                    <label class="popup-form-label" for="popup-edit-email">Email</label>
                    <input
                    type="email"
                    class="popup-form-input"
                    id="popup-edit-email"
                    name="email"
                    placeholder="Email"
                    >
                </div>

                <div class="popup-form-group">
                    <label class="popup-form-label" for="popup-edit-alamat">Alamat</label>
                    <textarea 
                        class="popup-form-input" 
                        id="popup-edit-alamat" 
                        name="alamat" 
                        placeholder="Alamat Karyawan..."
                        rows="3"></textarea>
                </div>

                <div class="popup-form-toggle">
                    <span class="popup-toggle-label">Akses Login</span>
                    <label class="popup-toggle-switch">
                        <input type="checkbox" id="popup-edit-akses">
                        <div class="popup-toggle-track">
                            <div class="popup-toggle-thumb"></div>
                        </div>
                    </label>
                </div>

                <!-- Tombol Batal + Simpan -->
                <div class="popup-form-actions">
                    <button class="popup-btn-batal" data-close>Batal</button>
                    <button class="popup-btn-simpan" id="popup-btn-edit-simpan">Simpan</button>
                </div>

            </div><!-- end popup-form-fields-col -->

        </div><!-- end popup-form-body -->
    </div>

    <!-- Popup Konfirmasi Hapus Karyawan -->
    <div class="popup popup-warning" id="popup-hapus-karyawan">
        <input type="hidden" id="popup-hapus-db-id">
        <button class="popup-close popup-close-float" data-close>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10" stroke="#555" stroke-width="1.8" />
                <path
                d="M15 9L9 15M9 9L15 15"
                stroke="#555"
                stroke-width="1.8"
                stroke-linecap="round"
                />
            </svg>
        </button>
        <div class="popup-body popup-body-warning">
            <div class="warning-icon-wrap">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none">
                    <path
                        d="M12 9V13M12 17H12.01"
                        stroke="#fff"
                        stroke-width="2.5"
                        stroke-linecap="round"
                    />
                </svg>
            </div>
            <h3 class="warning-title">
                Hapus karyawan <span id="hapus-nama-karyawan">ini</span>?
            </h3>
            <p class="warning-desc">
                Data karyawan akan dihapus permanen dan tidak bisa dikembalikan.
            </p>
            <div class="warning-actions">
                <button class="btn-batal-warning" data-close>Batal</button>
                <button class="btn-confirm-warning" id="btn-confirm-hapus" data-close>
                Ya, Hapus
                </button>
            </div>
        </div>
    </div>

    <div class="popup popup-export" id="popupExport">
        <div class="popup-export-header">
        <h3>Unduh Data Karyawan</h3>
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
            <div class="export-option" data-fmt="pdf">
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
                <span>Semua Data Karyawan <span>(123 Item)</span></span>
            </label>
            <label class="export-range-item">
                <input type="radio" name="exportRange" value="current" checked>
                <span>Hanya karyawan Yang Sedang ditampilkan <span id="exportRangeCount">(8 Karyawan)</span></span>
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
  <script src="{{ asset('js/karyawan.js') }}"></script>
@endsection