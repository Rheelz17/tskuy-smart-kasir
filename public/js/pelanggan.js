// ============================================================
// 1. VARIABEL GLOBAL & STATUS LOGIN
// ============================================================
const metaLogin = document.querySelector('meta[name="is-logged-in"]');
const isLoggedIn = metaLogin ? metaLogin.content === 'true' : false;

let cartItems = [];
let nextCartId = 1;

const overlay = document.getElementById("popup-overlay");
let activePopup = null;

// ============================================================
// 2. SISTEM POPUP INTI 
// ============================================================
function openPopup(id) {
    if (activePopup) activePopup.classList.remove("is-open");
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.add("is-open");
    if (overlay) overlay.classList.add("is-open");
    activePopup = el;
}

function closePopup() {
    if (activePopup) { activePopup.classList.remove("is-open"); activePopup = null; }
    if (overlay) overlay.classList.remove("is-open");
    document.querySelector(".cart-sidebar")?.classList.remove("is-open");
}

window._openPopup = openPopup;
window._closePopup = closePopup;

document.addEventListener("keydown", function (e) { if (e.key === "Escape") closePopup(); });
if (overlay) overlay.addEventListener("click", closePopup);

// ============================================================
// 3. FORMATTER & TOAST NOTIFICATION
// ============================================================
function formatRp(amount) { return "Rp " + parseInt(amount).toLocaleString("id-ID"); }

function showAddedToast(name) {
    let toast = document.getElementById("add-toast");
    if (!toast) {
        toast = document.createElement("div");
        toast.id = "add-toast";
        toast.style.cssText = "position:fixed; bottom:90px; left:50%; transform:translateX(-50%); background:#22c55e; color:#fff; padding:10px 20px; border-radius:30px; font-size:13px; font-weight:600; z-index:9999; opacity:0; transition:opacity 0.3s; display:flex; align-items:center; gap:8px; box-shadow:0 4px 12px rgba(34,197,94,0.3); pointer-events:none;";
        document.body.appendChild(toast);
    }
    toast.innerHTML = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" fill="#fff"/><path d="M8 12L11 15L16 9" stroke="#22c55e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg> ${name} ditambahkan`;
    toast.style.opacity = "1";
    clearTimeout(toast._timeout);
    toast._timeout = setTimeout(() => toast.style.opacity = "0", 2500);
}

// ============================================================
// 4. FUNGSI MANAJEMEN KERANJANG
// ============================================================
function renderCart() {
    const container = document.getElementById('cart-items-container');
    const emptyMsg = document.getElementById('cart-empty-msg');
    if (!container) return;
    container.innerHTML = '';

    if (cartItems.length === 0) {
        if(emptyMsg) emptyMsg.style.display = 'block';
        updateSummary();
        return;
    }
    if(emptyMsg) emptyMsg.style.display = 'none';

    cartItems.forEach(item => {
        const card = document.createElement('div');
        card.style.cssText = "display:flex; gap:12px; margin-bottom:15px; padding-bottom:15px; border-bottom:1px solid #eee;";
        card.innerHTML = `
            <img src="${item.image}" class="mini-img" alt="${item.name}" style="width:60px; height:60px; border-radius:10px; object-fit:cover;">
            <div class="mini-info" style="flex:1;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                    <p class="mini-name" style="font-size:13px; font-weight:600; color:#222;">${item.name}</p>
                    <button class="delete-item" data-cart-id="${item.cartId}" style="background:none; border:none; cursor:pointer; color:#ef4444; padding:0;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    </button>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:8px;">
                    <p class="mini-price" style="font-size:13px; font-weight:700; color:#efb100;">${formatRp(item.price * item.qty)}</p>
                    <div class="mini-qty" style="display:flex; align-items:center; gap:10px; background:#f4f4f4; padding:4px 8px; border-radius:20px;">
                        <button class="qty-btn minus" data-cart-id="${item.cartId}" style="background:none; border:none; font-weight:bold; cursor:pointer; font-size:14px; color:#555;">-</button>
                        <span class="qty-val" style="font-size:12px; font-weight:600; width:16px; text-align:center;">${item.qty}</span>
                        <button class="qty-btn plus" data-cart-id="${item.cartId}" style="background:none; border:none; font-weight:bold; cursor:pointer; font-size:14px; color:#555;">+</button>
                    </div>
                </div>
            </div>`;
        container.appendChild(card);
    });
    updateSummary();
}

function updateSummary() {
    let total = 0; let totalQty = 0;
    cartItems.forEach(item => { total += item.price * item.qty; totalQty += item.qty; });

    const elTotalPrice = document.getElementById('cart-total-price');
    if(elTotalPrice) elTotalPrice.innerText = formatRp(total);

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

function addToCart(menuData) {
    const existing = cartItems.find(item => item.id === menuData.id);
    if (existing) { existing.qty++; } else {
        cartItems.push({ cartId: nextCartId++, id: menuData.id, name: menuData.name, price: menuData.price, image: menuData.image, qty: 1 });
    }
    renderCart(); showAddedToast(menuData.name);
}

// ============================================================
// 5. PENYARINGAN KATALOG MENU 
// ============================================================
let activeKategori = "all";
let activeMood = "all";

function applyMenuFilter() {
    document.querySelectorAll(".menu-card").forEach((card) => {
        const kategori = card.dataset.category || "";
        const mood = card.dataset.mood || "";
        const isKategoriMatch = activeKategori === "all" || kategori === activeKategori;
        const isMoodMatch = activeMood === "all" || mood.includes(activeMood);
        card.style.display = (isKategoriMatch && isMoodMatch) ? "block" : "none";
    });
}

// ============================================================
// 6. EVENT DELEGATION GLOBAL 
// ============================================================
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll(".menu-tab").forEach(tab => {
        tab.addEventListener("click", function() {
            document.querySelectorAll(".menu-tab").forEach(t => t.classList.remove("active-tab"));
            this.classList.add("active-tab");
            activeKategori = this.dataset.filter;
            applyMenuFilter();
        });
    });

    document.querySelectorAll(".mood").forEach(btn => {
        btn.addEventListener("click", function() {
            document.querySelectorAll(".mood").forEach(m => m.classList.remove("active-mood"));
            this.classList.add("active-mood");
            activeMood = this.dataset.mood;
            applyMenuFilter();
        });
    });

    document.addEventListener("click", function (e) {
        const opener = e.target.closest("[data-open]");
        if (opener) {
            e.stopPropagation();
            const targetId = opener.dataset.open;
            if (targetId.startsWith("popup-detail-") && !isLoggedIn) { openPopup("popup-login-warning"); return; }
            openPopup(targetId); return;
        }

        const closer = e.target.closest("[data-close]");
        if (closer) { e.stopPropagation(); closePopup(); return; }

        const btnAddCard = e.target.closest('.qty-card');
        if (btnAddCard) {
            e.stopPropagation();
            if (!isLoggedIn) { openPopup('popup-login-warning'); return; }
            const card = btnAddCard.closest('.menu-card');
            if (card) { addToCart({ id: card.dataset.id, name: card.querySelector('.card-judul').innerText, price: parseInt(card.querySelector('.card-price').innerText.replace(/[^0-9]/g, '')), image: card.querySelector('.card-img img').src }); }
            return;
        }

        const btnAddDetail = e.target.closest('.btn-add-from-detail');
        if (btnAddDetail) {
            if (!isLoggedIn) { openPopup('popup-login-warning'); return; }
            const menuId = btnAddDetail.dataset.id;
            const card = document.querySelector(`.menu-card[data-id="${menuId}"]`);
            if(card) { addToCart({ id: menuId, name: card.querySelector('.card-judul').innerText, price: parseInt(card.querySelector('.card-price').innerText.replace(/[^0-9]/g, '')), image: card.querySelector('.card-img img').src }); }
            closePopup(); return;
        }

        const btnPlus = e.target.closest('.qty-btn.plus');
        if (btnPlus) {
            const item = cartItems.find(i => i.cartId === parseInt(btnPlus.dataset.cartId));
            if (item) { item.qty++; renderCart(); } return;
        }

        const btnMinus = e.target.closest('.qty-btn.minus');
        if (btnMinus) {
            const cartId = parseInt(btnMinus.dataset.cartId);
            const item = cartItems.find(i => i.cartId === cartId);
            if (item) { item.qty--; if (item.qty <= 0) cartItems = cartItems.filter(i => i.cartId !== cartId); renderCart(); } return;
        }

        const btnDelete = e.target.closest('.delete-item');
        if (btnDelete) { cartItems = cartItems.filter(i => i.cartId !== parseInt(btnDelete.dataset.cartId)); renderCart(); return; }

        const btnClear = e.target.closest('#btn-clear-cart');
        if (btnClear) { cartItems = []; renderCart(); return; }

        const floatingBar = e.target.closest('#floating-cart-bar');
        if (floatingBar) {
            document.querySelector('.cart-sidebar')?.classList.add('is-open');
            document.getElementById('popup-overlay')?.classList.add('is-open');
            return;
        }
    });

    renderCart();
});