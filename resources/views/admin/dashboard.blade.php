@extends('layouts.admin')

@section('title', 'Dashboard — Tskuy Admin')

@section('header_title', "Dashboard Hari Ini")
@section('header_subtitle', "Selamat datang kembali! Inilah yang terjadi hari ini.")

@section('content')
  <div class="page-title-section">
    <h1 class="page-title">Dashboard Hari Ini</h1>
    <p class="page-subtitle">Selamat datang kembali! Inilah yang terjadi hari ini.</p>
  </div>

  <main class="scroll-area">
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-info">
            <p class="stat-label">Total Penjualan</p>
            <p class="stat-value">Rp900.000,-</p>
            <p class="stat-change up">↑ 10.5% <span>dari kemarin</span></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
            <p class="stat-label">Transaksi</p>
            <p class="stat-value">102</p>
            <p class="stat-change up">↑ 5% <span>dari kemarin</span></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
            <p class="stat-label">Kustomer</p>
            <p class="stat-value">70</p>
            <p class="stat-change up">↑ 11.3% <span>dari kemarin</span></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
            <p class="stat-label">Avg. per Bulan</p>
            <p class="stat-value">Rp3.240.800,-</p>
            <p class="stat-change up">
                ↑ 11.3% <span>dari bulan lalu</span>
            </p>
            </div>
        </div>
    </div>

    <div class="chart-card">
        <div class="chart-header">
            <p class="chart-title">Ringkasan Penjualan</p>
            <div class="chart-tabs">
            <button class="tab-btn">Hari Ini</button>
            <button class="tab-btn active">Minggu</button>
            <button class="tab-btn">Tahun</button>
            </div>
        </div>
        <div class="chart-wrap">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <div class="bottom-row">
        <div class="mood-card">
            <p class="section-title">Analisis Mood Customer</p>
            <div class="mood-body">
            <div class="donut-wrap">
                <canvas id="moodChart"></canvas>
            </div>
            <div class="mood-legend">
                <div class="legend-item">
                <span class="legend-dot" style="background: #22c55e"></span
                ><span class="legend-label">Happy</span
                ><span class="legend-pct">20%</span>
                </div>
                <div class="legend-item">
                <span class="legend-dot" style="background: #f59e0b"></span
                ><span class="legend-label">Nongkrong</span
                ><span class="legend-pct">30%</span>
                </div>
                <div class="legend-item">
                <span class="legend-dot" style="background: #3b82f6"></span
                ><span class="legend-label">Penasaran</span
                ><span class="legend-pct">15%</span>
                </div>
                <div class="legend-item">
                <span class="legend-dot" style="background: #8b5cf6"></span
                ><span class="legend-label">Terserah</span
                ><span class="legend-pct">10%</span>
                </div>
                <div class="legend-item">
                <span class="legend-dot" style="background: #ef4444"></span
                ><span class="legend-label">Nugas</span
                ><span class="legend-pct">25%</span>
                </div>
            </div>
            </div>
        </div>

        <!-- Recent Transactions — hanya mobile -->
        <div class="recent-trx-card">
            <div class="card-header-row">
            <p class="section-title" style="margin-bottom: 0">
                Transaksi Terbaru
            </p>
            <!--
            PERUBAHAN dari SPA: bukan switchPage(), tapi navigasi ke penjualan.html
            dashboard.js handle klik ini via window.location.href
        -->
            <button class="see-all-link " id="btn-lihat-semua">
                Lihat Semua
            </button>
            </div>
            <div class="trx-item">
            <div class="trx-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <rect
                    x="2"
                    y="6"
                    width="20"
                    height="13"
                    rx="2"
                    stroke="#92400e"
                    stroke-width="1.8"
                />
                <path d="M2 10h20" stroke="#92400e" stroke-width="1.8" />
                </svg>
            </div>
            <div class="trx-info">
                <p class="trx-method">QRIS</p>
                <p class="trx-meta">Dzaky Julian | 14.30</p>
            </div>
            <div class="trx-right">
                <p class="trx-amount">Rp.20.000</p>
                <span class="trx-status success">Selesai</span>
            </div>
            </div>
            <div class="trx-item">
            <div class="trx-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <rect
                    x="2"
                    y="6"
                    width="20"
                    height="13"
                    rx="2"
                    stroke="#92400e"
                    stroke-width="1.8"
                />
                <path d="M2 10h20" stroke="#92400e" stroke-width="1.8" />
                </svg>
            </div>
            <div class="trx-info">
                <p class="trx-method">QRIS</p>
                <p class="trx-meta">Budi Santoso | 14.10</p>
            </div>
            <div class="trx-right">
                <p class="trx-amount">Rp.35.000</p>
                <span class="trx-status success">Selesai</span>
            </div>
            </div>
            <div class="trx-item">
            <div class="trx-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <rect
                    x="2"
                    y="6"
                    width="20"
                    height="13"
                    rx="2"
                    stroke="#92400e"
                    stroke-width="1.8"
                />
                <path d="M2 10h20" stroke="#92400e" stroke-width="1.8" />
                </svg>
            </div>
            <div class="trx-info">
                <p class="trx-method">Tunai</p>
                <p class="trx-meta">Rini Wulandari | 13.45</p>
            </div>
            <div class="trx-right">
                <p class="trx-amount">Rp.20.000</p>
                <span class="trx-status success">Selesai</span>
            </div>
            </div>
        </div>

        <div class="top-product-card">
            <p class="section-title">Top Produk</p>
            <div class="product-list">
            <div class="product-item">
                <div class="product-img"></div>
                <div class="product-info">
                <p class="product-name">Indomie Bangladesh</p>
                <p class="product-sold">150 terjual</p>
                </div>
                <span class="rank-badge gold">1st</span>
            </div>
            <div class="product-item">
                <div class="product-img"></div>
                <div class="product-info">
                <p class="product-name">Es Kopi ABC</p>
                <p class="product-sold">120 terjual</p>
                </div>
                <span class="rank-badge silver">2nd</span>
            </div>
            <div class="product-item">
                <div class="product-img"></div>
                <div class="product-info">
                <p class="product-name">Nasi Goreng</p>
                <p class="product-sold">98 terjual</p>
                </div>
                <span class="rank-badge bronze">3rd</span>
            </div>
            <div class="product-item">
                <div class="product-img"></div>
                <div class="product-info">
                <p class="product-name">Seblak Bandung</p>
                <p class="product-sold">75 terjual</p>
                </div>
                <span class="rank-badge plain">4th</span>
            </div>
            </div>
        </div>

        <div class="stock-alert-card">
            <div class="stock-alert-header">
            <p class="section-title">Peringatan Stok</p>
            <button class="manage-link">Kelola Stok</button>
            </div>
            <div class="alert-list">
            <div class="alert-item">
                <div class="alert-icon">!</div>
                <div class="alert-info">
                <p class="alert-name">Indomie Bangladesh</p>
                <p class="alert-stock critical">Kritis: 2 Unit Tersisa</p>
                </div>
                <button class="restock-btn">Restok</button>
            </div>
            <div class="alert-item">
                <div class="alert-icon">!</div>
                <div class="alert-info">
                <p class="alert-name">Kopi Susu ABC</p>
                <p class="alert-stock critical">Kritis: 3 Unit Tersisa</p>
                </div>
                <button class="restock-btn">Restok</button>
            </div>
            <div class="alert-item">
                <div
                class="alert-icon"
                style="background: #fef3e8; color: #f59e0b"
                >
                !
                </div>
                <div class="alert-info">
                <p class="alert-name">Cireng Isi</p>
                <p class="alert-stock warning">Hampir Habis: 7 Unit</p>
                </div>
                <button class="restock-btn" style="background: #f59e0b">
                Restok
                </button>
            </div>
            </div>
        </div>
    </div>
  </main>
@endsection

@section('scripts')
  <script src="{{ asset('js/dashboard-admin.js') }}"></script>
@endsection