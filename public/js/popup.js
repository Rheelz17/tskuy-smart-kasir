// ============================================================
// STATUS LOGIN (DARI BLADE LARAVEL)
// ============================================================
const metaLogin = document.querySelector('meta[name="is-logged-in"]');
// Jika meta tag ada, baca valuenya. Jika tidak ada (di halaman kasir), anggap true agar kasir bebas nambah pesanan.
const isLoggedIn = metaLogin ? metaLogin.content === 'true' : true; 

// ============================================================
// POPUP SYSTEM CORE
// ============================================================
const overlay = document.getElementById("popup-overlay");
let activePopup = null;

function openPopup(id) {
  if (activePopup) activePopup.classList.remove("is-open");

  const el = document.getElementById(id);
  if (!el) return;

  if (id === "popup-notif-panel") {
    el.classList.toggle("is-open");
    setTimeout(() => {
      document.addEventListener("click", closeNotifOnOutsideClick);
    }, 0);
    return;
  }

  el.classList.add("is-open");
  if(overlay) overlay.classList.add("is-open");
  activePopup = el;

  const notifPanel = document.getElementById("popup-notif-panel");
  if(notifPanel) notifPanel.classList.remove("is-open");
}

function closePopup() {
  if (activePopup) {
    activePopup.classList.remove("is-open");
    activePopup = null;
  }
  if(overlay) overlay.classList.remove("is-open");
  const notifPanel = document.getElementById("popup-notif-panel");
  if(notifPanel) notifPanel.classList.remove("is-open");
  
  // Tutup cart mobile kalau lagi kebuka
  document.querySelector(".cart-sidebar")?.classList.remove("cart-open");
}

window._openPopup = openPopup;
window._closePopup = closePopup;

function closeNotifOnOutsideClick(e) {
  const notifPanel = document.getElementById("popup-notif-panel");
  const notifBtn = e.target.closest('[data-open="popup-notif-panel"]');
  if (notifPanel && !notifPanel.contains(e.target) && !notifBtn) {
    notifPanel.classList.remove("is-open");
    document.removeEventListener("click", closeNotifOnOutsideClick);
  }
}

document.addEventListener("keydown", function (e) {
  if (e.key === "Escape") closePopup();
});

if(overlay) overlay.addEventListener("click", closePopup);

// ============================================================
// CART STATE & RENDER
// ============================================================
let cartItems = [];
let nextId = 1;

function formatRp(amount) {
  return "Rp" + amount.toLocaleString("id-ID");
}

function renderCart() {
  const orderDetails = document.querySelector(".order-details");
  if (!orderDetails) return;

  orderDetails.innerHTML = "";

  if (cartItems.length === 0) {
    orderDetails.innerHTML = `
        <div class="cart-empty" id="cart-empty-msg" style="display: flex; flex-direction: column; align-items: center; gap: 10px; padding: 24px 0; color: #aaa; font-size: 13px;">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none"><path d="M6 2L3 6V20C3 21.1 3.9 22 5 22H19C20.1 22 21 21.1 21 20V6L18 2H6Z" stroke="#ddd" stroke-width="1.8" stroke-linejoin="round"/><path d="M3 6H21" stroke="#ddd" stroke-width="1.8"/></svg>
            <p>Keranjang masih kosong</p>
        </div>`;
    updateSummary();
    return;
  }

  cartItems.forEach(item => {
    const card = document.createElement("div");
    card.className = "item-card-mini";
    card.dataset.id = item.id;
    card.innerHTML = `
      <img src="${item.img}" class="mini-img" alt="${item.name}">
      <div class="mini-info">
        <div class="mini-header">
          <p class="mini-name">${item.name}</p>
          <button class="delete-item" data-id="${item.id}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M3 6H5H21" stroke="#ef4444" stroke-width="2" stroke-linecap="round"/><path d="M8 6V4H16V6M19 6V20C19 21.1 18.1 22 17 22H7C5.9 22 5 21.1 5 20V6H19Z" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
        </div>
        <p class="mini-desc">${item.desc}</p>
        ${item.note ? `<div class="badge-note"><svg width="12" height="12" viewBox="0 0 24 24" fill="none"><rect x="4" y="2" width="16" height="20" rx="2" stroke="#854d0e" stroke-width="1.8"/><path d="M8 7H16M8 11H16M8 15H12" stroke="#854d0e" stroke-width="1.8" stroke-linecap="round"/></svg>${item.note}</div>` : ""}
        <div class="mini-price-row">
          <p class="mini-price">${formatRp(item.price * item.qty)}</p>
          <div class="mini-qty">
            <button class="qty-btn minus" data-id="${item.id}">-</button>
            <span class="qty-val">${item.qty}</span>
            <button class="qty-btn plus" data-id="${item.id}">+</button>
          </div>
        </div>
      </div>
    `;
    orderDetails.appendChild(card);
  });

  // Tombol Plus Minus & Hapus di dalam keranjang
  orderDetails.querySelectorAll(".qty-btn.minus").forEach(btn => {
    btn.addEventListener("click", function (e) {
      e.stopPropagation();
      const id = parseInt(this.dataset.id);
      const item = cartItems.find(i => i.id === id);
      if (item) {
        item.qty--;
        if (item.qty <= 0) cartItems = cartItems.filter(i => i.id !== id);
        renderCart();
      }
    });
  });

  orderDetails.querySelectorAll(".qty-btn.plus").forEach(btn => {
    btn.addEventListener("click", function (e) {
      e.stopPropagation();
      const id = parseInt(this.dataset.id);
      const item = cartItems.find(i => i.id === id);
      if (item) { item.qty++; renderCart(); }
    });
  });

  orderDetails.querySelectorAll(".delete-item").forEach(btn => {
    btn.addEventListener("click", function (e) {
      e.stopPropagation();
      const id = parseInt(this.dataset.id);
      cartItems = cartItems.filter(i => i.id !== id);
      renderCart();
    });
  });

  updateSummary();
}

function updateSummary() {
  const subtotal = cartItems.reduce((sum, item) => sum + item.price * item.qty, 0);
  const tax = Math.round(subtotal * 0.1);
  const total = subtotal + tax;

  document.querySelectorAll(".summary-subtotal").forEach(el => el.textContent = formatRp(subtotal));
  document.querySelectorAll(".summary-tax").forEach(el => el.textContent = formatRp(tax));
  document.querySelectorAll(".summary-total").forEach(el => el.textContent = formatRp(total));
  document.querySelectorAll(".popup-total-amount").forEach(el => el.textContent = formatRp(total));
  document.querySelectorAll(".total-value").forEach(el => el.textContent = formatRp(total));

  const totalQty = cartItems.reduce((sum, item) => sum + item.qty, 0);
  document.querySelectorAll(".cart-badge").forEach(badge => {
    badge.textContent = totalQty;
    badge.style.display = totalQty > 0 ? "flex" : "none";
  });

  const floatingBar = document.getElementById('floating-cart-bar');
  if (floatingBar) {
      if (totalQty > 0) {
          floatingBar.style.display = 'flex';
          document.getElementById('mobile-item-count').innerText = totalQty;
          document.getElementById('mobile-total-price').innerText = formatRp(total);
      } else {
          floatingBar.style.display = 'none';
      }
  }
}

// ============================================================
// 🎯 UNIFIED CLICK LISTENER (Pusat Pengendali Klik)
// ============================================================
let currentMenuForCart = null;

document.addEventListener("click", function (e) {
  
  // 1. CEK KLIK TOMBOL "TAMBAH/PESAN" DI KARTU MENU
  const qtyCard = e.target.closest(".qty-card");
  if (qtyCard) {
    e.stopPropagation(); // Biar event gak tembus ke kartu menu yang ada data-open nya
    
    if (!isLoggedIn) {
        openPopup("popup-login-warning");
        return;
    }

    const card = qtyCard.closest(".menu-card");
    if (!card) return;

    const name = card.querySelector(".card-judul")?.textContent || "";
    const desc = card.querySelector(".card-desc")?.textContent || "";
    const priceText = card.querySelector(".card-price")?.textContent || "Rp 0";
    const img = card.querySelector("img")?.src || "";
    const price = parseInt(priceText.replace(/\D/g, "")) || 0;

    const existing = cartItems.find(i => i.name === name);
    if (existing) {
      existing.qty++;
    } else {
      cartItems.push({ id: nextId++, name, desc, price, qty: 1, note: "", img });
    }
    renderCart();
    showAddedToast(name);
    return;
  }

  // 2. CEK KLIK ELEMEN PEMBUKA POPUP (Seperti klik sembarang di Kartu Menu)
  const opener = e.target.closest("[data-open]");
  if (opener) {
    e.stopPropagation();
    const targetId = opener.dataset.open;

    // ----- CEGATAN LOGIN: KHUSUS UNTUK DETAIL MENU -----
    if (targetId.startsWith("popup-detail-") && !isLoggedIn) {
        openPopup("popup-login-warning");
        return;
    }
    // ---------------------------------------------------

    // Jika buka detail menu, siapkan data ke dalam popup tersebut
    if (targetId.startsWith("popup-detail-")) {
        const card = opener.closest(".menu-card");
        if (card) {
            const name = card.querySelector(".card-judul")?.textContent || "";
            const desc = card.querySelector(".card-desc")?.textContent || "";
            const priceText = card.querySelector(".card-price")?.textContent || "Rp 0";
            const img = card.querySelector("img")?.src || "";
            const price = parseInt(priceText.replace(/\D/g, "")) || 0;

            currentMenuForCart = { name, desc, price, img };

            const popup = document.getElementById(targetId);
            if (popup) {
              const qtyValEl = popup.querySelector(".menu-detail-qty .qty-val");
              if (qtyValEl) qtyValEl.textContent = "1";
              popup.querySelectorAll("input[name='level-pedas']").forEach(r => r.checked = false);
              const catatanInput = popup.querySelector(".catatan-input");
              if (catatanInput) catatanInput.value = "";
            }
        }
    }

    openPopup(targetId);
    return;
  }

  // 3. CEK KLIK TUTUP POPUP
  const closer = e.target.closest("[data-close]");
  if (closer) {
    e.stopPropagation();
    closePopup();
    return;
  }

  // 4. CEK HAPUS KERANJANG PADA POPUP WARNING
  const btnClear = e.target.closest("#btn-clear-cart") || e.target.closest(".btn-confirm-warning");
  if (btnClear && (btnClear.id === 'btn-clear-cart' || btnClear.closest("#popup-hapus-keranjang"))) {
    cartItems = [];
    renderCart();
  }

  // 5. TOGGLE KERANJANG MOBILE (BAR BAWAH)
  const mobileCartBtn = e.target.closest("#mobile-cart-btn") || e.target.closest("#floating-cart-bar");
  if(mobileCartBtn) {
      e.stopPropagation();
      const cartSidebar = document.querySelector(".cart-sidebar");
      const isOpen = cartSidebar?.classList.toggle("cart-open");
      if(overlay) overlay.classList.toggle("is-open", isOpen);
  }
  
  // Tutup cart mobile
  const mobileCartClose = e.target.closest("#mobile-cart-close");
  if (mobileCartClose) {
    const cartSidebar = document.querySelector(".cart-sidebar");
    cartSidebar?.classList.remove("cart-open");
    if(overlay) overlay.classList.remove("is-open");
  }
});


// ============================================================
// LOGIC QTY & TAMBAH DARI DALAM POPUP DETAIL MENU
// ============================================================

document.querySelectorAll(".menu-detail-qty").forEach(qtyContainer => {
    qtyContainer.addEventListener("click", function (e) {
      const qtyVal = this.querySelector(".qty-val");
      if(!qtyVal) return;
      let qty = parseInt(qtyVal.textContent) || 1;
      if (e.target.closest(".plus")) qty = Math.min(qty + 1, 99);
      if (e.target.closest(".minus")) qty = Math.max(qty - 1, 1);
      qtyVal.textContent = qty;
    });
});

document.querySelectorAll(".menu-detail-footer .popup-btn").forEach(btn => {
    btn.addEventListener("click", function () {
        if (!currentMenuForCart) return;

        const popup = this.closest('.popup'); 
        const qty = parseInt(popup.querySelector(".menu-detail-qty .qty-val")?.textContent) || 1;
        const note = popup.querySelector(".catatan-input")?.value.trim() || "";
        const selectedLevel = popup.querySelector("input[name='level-pedas']:checked");
        const noteText = [selectedLevel ? selectedLevel.closest(".level-option").querySelector("span").textContent : "", note].filter(Boolean).join(", ");

        const existing = cartItems.find(i => i.name === currentMenuForCart.name);
        if (existing) {
            existing.qty += qty;
            if (noteText) existing.note = noteText; 
        } else {
            cartItems.push({ id: nextId++, name: currentMenuForCart.name, desc: currentMenuForCart.desc, price: currentMenuForCart.price, qty, note: noteText, img: currentMenuForCart.img });
        }

        renderCart();
        showAddedToast(currentMenuForCart.name);
        closePopup();
    });
});

// ============================================================
// TOAST NOTIFICATION
// ============================================================
function showAddedToast(name) {
  let toast = document.getElementById("add-toast");
  if (!toast) {
    toast = document.createElement("div");
    toast.id = "add-toast";
    toast.className = "add-toast";
    document.body.appendChild(toast);
  }
  toast.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" fill="#22c55e"/><path d="M8 12L11 15L16 9" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg> ${name} ditambahkan`;
  toast.classList.add("show");
  clearTimeout(toast._timeout);
  toast._timeout = setTimeout(() => toast.classList.remove("show"), 2500);
}

// ============================================================
// FILTER MENU (Mood + Kategori)
// ============================================================
let activeTag = "semua";
let activeKategori = document.querySelector('.menu-tab.active-tab')?.dataset.filter || "makanan"; 

function applyMenuFilter() {
  document.querySelectorAll(".menu-card").forEach((card) => {
    const kategori = card.dataset.category || card.dataset.kategori;
    const tags = card.dataset.tag ? card.dataset.tag.split(" ") : [];
    const cocokKategori = kategori === activeKategori;
    const cocokTag = activeTag === "semua" || tags.includes(activeTag);
    card.classList.toggle("menu-card--hidden", !(cocokKategori && cocokTag));
  });
}

document.querySelectorAll(".mood[data-tag]").forEach((btn) => {
  btn.addEventListener("click", function () {
    activeTag = this.dataset.tag;
    document.querySelectorAll(".mood[data-tag]").forEach((b) => b.classList.remove("active-mood"));
    this.classList.add("active-mood");
    applyMenuFilter();
  });
});

document.querySelectorAll(".menu-tab[data-filter], .menu-tab[data-kategori]").forEach((btn) => {
  btn.addEventListener("click", function () {
    activeKategori = this.dataset.filter || this.dataset.kategori;
    document.querySelectorAll(".menu-tab").forEach((b) => b.classList.remove("active-tab"));
    this.classList.add("active-tab");
    applyMenuFilter();
  });
});

applyMenuFilter();

// ============================================================
// FILTER ANTRIAN & PRESET TUNAI (Khusus Kasir)
// ============================================================

document.addEventListener("DOMContentLoaded", () => {
  const container = document.getElementById("queue-container");
  const prevBtn = document.getElementById("queue-prev");
  const nextBtn = document.getElementById("queue-next");
  const dotsContainer = document.getElementById("pagination-dots");

  // ==========================================
  // 1. FUNGSI HITUNG BADGE ANTRIAN
  // ==========================================
  function updateQueueBadges() {
    const cards = document.querySelectorAll(".queue-card[data-type]");
    const counts = { all: cards.length, pending: 0, dine: 0, takeaway: 0, openbill: 0 };

    cards.forEach(card => {
      const type = card.dataset.type;
      if (counts.hasOwnProperty(type)) counts[type]++;
    });

    document.querySelectorAll(".antrian-tab[data-filter]").forEach(tab => {
      const filter = tab.dataset.filter;
      const badge = tab.querySelector(".tab-badge");
      if (badge && counts.hasOwnProperty(filter)) {
        badge.textContent = counts[filter];
      }
    });
  }

  // ==========================================
  // 2. FUNGSI DINAMIS PAGINATION DOTS
  // ==========================================
  function setupPaginationDots() {
    dotsContainer.innerHTML = ""; // Bersihkan dot lama
    
    // Beri jeda microsecond agar browser selesai menyembunyikan/menampilkan kartu hasil filter
    setTimeout(() => {
      const maxScroll = container.scrollWidth - container.clientWidth;
      
      if (maxScroll <= 0) {
        dotsContainer.innerHTML = '<span class="dot active"></span>';
        return;
      }

      // Hitung berapa kali halaman bisa di-scroll berdasarkan lebar container
      const pageCount = Math.ceil(container.scrollWidth / container.clientWidth) || 1;

      for (let i = 0; i < pageCount; i++) {
        const dot = document.createElement("span");
        dot.classList.add("dot");
        if (i === 0) dot.classList.add("active");

        // Fitur Tambahan: Jika dot diklik, slide akan langsung meluncur ke halaman tersebut
        dot.addEventListener("click", () => {
          container.scrollTo({
            left: i * container.clientWidth,
            behavior: "smooth"
          });
        });

        dotsContainer.appendChild(dot);
      }
    }, 50);
  }

  // ==========================================
  // 3. SINKRONISASI AKTIF DOT SAAT SCROLL/SLIDE
  // ==========================================
  container.addEventListener("scroll", () => {
    const pageIndex = Math.round(container.scrollLeft / container.clientWidth);
    const dots = dotsContainer.querySelectorAll(".dot");
    
    dots.forEach((dot, index) => {
      dot.classList.toggle("active", index === pageIndex);
    });
  });

  // ==========================================
  // 4. LOGIKA TOMBOL PANAH (ARROW NEXT / PREV)
  // ==========================================
  nextBtn.addEventListener("click", () => {
    container.scrollBy({ left: container.clientWidth, behavior: "smooth" });
  });

  prevBtn.addEventListener("click", () => {
    container.scrollBy({ left: -container.clientWidth, behavior: "smooth" });
  });

  // ==========================================
  // 5. FILTER TAB ANTRIAN
  // ==========================================
  document.querySelectorAll(".antrian-tab[data-filter]").forEach((btn) => {
    btn.addEventListener("click", function () {
      const filter = this.dataset.filter;
          document.querySelectorAll(".antrian-tab[data-filter]").forEach((b) => 
        b.classList.remove("antrian-tab-active")
      );
      this.classList.add("antrian-tab-active");
      
      document.querySelectorAll(".queue-card[data-type]").forEach((card) => {
        if (filter === "all") {
          card.style.display = ""; 
        } else {
          if (card.dataset.type === filter) {
            card.style.display = ""; 
          } else {
            card.style.display = "none"; 
          }
        }
      });
    });
  });

  // RUN AWAL SAAT PAGE DI-REFRESH
  updateQueueBadges();
  setupPaginationDots();
});

queueContainer?.addEventListener("scroll", () => {
  const idx = Math.round(queueContainer.scrollLeft / queueContainer.offsetWidth);
  dots.forEach((d, i) => d.classList.toggle("active", i === idx));
});

document.querySelectorAll(".preset-btn").forEach(btn => {
  btn.addEventListener("click", function () {
    document.querySelectorAll(".preset-btn").forEach(b => b.classList.remove("preset-active"));
    this.classList.add("preset-active");
    const tunaiInput = document.querySelector(".tunai-input");
    const kembalianEl = document.querySelector(".tunai-kembalian");
    if (tunaiInput) tunaiInput.value = this.textContent;
    if (kembalianEl) {
      const total = cartItems.reduce((sum, item) => sum + item.price * item.qty, 0);
      const tax = Math.round(total * 0.1);
      const totalTagihan = total + tax;
      const dibayar = parseInt(this.textContent.replace(/\D/g, "")) || 0;
      const kembali = dibayar - totalTagihan;
      kembalianEl.textContent = kembali >= 0 ? formatRp(kembali) + ",-" : "Kurang " + formatRp(Math.abs(kembali));
    }
  });
});

// INIT
renderCart();