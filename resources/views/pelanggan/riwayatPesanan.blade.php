@extends('layouts.pelanggan')

@section('title', 'Riwayat Pesanan - Warkop Tskuy')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/style-dashboardKasir.css') }}" />
<link rel="stylesheet" href="{{ asset('css/kasir-pages.css') }}" />
@endsection

@section('content')
<main class="content-area" style="padding: 20px; overflow-y: auto; height: calc(100vh - 72px);">
    
    <div class="open-bill-hub" style="background: #fff; border-radius: 16px; border: 2px solid #efb100; padding: 20px; margin-bottom: 24px; box-shadow: 0 4px 15px rgba(239,177,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 12px; margin-bottom: 15px;">
            <div>
                <span style="background: #fff7d6; color: #d97706; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; border: 1px solid #fde68a;">📝 OPEN BILL ACTIVE</span>
                <h3 style="font-size: 16px; font-weight: 800; margin-top: 6px; color: #222;">Nota Meja Sesi Ini: #TSK-04</h3>
            </div>
            <p style="font-size: 13px; color: #888; font-weight: 500;">Meja Nomor: <strong>{{ Cookie::get('tskuy_table_number') ?? '4' }}</strong></p>
        </div>

        <div class="bill-items-list" style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; font-size: 13px; color: #444; background: #fafafa; padding: 10px 14px; border-radius: 8px;">
                <span>☕ Kopi Susu ABC <strong>x2</strong></span>
                <span style="font-weight: 700; color: #222;">Rp 24.000</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 13px; color: #444; background: #fafafa; padding: 10px 14px; border-radius: 8px;">
                <span>🍳 Indomie Bangladesh <strong>x1</strong></span>
                <span style="font-weight: 700; color: #222;">Rp 18.000</span>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; background: #fff8e7; padding: 12px 16px; border-radius: 12px; border: 1px dashed #efb100; margin-bottom: 20px;">
            <span style="font-size: 14px; font-weight: 700; color: #894b00;">Subtotal Berjalan:</span>
            <span style="font-size: 18px; font-weight: 800; color: #d97706;">Rp 42.000</span>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
            <a href="{{ route('pelanggan.orders') }}?mode=susulan" style="text-decoration: none; text-align: center; background: #222; color: #fff; padding: 14px; border-radius: 12px; font-weight: 700; font-size: 13px; display: flex; align-items: center; justify-content: center; gap: 6px;">
                ➕ Tambah Pesanan Susulan
            </a>
            <button data-open="popup-final-qris" style="background: #efb100; color: #fff; padding: 14px; border-radius: 12px; font-weight: 700; font-size: 13px; display: flex; align-items: center; justify-content: center; gap: 6px;">
                💳 Selesaikan & Bayar QRIS
            </button>
        </div>
    </div>

    <div class="past-orders-section">
        <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 12px; color: #555;">Transaksi Selesai Sebelumnya</h4>
        <p style="font-size: 12px; color: #aaa; text-align: center; padding: 20px;">Belum ada riwayat transaksi masa lalu.</p>
    </div>
</main>