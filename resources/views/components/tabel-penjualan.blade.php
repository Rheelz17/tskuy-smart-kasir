@props(['transactions'])

{{-- ══════════════════════════════════════════════════════════
     TAMPILAN MOBILE — CARD GRID
══════════════════════════════════════════════════════════ --}}
<div class="card-grid">
    <div class="trx-card-grid">
        @forelse($transactions as $order)
            @php
                // Mengambil data item untuk JavaScript modal detail jika dibutuhkan
                $itemsSerialized = $order->items ? $order->items->map(function ($item) {
                    return $item->quantity . 'x ' . ($item->menu->name ?? 'Menu Dihapus') . '|' . (int)$item->price . '|' . ($item->note ?? '');
                })->join(';;') : '';
            @endphp

            <div class="trx-card"
                 data-id="{{ $order->id }}"
                 data-kode="{{ $order->order_code }}"
                 data-customer="{{ $order->customer_name }}"
                 data-waktu="{{ $order->created_at }}"
                 data-total="{{ (int) $order->total }}"
                 data-subtotal="{{ (int) $order->subtotal }}"
                 data-pajak="{{ (int) $order->tax }}"
                 data-tipe="{{ $order->eating_option }}"
                 data-items="{{ $itemsSerialized }}">

                <div class="trx-card-top">
                    <span class="trx-card-id">#{{ $order->order_code }}</span>
                </div>

                <p class="trx-card-date">
                    Pelanggan: <strong>{{ $order->customer_name }}</strong><br>
                    Meja: {{ $order->table_id ?? '-' }}<br>
                    Waktu: {{ $order->created_at }}
                </p>

                <p class="trx-card-items">
                    @if($order->items)
                        @foreach($order->items->take(3) as $item)
                            {{ $item->quantity }}x {{ $item->menu->name ?? 'Menu Dihapus' }}{{ !$loop->last ? ', ' : '' }}
                        @endforeach
                        @if($order->items->count() > 3)
                            <span style="color:#94a3b8;">+{{ $order->items->count() - 3 }} lainnya</span>
                        @endif
                    @endif
                </p>

                <div class="trx-card-footer">
                    <span class="trx-card-payment">{{ strtoupper($order->eating_option) }}</span>
                    <span class="trx-card-amount">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>

                <button class="trx-card-detail-btn btn-detail-sales" data-id="{{ $order->id }}">
                    Lihat Detail
                </button>
            </div>
        @empty
            <div class="trx-card" style="grid-column: 1 / -1; text-align: center; padding: 32px;">
                <p style="color: #94a3b8; font-size: 13px;">Belum ada data transaksi.</p>
            </div>
        @endforelse
    </div>
</div>

<footer class="content-footer-mobile">
    <p class="data-info">
        Menampilkan {{ $transactions->firstItem() ?? 0 }} sampai {{ $transactions->lastItem() ?? 0 }} dari {{ $transactions->total() }} transaksi
    </p>
    <div class="pagination">
        {{ $transactions->links() }}
    </div>
</footer>


{{-- ══════════════════════════════════════════════════════════
     TAMPILAN DESKTOP — TABEL
══════════════════════════════════════════════════════════ --}}
<div class="table-container">
    <table id="tabel-penjualan">
        <thead>
            <tr>
                <th class="col-check"><input type="checkbox" id="check-all"></th>
                <th class="col-left">No Pesanan</th>
                <th>No Meja</th>
                <th>Waktu</th>
                <th>Item Pesanan</th>
                <th>Tipe</th>
                <th>Total</th>
                <th>Detail</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $order)
                @php
                    $itemsSerialized = $order->items ? $order->items->map(function ($item) {
                        return $item->quantity . 'x ' . ($item->menu->name ?? 'Menu Dihapus') . '|' . (int)$item->price . '|' . ($item->note ?? '');
                    })->join(';;') : '';
                @endphp

                <tr data-id="{{ $order->id }}"
                    data-kode="{{ $order->order_code }}"
                    data-waktu="{{ $order->created_at }}"
                    data-total="{{ (int) $order->total }}"
                    data-subtotal="{{ (int) $order->subtotal }}"
                    data-pajak="{{ (int) $order->tax }}"
                    data-tipe="{{ $order->eating_option }}"
                    data-items="{{ $itemsSerialized }}">

                    <td class="col-check">
                        <input type="checkbox" name="ids[]" value="{{ $order->id }}">
                    </td>
                    <td class="col-left text-bold">#{{ $order->order_code }}</td>
                    <td>{{ $order->table_id ?? 'Take Away' }}</td>
                    <td>{{ $order->created_at }}</td>
                    <td class="text-muted" style="max-width: 180px; line-height: 1.6;">
                        @if($order->items)
                            @foreach($order->items->take(3) as $item)
                                {{ $item->quantity }}x {{ $item->menu->name ?? 'Menu Dihapus' }}<br>
                            @endforeach
                            @if($order->items->count() > 3)
                                <span style="color:#94a3b8; font-size: 11px;">+{{ $order->items->count() - 3 }} item lainnya</span>
                            @endif
                        @endif
                    </td>
                    <td>{{ $order->eating_option }}</td>
                    <td class="text-bold">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                    <td>
                        <button class="view-btn btn-detail-sales" data-id="{{ $order->id }}">
                            <svg width="20" height="20" viewBox="0 0 33 33" fill="none">
                                <path d="M20.626 16.5a4.125 4.125 0 11-8.25 0 4.125 4.125 0 018.25 0z" stroke="#0F172B" stroke-width="2"/>
                                <path d="M16.502 6.875C10.345 6.875 5.133 10.922 3.381 16.5c1.752 5.578 6.964 9.625 13.121 9.625s11.369-4.047 13.121-9.625C27.87 10.922 22.659 6.875 16.502 6.875z" stroke="#0F172B" stroke-width="2"/>
                            </svg>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align: center; padding: 32px;" class="text-muted">
                        Belum ada data transaksi.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<footer class="content-footer">
    <p class="data-info">
        Menampilkan {{ $transactions->firstItem() ?? 0 }} sampai {{ $transactions->lastItem() ?? 0 }} dari {{ $transactions->total() }} transaksi
    </p>
    {{-- <div class="pagination">
        {{ $transactions->links() }}
    </div> --}}
</footer>