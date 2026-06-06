<header class="mobile-header">
<button class="hamburger-btn" id="hamburger-btn" aria-label="Buka menu">
    <span></span><span></span><span></span>
</button>
<div class="mobile-header-logo">
    <img
    src="/assets/img/logo_warkop.png"
    alt="Logo"
    class="mobile-logo-img"
    />
    <div class="mobile-brand-wrap">
    <span class="mobile-brand-sub">Warkop</span>
    <span class="mobile-brand-name">Tskuy</span>
    </div>
</div>
<button
    class="mobile-notif-btn"
    data-open="popup-notif-panel"
    aria-label="Notifikasi"
>
    <span class="mobile-notif-badge"></span>
    <svg width="19" height="25" viewBox="0 0 19 25" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path fill-rule="evenodd" clip-rule="evenodd" d="M10.4944 1.22289C10.4944 0.898558 10.3656 0.587512 10.1362 0.358176C9.90688 0.12884 9.59584 0 9.27151 0C8.94718 0 8.63613 0.12884 8.40679 0.358176C8.17746 0.587512 8.04862 0.898558 8.04862 1.22289V2.14005H7.36747C6.05657 2.13997 4.79491 2.63947 3.83934 3.53688C2.88377 4.43429 2.30613 5.66214 2.224 6.97046L1.95374 11.2922C1.84927 12.9396 1.29613 14.5272 0.354205 15.8829C0.159317 16.163 0.040237 16.4888 0.00854621 16.8286C-0.0231446 17.1683 0.0336322 17.5105 0.173355 17.8218C0.313077 18.1332 0.53099 18.403 0.8059 18.6052C1.08081 18.8074 1.40336 18.935 1.74218 18.9756L5.90856 19.4745V20.7891C5.90856 21.681 6.26287 22.5364 6.89355 23.1671C7.52422 23.7977 8.3796 24.152 9.27151 24.152C10.1634 24.152 11.0188 23.7977 11.6495 23.1671C12.2801 22.5364 12.6345 21.681 12.6345 20.7891V19.4745L16.8008 18.9743C17.1395 18.9336 17.4618 18.806 17.7365 18.6039C18.0113 18.4018 18.2291 18.1321 18.3688 17.821C18.5084 17.5098 18.5653 17.1679 18.5338 16.8283C18.5022 16.4887 18.3834 16.163 18.1888 15.8829C17.2469 14.5272 16.6937 12.9396 16.5893 11.2922L16.319 6.97169C16.2372 5.66315 15.6597 4.43499 14.7041 3.53733C13.7485 2.63966 12.4866 2.13999 11.1755 2.14005H10.4944V1.22289ZM7.36747 3.97439C6.52316 3.97429 5.71054 4.29597 5.09507 4.87395C4.4796 5.45194 4.10755 6.24276 4.05467 7.08542L3.78563 11.4071C3.6599 13.3891 2.99425 15.2989 1.8608 16.9297C1.8467 16.9499 1.83808 16.9735 1.83578 16.9981C1.83348 17.0226 1.83757 17.0474 1.84766 17.0699C1.85776 17.0924 1.8735 17.112 1.89338 17.1266C1.91325 17.1412 1.93657 17.1505 1.96108 17.1535L6.53101 17.7025C8.3519 17.9202 10.1911 17.9202 12.012 17.7025L16.5819 17.1535C16.6064 17.1505 16.6298 17.1412 16.6496 17.1266C16.6695 17.112 16.6853 17.0924 16.6954 17.0699C16.7054 17.0474 16.7095 17.0226 16.7072 16.9981C16.7049 16.9735 16.6963 16.9499 16.6822 16.9297C15.5492 15.2988 14.884 13.389 14.7586 11.4071L14.4884 7.08542C14.4355 6.24276 14.0634 5.45194 13.4479 4.87395C12.8325 4.29597 12.0199 3.97429 11.1755 3.97439H7.36747ZM9.27151 22.3177C8.42771 22.3177 7.7429 21.6329 7.7429 20.7891V19.8719H10.8001V20.7891C10.8001 21.6329 10.1153 22.3177 9.27151 22.3177Z" fill="#EFB100"/>
    </svg>
</button>
</header>

{{-- 🔥 SEARCH BAR KHUSUS MOBILE (Muncul hanya di halaman yang butuh search) 🔥 --}}
@if(Request::is('kasir/pos', 'pelanggan/orders'))
<div class="mobile-search-wrapper" style="padding: 10px 16px; background: #fff;">
    <div class="search-container" style="width: 100%; position: relative;">
        <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%);">
            <circle cx="11" cy="11" r="7" stroke="#fbbf24" stroke-width="2" />
            <path d="M16.5 16.5L21 21" stroke="#fbbf24" stroke-width="2" stroke-linecap="round" />
        </svg>
        <input type="text" class="search-bar" placeholder="What do you want eat today..." style="width: 100%; height: 40px; padding-left: 40px; border-radius: 12px; border: 1px solid #ddd; outline: none; background: #f8f8f8;" />
    </div>
</div>
@endif