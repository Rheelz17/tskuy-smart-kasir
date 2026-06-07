<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pembayaran - Tskuy</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background-color: #f4f4f4; display: flex; justify-content: center; align-items: flex-start; min-height: 100vh; }
        .receipt-container { background-color: #fff; width: 100%; max-width: 480px; min-height: 100vh; display: flex; flex-direction: column; box-shadow: 0 0 20px rgba(0,0,0,0.05); position: relative; }
        .header { text-align: center; padding: 30px 20px 10px; }
        .logo-box { display: flex; align-items: center; justify-content: center; gap: 10px; font-size: 24px; font-weight: 800; color: #8b5e34; }
        .logo-box img { width: 40px; height: 40px; }
        .status-section { text-align: center; padding: 20px; }
        .status-title { font-size: 16px; font-weight: 700; color: #222; margin-bottom: 6px; }
        .status-subtitle { font-size: 12px; color: #64748b; margin-bottom: 16px; }
        .check-icon { width: 64px; height: 64px; border: 4px solid #22c55e; border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 6px; }
        .completed-badge { display: inline-flex; align-items: center; gap: 6px; background: #dcfce7; color: #166534; font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: 20px; margin-top: 8px; }
        .details-card { padding: 0 25px; margin-top: 10px; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 13px; }
        .info-label { color: #666; }
        .info-value { color: #222; font-weight: 500; text-align: right; }
        .divider { border-top: 1px solid #eee; margin: 15px 0; }
        .divider-dashed { border-top: 1px dashed #ddd; margin: 15px 0; }
        .total-row { display: flex; justify-content: space-between; font-size: 14px; font-weight: 700; color: #222; margin-bottom: 15px; }
        .total-highlight { color: #efb100; font-size: 16px; }
        .thankyou-box { margin: 0 25px 20px; background: #f8fafc; border-radius: 12px; padding: 16px; text-align: center; }
        .thankyou-box p { font-size: 12px; color: #64748b; line-height: 1.7; }
        .footer { padding: 25px; margin-top: auto; display: flex; flex-direction: column; gap: 10px; }
        .btn-home { width: 100%; background-color: #efb100; color: #fff; border: none; padding: 16px; border-radius: 12px; font-size: 15px; font-weight: 700; cursor: pointer; transition: 0.2s; }
        .btn-home:hover { background-color: #d9a000; }
        .btn-riwayat { width: 100%; background-color: #f1f5f9; color: #334155; border: none; padding: 14px; border-radius: 12px; font-size: 14px; font-weight: 600; cursor: pointer; transition: 0.2s; }
        .btn-riwayat:hover { background-color: #e2e8f0; }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="header">
            <div class="logo-box">
                <img src="{{ asset('image/logo.png') }}" alt="Logo" onerror="this.style.display='none'">
                <span>Warkop<br><span style="color:#efb100">Tskuy</span></span>
            </div>
        </div>

        <div class="status-section">
            <div class="check-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="4" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <div class="status-title">Pembayaran Berhasil!</div>
            <div class="status-subtitle">Pesananmu telah lunas dan sedang diproses dapur 🍳</div>
            <div class="completed-badge">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                COMPLETED
            </div>
        </div>

        <div class="details-card">
            <div class="info-row">
                <span class="info-label">ID Transaksi</span>
                <span class="info-value">#{{ $order->order_code }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tipe Layanan</span>
                <span class="info-value" style="text-transform: capitalize;">{{ $order->eating_option }}</span>
            </div>
            @if($order->table_id)
            <div class="info-row">
                <span class="info-label">Nomor Meja</span>
                {{-- Ini akan ngambil nomor meja via relasi table kalau ada --}}
                <span class="info-value">#{{ $order->table_id }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Waktu Pemesanan</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }} WIB</span>
            </div>
            <div class="divider"></div>
            <div class="info-row">
                <span class="info-label">Subtotal</span>
                <span class="info-value">Rp{{ number_format($order->subtotal, 0, ',', '.') }},-</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tax (10%)</span>
                <span class="info-value">Rp{{ number_format($order->tax, 0, ',', '.') }},-</span>
            </div>
            <div class="divider-dashed"></div>
            <div class="total-row">
                <span>Total Tagihan</span>
                <span class="total-highlight">Rp{{ number_format($order->total, 0, ',', '.') }},-</span>
            </div>
            <div class="info-row">
                <span class="info-label">Metode Bayar</span>
                <span class="info-value">QRIS</span>
            </div>
            <div class="info-row" style="margin-bottom: 0;">
                <span class="info-label">Status Pembayaran</span>
                <span class="info-value" style="color: #22c55e; font-weight: 700;">LUNAS ✓</span>
            </div>
        </div>

        <div class="thankyou-box" style="margin-top: 20px;">
            <p>Terima kasih sudah nongkrong di <strong>Warkop Tskuy!</strong><br>Satu persen lebih baik setiap hari 🙌</p>
        </div>

        <div class="footer">
            <button onclick="window.location.href='{{ route('pelanggan.orders') }}'" class="btn-home">
                Pesan Lagi
            </button>
            <button onclick="window.location.href='{{ route('pelanggan.riwayat') }}'" class="btn-riwayat">
                Lihat Riwayat Transaksi
            </button>
        </div>
    </div>
</body>
</html>