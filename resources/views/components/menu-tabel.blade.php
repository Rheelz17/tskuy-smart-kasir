    <div class="table-container">
        <table id="tabel-menu">
            <thead>
                <tr>
                    <th class="col-foto">Foto</th>
                    <th class="col-left">Nama</th>
                    <th>ID Menu</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th>Harga</th>
                    <th>Status</th>
                    <th>Tindakan</th>
                </tr>
            </thead>
            <tbody>
                <tr data-kategori="minuman">
                    <td>
                        <div style="width:48px;height:48px;border-radius:8px;background:#e5e7eb;margin:0 auto;"></div>
                    </td>
                    <td class="col-left text-bold">Kopi Susu Tskuy</td>
                    <td>MNU001</td>
                    <td>Minuman</td>
                    <td><span class="stok-badge">50 Porsi</span></td>
                    <td>Rp 15.000</td>
                    <td>
                        <div class="popup-form-toggle" style="padding: 0; margin: 0; justify-content: center; border: none;">
                            <label class="popup-toggle-switch">
                                <input type="checkbox" class="toggle-status-menu" data-id="{{ $menu->id ?? '1' }}" checked>
                                <div class="popup-toggle-track">
                                    <div class="popup-toggle-thumb"></div>
                                </div>
                            </label>
                        </div>
                    </td>
                    <td>
                        <div class="tindakan-col">
                            <a href="#" class="btn-tindakan btn-edit-menu" data-id="MNU001" data-nama="Kopi Susu Tskuy" data-kategori="minuman" data-stok="50" data-harga="15000" data-status="tersedia">
                                <svg width="28" height="28" viewBox="0 0 40 40" fill="none">
                                    <rect width="40" height="40" rx="10" fill="#EFB100" fill-opacity="0.2" />
                                    <path d="M28 12L16 24c-1.1 1.1-4 1.5-4.5 1s.4-3.4 1.5-4.5L25 8.5c1.2-1.2 3-.3 3.5 1 .5.7.7 2.1-.5 2.5z" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M15 8H10a2 2 0 00-2 2v20a2 2 0 002 2h20a2 2 0 002-2v-5" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                            <a href="#" class="btn-tindakan btn-hapus-menu" data-nama="Kopi Susu Tskuy">
                                <svg width="28" height="28" viewBox="0 0 40 40" fill="none">
                                    <rect width="40" height="40" rx="10" fill="#FB2C36" fill-opacity="0.15" />
                                    <path d="M14 16v12a2 2 0 002 2h8a2 2 0 002-2V16M12 13h16M17 13v-2a1 1 0 011-1h4a1 1 0 011 1v2" stroke="#FB2C36" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>

                <tr data-kategori="makanan">
                    <td>
                        <div style="width:48px;height:48px;border-radius:8px;background:#e5e7eb;margin:0 auto;"></div>
                    </td>
                    <td class="col-left text-bold">Roti Bakar Cokelat</td>
                    <td>MNU002</td>
                    <td>Makanan</td>
                    <td><span class="stok-badge">20 Porsi</span></td>
                    <td>Rp 18.000</td>
                    <td>
                        <div class="popup-form-toggle" style="padding: 0; margin: 0; justify-content: center; border: none;">
                            <label class="popup-toggle-switch">
                                <input type="checkbox" class="toggle-status-menu" data-id="{{ $menu->id ?? '1' }}" checked>
                                <div class="popup-toggle-track">
                                    <div class="popup-toggle-thumb"></div>
                                </div>
                            </label>
                        </div>
                    </td>
                    <td>
                        <div class="tindakan-col">
                            <a href="#" class="btn-tindakan btn-edit-menu" data-id="MNU001" data-nama="Kopi Susu Tskuy" data-kategori="minuman" data-stok="50" data-harga="15000" data-status="tersedia">
                                <svg width="28" height="28" viewBox="0 0 40 40" fill="none">
                                    <rect width="40" height="40" rx="10" fill="#EFB100" fill-opacity="0.2" />
                                    <path d="M28 12L16 24c-1.1 1.1-4 1.5-4.5 1s.4-3.4 1.5-4.5L25 8.5c1.2-1.2 3-.3 3.5 1 .5.7.7 2.1-.5 2.5z" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M15 8H10a2 2 0 00-2 2v20a2 2 0 002 2h20a2 2 0 002-2v-5" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                            <a href="#" class="btn-tindakan btn-hapus-menu" data-nama="Kopi Susu Tskuy">
                                <svg width="28" height="28" viewBox="0 0 40 40" fill="none">
                                    <rect width="40" height="40" rx="10" fill="#FB2C36" fill-opacity="0.15" />
                                    <path d="M14 16v12a2 2 0 002 2h8a2 2 0 002-2V16M12 13h16M17 13v-2a1 1 0 011-1h4a1 1 0 011 1v2" stroke="#FB2C36" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>

                <tr data-kategori="cemilan">
                    <td>
                        <div style="width:48px;height:48px;border-radius:8px;background:#e5e7eb;margin:0 auto;"></div>
                    </td>
                    <td class="col-left text-bold">French Fries</td>
                    <td>MNU003</td>
                    <td>Cemilan</td>
                    <td><span class="stok-badge">0 Porsi</span></td>
                    <td>Rp 12.000</td>
                    <td>
                        <div class="popup-form-toggle" style="padding: 0; margin: 0; justify-content: center; border: none;">
                            <label class="popup-toggle-switch">
                                <input type="checkbox" class="toggle-status-menu" data-id="{{ $menu->id ?? '1' }}" checked>
                                <div class="popup-toggle-track">
                                    <div class="popup-toggle-thumb"></div>
                                </div>
                            </label>
                        </div>
                    </td>
                    <td>
                        <div class="tindakan-col">
                            <a href="#" class="btn-tindakan btn-edit-menu" data-id="MNU001" data-nama="Kopi Susu Tskuy" data-kategori="minuman" data-stok="50" data-harga="15000" data-status="tersedia">
                                <svg width="28" height="28" viewBox="0 0 40 40" fill="none">
                                    <rect width="40" height="40" rx="10" fill="#EFB100" fill-opacity="0.2" />
                                    <path d="M28 12L16 24c-1.1 1.1-4 1.5-4.5 1s.4-3.4 1.5-4.5L25 8.5c1.2-1.2 3-.3 3.5 1 .5.7.7 2.1-.5 2.5z" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M15 8H10a2 2 0 00-2 2v20a2 2 0 002 2h20a2 2 0 002-2v-5" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                            <a href="#" class="btn-tindakan btn-hapus-menu" data-nama="Kopi Susu Tskuy">
                                <svg width="28" height="28" viewBox="0 0 40 40" fill="none">
                                    <rect width="40" height="40" rx="10" fill="#FB2C36" fill-opacity="0.15" />
                                    <path d="M14 16v12a2 2 0 002 2h8a2 2 0 002-2V16M12 13h16M17 13v-2a1 1 0 011-1h4a1 1 0 011 1v2" stroke="#FB2C36" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>