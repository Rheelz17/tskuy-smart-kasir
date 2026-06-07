@extends('layouts.pelanggan')

@section('title', 'Riwayat Pesanan - Warkop Tskuy')

@section('content')
<main class="content-area">
    <section class="page-title-section" style="display: block; padding: 0 0 16px 0;">
        <h1 class="page-title">Riwayat Pesanan</h1>
        <p class="page-subtitle">Jurnal log seluruh transaksi dan status hidangan dari akunmu.</p>
    </section>

    @if(!$activeBill && $pastOrders->isEmpty())
        {{-- 📭 KOSONG TOTAL --}}
        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 60px 20px; background: #fff; border-radius: 16px; box-shadow: var(--shadow-sm); text-align: center;">
            <div style="font-size: 50px; margin-bottom: 12px;">☕</div>
            <h3 style="font-weight: 700; color: #1a1a1a; margin-bottom: 6px;">Belum Ada Riwayat Pesanan</h3>
            <p style="font-size: 13px; color: #94a3b8; max-width: 300px; margin-bottom: 20px;">Akunmu belum memiliki catatan transaksi. Yuk mulai pesan kopi atau camilan favoritmu!</p>
            <a href="{{ route('pelanggan.orders') }}" class="btn-outline" style="text-decoration: none; padding: 10px 24px;">Pesan Sekarang</a>
        </div>
    @else

        {{-- =====================================================
             📜 TABEL SEMUA TRANSAKSI (termasuk open bill aktif)
        ====================================================== --}}
        <p class="section-title" style="font-size: 15px; font-weight: 700; color: #1a1a1a; margin-bottom: 12px;">Semua Riwayat Transaksi</p>

        {{-- Gabungkan activeBill + pastOrders jadi satu koleksi untuk ditampilkan --}}
        @php
            $allOrders = collect($pastOrders);
            if ($activeBill) { $allOrders->prepend($activeBill); }
        @endphp

        {{-- 💻 VERSI DESKTOP --}}
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Kode Order</th>
                        <th>Tanggal & Waktu</th>
                        <th>Opsi Layanan</th>
                        <th>Total Bayar</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allOrders as $order)
                    @php
                        $isActive = $activeBill && $order->id === $activeBill->id;
                        $statusLower = strtolower($order->status);
                        $badgeClass = match($statusLower) {
                            'completed'  => 'success',
                            'cooking'    => 'cooking',
                            'pending'    => 'pending',
                            default      => 'pending',
                        };
                    @endphp
                    <tr @if($isActive) style="background: #fffbeb;" @endif>
                        <td class="text-bold">
                            {{ $order->order_code }}
                            @if($isActive)
                                <span style="background:#efb100; color:#fff; font-size:9px; font-weight:700; padding:2px 7px; border-radius:20px; margin-left:6px; vertical-align:middle;">AKTIF ⏳</span>
                            @endif
                        </td>
                        <td class="text-muted">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, H:i') }} WIB</td>
                        <td style="text-transform: capitalize;">
                            {{ $order->eating_option == 'dine in' ? '🍽️ Dine In' : '🛍️ Take Away' }}
                        </td>
                        <td class="text-bold" style="color: var(--kuning);">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                        <td>
                            <span class="status-badge {{ $badgeClass }}">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td>
                            <button class="view-btn" data-open="popup-detail-{{ $order->id }}">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#efb100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- 📱 VERSI MOBILE --}}
        <div class="card-grid">
            <div class="trx-card-grid">
                @foreach($allOrders as $order)
                @php
                    $isActive = $activeBill && $order->id === $activeBill->id;
                    $statusLower = strtolower($order->status);
                    $badgeClass = match($statusLower) {
                        'completed'  => 'success',
                        'cooking'    => 'cooking',
                        'pending'    => 'pending',
                        default      => 'pending',
                    };
                @endphp
                <div class="trx-card" @if($isActive) style="border: 1.5px solid #efb100; background: #fffbeb;" @endif>
                    <div class="trx-card-top">
                        <div>
                            <span class="trx-card-id">
                                {{ $order->order_code }}
                                @if($isActive)
                                    <span style="background:#efb100; color:#fff; font-size:9px; font-weight:700; padding:2px 7px; border-radius:20px; margin-left:4px; vertical-align:middle;">AKTIF</span>
                                @endif
                            </span>
                            <div class="trx-card-date">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y - H:i') }}</div>
                        </div>
                        <span class="status-badge {{ $badgeClass }}">
                            {{ $order->status }}
                        </span>
                    </div>
                    <div class="trx-card-items" style="margin-top: 4px;">
                        <div style="font-size: 11px; color: #64748b;">Layanan: <span style="text-transform: capitalize; color: #1a1a1a; font-weight: 500;">{{ $order->eating_option }}</span></div>
                    </div>
                    <div class="trx-card-footer">
                        <span class="trx-card-payment">Total Transaksi</span>
                        <span class="trx-card-amount" style="color: var(--kuning);">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                    <button class="trx-card-detail-btn" data-open="popup-detail-{{ $order->id }}"
                        @if($isActive) style="background: #efb100;" @endif>
                        @if($isActive) Lihat Nota & Aksi @else Lihat Detail Nota @endif
                    </button>
                </div>
                @endforeach
            </div>
        </div>

    @endif
</main>
@endsection

@section('page_popups')

    {{-- =====================================================
         🧾 POPUP DETAIL NOTA STRUK (untuk SEMUA order)
    ====================================================== --}}
    @php
        $allPopups = collect($pastOrders);
        if ($activeBill) { $allPopups->prepend($activeBill); }
    @endphp

    @foreach($allPopups as $order)
    @php
        $isOpenBill = $activeBill && $order->id === $activeBill->id;
    @endphp

    <div class="popup popup-detail-transaksi" id="popup-detail-{{ $order->id }}">
        <div class="struk-desktop-header">
            <h3>Detail Nota Transaksi</h3>
            <button class="popup-close-white" data-close>&times;</button>
        </div>
        <div class="struk-mobile-header">
            <button class="struk-mobile-back" data-close>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
            </button>
            <span class="struk-mobile-title">Detail Nota</span>
        </div>

        <div class="struk-body">
            <div class="struk-logo-wrap">
                <img src="{{ asset('image/logo_warkop.png') }}" class="struk-logo-img" alt="Logo">
                <span class="struk-nama-warung">Warkop Tskuy</span>
                <span class="struk-alamat-warung">UPI Kampus Cibiru, Bandung</span>
            </div>

            <hr class="struk-divider-dashed">

            @if($order->is_open_bill == 1 && $order->payment_status !== 'paid')
            {{-- Badge Open Bill Aktif --}}
            <div style="text-align:center; margin-bottom: 12px;">
                <span style="background:#fffbeb; border:1.5px solid #efb100; color:#92400e; font-size:11px; font-weight:700; padding:5px 14px; border-radius:20px;">
                    ⏳ Open Bill – Sedang Berjalan
                </span>
            </div>
            @endif

            <div class="struk-info-section">
                <div class="struk-info-row">
                    <span class="struk-info-label">Kode Order</span>
                    <span class="struk-info-value text-bold">{{ $order->order_code }}</span>
                </div>
                <div class="struk-info-row">
                    <span class="struk-info-label">Waktu</span>
                    <span class="struk-info-value">{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</span>
                </div>
                <div class="struk-info-row">
                    <span class="struk-info-label">Nama Pemesan</span>
                    <span class="struk-info-value">{{ $order->customer_name ?? auth()->user()->name }}</span>
                </div>
                <div class="struk-info-row">
                    <span class="struk-info-label">Opsi / Tempat</span>
                    <span class="struk-info-value text-bold" style="text-transform: capitalize;">
                        {{ $order->eating_option }} {{ $order->table_number ? '(Meja #' . $order->table_number . ')' : '' }}
                    </span>
                </div>
                <div class="struk-info-row">
                    <span class="struk-info-label">Status Sesi</span>
                    <span class="struk-info-value {{ strtolower($order->status) == 'completed' ? 'lunas' : 'pending' }}">{{ $order->status }}</span>
                </div>
            </div>

            <hr class="struk-divider-dashed">
            <div class="struk-section-label">Rincian Hidangan:</div>

            <div style="display: flex; flex-direction: column; gap: 10px;">
                @if(isset($order->items))
                    @foreach($order->items as $item)
                    <div class="struk-item">
                        <div class="struk-item-top">
                            <span class="struk-item-nama">{{ $item->menu_name }}</span>
                            <span class="struk-item-harga">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                        </div>
                        <div class="struk-item-satuan">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                        @if(isset($item->note) && $item->note)
                            <div class="struk-item-note">💬 {{ $item->note }}</div>
                        @endif
                    </div>
                    @endforeach
                @endif
            </div>

            <hr class="struk-divider-dashed">

            <div class="struk-subtotal-row">
                <span class="struk-subtotal-label">Subtotal</span>
                <span class="struk-subtotal-value">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="struk-subtotal-row">
                <span class="struk-subtotal-label">Pajak (10%)</span>
                <span class="struk-subtotal-value">Rp {{ number_format($order->tax, 0, ',', '.') }}</span>
            </div>
            <div class="struk-total-row">
                <span class="struk-total-label">Total Pembayaran</span>
                <span class="struk-total-value">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
            </div>

           @if($order->is_open_bill == 1 && $order->payment_status !== 'paid')
            {{-- ================================================
                 🔥 ACTION BUTTONS KHUSUS OPEN BILL
            ================================================= --}}
            <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 20px; padding-top: 16px; border-top: 1px dashed #fce4a0;">
                {{-- Tombol Tambah Pesanan --}}
                <a href="{{ route('pelanggan.order.continue', $order->id) }}"
                   style="display:flex; align-items:center; justify-content:center; gap:10px; background:#222; color:#fff; padding:14px; border-radius:12px; font-size:13px; font-weight:700; text-decoration:none; text-align:center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 5V19M5 12H19" stroke="white" stroke-width="2.5" stroke-linecap="round"/></svg>
                    Tambah Pesanan
                </a>
                {{-- Tombol Selesaikan & Bayar --}}
                <a href="{{ route('pelanggan.qris', ['orderCode' => $order->order_code]) }}"
                   style="display:flex; align-items:center; justify-content:center; gap:10px; background:#efb100; color:#fff; padding:14px; border-radius:12px; font-size:13px; font-weight:700; text-decoration:none; text-align:center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="8" height="8" rx="1" stroke="white" stroke-width="2"/><rect x="13" y="3" width="8" height="8" rx="1" stroke="white" stroke-width="2"/><rect x="3" y="13" width="8" height="8" rx="1" stroke="white" stroke-width="2"/><path d="M13 13h3v3M16 16v5M13 16h3M13 21h8" stroke="white" stroke-width="2" stroke-linecap="round"/></svg>
                    Selesaikan & Bayar via QRIS
                </a>
            </div>
            @else
            <div class="struk-footer-text">
                Terima kasih sudah nongkrong di Warkop Tskuy!<br>Satu persen lebih baik setiap hari 🙌
            </div>
            @endif
        </div>
    </div>
    @endforeach

@endsection