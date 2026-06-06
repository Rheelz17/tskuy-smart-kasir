@extends('layouts.pelanggan')

@section('title', 'Riwayat Pesanan - Warkop Tskuy')

@section('content')
<main class="content-area">
    <section class="page-title-section" style="display: block; padding: 0 0 16px 0;">
        <h1 class="page-title">Riwayat Pesanan</h1>
        <p class="page-subtitle">Jurnal log seluruh transaksi dan status hidangan dari akunmu.</p>
    </section>

    @if(!$activeBill && $pastOrders->isEmpty())
        <!-- 📭 KONDISI JIKA BELUM ADA LOG SAMA SEKALI -->
        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 60px 20px; background: #fff; border-radius: 16px; box-shadow: var(--shadow-sm); text-align: center;">
            <div style="font-size: 50px; margin-bottom: 12px;">☕</div>
            <h3 style="font-weight: 700; color: #1a1a1a; margin-bottom: 6px;">Belum Ada Riwayat Pesanan</h3>
            <p style="font-size: 13px; color: #94a3b8; max-width: 300px; margin-bottom: 20px;">Akunmu belum memiliki catatan transaksi. Yuk mulai pesan kopi atau camilan favoritmu!</p>
            <a href="{{ route('pelanggan.orders') }}" class="btn-outline" style="text-decoration: none; padding: 10px 24px;">Pesan Sekarang</a>
        </div>
    @else

        <!-- 📝 LOG SEKSI 1: PESANAN BERJALAN (SEDANG DIPROSES / OPEN BILL) -->
        @if($activeBill)
            <div style="background: #fffbeb; border: 1.5px solid #efb100; padding: 20px; border-radius: 16px; margin-bottom: 28px; box-shadow: var(--shadow-sm);">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 14px;">
                    <div>
                        <span style="background: #efb100; color: #fff; font-size: 10px; font-weight: 700; padding: 4px 10px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.5px;">Pemesanan Aktif ⏳</span>
                        <h3 style="font-size: 16px; font-weight: 800; color: #1a1a1a; margin-top: 6px;">{{ $activeBill->order_code }}</h3>
                        <p style="font-size: 11px; color: #64748b;">Waktu Order: {{ \Carbon\Carbon::parse($activeBill->created_at)->format('H:i') }} WIB</p>
                    </div>
                    <span class="status-badge pending" style="font-size: 12px; padding: 6px 14px;">{{ $activeBill->status }}</span>
                </div>

                <div style="background: #fff; border-radius: 10px; padding: 12px; border: 1px solid #fce4a0;">
                    <p style="font-size: 12px; font-weight: 700; color: #92400e; margin-bottom: 8px;">Rincian Item Diproses:</p>
                    <ul style="list-style: none; padding: 0; font-size: 13px; color: #334155; display: flex; flex-direction: column; gap: 4px;">
                        @foreach($activeBill->items as $item)
                            <li style="display: flex; justify-content: space-between;">
                                <span>• {{ $item->menu_name }} <strong style="color: #64748b;">x{{ $item->quantity }}</strong></span>
                                <span style="font-weight: 600;">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                            </li>
                        @endforeach
                    </ul>
                    
                    <div style="margin-top: 8px; font-size: 11px; color: #64748b;">
                        📍 Layanan: <span style="text-transform: capitalize; color: #1a1a1a; font-weight: 600;">{{ $activeBill->eating_option }}</span> 
                        @if($activeBill->table_number)
                            | Meja: <span style="color: #1a1a1a; font-weight: 600;">#{{ $activeBill->table_number }}</span>
                        @endif
                    </div>

                    <div style="border-top: 1px dashed #fce4a0; margin-top: 10px; padding-top: 8px; display: flex; justify-content: space-between; font-weight: 800; font-size: 14px; color: #1a1a1a;">
                        <span>Total Tagihan Sementara:</span>
                        <span style="color: #efb100;">Rp {{ number_format($activeBill->total, 0, ',', '.') }}</span>
                    </div>
                </div>
                <button class="trx-card-detail-btn" data-open="popup-detail-{{ $activeBill->id }}" style="margin-top: 12px; background: #222;">
                    Lihat Live Nota Struk
                </button>
            </div>
        @endif

        <!-- 📜 LOG SEKSI 2: JURNAL TRANSAKSI LAMPAU (SELESAI / LUNAS / REKOR SEBELUMNYA) -->
        @if(!$pastOrders->isEmpty())
            <p class="section-title" style="font-size: 15px; font-weight: 700; color: #1a1a1a; margin-bottom: 12px;">Arsip Riwayat Transaksi</p>
            
            <!-- 💻 Versi Monitor / Desktop -->
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
                        @foreach($pastOrders as $order)
                            <tr>
                                <td class="text-bold">{{ $order->order_code }}</td>
                                <td class="text-muted">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, H:i') }} WIB</td>
                                <td style="text-transform: capitalize;">
                                    {{ $order->eating_option == 'dine in' ? '🍽️ Dine In' : '🛍️ Take Away' }}
                                </td>
                                <td class="text-bold" style="color: var(--kuning);">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                <td>
                                    <span class="status-badge {{ strtolower($order->status) == 'completed' ? 'success' : 'failed' }}">
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

            <!-- 📱 Versi Layar HP / Mobile -->
            <div class="card-grid">
                <div class="trx-card-grid">
                    @foreach($pastOrders as $order)
                        <div class="trx-card">
                            <div class="trx-card-top">
                                <div>
                                    <span class="trx-card-id">{{ $order->order_code }}</span>
                                    <div class="trx-card-date">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y - H:i') }}</div>
                                </div>
                                <span class="status-badge {{ strtolower($order->status) == 'completed' ? 'success' : 'failed' }}">
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
                            <button class="trx-card-detail-btn" data-open="popup-detail-{{ $order->id }}">
                                Lihat Detail Nota
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    @endif
</main>
@endsection

@section('page_popups')
    <!-- 🧾 POPUP INLINE DETAIL NOTA STRUK -->
    @php 
        $allPopups = collect($pastOrders);
        if($activeBill) { $allPopups->prepend($activeBill); }
    @endphp

    @foreach($allPopups as $order)
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
                        {{ $order->eating_option }} {{$order->table_number ? '(Meja #' . $order->table_number . ')' : ''}}
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

            <div class="struk-footer-text">
                Terima kasih sudah nongkrong di Warkop Tskuy!<br>Satu persen lebih baik setiap hari 🙌
            </div>
        </div>
    </div>
    @endforeach
@endsection