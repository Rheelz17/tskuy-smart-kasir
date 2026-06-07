<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran QRIS - Tskuy</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background-color: #f4f4f4; display: flex; justify-content: center; align-items: flex-start; min-height: 100vh; }
        .qris-container { background-color: #fff; width: 100%; max-width: 480px; min-height: 100vh; position: relative; display: flex; flex-direction: column; box-shadow: 0 0 20px rgba(0,0,0,0.05); }
        .topbar { display: flex; align-items: center; padding: 20px; background: #fff; position: sticky; top: 0; z-index: 10; border-bottom: 1px solid #f0f0f0; }
        .back-btn { background: none; border: none; cursor: pointer; color: #efb100; display: flex; align-items: center; padding-right: 15px; }
        .page-title { font-size: 16px; font-weight: 700; color: #333; margin-left: 10px; }
        .content { padding: 25px 20px; flex: 1; text-align: center; }
        .sub-title { font-size: 13px; color: #666; font-weight: 600; margin-bottom: 15px; }

        /* Open Bill Notice */
        .openbill-notice {
            background: #fffbeb;
            border: 1.5px solid #efb100;
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 20px;
            text-align: left;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        .openbill-notice-icon { font-size: 20px; flex-shrink: 0; }
        .openbill-notice-text p { font-size: 12px; color: #92400e; line-height: 1.5; margin: 0; }
        .openbill-notice-text strong { font-size: 13px; color: #78350f; }

        .timer { font-size: 38px; font-weight: 800; color: #efb100; margin-bottom: 10px; }
        .instruction { font-size: 12px; color: #888; margin-bottom: 25px; line-height: 1.5; padding: 0 10px; }
        .qris-logo { width: 100px; margin-bottom: 15px; }
        .qr-box { background: #fff; border: 2px dashed #ddd; padding: 15px; border-radius: 16px; display: inline-block; margin-bottom: 30px; }
        .qr-box img { width: 200px; height: 200px; object-fit: contain; }
        .summary-card { background: #fff; border: 1px solid #eee; border-radius: 12px; padding: 20px; text-align: left; margin-bottom: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 13px; color: #666; }
        .summary-divider { border-top: 1px dashed #ddd; margin: 15px 0; }
        .summary-total { display: flex; justify-content: space-between; font-size: 15px; font-weight: 800; color: #333; }
        .total-amount { color: #efb100; }
        .footer { padding: 20px; background: #fff; border-top: 1px solid #f0f0f0; position: sticky; bottom: 0; }
        .btn-confirm { width: 100%; background-color: #efb100; color: #fff; border: none; padding: 16px; border-radius: 12px; font-size: 15px; font-weight: 700; cursor: pointer; transition: 0.2s; }
        .btn-confirm:hover { background-color: #d9a000; }
    </style>
</head>
<body>
    <div class="qris-container">
        <div class="topbar">
            <a href="{{ route('pelanggan.riwayat') }}" class="back-btn">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#efb100" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
            </a>
            <h1 class="page-title">
                @if($order->is_open_bill)
                    Bayar Tagihan Open Bill
                @else
                    Pembayaran QRIS
                @endif
            </h1>
        </div>

        <div class="content">

            {{-- Notice khusus Open Bill --}}
            @if($order->is_open_bill)
            <div class="openbill-notice">
                <div class="openbill-notice-icon">📋</div>
                <div class="openbill-notice-text">
                    <strong>Pelunasan Open Bill</strong>
                    <p>Kamu sedang menyelesaikan tagihan untuk order <strong>{{ $order->order_code }}</strong>. Scan QR di bawah untuk membayar lunas semua pesananmu.</p>
                </div>
            </div>
            @endif

            <h2 class="sub-title">QR Code & Instruksi</h2>

            @if(!$order->is_open_bill)
            {{-- Timer hanya untuk pay_now --}}
            <div class="timer" id="countdown">04:59</div>
            <p class="instruction">Scan kode QR di bawah & lakukan pembayaran sebelum waktu habis</p>
            @else
            <p class="instruction">Scan kode QR di bawah & konfirmasi pembayaran untuk menyelesaikan sesi open bill kamu</p>
            @endif

            <img src="https://upload.wikimedia.org/wikipedia/commons/a/a2/Logo_QRIS.svg" alt="QRIS Logo" class="qris-logo">

            <div class="qr-box">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ $order->order_code }}" alt="QR Code">
            </div>

            <div class="summary-card">
                <div class="summary-row">
                    <span style="font-weight:600; color:#1a1a1a;">{{ $order->order_code }}</span>
                    <span style="font-size:11px; color:#64748b;">{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</span>
                </div>
                <div class="summary-divider"></div>
                <div class="summary-row"><span>Subtotal</span><span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
                <div class="summary-row"><span>Tax (10%)</span><span>Rp {{ number_format($order->tax, 0, ',', '.') }}</span></div>
                <div class="summary-divider"></div>
                <div class="summary-total">
                    <span>Total Tagihan</span>
                    <span class="total-amount">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="footer">
            <form action="{{ route('pelanggan.qris.pay', $order->order_code) }}" method="POST">
                @csrf
                <button type="submit" class="btn-confirm">
                    @if($order->is_open_bill)
                        ✅ Konfirmasi Pembayaran Lunas
                    @else
                        Cek Status Pembayaran
                    @endif
                </button>
            </form>
        </div>
    </div>

    <script>
        @if(!$order->is_open_bill)
        // Timer hanya untuk pay_now
        let time = 4 * 60 + 59;
        const timerEl = document.getElementById('countdown');
        if (timerEl) {
            const interval = setInterval(() => {
                let m = Math.floor(time / 60);
                let s = time % 60;
                timerEl.innerHTML = (m < 10 ? '0' + m : m) + ':' + (s < 10 ? '0' + s : s);
                if (time <= 0) {
                    clearInterval(interval);
                    timerEl.innerHTML = '00:00';
                    timerEl.style.color = '#ef4444';
                }
                time--;
            }, 1000);
        }
        @endif
    </script>
</body>
</html>