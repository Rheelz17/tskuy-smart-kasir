// ==========================================
// 1. BACA DATA DARI LARAVEL (META TAGS)
// ==========================================
const metaLogin = document.querySelector('meta[name="is-logged-in"]');
const isLoggedIn = metaLogin ? metaLogin.content === 'true' : false;

// Data Keranjang
let cart = [];

document.addEventListener('DOMContentLoaded', () => {
    // ==========================================
    // 2. LOGIC TOMBOL TAMBAH/PESAN DI KARTU MENU
    // ==========================================
    document.querySelectorAll('.qty-card').forEach(button => {
        button.addEventListener('click', (e) => {
            e.stopPropagation(); // Mencegah popup detail kebuka pas klik tombol ini
            
            // CEK LOGIN
            if (!isLoggedIn) {
                openPopup('popup-login-warning');
                return;
            }

            // Kalau lolos (sudah login), ambil data menu
            const card = button.closest('.menu-card');
            const menuData = {
                id: card.dataset.id,
                name: card.querySelector('.card-judul').innerText,
                price: parseInt(card.querySelector('.card-price').innerText.replace(/[^0-9]/g, '')),
                image: card.querySelector('.card-img img').src
            };

            addToCart(menuData);
        });
    });

    // ==========================================
    // 3. LOGIC TOMBOL TAMBAH DI POPUP DETAIL
    // ==========================================
    document.querySelectorAll('.btn-add-from-detail').forEach(button => {
        button.addEventListener('click', () => {
            // CEK LOGIN
            if (!isLoggedIn) {
                openPopup('popup-login-warning');
                return;
            }
            
            // Ambil info berdasarkan ID dari dataset tombol
            const menuId = button.dataset.id;
            const card = document.querySelector(`.menu-card[data-id="${menuId}"]`);
            if(card) {
                const menuData = {
                    id: menuId,
                    name: card.querySelector('.card-judul').innerText,
                    price: parseInt(card.querySelector('.card-price').innerText.replace(/[^0-9]/g, '')),
                    image: card.querySelector('.card-img img').src
                };
                addToCart(menuData);
            }
        });
    });

    // ==========================================
    // 4. LOGIC FLOATING BAR (MOBILE)
    // ==========================================
    const floatingBar = document.getElementById('floating-cart-bar');
    if (floatingBar) {
        floatingBar.addEventListener('click', () => {
            const sidebar = document.querySelector('.cart-sidebar');
            sidebar.classList.add('is-open');
            document.getElementById('popup-overlay').classList.add('is-open');
        });
    }

    // Tombol Hapus Keranjang
    const btnClear = document.getElementById('btn-clear-cart');
    if(btnClear) {
        btnClear.addEventListener('click', () => {
            cart = [];
            renderCart();
        });
    }
});

// ==========================================
// FUNGSI UTAMA KERANJANG
// ==========================================
function addToCart(item) {
    const existing = cart.find(c => c.id === item.id);
    if (existing) {
        existing.qty++;
    } else {
        cart.push({ ...item, qty: 1 });
    }
    renderCart();
}

function renderCart() {
    const container = document.getElementById('cart-items-container');
    const emptyMsg = document.getElementById('cart-empty-msg');
    let total = 0;
    let itemCount = 0;

    container.innerHTML = '';
    
    cart.forEach(item => {
        total += item.price * item.qty;
        itemCount += item.qty;
        
        container.innerHTML += `
            <div class="item-card-mini">
                <img src="${item.image}" class="mini-img">
                <div class="mini-info">
                    <p class="mini-name">${item.name}</p>
                    <div class="mini-price-row">
                        <p class="mini-price">Rp ${(item.price * item.qty).toLocaleString('id-ID')}</p>
                        <div class="mini-qty">
                            <span class="qty-val">${item.qty}x</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });

    // Update UI Desktop
    document.getElementById('cart-total-price').innerText = 'Rp ' + total.toLocaleString('id-ID');
    emptyMsg.style.display = cart.length > 0 ? 'none' : 'block';

    // Update Floating Bar (Mobile)
    const floatingBar = document.getElementById('floating-cart-bar');
    if (itemCount > 0) {
        floatingBar.style.display = 'flex';
        document.getElementById('mobile-item-count').innerText = itemCount;
        document.getElementById('mobile-total-price').innerText = 'Rp ' + total.toLocaleString('id-ID');
    } else {
        floatingBar.style.display = 'none';
    }
}