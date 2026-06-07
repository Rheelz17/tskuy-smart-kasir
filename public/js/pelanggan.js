const metaLogin = document.querySelector('meta[name="is-logged-in"]');
const isLoggedIn = metaLogin ? metaLogin.content === 'true' : false;

let cartItems = [];
let nextCartId = 1;

const overlay = document.getElementById("popup-overlay");
let activePopup = null;

function openPopup(id) {
    if (activePopup) activePopup.classList.remove("is-open");
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.add("is-open");
    if (overlay) overlay.classList.add("is-open");
    activePopup = el;

    const sidebar = document.querySelector('.sidebar');
    if (sidebar) sidebar.classList.remove('show-drawer');
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

function renderCart() {
    const container = document.getElementById('cart-items-container');
    const emptyMsg = document.getElementById('cart-empty-msg');
    if (!container) return;
    container.innerHTML = '';

    if (cartItems.length === 0) {
        if(emptyMsg) emptyMsg.style.display = 'flex';
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
                    <p class="mini-name" style="font-size:13px; font-weight:600; color:#222; margin:0;">${item.name}</p>
                    <button class="delete-item" data-cart-id="${item.cartId}" style="background:none; border:none; cursor:pointer; color:#ef4444; padding:0;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    </button>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:8px;">
                    <p class="mini-price" style="font-size:13px; font-weight:700; color:#efb100; margin:0;">${formatRp(item.price * item.qty)}</p>
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
    let subtotal = 0; let totalQty = 0;
    cartItems.forEach(item => { subtotal += item.price * item.qty; totalQty += item.qty; });

    let tax = subtotal * 0.10;
    let grandTotal = subtotal + tax;

    const elTotalPrice = document.getElementById('cart-total-price');
    if(elTotalPrice) elTotalPrice.innerText = formatRp(grandTotal);

    const floatingBar = document.getElementById('floating-cart-bar');
    if (floatingBar) {
        if (totalQty > 0) {
            floatingBar.style.display = 'flex';
            document.getElementById('mobile-item-count').innerText = totalQty;
            document.getElementById('mobile-total-price').innerText = formatRp(grandTotal);
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

// 🔥 FUNGSI CHECKOUT UNIFIED (MIDTRANS + DB) 🔥
function submitOrderToDatabase(orderType) {
    const activeOrderId = document.getElementById('active_order_id')?.value || null; 
    const url = activeOrderId ? `/pelanggan/checkout/${activeOrderId}/add` : '/pelanggan/checkout';

    if (cartItems.length === 0) { alert("Keranjang belanja kosong!"); return; }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const eatingOption = document.querySelector('[name="eating_option"]:checked')?.value || 'dine in';
    const tableNumber  = document.getElementById('co-meja')?.value || '';
    const globalNote   = document.getElementById('co-catatan')?.value || '';

    const payload = { 
        type: orderType, 
        eating_option: eatingOption,
        table_number: tableNumber,
        items: cartItems.map(item => ({ 
            id: parseInt(item.id), 
            qty: item.qty,
            catatan: globalNote 
        })) 
    };

    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            cartItems = []; 
            renderCart();
            closePopup();

            // Kalo Pay Now, panggil popup Snap Midtrans
            if (orderType === 'pay_now' && data.snap_token) {
                snap.pay(data.snap_token, {
                    onSuccess: function(result) {
                        // Anti-kesasar ke example.com! Langsung paksa ke halaman sukses
                        window.location.href = `/pelanggan/payment/success/${data.order_code}`;
                    },
                    onPending: function(result) {
                        window.location.href = `/pelanggan/riwayat`;
                    },
                    onError: function(result) {
                        alert("Pembayaran gagal!");
                        window.location.href = `/pelanggan/riwayat`;
                    },
                    onClose: function() {
                        alert('Anda menutup popup sebelum menyelesaikan pembayaran.');
                        window.location.href = `/pelanggan/riwayat`;
                    }
                });
            } else {
                // Kalo Open Bill, langsung pindah ke riwayat
                if (data.message) alert(data.message);
                window.location.href = data.redirect_url;
            }
        } else { 
            alert("Gagal memproses database: " + data.message); 
        }
    })
    .catch(err => { 
        console.error(err); 
        alert("Terjadi gangguan sinkronisasi sistem."); 
    });
}

function payNow()
{
    if (cartItems.length === 0) {
        alert("Keranjang kosong");
        return;
    }
    const csrfToken =
        document.querySelector(
            'meta[name="csrf-token"]'
        ).content;
    fetch('/pelanggan/checkout/pay-now', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            items: cartItems
        })
    })
    .then(async res => {
        const data = await res.json();
        console.log(data);
        snap.pay(data.snap_token);
    })
    .catch(err => {
        console.error(err);
    });
}

let activeKategori = "all";
let activeMood = "all";
let searchQuery = ""; 

function applyMenuFilter() {
    document.querySelectorAll(".menu-card").forEach((card) => {
        const kategori = card.dataset.category || "";
        const mood = card.dataset.mood || "";
        const namaMenu = card.querySelector('.card-judul').innerText.toLowerCase(); 
        const isKategoriMatch = activeKategori === "all" || kategori === activeKategori;
        const isMoodMatch = activeMood === "all" || mood.includes(activeMood);
        const isSearchMatch = searchQuery === "" || namaMenu.includes(searchQuery); 
        card.style.display = (isKategoriMatch && isMoodMatch && isSearchMatch) ? "block" : "none";
    });
}

document.addEventListener('DOMContentLoaded', () => {
    
    const searchInput = document.querySelector('.search-bar');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            searchQuery = e.target.value.toLowerCase(); 
            applyMenuFilter(); 
        });
    }

    const hamburgerBtn = document.getElementById('hamburger-btn');
    const sidebar = document.querySelector('.sidebar');
    if(hamburgerBtn && sidebar) {
        hamburgerBtn.addEventListener('click', function(e) {
            e.stopPropagation(); 
            sidebar.classList.toggle('show-drawer');
        });
    }
    document.addEventListener('click', function(e) {
        if (sidebar && sidebar.classList.contains('show-drawer')) {
            if (!sidebar.contains(e.target) && !hamburgerBtn.contains(e.target)) {
                sidebar.classList.remove('show-drawer');
            }
        }
    });

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
            if (targetId === 'popup-payment') {
                if (cartItems.length === 0) { alert('Keranjang belanja kosong!'); return; }
            }
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

        const pdBtnPlus = e.target.closest('.pd-btn-plus');
        if (pdBtnPlus) {
            const valEl = pdBtnPlus.previousElementSibling;
            if(valEl) valEl.innerText = parseInt(valEl.innerText) + 1;
            const popup = pdBtnPlus.closest('.pd-content');
            if(popup) popup.querySelectorAll('.pd-qty-val').forEach(el => el.innerText = valEl.innerText);
            return;
        }

        const pdBtnMin = e.target.closest('.pd-btn-min');
        if (pdBtnMin) {
            const valEl = pdBtnMin.nextElementSibling;
            let v = parseInt(valEl.innerText);
            if(v > 1) {
                valEl.innerText = v - 1;
                const popup = pdBtnMin.closest('.pd-content');
                if(popup) popup.querySelectorAll('.pd-qty-val').forEach(el => el.innerText = valEl.innerText);
            }
            return;
        }

        const btnAddDetailV2 = e.target.closest('.btn-add-from-detail-v2');
        if (btnAddDetailV2) {
            if (!isLoggedIn) { openPopup('popup-login-warning'); return; }
            const menuId = btnAddDetailV2.dataset.id;
            const card = document.querySelector(`.menu-card[data-id="${menuId}"]`);
            const popup = btnAddDetailV2.closest('.pd-content');
            
            let qty = 1;
            if(popup) {
                const mobileQty = popup.querySelector('.mobile-qty .pd-qty-val');
                const desktopQty = popup.querySelector('.desktop-qty .pd-qty-val');
                if(mobileQty && mobileQty.offsetParent !== null) qty = parseInt(mobileQty.innerText);
                else if(desktopQty) qty = parseInt(desktopQty.innerText);
            }

            if(card) { 
                const menuData = { 
                    id: menuId, 
                    name: card.querySelector('.card-judul').innerText, 
                    price: parseInt(card.querySelector('.card-price').innerText.replace(/[^0-9]/g, '')), 
                    image: card.querySelector('.card-img img').src 
                };
                
                const existing = cartItems.find(item => item.id == menuData.id);
                if (existing) { 
                    existing.qty += qty; 
                } else {
                    cartItems.push({ cartId: nextCartId++, id: menuData.id, name: menuData.name, price: menuData.price, image: menuData.image, qty: qty });
                }
                renderCart(); 
                showAddedToast(menuData.name);
            }
            closePopup(); return;
        }

        const btnOpenBillAction = e.target.closest('#choice-open-bill');
        if (btnOpenBillAction) {
            e.stopPropagation();
            submitOrderToDatabase('open_bill');
            return;
        }

        const btnPayNowAction =
            e.target.closest('#choice-pay-now');
        if (btnPayNowAction) {
            e.stopPropagation();
            payNow();
            return;
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
    });

    // Tambahkan di riwayat.js atau script di riwayat.blade.php
    function showDetailModal(order) {
    // 1. Munculin Modal (seperti gambar image_b9efd9.png)
    // 2. Tombol "Tambah Pesanan"
    document.getElementById('btn-add-more').onclick = () => {
        window.location.href = `/pelanggan/orders/${order.id}/add`;
    };
    // 3. Tombol "Selesaikan Pesanan"
    document.getElementById('btn-finish').onclick = () => {
        window.location.href = `/pelanggan/payment/qris/${order.order_code}`;
    };
}

    renderCart();
});