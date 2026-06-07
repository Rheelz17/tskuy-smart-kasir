{{--
    Partial: resources/views/partials/popup-detail-menu.blade.php
    Dipakai oleh: pos.blade.php dan ordersPelanggan.blade.php
    Variable yang dibutuhkan: $menu (dengan relasi options.values sudah di-load)
--}}

<div class="popup popup-menu-detail" id="popup-detail-menu-{{ $menu->id }}">

    {{-- Tombol tutup --}}
    <button class="popup-close popup-close-float" data-close>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="10" stroke="#555" stroke-width="1.8"/>
            <path d="M15 9L9 15M9 9L15 15" stroke="#555" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
    </button>

    <div class="menu-detail-inner">
        {{-- Gambar menu --}}
        <div class="menu-detail-img-wrap">
            <img src="{{ asset('image/' . ($menu->image ?? 'placeholder.png')) }}"
                 alt="{{ $menu->name }}"
                 class="menu-detail-img">
        </div>

        {{-- Info dasar --}}
        <div class="menu-detail-info">
            <h2 class="menu-detail-name">{{ $menu->name }}</h2>
            <p class="menu-detail-price" id="harga-display-{{ $menu->id }}">
                Rp {{ number_format($menu->price, 0, ',', '.') }},-
            </p>
            @if($menu->description)
            <p class="menu-detail-desc" style="font-size:12px; color:#666; margin-bottom:12px;">
                {{ $menu->description }}
            </p>
            @endif

            {{-- Qty (desktop) --}}
            <div class="menu-detail-row desktop-qty">
                <span class="menu-detail-label">Jumlah Porsi</span>
                <div class="menu-detail-qty">
                    <button class="qty-btn pd-btn-min">-</button>
                    <span class="pd-qty-val">1</span>
                    <button class="qty-btn pd-btn-plus">+</button>
                </div>
            </div>

            {{-- ============================================================
                 OPSI DINAMIS — di-render dari menu_options + menu_option_values
                 Setiap option = 1 grup pilihan (radio jika single, bisa dikembangkan)
            ============================================================ --}}
            @if($menu->options && $menu->options->count() > 0)
                @foreach($menu->options as $option)
                <div class="menu-detail-section option-group"
                     data-option-id="{{ $option->id }}"
                     data-option-name="{{ $option->name }}">

                    <div style="display:flex; justify-content:space-between; align-items:baseline; margin-bottom:6px;">
                        <p class="menu-detail-label">{{ $option->name }}</p>
                        @if($option->values->where('additional_price', '>', 0)->count() > 0)
                            <span style="font-size:10px; color:#22c55e; font-weight:600;">
                                Ada tambahan harga
                            </span>
                        @endif
                    </div>

                    <div class="option-values-list">
                        @foreach($option->values as $idx => $val)
                        <label class="option-value-item" style="
                            display:flex; justify-content:space-between; align-items:center;
                            padding:10px 12px;
                            border:1.5px solid {{ $idx === 0 ? '#efb100' : '#e5e7eb' }};
                            border-radius:10px; margin-bottom:6px; cursor:pointer;
                            transition:all .15s;
                            background:{{ $idx === 0 ? '#fffbeb' : '#fff' }};
                        ">
                            <div style="display:flex; align-items:center; gap:10px;">
                                {{-- Radio button custom --}}
                                <input type="radio"
                                       name="opt-{{ $menu->id }}-{{ $option->id }}"
                                       value="{{ $val->id }}"
                                       data-option-id="{{ $option->id }}"
                                       data-option-name="{{ $option->name }}"
                                       data-value-label="{{ $val->value }}"
                                       data-additional-price="{{ $val->additional_price }}"
                                       class="option-radio"
                                       {{ $idx === 0 ? 'checked' : '' }}
                                       style="display:none;">
                                <div class="radio-dot" style="
                                    width:18px; height:18px; border-radius:50%;
                                    border:2px solid {{ $idx === 0 ? '#efb100' : '#d1d5db' }};
                                    background:{{ $idx === 0 ? '#efb100' : '#fff' }};
                                    display:flex; align-items:center; justify-content:center;
                                    flex-shrink:0; transition:all .15s;
                                ">
                                    @if($idx === 0)
                                    <div style="width:7px; height:7px; border-radius:50%; background:#fff;"></div>
                                    @endif
                                </div>
                                <span style="font-size:13px; color:#333; font-weight:500;">
                                    {{ $val->value }}
                                </span>
                            </div>
                            @if($val->additional_price > 0)
                            <span class="opt-price-tag" style="
                                font-size:11px; font-weight:700; color:#22c55e;
                                background:#f0fdf4; padding:2px 8px; border-radius:20px;
                            ">
                                +Rp {{ number_format($val->additional_price, 0, ',', '.') }}
                            </span>
                            @else
                            <span style="font-size:11px; color:#bbb;">Gratis</span>
                            @endif
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            @else
                {{-- Fallback: tidak ada opsi untuk menu ini --}}
                <p style="font-size:12px; color:#aaa; margin:8px 0 4px;">
                    Menu ini tidak memiliki variasi tambahan.
                </p>
            @endif
        </div>
    </div>

    {{-- Bagian bawah: catatan + qty mobile + tombol tambah --}}
    <div class="menu-detail-catatan">
        <p class="menu-detail-label">Catatan Khusus <span style="color:#bbb; font-size:11px; font-weight:400;">(opsional)</span></p>
        <input type="text"
               class="catatan-input"
               id="catatan-{{ $menu->id }}"
               placeholder="Contoh: Sayur banyakin, jangan pakai bawang">
    </div>

    {{-- Qty (mobile) --}}
    <div class="menu-detail-row mobile-qty" style="padding:0 0 12px 0;">
        <span class="menu-detail-label">Jumlah Porsi</span>
        <div class="menu-detail-qty">
            <button class="qty-btn pd-btn-min">-</button>
            <span class="pd-qty-val">1</span>
            <button class="qty-btn pd-btn-plus">+</button>
        </div>
    </div>

    <div class="menu-detail-footer">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; font-size:12px; color:#888;">
            <span>Total harga</span>
            <span id="total-harga-{{ $menu->id }}" style="font-weight:700; color:#efb100; font-size:14px;">
                Rp {{ number_format($menu->price, 0, ',', '.') }}
            </span>
        </div>
        <button class="popup-btn btn-add-from-detail"
                data-id="{{ $menu->id }}"
                data-name="{{ $menu->name }}"
                data-base-price="{{ $menu->price }}"
                data-image="{{ asset('image/' . ($menu->image ?? 'placeholder.png')) }}">
            Tambahkan ke Keranjang
        </button>
    </div>
</div>

{{-- Script untuk interaksi opsi (radio styling + hitung harga) --}}
<script>
(function() {
    const menuId   = {{ $menu->id }};
    const basePrice = {{ $menu->price }};

    function recalcHarga() {
        const popup = document.getElementById('popup-detail-menu-' + menuId);
        if (!popup) return;

        let extra = 0;
        popup.querySelectorAll('.option-radio:checked').forEach(r => {
            extra += parseFloat(r.dataset.additionalPrice) || 0;
        });

        const qtyEl = popup.querySelector('.pd-qty-val');
        const qty   = qtyEl ? parseInt(qtyEl.innerText) || 1 : 1;
        const total = (basePrice + extra) * qty;

        const totalEl = document.getElementById('total-harga-' + menuId);
        if (totalEl) totalEl.innerText = 'Rp ' + total.toLocaleString('id-ID');

        // Update tombol tambah dengan harga terbaru
        const btn = popup.querySelector('.btn-add-from-detail');
        if (btn) btn.dataset.computedPrice = basePrice + extra;
    }

    // Pasang listener saat popup sudah ada di DOM
    document.addEventListener('DOMContentLoaded', function() {
        const popup = document.getElementById('popup-detail-menu-' + menuId);
        if (!popup) return;

        // Styling radio pada klik
        popup.addEventListener('click', function(e) {
            const label = e.target.closest('.option-value-item');
            if (!label) return;

            const radio = label.querySelector('.option-radio');
            if (!radio) return;

            // Deselect semua dalam grup yang sama
            const optionId = radio.dataset.optionId;
            popup.querySelectorAll(`.option-radio[data-option-id="${optionId}"]`).forEach(r => {
                r.checked = false;
                const parentLabel = r.closest('.option-value-item');
                if (parentLabel) {
                    parentLabel.style.borderColor = '#e5e7eb';
                    parentLabel.style.background  = '#fff';
                    const dot = parentLabel.querySelector('.radio-dot');
                    if (dot) {
                        dot.style.borderColor = '#d1d5db';
                        dot.style.background  = '#fff';
                        dot.innerHTML         = '';
                    }
                }
            });

            // Select yang diklik
            radio.checked = true;
            label.style.borderColor = '#efb100';
            label.style.background  = '#fffbeb';
            const dot = label.querySelector('.radio-dot');
            if (dot) {
                dot.style.borderColor = '#efb100';
                dot.style.background  = '#efb100';
                dot.innerHTML         = '<div style="width:7px;height:7px;border-radius:50%;background:#fff;"></div>';
            }

            recalcHarga();
        });

        // Recalc saat qty berubah
        const observer = new MutationObserver(recalcHarga);
        popup.querySelectorAll('.pd-qty-val').forEach(el => {
            observer.observe(el, { childList: true, subtree: true, characterData: true });
        });

        recalcHarga();
    });
})();
</script>
