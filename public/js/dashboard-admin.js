/* ============================================================
   dashboard.js — Logika khusus halaman Dashboard
   Dipanggil SETELAH admin.js
   Berisi:
   1. Sales line chart
   2. Mood donut chart
   3. Chart tab (Hari Ini / Minggu / Tahun)
   4. Tombol "Lihat Semua" di recent transactions
      → navigasi ke penjualan.html

   PERUBAHAN dari versi SPA:
   - Tidak ada switchPage(), navigasi pakai href
   - Chart init tetap sama
============================================================ */

document.addEventListener("DOMContentLoaded", function () {
  /* ============================================================
     SALES LINE CHART
  ============================================================ */
  const salesCtx = document.getElementById("salesChart");
  if (salesCtx) {
    new Chart(salesCtx.getContext("2d"), {
      type: "line",
      data: {
        labels: ["Sen", "Sel", "Rab", "Kam", "Jum", "Sab", "Min"],
        datasets: [
          {
            data: [120, 200, 150, 280, 220, 400, 580],
            borderColor: "#1a1a1a",
            backgroundColor: "transparent",
            borderWidth: 2.5,
            pointBackgroundColor: "#efb100",
            pointRadius: 4,
            pointHoverRadius: 6,
            tension: 0.4,
            fill: false,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: {
            grid: { display: false },
            ticks: { font: { size: 11, family: "Poppins" }, color: "#94a3b8" },
          },
          y: {
            grid: { color: "#f1f5f9" },
            ticks: { font: { size: 11, family: "Poppins" }, color: "#94a3b8" },
            beginAtZero: true,
          },
        },
      },
    });
  }

  /* ============================================================
     MOOD DONUT CHART
  ============================================================ */
  const moodCtx = document.getElementById("moodChart");
  if (moodCtx) {
    new Chart(moodCtx.getContext("2d"), {
      type: "doughnut",
      data: {
        labels: ["Happy", "Nongkrong", "Penasaran", "Terserah", "Nugas"],
        datasets: [
          {
            data: [20, 30, 15, 10, 25],
            backgroundColor: [
              "#22c55e",
              "#f59e0b",
              "#3b82f6",
              "#8b5cf6",
              "#ef4444",
            ],
            borderWidth: 3,
            borderColor: "#fff",
            hoverOffset: 6,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: "65%",
        plugins: { legend: { display: false } },
      },
    });
  }

  /* ============================================================
     CHART TAB — Hari Ini / Minggu / Tahun
     Hanya update active state, data chart bisa dikembangkan nanti
  ============================================================ */
  document.querySelectorAll(".tab-btn").forEach((btn) => {
    btn.addEventListener("click", function () {
      document
        .querySelectorAll(".tab-btn")
        .forEach((b) => b.classList.remove("active"));
      this.classList.add("active");
      // TODO: update data chart sesuai tab yang dipilih
    });
  });

  /* ============================================================
     TOMBOL "LIHAT SEMUA" — navigasi ke halaman penjualan
     PERUBAHAN dari SPA: dulu switchPage(), sekarang href langsung
  ============================================================ */
  const seeAllBtn = document.getElementById("btn-lihat-semua");
  if (seeAllBtn) {
    seeAllBtn.addEventListener("click", function () {
      window.location.href = "/admin/penjualan.html";
    });
  }
});
