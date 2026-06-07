<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi - Warkop Tskuy</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }

        body {
            background: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
        }

        .receipt-wrap {
            width: 100%;
            max-width: 420px;
            min-height: 100vh;
            background: #fff;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }

        /* ── HEADER ── */
        .receipt-header {
            background: #1a1a1a;
            padding: 22px 24px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .logo-circle {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            overflow: hidden;
            background: #2a2a2a;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .logo-circle img { width: 100%; height: 100%; object-fit: cover; }
        .logo-circle .logo-fallback {
            font-size: 18px;
            font-weight: 800;
            color: #efb100;
        }
        .header-text .store-name {
            font-size: 16px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.3px;
        }
        .header-text .store-name span { color: #efb100; }
        .header-text .store-sub {
            font-size: 10px;
            color: #888;
            font-weight: 500;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* ── SUCCESS BANNER ── */
        .success-banner {
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
            padding: 24px 24px 20px;
            text-align: center;
            position: relative;
        }
        .success-banner::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 20px;
            background: #fff;
            border-radius: 20px 20px 0 0;
        }
        .check-ring {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: rgba(255,255,255,0.15);
            border: 3px solid rgba(255,255,255,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            animation: pop .4s cubic-bezier(.36,.07,.19,.97) both;
        }
        @keyframes pop {
            0%   { transform: scale(0.5); opacity: 0; }
            70%  { transform: scale(1.1); }
            100% { transform: scale(1);   opacity: 1; }
        }
        .success-title {
            font-size: 17px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 4px;
        }
        .success-sub {
            font-size: 11px;
            color: rgba(255,255,255,0.75);
            font-weight: 500;
        }

        /* ── BODY ── */
        .receipt-body {
            padding: 0 22px;
            flex: 1;
        }

        /* Order Meta */
        .order-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 0 14px;
            border-bottom: 1px solid #f0f0f0;
        }
        .order-code {
            font-size: 18px;
            font-weight: 800;
            color: #1a1a1a;
            letter-spacing: -0.5px;
        }
        .order-code span { color: #efb100; }
        .order-meta-right {
            text-align: right;
        }
        .order-meta-right .type-badge {
            display: inline-block;
            background: #1a1a1a;
            color: #efb100;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .order-meta-right .order-time {
            font-size: 10px;
            color: #999;
            display: block;
        }

        /* Info rows */
        .info-block { padding: 14px 0; border-bottom: 1px solid #f3f3f3; }
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12.5px;
            margin-bottom: 9px;
        }
        .info-row:last-child { margin-bottom: 0; }
        .info-label { color: #888; font-weight: 500; }
        .info-value { color: #1a1a1a; font-weight: 600; text-align: right; }

        /* Items list */
        .items-section { padding: 14px 0; border-bottom: 1px solid #f3f3f3; }
        .items-title {
            font-size: 10px;
            font-weight: 700;
            color: #bbb;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            font-size: 12.5px;
            margin-bottom: 8px;
            gap: 8px;
        }
        .item-row:last-child { margin-bottom: 0; }
        .item-left { display: flex; align-items: flex-start; gap: 8px; flex: 1; min-width: 0; }
        .item-qty {
            background: #fef3c7;
            color: #92400e;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 6px;
            flex-shrink: 0;
            margin-top: 1px;
        }
        .item-name { color: #222; font-weight: 600; line-height: 1.4; }
        .item-note { font-size: 10px; color: #999; font-weight: 400; margin-top: 1px; }
        .item-price { color: #efb100; font-weight: 700; font-size: 12px; flex-shrink: 0; }

        /* Total section */
        .total-section { padding: 14px 0; }
        .total-sub-row {
            display: flex;
            justify-content: space-between;
            font-size: 12.5px;
            margin-bottom: 7px;
        }
        .total-sub-row .lbl { color: #888; }
        .total-sub-row .val { color: #555; font-weight: 600; }
        .dashed { border-top: 1.5px dashed #e5e5e5; margin: 10px 0; }
        .total-main-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 4px;
        }
        .total-main-row .lbl { font-size: 13px; font-weight: 700; color: #1a1a1a; }
        .total-main-row .val { font-size: 18px; font-weight: 800; color: #efb100; }

        /* Payment method */
        .pay-method-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8f8f8;
            border-radius: 10px;
            padding: 10px 14px;
            margin-top: 10px;
        }
        .pay-method-row .lbl { font-size: 11px; color: #888; font-weight: 500; }
        .pay-method-row .val {
            font-size: 12px;
            font-weight: 700;
            color: #1a1a1a;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }
        .pay-status-dot {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            color: #16a34a;
            font-weight: 700;
        }
        .pay-status-dot::before {
            content: '';
            width: 6px;
            height: 6px;
            background: #16a34a;
            border-radius: 50%;
            display: inline-block;
        }

        /* Kembalian box — hanya muncul kalau cash */
        .kembalian-box {
            background: #fff7ed;
            border: 1.5px solid #fed7aa;
            border-radius: 12px;
            padding: 12px 16px;
            margin-top: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .kembalian-box .kb-left .kb-label { font-size: 10px; color: #c2410c; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .kembalian-box .kb-left .kb-sub   { font-size: 10px; color: #ea580c; margin-top: 1px; }
        .kembalian-box .kb-right { font-size: 18px; font-weight: 800; color: #ea580c; }

        /* Barcode-style decorative separator */
        .barcode-sep {
            display: flex;
            align-items: center;
            gap: 3px;
            padding: 16px 22px;
        }
        .barcode-sep .b { background: #e0e0e0; border-radius: 2px; height: 28px; flex: 1; }
        .barcode-sep .b:nth-child(3n)   { flex: 2; }
        .barcode-sep .b:nth-child(5n+1) { flex: 1.5; }

        /* Footer */
        .receipt-footer {
            padding: 0 22px 32px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .thankyou-note {
            text-align: center;
            font-size: 11px;
            color: #bbb;
            padding: 0 10px 16px;
            line-height: 1.6;
        }
        .thankyou-note strong { color: #999; }

        .btn-primary {
            width: 100%;
            background: #efb100;
            color: #fff;
            border: none;
            padding: 16px;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            transition: background .2s, transform .1s;
            letter-spacing: 0.2px;
        }
        .btn-primary:hover  { background: #d9a000; }
        .btn-primary:active { transform: scale(.98); }

        .btn-secondary {
            width: 100%;
            background: #f3f3f3;
            color: #444;
            border: none;
            padding: 14px;
            border-radius: 14px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            transition: background .2s;
        }
        .btn-secondary:hover { background: #e8e8e8; }
    </style>
</head>
<body>

@php
    $isCash      = $payment && strtolower($payment->payment_method) === 'cash';
    $paidAmount  = $payment ? $payment->paid_amount : 0;
    $kembalian   = $isCash ? max(0, $paidAmount - $order->total) : 0;
    $methodLabel = $payment ? strtoupper($payment->payment_method) : 'QRIS';
@endphp

<div class="receipt-wrap">

    {{-- HEADER --}}
    <div class="receipt-header">
        <div class="logo-circle">
            <img src="{{ asset('image/logo.png') }}"
                 alt="Logo"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
            <span class="logo-fallback" style="display:none;">T</span>
        </div>
        <div class="header-text">
            <div class="store-name">Warkop <span>Tskuy</span></div>
            <div class="store-sub">Struk Kasir</div>
        </div>
    </div>

    {{-- SUCCESS BANNER --}}
    <div class="success-banner">
        <div class="check-ring">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="none"
                 stroke="#fff" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
        </div>
        <div class="success-title">Pembayaran Berhasil!</div>
        <div class="success-sub">
            @if($isCash)
                Tunai diterima — pesanan langsung masuk dapur 🍳
            @else
                QRIS terkonfirmasi — pesanan masuk dapur 🍳
            @endif
        </div>
    </div>

    <div class="receipt-body">

        {{-- ORDER META --}}
        <div class="order-meta">
            <div>
                <div class="order-code">
                    <span>#</span>{{ $order->order_code }}
                </div>
                <div style="font-size:10px; color:#bbb; margin-top:2px; font-weight:500;">
                    Kasir: {{ auth()->user()->name ?? 'Kasir' }}
                </div>
            </div>
            <div class="order-meta-right">
                <span class="type-badge">Take Away</span>
                <span class="order-time">
                    {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y · H:i') }} WIB
                </span>
            </div>
        </div>

        {{-- INFO PELANGGAN --}}
        <div class="info-block">
            <div class="info-row">
                <span class="info-label">Nama Pelanggan</span>
                <span class="info-value">{{ $order->customer_name ?? '—' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">No. Transaksi</span>
                <span class="info-value" style="font-size:11px; color:#888;">{{ $order->order_code }}</span>
            </div>
        </div>

        {{-- ITEM LIST --}}
        <div class="items-section">
            <div class="items-title">Pesanan</div>
            @foreach($items as $item)
            <div class="item-row">
                <div class="item-left">
                    <span class="item-qty">{{ $item->quantity }}×</span>
                    <div>
                        <div class="item-name">{{ $item->menu_name }}</div>
                        @if($item->note)
                            <div class="item-note">📝 {{ $item->note }}</div>
                        @endif
                    </div>
                </div>
                <span class="item-price">
                    Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                </span>
            </div>
            @endforeach
        </div>

        {{-- TOTAL --}}
        <div class="total-section">
            <div class="total-sub-row">
                <span class="lbl">Subtotal</span>
                <span class="val">Rp{{ number_format($order->subtotal, 0, ',', '.') }},-</span>
            </div>
            <div class="total-sub-row">
                <span class="lbl">Pajak (10%)</span>
                <span class="val">Rp{{ number_format($order->tax, 0, ',', '.') }},-</span>
            </div>
            <div class="dashed"></div>
            <div class="total-main-row">
                <span class="lbl">Total Tagihan</span>
                <span class="val">Rp{{ number_format($order->total, 0, ',', '.') }},-</span>
            </div>

            {{-- METODE BAYAR --}}
            <div class="pay-method-row">
                <div>
                    <div class="lbl">Metode Bayar</div>
                    <div class="val" style="margin-top:2px;">{{ $methodLabel }}</div>
                </div>
                <div class="pay-status-dot">LUNAS</div>
            </div>

            {{-- KEMBALIAN — hanya kalau cash --}}
            @if($isCash)
            <div class="kembalian-box">
                <div class="kb-left">
                    <div class="kb-label">Kembalian</div>
                    <div class="kb-sub">Tunai: Rp{{ number_format($paidAmount, 0, ',', '.') }},-</div>
                </div>
                <div class="kb-right">Rp{{ number_format($kembalian, 0, ',', '.') }},-</div>
            </div>
            @endif
        </div>

    </div>

    {{-- BARCODE DECORATIVE --}}
    <div class="barcode-sep">
        @for($i = 0; $i < 30; $i++)
            <div class="b"></div>
        @endfor
    </div>

    {{-- THANK YOU + FOOTER --}}
    <div class="thankyou-note">
        Terima kasih telah berbelanja di <strong>Warkop Tskuy!</strong><br>
        Satu persen lebih baik setiap hari 🙌
    </div>

    <div class="receipt-footer">
        <button class="btn-primary"
                onclick="window.location.href='{{ route('kasir.pos') }}'">
            ← Kembali ke POS
        </button>
        <button class="btn-secondary"
                onclick="window.location.href='{{ route('kasir.penjualan') }}'">
            Lihat Riwayat Penjualan
        </button>
    </div>

</div>

</body>
</html>