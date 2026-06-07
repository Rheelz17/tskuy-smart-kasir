@extends('layouts.kasir')

@section('title', 'Detail Penjualan — Kasir')

@section('header_title', 'Detail Penjualan')
@section('header_subtitle', 'Daftar transaksi warkop yang masuk melalui kasir.')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/kasir-pages.css') }}" />
@endsection

@section('content')
<div class="page-title-section">
    <h1 class="page-title">Detail Penjualan</h1>
    <p class="page-subtitle">Daftar transaksi warkop yang masuk melalui kasir.</p>
</div>

<main class="scroll-area">
    <div class="action-bar no-tabs">
        <div class="action-right">
            <div class="search-wrapper">
                <form method="GET" action="{{ route('kasir.penjualan') }}" id="search-form">
                    <input type="text"
                           name="q"
                           value="{{ request('q') }}"
                           placeholder="Cari kode order atau nama pelanggan..."
                           id="search-penjualan-input">
                </form>
                <span class="search-icon">
                    <svg width="16" height="16" viewBox="0 0 26 26" fill="none">
                        <path d="M25.103 22.071L19.66 16.628A10.721 10.721 0 1010.721 21.443c2.182 0 4.212-.662 5.907-1.786l5.443 5.443a2.143 2.143 0 003.032-3.03zM3.216 10.721a7.505 7.505 0 1115.01 0 7.505 7.505 0 01-15.01 0z" fill="#F8B602"/>
                    </svg>
                </span>
            </div>
            <button class="btn-outline" id="btnExport">
                <svg width="16" height="16" viewBox="0 0 30 30" fill="none">
                    <path d="M15.603 14.095V5.284M13.132 7.549l1.375-1.742a2.32 2.32 0 013.563 0l1.375 1.742M21.639 19.836H9.567M11.768 10.893C5.511 12.51 6.009 17.071 6.009 17.071s-.498 4.577 5.759 6.174a13.124 13.124 0 007.663 0c6.255-1.616 5.759-6.174 5.759-6.174s.496-4.578-5.759-6.178z" stroke="#EFB100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Export
            </button>
        </div>
    </div>

    {{-- ============================================================
         TABEL DESKTOP
    ============================================================ --}}
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>No. Transaksi</th>
                    <th>Pelanggan</th>
                    <th>Tanggal & Waktu</th>
                    <th>Tipe Pesanan</th>
                    <th>Total Bayar</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td class="text-bold">{{ $order->order_code }}</td>
                    <td>{{ $order->customer_name ?? '-' }}</td>
                    <td class="text-muted">
                        {{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, H:i') }} WIB
                    </td>
                    <td style="text-transform:capitalize;">
                        @if($order->table_number)
                            🍽️ Dine In (Meja {{ $order->table_number }})
                        @elseif(str_contains(strtolower($order->order_type ?? ''), 'take'))
                            🛍️ Take Away
                        @else
                            {{ $order->order_type ?? '-' }}
                        @endif
                    </td>
                    <td class="text-bold" style="color:var(--kuning);">
                        Rp {{ number_format($order->total, 0, ',', '.') }}
                    </td>
                    <td>
                        <span class="status-badge {{ strtolower($order->status) === 'completed' ? 'success' : (strtolower($order->status) === 'cancelled' ? 'failed' : 'pending') }}">
                            {{ $order->status }}
                        </span>
                    </td>
                    <td>
                        <button class="view-btn btn-lihat-detail"
                                data-order-id="{{ $order->id }}">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#efb100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding:40px; color:#aaa;">
                        Belum ada data penjualan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ============================================================
         CARD MOBILE
    ============================================================ --}}
    <div class="card-grid">
        <div class="trx-card-grid">
            @foreach($orders as $order)
            <div class="trx-card">
                <div class="trx-card-top">
                    <div>
                        <span class="trx-card-id">{{ $order->order_code }}</span>
                        <div class="trx-card-date">
                            {{ \Carbon\Carbon::parse($order->created_at)->format('d M Y - H:i') }}
                        </div>
                    </div>
                    <span class="status-badge {{ strtolower($order->status) === 'completed' ? 'success' : (strtolower($order->status) === 'cancelled' ? 'failed' : 'pending') }}">
                        {{ $order->status }}
                    </span>
                </div>
                <div class="trx-card-items" style="margin-top:4px;">
                    <div style="font-size:11px; color:#64748b;">
                        Pelanggan: <span style="color:#1a1a1a; font-weight:500;">{{ $order->customer_name ?? '-' }}</span>
                    </div>
                    <div style="font-size:11px; color:#64748b;">
                        Layanan: <span style="text-transform:capitalize; color:#1a1a1a; font-weight:500;">{{ $order->order_type ?? '-' }}</span>
                    </div>
                </div>
                <div class="trx-card-footer">
                    <span class="trx-card-payment">Total Transaksi</span>
                    <span class="trx-card-amount" style="color:var(--kuning);">
                        Rp {{ number_format($order->total, 0, ',', '.') }}
                    </span>
                </div>
                <button class="trx-card-detail-btn btn-lihat-detail" data-order-id="{{ $order->id }}">
                    Lihat Detail Nota
                </button>
            </div>
            @endforeach
        </div>
    </div>

    {{-- PAGINATION --}}
    @if($orders->hasPages())
    <footer class="content-footer">
        <p class="data-info">
            Menampilkan {{ $orders->firstItem() }}–{{ $orders->lastItem() }} dari {{ $orders->total() }} transaksi
        </p>
        <div class="pagination">
            @if($orders->onFirstPage())
                <button class="page-link disabled">Sebelumnya</button>
            @else
                <a href="{{ $orders->previousPageUrl() }}" class="page-link">Sebelumnya</a>
            @endif

            @foreach($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                @if($page == $orders->currentPage())
                    <button class="page-number active">{{ $page }}</button>
                @else
                    <a href="{{ $url }}" class="page-number">{{ $page }}</a>
                @endif
            @endforeach

            @if($orders->hasMorePages())
                <a href="{{ $orders->nextPageUrl() }}" class="page-link">Selanjutnya</a>
            @else
                <button class="page-link disabled">Selanjutnya</button>
            @endif
        </div>
    </footer>
    @endif
</main>
@endsection

@section('page_popups')
{{-- ============================================================
     DATA POPUP — Di-render di JS dari data JSON yang di-embed
============================================================ --}}
<script>
    window.penjualanData = @json($orders->items()->map(function($o) {
        return [
            'id'          => $o->id,
            'order_code'  => $o->order_code,
            'customer'    => $o->customer_name ?? '-',
            'waktu'       => \Carbon\Carbon::parse($o->created_at)->format('d/m/Y H:i'),
            'order_type'  => $o->order_type ?? '-',
            'table'       => $o->table_number ? 'Meja ' . $o->table_number : null,
            'subtotal'    => $o->subtotal,
            'tax'         => $o->tax,
            'total'       => $o->total,
            'status'      => $o->status,
            'items'       => collect($o->items)->map(fn($i) => [
                'nama'    => $i->menu_name,
                'qty'     => $i->quantity,
                'price'   => $i->price,
                'note'    => $i->note ?? null,
            ])->toArray(),
        ];
    })->toArray());
</script>

{{-- Satu popup yang di-fill JS --}}
<div class="popup popup-detail-transaksi" id="popup-detail-transaksi">
    <div class="struk-desktop-header">
        <span>Detail Transaksi</span>
        <button class="popup-close popup-close-white" data-close aria-label="Tutup">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10" stroke="#fff" stroke-width="1.8"/>
                <path d="M15 9L9 15M9 9L15 15" stroke="#fff" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
        </button>
    </div>
    <div class="struk-mobile-header">
        <button class="struk-mobile-back" data-close>
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M19 12H5M5 12L12 19M5 12L12 5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
        <span class="struk-mobile-title">Detail Transaksi</span>
    </div>

    <div class="struk-body">
        <div class="struk-logo-wrap">
            <img src="{{ asset('image/logo_warkop.png') }}" alt="Logo Tskuy" class="struk-logo-img">
            <p class="struk-nama-warung">WARKOP TSKUY</p>
            <p class="struk-alamat-warung">UPI Kampus Cibiru, Bandung</p>
        </div>

        <hr class="struk-divider-dashed">

        <div class="struk-info-section">
            <div class="struk-info-row">
                <span class="struk-info-label">No. Transaksi:</span>
                <span class="struk-info-value" id="struk-no-trx">-</span>
            </div>
            <div class="struk-info-row">
                <span class="struk-info-label">Pelanggan:</span>
                <span class="struk-info-value" id="struk-customer">-</span>
            </div>
            <div class="struk-info-row">
                <span class="struk-info-label">Tanggal & Waktu:</span>
                <span class="struk-info-value" id="struk-waktu">-</span>
            </div>
            <div class="struk-info-row">
                <span class="struk-info-label">Tipe Pesanan:</span>
                <span class="struk-info-value" id="struk-tipe">-</span>
            </div>
            <div class="struk-info-row">
                <span class="struk-info-label">Status:</span>
                <span class="struk-info-value" id="struk-status">-</span>
            </div>
        </div>

        <hr class="struk-divider-dashed">

        <p class="struk-section-label">PESANAN</p>
        <div id="struk-items-list"></div>

        <hr class="struk-divider-dashed">

        <div class="struk-subtotal-row">
            <span class="struk-subtotal-label">Subtotal:</span>
            <span class="struk-subtotal-value" id="struk-subtotal">-</span>
        </div>
        <div class="struk-subtotal-row">
            <span class="struk-subtotal-label">Pajak/PBI (10%):</span>
            <span class="struk-subtotal-value" id="struk-pajak">-</span>
        </div>
        <hr class="struk-divider-dashed" style="margin-top:8px;">
        <div class="struk-total-row">
            <span class="struk-total-label">Total</span>
            <span class="struk-total-value" id="struk-total">-</span>
        </div>

        <p class="struk-footer-text">
            Terimakasih telah berkunjung!<br>warkoptskuy.com
        </p>
    </div>

    <div class="struk-actions">
        <button class="btn-struk-unduh" id="btn-unduh-struk">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <polyline points="7 10 12 15 17 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <line x1="12" y1="15" x2="12" y2="3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            Unduh
        </button>
        <button class="btn-struk-cetak" id="btn-cetak-struk">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                <path d="M6 9V2h12v7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <rect x="6" y="14" width="12" height="8" rx="1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Cetak Struk
        </button>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function formatRp(n) { return 'Rp ' + parseInt(n).toLocaleString('id-ID') + ',-'; }

    // Klik tombol lihat detail (tabel & card)
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-lihat-detail');
        if (!btn) return;

        const orderId = parseInt(btn.dataset.orderId);
        const order   = (window.penjualanData || []).find(o => o.id === orderId);
        if (!order) return;

        // Isi data ke elemen struk
        document.getElementById('struk-no-trx').innerText   = order.order_code;
        document.getElementById('struk-customer').innerText = order.customer;
        document.getElementById('struk-waktu').innerText    = order.waktu;
        document.getElementById('struk-tipe').innerText     = order.table
            ? order.order_type + ' (' + order.table + ')'
            : order.order_type;
        document.getElementById('struk-subtotal').innerText = formatRp(order.subtotal);
        document.getElementById('struk-pajak').innerText    = formatRp(order.tax);
        document.getElementById('struk-total').innerText    = formatRp(order.total);

        const statusEl = document.getElementById('struk-status');
        statusEl.innerText  = order.status;
        statusEl.className  = 'struk-info-value ' +
            (order.status === 'COMPLETED' ? 'lunas' : order.status === 'CANCELLED' ? 'failed' : 'pending');

        // Render item pesanan
        const listEl = document.getElementById('struk-items-list');
        listEl.innerHTML = order.items.map(item => `
            <div class="struk-item">
                <div class="struk-item-top">
                    <div>
                        <p class="struk-item-nama">${item.qty}x ${item.nama}</p>
                        <p class="struk-item-satuan">@ ${formatRp(item.price)}</p>
                    </div>
                    <span class="struk-item-harga">${formatRp(item.price * item.qty)}</span>
                </div>
                ${item.note ? `<span class="struk-item-note">📝 ${item.note}</span>` : ''}
            </div>
        `).join('');

        window._openPopup('popup-detail-transaksi');
    });

    // Search submit saat Enter
    document.getElementById('search-penjualan-input')?.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') document.getElementById('search-form').submit();
    });

    // Export
    document.getElementById('btnExport')?.addEventListener('click', () => {
        window._openPopup?.('popupExport');
    });
});
</script>
@endsection