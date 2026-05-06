// ============================================================
// POPUP SYSTEM
// Menggantikan radio button hack sebelumnya.
// Cara kerja:
//   - [data-open="id"] → openPopup(id)
//   - [data-close]     → closePopup()
//   - Klik overlay     → closePopup()
//   - Tekan Escape     → closePopup()
// ============================================================

const overlay = document.getElementById("popup-overlay");
let activePopup = null; // Menyimpan referensi popup yang sedang terbuka

/**
 * Buka popup berdasarkan ID elemen.
 * Jika ada popup lain yang sedang terbuka, tutup dulu baru buka yang baru.
 * Notif panel punya treatment berbeda (bukan centered popup).
 */
function openPopup(id) {
  // Tutup popup sebelumnya jika ada
  if (activePopup) {
    activePopup.classList.remove("is-open");
  }

  const el = document.getElementById(id);
  if (!el) return;

  // Panel notif: tidak pakai overlay gelap
  if (id === "popup-notif-panel") {
    el.classList.toggle("is-open");
    // Tutup jika klik di luar panel notif
    setTimeout(() => {
      document.addEventListener("click", closeNotifOnOutsideClick);
    }, 0);
    return;
  }

  el.classList.add("is-open");
  overlay.classList.add("is-open");
  activePopup = el;

  // Tutup notif panel jika masih terbuka
  const notifPanel = document.getElementById("popup-notif-panel");
  notifPanel.classList.remove("is-open");
}

/**
 * Tutup semua popup yang sedang aktif.
 */
function closePopup() {
  if (activePopup) {
    activePopup.classList.remove("is-open");
    activePopup = null;
  }
  overlay.classList.remove("is-open");

  // Tutup notif panel juga
  const notifPanel = document.getElementById("popup-notif-panel");
  notifPanel.classList.remove("is-open");
}

/**
 * Tutup notif panel jika user klik di luar areanya.
 */
function closeNotifOnOutsideClick(e) {
  const notifPanel = document.getElementById("popup-notif-panel");
  const notifBtn = document.querySelector('[data-open="popup-notif-panel"]');
  if (!notifPanel.contains(e.target) && !notifBtn.contains(e.target)) {
    notifPanel.classList.remove("is-open");
    document.removeEventListener("click", closeNotifOnOutsideClick);
  }
}

// Event delegation untuk semua [data-open] dan [data-close]
document.addEventListener("click", function (e) {
  // Cari elemen dengan data-open terdekat dari target klik
  const opener = e.target.closest("[data-open]");
  if (opener) {
    e.stopPropagation();
    openPopup(opener.dataset.open);
    return;
  }

  // Cari elemen dengan data-close terdekat dari target klik
  const closer = e.target.closest("[data-close]");
  if (closer) {
    closePopup();
    return;
  }
});

// Klik overlay → tutup popup
overlay.addEventListener("click", closePopup);

// Tekan Escape → tutup popup
document.addEventListener("keydown", function (e) {
  if (e.key === "Escape") closePopup();
});

// ============================================================
// FILTER MENU (Mood + Kategori)
// Menggantikan CSS sibling selector radio hack sebelumnya.
// ============================================================

let activeMood = "semua"; // State mood aktif saat ini
let activeKategori = "makanan"; // State kategori aktif saat ini

/**
 * Terapkan filter ke semua .menu-card berdasarkan mood & kategori aktif.
 * Card yang tidak cocok diberi class .menu-card--hidden (display:none di CSS).
 */
function applyMenuFilter() {
  document.querySelectorAll(".menu-card").forEach((card) => {
    const kategori = card.dataset.kategori;
    const moods = card.dataset.mood ? card.dataset.mood.split(" ") : [];

    const cocokKategori = kategori === activeKategori;
    const cocokMood = activeMood === "semua" || moods.includes(activeMood);

    // Tampilkan/sembunyikan card
    card.classList.toggle("menu-card--hidden", !(cocokKategori && cocokMood));
  });
}

// Event listener untuk tombol mood
document.querySelectorAll(".mood[data-mood]").forEach((btn) => {
  btn.addEventListener("click", function () {
    activeMood = this.dataset.mood;

    // Update class active pada tombol mood
    document
      .querySelectorAll(".mood[data-mood]")
      .forEach((b) => b.classList.remove("active-mood"));
    this.classList.add("active-mood");

    applyMenuFilter();
  });
});

// Event listener untuk tombol kategori menu
document.querySelectorAll(".menu-tab[data-kategori]").forEach((btn) => {
  btn.addEventListener("click", function () {
    activeKategori = this.dataset.kategori;

    // Update class active pada tombol tab
    document
      .querySelectorAll(".menu-tab[data-kategori]")
      .forEach((b) => b.classList.remove("active-tab"));
    this.classList.add("active-tab");

    applyMenuFilter();
  });
});

// Jalankan filter awal saat halaman load
applyMenuFilter();

// ============================================================
// FILTER ANTRIAN (di dalam popup antrian full)
// Menggantikan radio button + CSS sibling selector sebelumnya.
// ============================================================

document.querySelectorAll(".antrian-tab[data-filter]").forEach((btn) => {
  btn.addEventListener("click", function () {
    const filter = this.dataset.filter;

    // Update class active pada tab
    document
      .querySelectorAll(".antrian-tab[data-filter]")
      .forEach((b) => b.classList.remove("antrian-tab-active"));
    this.classList.add("antrian-tab-active");

    // Tampilkan/sembunyikan card antrian
    document.querySelectorAll(".antrian-card[data-type]").forEach((card) => {
      if (filter === "all") {
        card.classList.remove("antrian-card--hidden");
      } else if (filter === "served") {
        // Belum ada card served, semua disembunyikan
        card.classList.add("antrian-card--hidden");
      } else {
        card.classList.toggle(
          "antrian-card--hidden",
          card.dataset.type !== filter,
        );
      }
    });
  });
});

// ============================================================
// QUEUE SCROLL (Prev / Next)
// Logika scroll antrian mini di dashboard — tidak berubah,
// hanya dipindahkan ke JS karena radio button sudah tidak ada.
// ============================================================

const queueContainer = document.getElementById("queue-container");
const dots = document.querySelectorAll("#pagination-dots .dot");

document.getElementById("queue-prev").addEventListener("click", () => {
  queueContainer.scrollBy({ left: -260, behavior: "smooth" });
});

document.getElementById("queue-next").addEventListener("click", () => {
  queueContainer.scrollBy({ left: 260, behavior: "smooth" });
});

// Update dot aktif saat scroll
queueContainer.addEventListener("scroll", () => {
  const idx = Math.round(
    queueContainer.scrollLeft / queueContainer.offsetWidth,
  );
  dots.forEach((d, i) => d.classList.toggle("active", i === idx));
});
