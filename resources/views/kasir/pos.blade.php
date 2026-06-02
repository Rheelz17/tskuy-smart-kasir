@extends('layouts.kasir')

@section('title', 'Dashboard Kasir - Tskuy POS')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/style-dashboardKasir.css') }}" />
@endsection

@section('content')
<main class="content-area">

    <!-- <section class="queue-section">
        <div class="queue-header">
            <h3 class="section-title">Antrian</h3>
            <button class="see-all" data-open="popup-antrian-full">See all</button>
        </div>

        <div class="queue-wrapper">
            <button class="nav-arrow left" id="queue-prev">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                    <path d="M15 18L9 12L15 6" stroke="#555" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
            <div class="queue-container" id="queue-container">
                <div class="queue-card queue-green" data-open="popup-detail-antrian">
                    <div class="card-top">
                        <div class="table-info">
                            <p class="table-number">Table 10</p>
                            <p class="order-id">Order #T0123</p>
                        </div>
                        <span class="time-status">Just now</span>
                    </div>
                    <span class="badge badge-dine">Dine in</span>
                </div>
                <div class="queue-card queue-green" data-open="popup-detail-antrian">
                    <div class="card-top">
                        <div class="table-info">
                            <p class="table-number">Table 11</p>
                            <p class="order-id">Order #T0124</p>
                        </div>
                        <span class="time-status">Just now</span>
                    </div>
                    <span class="badge badge-dine">Dine in</span>
                </div>
                <div class="queue-card queue-green" data-open="popup-detail-antrian">
                    <div class="card-top">
                        <div class="table-info">
                            <p class="table-number">Table 02</p>
                            <p class="order-id">Order #T0122</p>
                        </div>
                        <span class="time-status">1 min ago</span>
                    </div>
                    <span class="badge badge-takeaway">Take away</span>
                </div>
                <div class="queue-card queue-green" data-open="popup-detail-antrian">
                    <div class="card-top">
                        <div class="table-info">
                            <p class="table-number">Table 15</p>
                            <p class="order-id">Order #T0121</p>
                        </div>
                        <span class="time-status">5 min ago</span>
                    </div>
                    <span class="badge badge-dine">Dine in</span>
                </div>
            </div>
            <button class="nav-arrow right" id="queue-next">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                    <path d="M9 18L15 12L9 6" stroke="#555" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
        </div>

        <div class="pagination-dots" id="pagination-dots">
            <span class="dot active"></span>
            <span class="dot"></span>
            <span class="dot"></span>
        </div>
    </section> -->
    <section class="queue-section">
      <div class="queue-header">
          <h3 class="section-title">Antrian</h3>
          
          <div class="antrian-tabs" style="display: flex; gap: 8px; margin: 0; border: none; padding: 0;">
              <button class="antrian-tab antrian-tab-active" data-filter="all">All <span class="tab-badge tab-badge-yellow"></span></button>
              <button class="antrian-tab" data-filter="pending">Pending <span class="tab-badge tab-badge-outline"></span></button>
              <button class="antrian-tab" data-filter="dine">Dine in <span class="tab-badge tab-badge-outline"></span></button>
              <button class="antrian-tab" data-filter="takeaway">Take away <span class="tab-badge tab-badge-outline"></span></button>
              <button class="antrian-tab" data-filter="openbill">Open Bill <span class="tab-badge tab-badge-outline"></span></button>
          </div>
      </div>

      <div class="queue-wrapper">
          <button class="nav-arrow left" id="queue-prev">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                  <path d="M15 18L9 12L15 6" stroke="#555" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
          </button>
          
          <div class="queue-container" id="queue-container">
              <div class="queue-card queue-green" data-type="dine" data-open="popup-detail-antrian">
                  <div class="card-top">
                      <div class="table-info">
                          <p class="table-number">Table 10</p>
                          <p class="order-id">Order #T0123</p>
                      </div>
                      <span class="time-status">Just now</span>
                  </div>
                  <span class="badge badge-dine">Dine in</span>
              </div>

              <div class="queue-card queue-green" data-type="dine" data-open="popup-detail-antrian">
                  <div class="card-top">
                      <div class="table-info">
                          <p class="table-number">Table 11</p>
                          <p class="order-id">Order #T0124</p>
                      </div>
                      <span class="time-status">Just now</span>
                  </div>
                  <span class="badge badge-dine">Dine in</span>
              </div>

              <div class="queue-card queue-green" data-type="takeaway" data-open="popup-detail-antrian">
                  <div class="card-top">
                      <div class="table-info">
                          <p class="table-number">Table 02</p>
                          <p class="order-id">Order #T0122</p>
                      </div>
                      <span class="time-status">1 min ago</span>
                  </div>
                  <span class="badge badge-takeaway">Take away</span>
              </div>

              <div class="queue-card queue-green" data-type="pending" data-open="popup-detail-antrian">
                  <div class="card-top">
                      <div class="table-info">
                          <p class="table-number">Table 05</p>
                          <p class="order-id">Order #T0125</p>
                      </div>
                      <span class="time-status">Just now</span>
                  </div>
                  <span class="badge" style="background: #fee2e2; color: #ef4444;">Pending</span>
              </div>

              <div class="queue-card queue-green" data-type="dine" data-open="popup-detail-antrian">
                  <div class="card-top">
                      <div class="table-info">
                          <p class="table-number">Table 15</p>
                          <p class="order-id">Order #T0121</p>
                      </div>
                      <span class="time-status">5 min ago</span>
                  </div>
                  <span class="badge badge-dine">Dine in</span>
              </div>
              <div class="queue-card queue-green" data-type="dine" data-open="popup-detail-antrian">
                  <div class="card-top">
                      <div class="table-info">
                          <p class="table-number">Table 15</p>
                          <p class="order-id">Order #T0121</p>
                      </div>
                      <span class="time-status">5 min ago</span>
                  </div>
                  <span class="badge badge-dine">Dine in</span>
              </div>
          </div>

          <button class="nav-arrow right" id="queue-next">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                  <path d="M9 18L15 12L9 6" stroke="#555" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
          </button>
      </div>

      <div class="pagination-dots" id="pagination-dots">
          <span class="dot active"></span>
          <span class="dot"></span>
          <span class="dot"></span>
      </div>
    </section> 

    <section class="mood-section">
        <!-- <p class="section-label">Menu berdasarkan mood:</p>
        <p class="section-labelmini">Pilih menu berdasarkan mood kamu!</p> -->
        <!-- <div class="kategori-mood">
            <button class="mood" data-mood="chill">
                <img src="{{ asset('image/chill.png') }}" alt="chill" class="icon">
                Chill
            </button>
            <button class="mood" data-mood="lapar">
                <img src="{{ asset('image/lapar.png') }}" alt="lapar" class="icon">
                Lapar
            </button>
            <button class="mood" data-mood="fokus">
                <img src="{{ asset('image/fokus.png') }}" alt="fokus" class="icon">
                Fokus
            </button>
            <button class="mood" data-mood="bahagia">
                <img src="{{ asset('image/bahagia.png') }}" alt="bahagia" class="icon">
                Bahagia
            </button>
            <button class="mood" data-mood="sedih">
                <img src="{{ asset('image/sedih.png') }}" alt="sedih" class="icon">
                Sedih
            </button>
        </div> -->
        <p class="section-label">Rekomendasi & Performa Menu:</p>
        <p class="section-labelmini">Saring menu terlaris atau promo aktif secara cepat</p>
        <div class="kategori-mood">
            <button class="mood active-mood" data-tag="semua">
                Semua
            </button>
            <button class="mood" data-tag="bestseller">
                <img src="{{ asset('image/bestseller.png') }}" alt="best seller" class="icon">
                Best Seller
            </button>
            <button class="mood" data-tag="promo">
                <img src="{{ asset('image/promo.png') }}" alt="promo" class="icon">
                Promo Active
            </button>
            <button class="mood" data-tag="rekomendasi">
                <img src="{{ asset('image/rekomendasi.png') }}" alt="rekomendasi" class="icon">
                Rekomendasi
            </button>
            <button class="mood" data-tag="baru">
                <img src="{{ asset('image/baru.png') }}" alt="menu baru" class="icon">
                Menu Baru
            </button>
        </div>
    </section>

    <section class="menu-choice">
        <div class="kategori-menu">
            <p class="section-label">Pilihan Menu:</p>
            <div class="kategori-menu-btn">
                <button class="menu-tab active-tab" data-kategori="makanan" id="tab-makanan">
                    <svg width="36" height="40" viewBox="0 0 36 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M32.657 29.1089H32.0082C32.1884 28.5444 32.1104 27.9175 31.7992 27.4283C31.4877 26.9391 30.9821 26.6486 30.4429 26.6489H29.8895L29.8892 21.7288C29.8865 19.7928 28.976 17.998 27.4865 16.9928C27.8499 16.192 28.0957 15.332 28.2149 14.4449C28.3681 13.2532 28.2522 12.142 27.8875 11.1872L30.5319 8.24834C31.007 8.50133 31.5621 8.48881 32.0276 8.21446C32.4932 7.94011 32.814 7.43657 32.8917 6.85735C32.9697 6.27843 32.7956 5.69251 32.4226 5.27899L30.857 3.53892C30.4843 3.12601 29.9575 2.93375 29.437 3.02042C28.9166 3.1074 28.4639 3.46323 28.217 3.97956C27.9698 4.49592 27.9577 5.11175 28.184 5.63972L25.5394 8.57792C24.6799 8.17265 23.6802 8.04387 22.6074 8.21416C21.0413 8.46288 19.4867 9.32713 18.1953 10.6602L18.0195 10.4926H18.0192C16.8772 9.40045 15.3507 8.9442 13.8697 9.25209L7.3446 10.5951C5.43737 10.9876 3.71387 12.1097 2.47356 13.7665C1.23289 15.4233 0.553675 17.5104 0.553675 19.6656V21.114C0.546535 23.8047 1.604 26.363 3.44556 28.1101C3.36316 28.4351 3.32114 28.7714 3.32114 29.109H0.553711C0.248013 29.109 0 29.3842 0 29.7239V30.954C0.000549281 31.3355 0.107114 31.7072 0.305423 32.0188C0.503725 32.3301 0.783605 32.5657 1.10713 32.6932V33.4141C1.10795 34.2292 1.39991 35.0108 1.91874 35.5872C2.43757 36.1636 3.14099 36.4881 3.87456 36.489H29.3354C30.0693 36.4881 30.7727 36.1637 31.2915 35.5872C31.8103 35.0107 32.102 34.2292 32.1028 33.4141V32.6932C32.4264 32.5657 32.7065 32.3301 32.9045 32.0188C33.1028 31.7072 33.2094 31.3355 33.21 30.954V29.7239C33.21 29.5609 33.1515 29.4044 33.0479 29.289C32.9441 29.174 32.8032 29.109 32.6565 29.109L32.657 29.1089ZM29.2919 6.14877C29.3957 6.03341 29.4542 5.87716 29.4542 5.7139C29.4542 5.55094 29.3957 5.39438 29.2919 5.27902C29.1848 5.16459 29.1235 5.00681 29.1221 4.8417C29.1205 4.6766 29.179 4.51761 29.2842 4.40073C29.3891 4.28384 29.5322 4.21915 29.6808 4.22067C29.8297 4.2222 29.9714 4.29025 30.0744 4.40927L31.6394 6.14818C31.747 6.26262 31.8086 6.42009 31.8102 6.5858C31.8119 6.75121 31.7536 6.9102 31.6484 7.02739C31.543 7.14427 31.3999 7.20927 31.251 7.20745C31.1022 7.20592 30.9602 7.13756 30.8572 7.01793C30.7533 6.90257 30.6124 6.83787 30.4658 6.83787C30.3188 6.83787 30.1782 6.90257 30.0744 7.01793L27.3103 10.0898C27.0834 9.76448 26.8203 9.47213 26.5275 9.22004L29.2919 6.14877ZM23.5018 9.36776C24.5345 9.36776 25.4747 9.73458 26.1607 10.4969C27.8872 12.4153 27.3604 16.1219 24.9866 18.7594C22.6127 21.3971 19.277 21.9824 17.5503 20.064C16.7357 19.1589 16.3948 17.8185 16.5917 16.2899C16.7966 14.696 17.5542 13.102 18.7245 11.8014C20.1552 10.212 21.9352 9.3676 23.5019 9.3676L23.5018 9.36776ZM1.66061 21.1146V19.6663C1.66088 17.7986 2.24975 15.9898 3.32477 14.5539C4.39977 13.1181 5.89335 12.1455 7.54626 11.8055L14.0713 10.4624C15.223 10.2229 16.4106 10.5778 17.2985 11.4271L17.4284 11.5507C16.3814 12.9152 15.7022 14.5103 15.4957 16.1167C15.2488 18.0378 15.7003 19.7489 16.7674 20.9346C17.6438 21.9084 18.8391 22.4207 20.1962 22.4207C20.4997 22.4201 20.8029 22.3957 21.1034 22.3475C22.7736 22.0823 24.4306 21.1176 25.7693 19.6299C26.2021 19.1496 26.5913 18.6228 26.9316 18.058C28.0797 18.8426 28.78 20.232 28.7822 21.7295V27.2645C28.7822 27.4278 28.8405 27.5843 28.9443 27.6994C29.0481 27.8147 29.189 27.8797 29.3357 27.8797H30.4428C30.7485 27.8797 30.9962 28.155 30.9962 28.4947C30.9962 28.8343 30.7485 29.1096 30.4428 29.1096H26.0147C26.0139 28.2945 25.7219 27.5129 25.2031 26.9365C24.6843 26.36 23.9809 26.0356 23.247 26.0347C23.1712 26.0347 23.0954 26.038 23.0196 26.0451H23.0199C22.7326 25.3062 22.1962 24.7221 21.5257 24.4179C20.8553 24.1136 20.1041 24.1136 19.4337 24.4179C18.7629 24.7221 18.2265 25.3062 17.9395 26.0451H17.9362L17.6531 24.893H17.6533C17.4751 24.1704 17.0658 23.5448 16.5022 23.1334C15.9386 22.7217 15.2594 22.5527 14.5917 22.6577L10.2689 23.344C11.2978 21.9329 12.7307 19.5806 12.7307 17.4248C12.7307 17.2438 12.659 17.0723 12.5348 16.9554C12.4107 16.8385 12.2467 16.7885 12.0861 16.8181C11.9537 16.8425 9.14664 17.3912 7.68963 19.9068L7.10268 18.9284C6.93321 18.6458 6.58934 18.5695 6.33501 18.7581C6.25728 18.8158 4.42806 20.2162 4.42806 24.1896V24.8048C4.42779 25.2794 4.48245 25.7521 4.59066 26.2114C4.34539 26.4262 4.12841 26.678 3.94604 26.9594C2.48318 25.4512 1.65456 23.332 1.66061 21.1146ZM6.64211 25.4197C6.2889 25.4197 5.93814 25.4823 5.60282 25.6059C5.55805 25.3416 5.53525 25.0733 5.53525 24.8048V24.1898C5.53525 22.0246 6.13291 20.8116 6.55148 20.2274L7.28864 21.4561V21.4558C7.40262 21.6459 7.60202 21.75 7.80664 21.7262C8.01098 21.7027 8.18677 21.555 8.26313 21.3432C8.93741 19.4697 10.5823 18.6061 11.5414 18.2496C11.0471 20.8546 8.49326 23.7232 8.46499 23.755L8.46472 23.7547C8.29636 23.942 8.2546 24.228 8.36117 24.4645C8.46773 24.7013 8.69873 24.8359 8.93438 24.7984L14.7481 23.8752C15.1488 23.8121 15.5564 23.9137 15.8945 24.1606C16.2329 24.4075 16.4785 24.7828 16.5853 25.2165L16.8262 26.1974C16.2782 26.4037 15.8023 26.7955 15.4647 27.318C15.1274 27.8402 14.9456 28.4667 14.9448 29.1097H9.96329C9.9622 28.1314 9.61201 27.1935 8.98933 26.5017C8.36667 25.8101 7.52268 25.4207 6.64209 25.4198L6.64211 25.4197ZM16.0513 29.1096C16.0519 28.6204 16.2271 28.1516 16.5383 27.8056C16.8498 27.4598 17.2717 27.2651 17.7119 27.2645C17.8657 27.2648 18.0187 27.2886 18.1664 27.3347C18.3134 27.3808 18.471 27.357 18.6018 27.2694C18.7325 27.1818 18.8248 27.0381 18.8569 26.8723C18.9654 26.3197 19.2961 25.8524 19.754 25.6055C20.2121 25.3584 20.7469 25.3584 21.205 25.6055C21.6629 25.8524 21.9936 26.3197 22.1021 26.8723C22.1342 27.0381 22.2265 27.1818 22.3572 27.2694C22.488 27.357 22.6456 27.3808 22.7926 27.3347C22.9403 27.2886 23.0933 27.2648 23.2468 27.2645C23.6871 27.2651 24.1093 27.4598 24.4204 27.8056C24.7319 28.1516 24.9068 28.6204 24.9074 29.1096H16.0513ZM6.64211 26.6496C7.22906 26.6505 7.79186 26.9099 8.20712 27.371C8.62213 27.8321 8.8556 28.4574 8.85614 29.1096H4.42807C4.4289 28.4574 4.66236 27.8321 5.07737 27.371C5.49237 26.9099 6.05514 26.6505 6.64211 26.6496ZM29.3355 35.2597H3.87461C3.43433 35.2591 3.01244 35.0644 2.70102 34.7186C2.38983 34.3726 2.2146 33.9038 2.21405 33.4146V32.7997H30.9965V33.4146C30.9959 33.9038 30.8207 34.3726 30.5095 34.7186C30.1983 35.0644 29.7761 35.2591 29.3359 35.2597H29.3355ZM32.1029 30.9546C32.1026 31.294 31.8552 31.5693 31.5495 31.5696H1.66032C1.3549 31.5693 1.10716 31.294 1.10689 30.9546V30.3397H32.1027L32.1029 30.9546Z" fill="#EFB100" />
                    </svg>
                    Makanan <small>8 Jenis</small>
                </button>
                <button class="menu-tab" data-kategori="minuman" id="tab-minuman">
                    <svg width="32" height="33" viewBox="0 0 32 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M31.0585 18.7001H28.5365L23.7591 13.4655C23.653 13.3495 23.5135 13.2606 23.3547 13.2084L21.0089 12.4374L21.2917 8.8H21.9604C22.1267 8.8 22.2864 8.74214 22.4041 8.63898C22.5218 8.53581 22.5878 8.39608 22.5878 8.25007V6.05008C22.5872 5.70893 22.4664 5.37651 22.2419 5.09786C22.0171 4.81948 21.6995 4.60878 21.3331 4.4947V1.65005C21.3325 1.21256 21.1338 0.793332 20.7811 0.483889C20.428 0.17467 19.9497 0.000544966 19.4507 0H3.13741C2.63832 0.000545805 2.15974 0.17467 1.80705 0.483889C1.45398 0.793381 1.25565 1.21261 1.25503 1.65005V4.4947C0.888253 4.60878 0.570682 4.81948 0.346224 5.09786C0.121431 5.37651 0.000629672 5.70892 0 6.05008V8.25007C0 8.39608 0.0663168 8.53581 0.184007 8.63898C0.3017 8.74214 0.461112 8.8 0.62768 8.8H1.29646L1.71148 14.141C1.07539 14.5253 0.677145 15.1451 0.631713 15.8211C0.586567 16.4969 0.899474 17.1532 1.47986 17.6C0.938108 18.0168 0.627727 18.618 0.627727 19.2501C0.627727 19.8819 0.938139 20.4831 1.47986 20.9001C0.938108 21.3169 0.627727 21.9181 0.627727 22.5499C0.627727 23.182 0.938139 23.7832 1.47986 24.2C0.996332 24.5725 0.694632 25.0941 0.637372 25.6566C0.580395 26.2188 0.772496 26.7788 1.17352 27.2204C1.57485 27.662 2.15429 27.9516 2.79225 28.0288L3.1391 32.4875C3.16152 32.7757 3.43488 32.9997 3.76491 33H18.8237C19.1534 33 19.427 32.7759 19.4495 32.4875L19.65 29.9155L23.7137 31.0042C24.6481 31.2539 25.6596 31.1453 26.4978 30.7053L28.4089 29.6999H31.0588C31.5781 29.6996 31.9994 29.3303 32 28.8748V19.5251C31.9994 19.0699 31.5778 18.7004 31.0585 18.7001ZM3.13767 1.09981H19.451C19.7972 1.10009 20.078 1.34626 20.0783 1.64975V4.39966H2.50967V1.64975C2.5103 1.34626 2.79113 1.10009 3.13767 1.09981ZM1.25526 6.04979C1.25557 5.74629 1.53641 5.50012 1.88263 5.49985H20.7059C21.0524 5.50012 21.3329 5.74629 21.3335 6.04979V7.69985H1.25495L1.25526 6.04979ZM2.55454 8.7997H20.0339L19.7824 12.0338L18.4703 11.6026C18.3645 11.5676 18.2524 11.5496 18.1391 11.5496H14.1174C13.4234 11.5505 12.7537 11.7753 12.2362 12.1809C11.7187 12.5867 11.3896 13.1451 11.3117 13.7496H3.13715C3.07053 13.7496 3.00452 13.7526 2.93914 13.757L2.55454 8.7997ZM18.879 23.65H7.81988C8.11067 23.2104 8.21841 22.6956 8.12469 22.194C8.03066 21.6926 7.74111 21.2352 7.30524 20.9001C7.84699 20.483 8.15769 19.8818 8.15769 19.25C8.15769 18.6179 7.84696 18.0167 7.30524 17.5999C7.74113 17.2648 8.03068 16.8074 8.12469 16.3058C8.21841 15.8041 8.11068 15.2897 7.81988 14.85H11.4568C11.652 15.332 12.0125 15.7496 12.4889 16.0449C12.9652 16.3405 13.5344 16.4993 14.1179 16.5001H17.2329L19.2308 19.1273L18.879 23.65ZM3.13719 20.3499C2.73181 20.3494 2.35196 20.1772 2.1172 19.8879C1.88213 19.5986 1.82079 19.2277 1.9528 18.892C2.08481 18.556 2.39336 18.2967 2.7819 18.1955C2.8971 18.1655 3.01666 18.1502 3.13715 18.1499H5.64691C6.09525 18.1499 6.50932 18.3598 6.73353 18.7001C6.95771 19.0404 6.95771 19.4596 6.73353 19.8C6.50936 20.1403 6.09527 20.3499 5.64691 20.3499L3.13719 20.3499ZM3.13719 14.85H5.64695C6.09529 14.85 6.50936 15.0596 6.73357 15.4C6.95778 15.7403 6.95774 16.1598 6.73357 16.5001C6.5094 16.8404 6.09531 17.05 5.64695 17.05H3.13719C2.68884 17.05 2.27446 16.8404 2.05024 16.5001C1.82603 16.1598 1.82607 15.7403 2.05024 15.4C2.27442 15.0596 2.68882 14.85 3.13719 14.85ZM1.88215 22.5501C1.88246 22.2586 2.01478 21.9789 2.25016 21.7728C2.48554 21.5665 2.80436 21.4505 3.1372 21.4502H5.64697C6.09531 21.4502 6.50938 21.6598 6.73359 22.0002C6.9578 22.3405 6.95776 22.7597 6.73359 23.1C6.50942 23.4406 6.09533 23.6502 5.64697 23.6502H3.1372C2.80437 23.6497 2.48555 23.5337 2.25016 23.3276C2.01478 23.1213 1.88246 22.8419 1.88215 22.5501ZM1.88215 25.85C1.88246 25.5582 2.01478 25.279 2.25016 25.0727C2.48554 24.8666 2.80436 24.7504 3.1372 24.7501H4.39192C4.84027 24.7501 5.25466 24.9597 5.47887 25.3C5.70304 25.6403 5.70304 26.0598 5.47887 26.4001C5.2547 26.7405 4.84029 26.9501 4.39192 26.9501H3.1372C2.80437 26.9498 2.48555 26.8338 2.25016 26.6275C2.01478 26.4214 1.88246 26.1414 1.88215 25.85ZM18.2369 31.9001H4.35062L4.05111 28.05L4.39203 28.0503C4.98234 28.0506 5.55365 27.8685 6.00606 27.5361C6.45814 27.2037 6.76233 26.7419 6.86449 26.2324C6.96665 25.7228 6.86044 25.198 6.56466 24.7501H18.7939L18.2369 31.9001ZM30.7443 28.6002H28.3214C28.1508 28.6002 27.9836 28.6409 27.8372 28.7178L25.8514 29.762C25.3181 30.0423 24.6742 30.1114 24.0796 29.9522L19.7369 28.7888L20.4898 19.1087C20.5035 18.9272 20.4487 18.7468 20.3341 18.5953L18.1839 15.7678C18.0095 15.538 17.715 15.4002 17.4002 15.4005H14.1171C13.5567 15.4005 13.0389 15.1385 12.7587 14.713C12.4784 14.2875 12.4784 13.7635 12.7587 13.338C13.0389 12.9125 13.5567 12.6505 14.1171 12.6505H18.0818L22.8329 14.2124L27.6507 19.491C27.8288 19.6872 28.0994 19.801 28.3855 19.8005H30.7443L30.7443 28.6002Z" fill="#EFB100" />
                    </svg>
                    Minuman <small>6 Jenis</small>
                </button>
                <button class="menu-tab" data-kategori="snack" id="tab-snack">
                    <svg width="38" height="33" viewBox="0 0 38 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.36647 14.7409L10.4626 15.2016C10.5653 15.8643 10.9512 16.4729 11.5486 16.9142C12.1464 17.3552 12.9145 17.5992 13.7103 17.6001H24.1914C25.0596 17.5992 25.8921 17.3094 26.5061 16.7939C27.12 16.2783 27.4656 15.5791 27.4666 14.8501V11.6793C27.8827 11.366 28.1207 10.9193 28.1216 10.4501V8.80004C28.122 8.30169 27.9604 7.81232 27.6549 7.38494L33.9178 0.896286L33.9182 0.896559C34.0771 0.731714 34.1093 0.504645 34.0004 0.313327C33.8915 0.122009 33.6614 1.38592e-05 33.4088 1.38592e-05H30.0363C29.8387 1.38592e-05 29.6518 0.0750654 29.5273 0.203887L23.8839 6.05011H21.4014L26.3761 0.896273L26.3764 0.896545C26.5354 0.7317 26.5676 0.504904 26.4587 0.313586C26.3501 0.121995 26.12 0 25.8674 0H22.4946C22.2973 0.000272902 22.1104 0.0750516 21.9859 0.203873L16.3425 6.0501H14.7549C12.6592 6.05365 10.7222 6.98786 9.66252 8.5058L8.00212 10.8958V10.896C7.54573 11.5524 7.43327 12.3382 7.69169 13.0665C7.95011 13.7952 8.55645 14.4014 9.36647 14.7409ZM8.94746 12.7527C8.81159 12.3761 8.84279 11.971 9.03523 11.6119L12.9912 13.4233L12.4464 14.2238V14.224C12.2735 14.4773 11.8954 14.5731 11.5844 14.4426L9.95227 13.7571C9.465 13.5548 9.10058 13.1907 8.94746 12.7527ZM11.0035 8.78745L14.6032 11.5352H14.6035C14.2336 11.762 13.9251 12.0518 13.6976 12.3853L13.6413 12.4678L9.68535 10.6564L10.7944 9.05977C10.8595 8.96617 10.9297 8.8756 11.0035 8.78745ZM19.5364 7.15018L21.1367 8.8963L19.5825 10.5063L16.2516 7.15018H19.5364ZM18.3678 11.0002L16.5406 10.9999C16.3313 11.0002 16.1223 11.0174 15.9165 11.0513L11.903 7.98771H11.9034C12.6598 7.48363 13.5859 7.19185 14.5506 7.15419L18.3678 11.0002ZM26.1566 14.8503V14.85C26.156 15.2875 25.9486 15.7067 25.5803 16.0162C25.2117 16.3254 24.7124 16.4995 24.1914 16.5H13.7103C13.3479 16.4998 12.9929 16.4154 12.6838 16.2563C12.375 16.0972 12.1247 15.8696 11.9599 15.5986C12.635 15.5751 13.2484 15.2621 13.5839 14.7695L14.8354 12.9308C15.0073 12.6787 15.2557 12.4691 15.5554 12.323C15.8548 12.177 16.1948 12.1003 16.541 12.1001H18.0444L17.8864 12.2638C17.4443 12.7218 17.327 13.3429 17.5783 13.8934C17.8295 14.4439 18.4117 14.8399 19.1051 14.9324C19.7984 15.0247 20.498 14.7995 20.9401 14.3415L23.1037 12.1001H26.157L26.1566 14.8503ZM26.8116 8.80017V10.4502V10.45C26.8113 10.6465 26.6865 10.8282 26.484 10.9267C26.3845 10.9753 26.2714 11.0004 26.1566 10.9999H24.165L26.7326 8.33975V8.34003C26.7846 8.48931 26.8112 8.64433 26.8116 8.79989L26.8116 8.80017ZM30.3483 1.10008H32.0348L19.9219 13.649C19.6927 13.8804 19.2841 13.9142 19.0055 13.7246C18.7269 13.5349 18.6817 13.1924 18.9041 12.9563L30.3483 1.10008ZM21.9843 8.01779L21.1892 7.15018H22.822L21.9843 8.01779ZM22.8067 1.10008H24.4935L19.7154 6.05004H18.0286L22.8067 1.10008Z" fill="#EFB100" />
                        <path d="M38 23.1H37.9981C37.9981 23.0942 37.9974 23.088 37.9974 23.0833C37.9811 21.7477 36.007 20.6579 32.129 19.8441C28.6142 19.1063 23.9514 18.7003 19.0001 18.7003C14.0487 18.7003 9.38617 19.1066 5.87113 19.8443C1.99311 20.6582 0.0190698 21.7479 0.00250986 23.0836C-0.0111425 23.3199 -0.0351965 25.0178 1.89142 26.7645C3.18909 27.9411 5.07834 28.8771 7.50657 29.5469C9.6091 30.1269 12.126 30.507 15.02 30.6844L15.0204 31.3501C15.021 31.7875 15.2284 32.2071 15.5967 32.5162C15.9653 32.8254 16.4646 32.9996 16.9857 33.0001H20.9163C21.4373 32.9996 21.9367 32.8254 22.3052 32.5162C22.6735 32.207 22.8809 31.7875 22.8812 31.3501V30.6901C25.8162 30.5155 28.3663 30.1334 30.4926 29.5469C32.9208 28.8772 34.81 27.9411 36.108 26.7645C37.9484 25.0959 38.0089 23.4728 37.9985 23.1248C37.9989 23.1166 37.9998 23.1084 37.9998 23.1L38 23.1ZM6.18912 20.9112C9.60288 20.1947 14.1525 19.8001 19.0002 19.8001C23.8479 19.8001 28.3977 20.1948 31.8112 20.9112C35.41 21.6666 36.6872 22.5626 36.6872 23.1C36.6872 23.1098 36.6872 23.1177 36.6878 23.1264C36.6592 23.5053 36.0143 24.0506 34.4101 24.5991C33.8312 23.8691 32.2885 23.2818 29.7377 22.8166C26.8482 22.2902 23.0177 22 18.9513 22C14.8848 22 11.054 22.2901 8.1649 22.8169C5.64662 23.2759 4.11071 23.854 3.51489 24.5714C1.93279 24.0193 1.31583 23.4732 1.31583 23.0999H1.31323C1.31323 22.5625 2.59009 21.6665 6.1895 20.9111L6.18912 20.9112ZM33.0736 24.9954C32.6897 25.0947 32.2707 25.193 31.814 25.2888C28.3999 26.0055 23.8503 26.4001 19.0029 26.4001C14.1553 26.4001 9.60538 26.0055 6.19187 25.2891C5.70557 25.187 5.26318 25.0825 4.85909 24.9763C5.3431 24.6766 6.42687 24.2593 8.44252 23.892C11.2429 23.3813 14.975 23.0999 18.951 23.0999C22.9268 23.0999 26.6589 23.3813 29.4595 23.892C31.5194 24.2672 32.6055 24.6946 33.0735 24.9957L33.0736 24.9954ZM21.5716 31.3501C21.5712 31.6538 21.278 31.8997 20.9166 31.9H16.9859C16.6245 31.8997 16.3313 31.6538 16.3309 31.3501V30.7496C17.1907 30.7832 18.0804 30.7998 19.0003 30.8001C19.8852 30.8001 20.7424 30.7846 21.5715 30.7534L21.5716 31.3501ZM35.1041 26.0552C31.7251 29.0676 24.4816 29.7 18.9994 29.7C9.06839 29.7 4.76031 27.7181 2.89544 26.0552C2.61427 25.806 2.36039 25.5358 2.13707 25.2478C3.07616 25.6599 4.32149 26.0295 5.87396 26.3559C9.38816 27.0938 14.0506 27.4999 19.0021 27.4999C23.9534 27.4999 28.616 27.0936 32.131 26.3559C33.6787 26.0311 34.9213 25.6629 35.8595 25.2513V25.2516C35.6368 25.5379 35.3839 25.8067 35.1041 26.0551L35.1041 26.0552Z" fill="#EFB100" />
                    </svg>
                    Snack <small>6 Jenis</small>
                </button>
            </div>
        </div>

        <div class="menu-grid">
            <div class="menu-card" data-kategori="makanan" data-tag="rekomendasi baru" data-open="popup-detail-menu">
                <div class="card-img"><img src="https://images.unsplash.com/photo-1512058564366-18510be2db19?w=300&h=200&fit=crop" alt="Nasi Goreng"></div>
                <div class="card-body">
                    <p class="card-judul">Nasi Goreng</p>
                    <p class="card-desc">Nasi goreng ayam, telur, sosis</p>
                    <p class="card-price">Rp 15.000</p>
                    <div class="qty-card"><span class="qty-btn plus"><svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                                <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
                            </svg></span><span class="qty-tambah">Tambah</span></div>
                </div>
            </div>
            <div class="menu-card" data-kategori="makanan" data-tag="bestseller" data-open="popup-detail-menu">
                <div class="card-img"><img src="https://images.unsplash.com/photo-1569050467447-ce54b3bbc37d?w=300&h=200&fit=crop" alt="Mie Bangladesh"></div>
                <div class="card-body">
                    <p class="card-judul">Mie Bangladesh</p>
                    <p class="card-desc">Mie goreng pedas khas warkop</p>
                    <p class="card-price">Rp 15.000</p>
                    <div class="qty-card"><span class="qty-btn plus"><svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                                <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
                            </svg></span><span class="qty-tambah">Tambah</span></div>
                </div>
            </div>
            <div class="menu-card" data-kategori="makanan" data-tag="bestseller promo" data-open="popup-detail-menu">
                <div class="card-img"><img src="https://images.unsplash.com/photo-1585032226651-759b368d7246?w=300&h=200&fit=crop" alt="Seblak Bandung"></div>
                <div class="card-body">
                    <p class="card-judul">Seblak Bandung</p>
                    <p class="card-desc">Seblak kerupuk kuah pedas</p>
                    <p class="card-price">Rp 15.000</p>
                    <div class="qty-card"><span class="qty-btn plus"><svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                                <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
                            </svg></span><span class="qty-tambah">Tambah</span></div>
                </div>
            </div>
            <div class="menu-card" data-kategori="makanan" data-tag="bestseller" data-open="popup-detail-menu">
                <div class="card-img"><img src="https://images.unsplash.com/photo-1555126634-323283e090fa?w=300&h=200&fit=crop" alt="Mie Jebew"></div>
                <div class="card-body">
                    <p class="card-judul">Mie Jebew</p>
                    <p class="card-desc">Mie goreng level pedas ekstrem</p>
                    <p class="card-price">Rp 15.000</p>
                    <div class="qty-card"><span class="qty-btn plus"><svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                                <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
                            </svg></span><span class="qty-tambah">Tambah</span></div>
                </div>
            </div>
            <div class="menu-card" data-kategori="makanan" data-tag="rekomendasi promo" data-open="popup-detail-menu">
                <div class="card-img"><img src="https://images.unsplash.com/photo-1603133872878-684f208fb84b?w=300&h=200&fit=crop" alt="Nasi Uduk"></div>
                <div class="card-body">
                    <p class="card-judul">Nasi Uduk</p>
                    <p class="card-desc">Nasi uduk + ayam goreng + lalapan</p>
                    <p class="card-price">Rp 18.000</p>
                    <div class="qty-card"><span class="qty-btn plus"><svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                                <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
                            </svg></span><span class="qty-tambah">Tambah</span></div>
                </div>
            </div>
            <div class="menu-card" data-kategori="makanan" data-tag="promo rekomendasi" data-open="popup-detail-menu">
                <div class="card-img"><img src="https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=300&h=200&fit=crop" alt="Burger Warkop"></div>
                <div class="card-body">
                    <p class="card-judul">Burger Warkop</p>
                    <p class="card-desc">Beef patty + saus spesial warkop</p>
                    <p class="card-price">Rp 22.000</p>
                    <div class="qty-card"><span class="qty-btn plus"><svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                                <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
                            </svg></span><span class="qty-tambah">Tambah</span></div>
                </div>
            </div>
            <div class="menu-card" data-kategori="makanan" data-tag="rekomendasi bestseller" data-open="popup-detail-menu">
                <div class="card-img"><img src="https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=300&h=200&fit=crop" alt="Indomie Nyemek"></div>
                <div class="card-body">
                    <p class="card-judul">Indomie Nyemek</p>
                    <p class="card-desc">Indomie kuah kurang, extra telur</p>
                    <p class="card-price">Rp 12.000</p>
                    <div class="qty-card"><span class="qty-btn plus"><svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                                <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
                            </svg></span><span class="qty-tambah">Tambah</span></div>
                </div>
            </div>
            <div class="menu-card" data-kategori="makanan" data-tag="promo bestseller" data-open="popup-detail-menu">
                <div class="card-img"><img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=300&h=200&fit=crop" alt="Nasi Campur"></div>
                <div class="card-body">
                    <p class="card-judul">Nasi Campur</p>
                    <p class="card-desc">Nasi + 3 lauk pilihan + sayur</p>
                    <p class="card-price">Rp 20.000</p>
                    <div class="qty-card"><span class="qty-btn plus"><svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                                <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
                            </svg></span><span class="qty-tambah">Tambah</span></div>
                </div>
            </div>
            <div class="menu-card" data-kategori="minuman" data-tag="promo rekomendasi" data-open="popup-detail-menu">
                <div class="card-img"><img src="https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=300&h=200&fit=crop" alt="Es Kopi ABC"></div>
                <div class="card-body">
                    <p class="card-judul">Es Kopi ABC</p>
                    <p class="card-desc">Kopi susu dingin khas warkop</p>
                    <p class="card-price">Rp 12.000</p>
                    <div class="qty-card"><span class="qty-btn plus"><svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                                <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
                            </svg></span><span class="qty-tambah">Tambah</span></div>
                </div>
            </div>
            <div class="menu-card" data-kategori="minuman" data-tag="rekomendasi" data-open="popup-detail-menu">
                <div class="card-img"><img src="https://images.unsplash.com/photo-1544145945-f90425340c7e?w=300&h=200&fit=crop" alt="Teh Tarik"></div>
                <div class="card-body">
                    <p class="card-judul">Teh Tarik</p>
                    <p class="card-desc">Teh susu kental manis hangat</p>
                    <p class="card-price">Rp 8.000</p>
                    <div class="qty-card"><span class="qty-btn plus"><svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                                <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
                            </svg></span><span class="qty-tambah">Tambah</span></div>
                </div>
            </div>
            <div class="menu-card" data-kategori="minuman" data-tag="bestseller baru" data-open="popup-detail-menu">
                <div class="card-img"><img src="https://images.unsplash.com/photo-1497534446932-c925b458314e?w=300&h=200&fit=crop" alt="Jus Alpukat"></div>
                <div class="card-body">
                    <p class="card-judul">Jus Alpukat</p>
                    <p class="card-desc">Jus alpukat segar + susu kental</p>
                    <p class="card-price">Rp 12.000</p>
                    <div class="qty-card"><span class="qty-btn plus"><svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                                <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
                            </svg></span><span class="qty-tambah">Tambah</span></div>
                </div>
            </div>
            <div class="menu-card" data-kategori="minuman" data-tag="promo rekomendasi" data-open="popup-detail-menu">
                <div class="card-img"><img src="https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=300&h=200&fit=crop" alt="Americano"></div>
                <div class="card-body">
                    <p class="card-judul">Americano</p>
                    <p class="card-desc">Kopi hitam espresso double shot</p>
                    <p class="card-price">Rp 15.000</p>
                    <div class="qty-card"><span class="qty-btn plus"><svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                                <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
                            </svg></span><span class="qty-tambah">Tambah</span></div>
                </div>
            </div>
            <div class="menu-card" data-kategori="minuman" data-tag="rekomendasi bestseller" data-open="popup-detail-menu">
                <div class="card-img"><img src="https://images.unsplash.com/photo-1550583724-b2692b85b150?w=300&h=200&fit=crop" alt="Susu Cokelat"></div>
                <div class="card-body">
                    <p class="card-judul">Susu Cokelat</p>
                    <p class="card-desc">Susu full cream + cokelat bubuk</p>
                    <p class="card-price">Rp 10.000</p>
                    <div class="qty-card"><span class="qty-btn plus"><svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                                <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
                            </svg></span><span class="qty-tambah">Tambah</span></div>
                </div>
            </div>
            <div class="menu-card" data-kategori="minuman" data-tag="rekomendasi baru" data-open="popup-detail-menu">
                <div class="card-img"><img src="https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=300&h=200&fit=crop" alt="Matcha Latte"></div>
                <div class="card-body">
                    <p class="card-judul">Matcha Latte</p>
                    <p class="card-desc">Matcha Jepang + susu oat creamy</p>
                    <p class="card-price">Rp 18.000</p>
                    <div class="qty-card"><span class="qty-btn plus"><svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                                <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
                            </svg></span><span class="qty-tambah">Tambah</span></div>
                </div>
            </div>
            <div class="menu-card" data-kategori="snack" data-tag="rekomendasi baru" data-open="popup-detail-menu">
                <div class="card-img"><img src="https://images.unsplash.com/photo-1548940740-204726a19be3?w=300&h=200&fit=crop" alt="Pancong Cokelat"></div>
                <div class="card-body">
                    <p class="card-judul">Pancong Cokelat</p>
                    <p class="card-desc">Kue pancong isi cokelat lumer</p>
                    <p class="card-price">Rp 10.000</p>
                    <div class="qty-card"><span class="qty-btn plus"><svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                                <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
                            </svg></span><span class="qty-tambah">Tambah</span></div>
                </div>
            </div>
            <div class="menu-card" data-kategori="snack" data-tag="bestseller" data-open="popup-detail-menu">
                <div class="card-img"><img src="https://images.unsplash.com/photo-1600628421066-f6e6e1e5a9c7?w=300&h=200&fit=crop" alt="Keju Aroma"></div>
                <div class="card-body">
                    <p class="card-judul">Keju Aroma</p>
                    <p class="card-desc">Snack keju goreng renyah gurih</p>
                    <p class="card-price">Rp 10.000</p>
                    <div class="qty-card"><span class="qty-btn plus"><svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                                <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
                            </svg></span><span class="qty-tambah">Tambah</span></div>
                </div>
            </div>
            <div class="menu-card" data-kategori="snack" data-tag="rekomendasi promo" data-open="popup-detail-menu">
                <div class="card-img"><img src="https://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=300&h=200&fit=crop" alt="Risoles Mayo"></div>
                <div class="card-body">
                    <p class="card-judul">Risoles Mayo</p>
                    <p class="card-desc">Risoles isi mayo telur keju</p>
                    <p class="card-price">Rp 8.000</p>
                    <div class="qty-card"><span class="qty-btn plus"><svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                                <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
                            </svg></span><span class="qty-tambah">Tambah</span></div>
                </div>
            </div>
            <div class="menu-card" data-kategori="snack" data-tag="rekomendasi bestseller" data-open="popup-detail-menu">
                <div class="card-img"><img src="https://images.unsplash.com/photo-1589301760014-d929f3979dbc?w=300&h=200&fit=crop" alt="Pisang Goreng"></div>
                <div class="card-body">
                    <p class="card-judul">Pisang Goreng</p>
                    <p class="card-desc">Pisang goreng crispy topping keju</p>
                    <p class="card-price">Rp 8.000</p>
                    <div class="qty-card"><span class="qty-btn plus"><svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                                <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
                            </svg></span><span class="qty-tambah">Tambah</span></div>
                </div>
            </div>
            <div class="menu-card" data-kategori="snack" data-tag="baru rekomendasi" data-open="popup-detail-menu">
                <div class="card-img"><img src="https://images.unsplash.com/photo-1499636136210-6f4ee915583e?w=300&h=200&fit=crop" alt="Cookies Susu"></div>
                <div class="card-body">
                    <p class="card-judul">Cookies Susu</p>
                    <p class="card-desc">Cookies butter lembut isi susu</p>
                    <p class="card-price">Rp 12.000</p>
                    <div class="qty-card"><span class="qty-btn plus"><svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                                <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
                            </svg></span><span class="qty-tambah">Tambah</span></div>
                </div>
            </div>
            <div class="menu-card" data-kategori="snack" data-tag="bestseller promo" data-open="popup-detail-menu">
                <div class="card-img"><img src="https://images.unsplash.com/photo-1541592106381-b31e9677c0e5?w=300&h=200&fit=crop" alt="Cireng Isi"></div>
                <div class="card-body">
                    <p class="card-judul">Cireng Isi</p>
                    <p class="card-desc">Cireng isi oncom + sambel kacang</p>
                    <p class="card-price">Rp 8.000</p>
                    <div class="qty-card"><span class="qty-btn plus"><svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                                <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
                            </svg></span><span class="qty-tambah">Tambah</span></div>
                </div>
            </div>
        </div>
    </section>
</main>

<aside class="cart-sidebar">
    <div class="cart-header">
        <div class="cart-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                <path d="M6 2L3 6V20C3 21.1 3.9 22 5 22H19C20.1 22 21 21.1 21 20V6L18 2H6Z" stroke="white" stroke-width="1.8" stroke-linejoin="round" />
                <path d="M3 6H21" stroke="white" stroke-width="1.8" />
                <path d="M16 10C16 12.2 14.2 14 12 14C9.8 14 8 12.2 8 10" stroke="white" stroke-width="1.8" stroke-linecap="round" />
            </svg>
            Pesanan Saat Ini
        </div>
        <button class="clear-btn" data-open="popup-hapus-keranjang">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                <path d="M3 6H5H21" stroke="white" stroke-width="2" stroke-linecap="round" />
                <path d="M8 6V4H16V6M19 6V20C19 21.1 18.1 22 17 22H7C5.9 22 5 21.1 5 20V6H19Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>
    </div>

    <div class="cart-content">
        <div class="customer-section">
            <p class="cart-section-label">Informasi Pelanggan</p>
            <input type="text" class="customer-input" placeholder="Nama Pelanggan">
        </div>

        <div class="order-details">
            <p class="cart-section-label">Detail Pesanan</p>
            <!-- <div class="item-card-mini">
                <img src="https://images.unsplash.com/photo-1512058564366-18510be2db19?w=80&h=80&fit=crop" class="mini-img" alt="Nasgor">
                <div class="mini-info">
                    <div class="mini-header">
                        <p class="mini-name">Nasgor</p>
                        <button class="delete-item">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                                <path d="M3 6H5H21" stroke="#ef4444" stroke-width="2" stroke-linecap="round" />
                                <path d="M8 6V4H16V6M19 6V20C19 21.1 18.1 22 17 22H7C5.9 22 5 21.1 5 20V6H19Z" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>
                    <p class="mini-desc">Nasi goreng ayam suwir</p>
                    <div class="badge-note"><svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                            <rect x="4" y="2" width="16" height="20" rx="2" stroke="#854d0e" stroke-width="1.8" />
                            <path d="M8 7H16M8 11H16M8 15H12" stroke="#854d0e" stroke-width="1.8" stroke-linecap="round" />
                        </svg>Pedas</div>
                    <div class="mini-price-row">
                        <p class="mini-price">Rp. 15.000</p>
                        <div class="mini-qty">
                            <button class="qty-btn minus">-</button>
                            <span class="qty-val">1</span>
                            <button class="qty-btn plus">+</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="item-card-mini">
                <img src="https://images.unsplash.com/photo-1569050467447-ce54b3bbc37d?w=80&h=80&fit=crop" class="mini-img" alt="Mie Bangladesh">
                <div class="mini-info">
                    <div class="mini-header">
                        <p class="mini-name">Mie Bangladesh</p>
                        <button class="delete-item">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                                <path d="M3 6H5H21" stroke="#ef4444" stroke-width="2" stroke-linecap="round" />
                                <path d="M8 6V4H16V6M19 6V20C19 21.1 18.1 22 17 22H7C5.9 22 5 21.1 5 20V6H19Z" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>
                    <p class="mini-desc">Mie goreng pedas khas warkop</p>
                    <div class="badge-note"><svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                            <rect x="4" y="2" width="16" height="20" rx="2" stroke="#854d0e" stroke-width="1.8" />
                            <path d="M8 7H16M8 11H16M8 15H12" stroke="#854d0e" stroke-width="1.8" stroke-linecap="round" />
                        </svg>Pedas</div>
                    <div class="mini-price-row">
                        <p class="mini-price">Rp. 15.000</p>
                        <div class="mini-qty">
                            <button class="qty-btn minus">-</button>
                            <span class="qty-val">1</span>
                            <button class="qty-btn plus">+</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="item-card-mini">
                <img src="https://images.unsplash.com/photo-1585032226651-759b368d7246?w=80&h=80&fit=crop" class="mini-img" alt="Seblak Bandung">
                <div class="mini-info">
                    <div class="mini-header">
                        <p class="mini-name">Seblak Bandung</p>
                        <button class="delete-item">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                                <path d="M3 6H5H21" stroke="#ef4444" stroke-width="2" stroke-linecap="round" />
                                <path d="M8 6V4H16V6M19 6V20C19 21.1 18.1 22 17 22H7C5.9 22 5 21.1 5 20V6H19Z" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>
                    <p class="mini-desc">Seblak kerupuk kuah pedas</p>
                    <div class="badge-note"><svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                            <rect x="4" y="2" width="16" height="20" rx="2" stroke="#854d0e" stroke-width="1.8" />
                            <path d="M8 7H16M8 11H16M8 15H12" stroke="#854d0e" stroke-width="1.8" stroke-linecap="round" />
                        </svg>Pedas</div>
                    <div class="mini-price-row">
                        <p class="mini-price">Rp. 15.000</p>
                        <div class="mini-qty">
                            <button class="qty-btn minus">-</button>
                            <span class="qty-val">1</span>
                            <button class="qty-btn plus">+</button>
                        </div>
                    </div>
                </div>
            </div> -->
            <div class="cart-empty" id="cart-empty-msg" style="display: flex; flex-direction: column; align-items: center; gap: 10px; padding: 24px 0; color: #aaa; font-size: 13px;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none"><path d="M6 2L3 6V20C3 21.1 3.9 22 5 22H19C20.1 22 21 21.1 21 20V6L18 2H6Z" stroke="#ddd" stroke-width="1.8" stroke-linejoin="round"/><path d="M3 6H21" stroke="#ddd" stroke-width="1.8"/></svg>
                <p>Keranjang masih kosong</p>
            </div>
        </div>
    </div>

    <div class="summary-section">
        <p class="summary-label">Ringkasan Order</p>
        <div class="summary-card">
            <div class="summary-row"><span class="label">Subtotal</span><span class="value">Rp45.000</span></div>
            <div class="summary-row"><span class="label">Tax (10%)</span><span class="value">Rp4.500</span></div>
            <div class="garis"></div>
            <div class="summary-row total"><span class="label total-label">Total</span><span class="value total-value">Rp49.500</span></div>
        </div>
    </div>

    <div class="cart-footer">
        <button class="order-btn" data-open="popup-payment">Order Now</button>
    </div>
</aside>

@endsection

@section('page_popups')
    <div class="popup" id="popup-payment">
      <div class="popup-header">
        <span>Pilih Metode Pembayaran</span>
        <button class="popup-close" data-close>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="#555" stroke-width="1.8"/><path d="M15 9L9 15M9 9L15 15" stroke="#555" stroke-width="1.8" stroke-linecap="round"/></svg>
        </button>
      </div>
      <div class="popup-body">
        <p class="popup-total-label">Total Tagihan:</p>
        <p class="popup-total-amount">Rp 49.500</p>
        <button class="payment-option" data-open="popup-qris-confirm">
          <div class="payment-option-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="7" height="7" rx="1" stroke="#eab308" stroke-width="1.8"/><rect x="14" y="3" width="7" height="7" rx="1" stroke="#eab308" stroke-width="1.8"/><rect x="3" y="14" width="7" height="7" rx="1" stroke="#eab308" stroke-width="1.8"/><rect x="5" y="5" width="3" height="3" fill="#eab308"/><rect x="16" y="5" width="3" height="3" fill="#eab308"/><rect x="5" y="16" width="3" height="3" fill="#eab308"/><path d="M14 14H17M17 14V17M17 17H20M20 17V20M14 17H15M15 20H20" stroke="#eab308" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </div>
          <span class="payment-option-text">QRIS</span>
          <div class="payment-option-arrow"><svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M9 18L15 12L9 6" stroke="#ccc" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
        </button>

        <button class="payment-option" data-open="popup-tunai">
          <div class="payment-option-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none"><rect x="2" y="6" width="20" height="13" rx="2" stroke="#eab308" stroke-width="1.8"/><path d="M2 10H22" stroke="#eab308" stroke-width="1.8"/><circle cx="12" cy="15" r="2" stroke="#eab308" stroke-width="1.5"/></svg>
          </div>
          <span class="payment-option-text">CASH</span>
          <div class="payment-option-arrow"><svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M9 18L15 12L9 6" stroke="#ccc" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
        </button>
      </div>
    </div>

    <div class="popup popup-confirm" id="popup-qris-confirm">
      <button class="popup-close popup-close-float" data-close>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="#555" stroke-width="1.8"/><path d="M15 9L9 15M9 9L15 15" stroke="#555" stroke-width="1.8" stroke-linecap="round"/></svg>
      </button>
      <div class="popup-body popup-body-confirm">
        <h2 class="confirm-title">Lanjutkan Pembayaran via QRIS?</h2>

        <div class="konfir-qris-image">
          <img src="{{ asset('image/img_konfirqris.png') }}" alt="QRIS konfir" style="height: 40%;">
        </div>
        <p class="confirm-desc">Pastikan pesanan yang kamu pilih, serta metode pembayaran sudah tepat.</p>

        <button class="popup-btn" data-open="popup-qris-code">Lanjutkan Pembayaran</button>
      </div>
    </div>

    <div class="popup" id="popup-qris-code">
      <div class="popup-header popup-header-yellow">
        <span>Pembayaran QRIS</span>
        <button class="popup-close popup-close-white" data-close>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="#fff" stroke-width="1.8"/><path d="M15 9L9 15M9 9L15 15" stroke="#fff" stroke-width="1.8" stroke-linecap="round"/></svg>
        </button>
      </div>
      <div class="popup-body">
        <p class="popup-total-label">Total Tagihan:</p>
        <p class="popup-total-amount">Rp 49.500</p>

        <div class="qris-logo"><img src="{{ asset('image/Logo_QRIS.png') }}" alt="QRIS Logo" style="height: 40px;"></div>
        <div class="qr-code-box">
          <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=TskuyKasirPembayaran49500" alt="QR Code" class="qr-img">
        </div>
        <p class="qris-waiting">Menunggu pelanggan scan kode...</p>

        <button class="popup-btn" data-open="popup-success">Cek Status Pembayaran</button>
      </div>
    </div>

    <div class="popup" id="popup-tunai">
      <div class="popup-header popup-header-yellow">
        <span>Pembayaran Tunai</span>
        <button class="popup-close popup-close-white" data-close>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="#fff" stroke-width="1.8"/><path d="M15 9L9 15M9 9L15 15" stroke="#fff" stroke-width="1.8" stroke-linecap="round"/></svg>
        </button>
      </div>
      <div class="popup-body">
        <p class="popup-total-label">Total Tagihan:</p>
        <p class="popup-total-amount">Rp 49.500</p>

        <p class="tunai-label">Uang Diterima:</p>
        <input type="text" class="tunai-input" value="Rp50.000,-" readonly>

        <div class="tunai-presets">
          <button class="preset-btn">Rp10.000,-</button>
          <button class="preset-btn">Rp20.000,-</button>
          <button class="preset-btn preset-active">Rp50.000,-</button>
          <button class="preset-btn">Rp100.000,-</button>
        </div>

        <p class="tunai-label">Total kembali:</p>
        <div class="tunai-kembalian">Rp500,-</div>
        <button class="popup-btn" data-open="popup-success">Bayar</button>
      </div>
    </div>

    <div class="popup" id="popup-success">
      <button class="popup-close popup-close-float" data-close>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="#555" stroke-width="1.8"/><path d="M15 9L9 15M9 9L15 15" stroke="#555" stroke-width="1.8" stroke-linecap="round"/></svg>
      </button>
      <div class="popup-body">
        <h2 class="success-title">Pembayaran Berhasil!</h2>
        <div class="success-icon"><svg width="48" height="48" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17L4 12" stroke="#22c55e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
        <div class="struk-divider"></div>
        <div class="struk-rows">
          <div class="struk-row"><span class="struk-label">ID Transaksi:</span><span class="struk-value">#T0945</span></div>
          <div class="struk-row"><span class="struk-label">Tipe:</span><span class="struk-value">Dine in</span></div>
          <div class="struk-row"><span class="struk-label">Waktu Pemesanan:</span><span class="struk-value">08.35 WIB</span></div>
        </div>
        <div class="struk-divider"></div>
        <div class="struk-rows">
          <div class="struk-row"><span class="struk-label">Subtotal:</span><span class="struk-value">Rp45.000,-</span></div>
          <div class="struk-row"><span class="struk-label">Tax (10%):</span><span class="struk-value">Rp4.500,-</span></div>
          <div class="struk-row"><span class="struk-label">Total Tagihan:</span><span class="struk-value struk-bold">Rp49.500,-</span></div>
          <div class="struk-row"><span class="struk-label">Uang Tunai:</span><span class="struk-value struk-bold">Rp50.000,-</span></div>
        </div>
        <div class="struk-divider"></div>
        <div class="struk-row struk-kembalian"><span class="struk-label">Uang Kembali:</span><span class="struk-value struk-bold">Rp500,-</span></div>
        <p class="struk-printing">Sedang Mencetak Struk....</p>
        <button class="popup-btn popup-btn-selesai" data-close>Selesai</button>
      </div>
    </div>

    <div class="popup popup-warning" id="popup-hapus-keranjang">
      <button class="popup-close popup-close-float" data-close>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="#555" stroke-width="1.8"/><path d="M15 9L9 15M9 9L15 15" stroke="#555" stroke-width="1.8" stroke-linecap="round"/></svg>
      </button>
      <div class="popup-body popup-body-warning">
        <div class="warning-icon-wrap"><svg width="32" height="32" viewBox="0 0 24 24" fill="none"><path d="M12 9V13M12 17H12.01" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/></svg></div>
        <h3 class="warning-title">Apa kamu yakin untuk menghapus semua item di keranjang?</h3>
        <p class="warning-desc">Semua item yang telah dipilih akan di hapus dari keranjang</p>
        <div class="warning-actions">
          <button class="btn-batal-warning" data-close>Batal</button>
          <button class="btn-confirm-warning" data-close>Ya, Hapus Semua Item</button>
        </div>
      </div>
    </div>

    <div class="popup popup-warning" id="popup-batal-pesanan">
      <div class="popup-body popup-body-warning">
        <div class="warning-icon-wrap"><svg width="32" height="32" viewBox="0 0 24 24" fill="none"><path d="M12 9V13M12 17H12.01" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/></svg></div>
        <h3 class="warning-title">Apa kamu yakin ingin membatalkan pesanan #T0945 (MEJA 4)?</h3>
        <p class="warning-desc">Tindakan ini akan membatalkan pesanan dan tidak dapat dikembalikan</p>
        <div class="warning-actions">

          <button class="btn-batal-warning" data-close>Tidak</button>
          <button class="btn-confirm-warning" data-open="popup-alasan-batal">Ya, Batalkan Pesanan</button>
        </div>
      </div>
    </div>

    <div class="popup" id="popup-alasan-batal">
      <div class="popup-header popup-header-yellow">
        <span>Konfirmasi Batalkan Pesanan</span>
        <button class="popup-close popup-close-white" data-close>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="#fff" stroke-width="1.8"/><path d="M15 9L9 15M9 9L15 15" stroke="#fff" stroke-width="1.8" stroke-linecap="round"/></svg>
        </button>
      </div>
      <div class="popup-body">
        <p class="alasan-intro">Pesanan <strong>#T0945 (MEJA 4)</strong> akan dibatalkan sepenuhnya. Mohon pilih alasan:</p>
        <div class="alasan-list">
          <label class="alasan-option"><input type="radio" name="alasan" value="stok"><span class="alasan-text">Stok Habis/ Bahan Kosong</span></label>
          <label class="alasan-option"><input type="radio" name="alasan" value="ganti" checked><span class="alasan-text">Pelanggan Minta Ganti Menu</span></label>
          <label class="alasan-option"><input type="radio" name="alasan" value="salah"><span class="alasan-text">Kesalahan Input</span></label>
          <label class="alasan-option"><input type="radio" name="alasan" value="lain"><span class="alasan-text">Lainnya</span></label>
          <input type="text" class="alasan-input" placeholder="Ketikkan Alasan Di Sini">
        </div>
        <p class="alasan-note">Uang yang telah diterima akan dikembalikan manual</p>
        <button class="popup-btn popup-btn-danger" data-close>Batalkan Pesanan</button>
        <button class="btn-kembali" data-close>Kembali</button>
      </div>
    </div>

    <div class="popup" id="popup-detail-antrian">
      <div class="popup-header popup-header-yellow">
        <span>Detail Pesanan</span>
        <button class="popup-close popup-close-white" data-close>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="#fff" stroke-width="1.8"/><path d="M15 9L9 15M9 9L15 15" stroke="#fff" stroke-width="1.8" stroke-linecap="round"/></svg>
        </button>
      </div>
      <div class="popup-body">
        <h3 class="detail-antrian-title">Detail Pesanan: MEJA 4</h3>
        <p class="detail-antrian-meta">Id: #T0945 &nbsp;|&nbsp; Tipe: Dine In &nbsp;|&nbsp; Masuk: 08.35</p>
        <div class="detail-item-list">
          <div class="detail-item">
            <p class="detail-item-name">[1X] Kopi Susu Aren</p>
            <div class="detail-item-note"><svg width="13" height="13" viewBox="0 0 24 24" fill="none"><rect x="4" y="2" width="16" height="20" rx="2" stroke="#92400e" stroke-width="1.8"/><path d="M8 7H16M8 11H16M8 15H12" stroke="#92400e" stroke-width="1.8" stroke-linecap="round"/></svg>Less Sugar</div>
          </div>
          <div class="detail-item-divider"></div>
          <div class="detail-item">
            <p class="detail-item-name">[1X] Indomie Nyemek</p>
            <div class="detail-item-note"><svg width="13" height="13" viewBox="0 0 24 24" fill="none"><rect x="4" y="2" width="16" height="20" rx="2" stroke="#92400e" stroke-width="1.8"/><path d="M8 7H16M8 11H16M8 15H12" stroke="#92400e" stroke-width="1.8" stroke-linecap="round"/></svg>Pedas lv.3, telur setengah mateng</div>
          </div>
        </div>
        <button class="popup-btn popup-btn-green" data-close>Tandai Sebagai Selesai</button>

        <button class="popup-btn popup-btn-danger" data-open="popup-alasan-batal">Batalkan Pesanan</button>
      </div>
    </div>

    <div class="popup popup-menu-detail" id="popup-detail-menu">
      <button class="popup-close popup-close-float" data-close>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="#555" stroke-width="1.8"/><path d="M15 9L9 15M9 9L15 15" stroke="#555" stroke-width="1.8" stroke-linecap="round"/></svg>
      </button>
      <div class="menu-detail-inner">
        <div class="menu-detail-img-wrap">
          <img src="https://images.unsplash.com/photo-1512058564366-18510be2db19?w=400&h=400&fit=crop" alt="Nasi Goreng" class="menu-detail-img">
        </div>
        <div class="menu-detail-info">
          <h2 class="menu-detail-name">Nasi Goreng</h2>
          <p class="menu-detail-price">Rp15.000,-</p>
          <p class="menu-detail-desc">Nasi goreng ayam suwir dengan telur dan potongan sosis.</p>
          <div class="menu-detail-row">
            <span class="menu-detail-label">Jumlah Porsi</span>
            <div class="menu-detail-qty">
              <button class="qty-btn minus">-</button>
              <span class="qty-val">1</span>
              <button class="qty-btn plus">+</button>
            </div>
          </div>
          <div class="menu-detail-section">
            <p class="menu-detail-label">Level Pedas</p>
            <p class="menu-detail-wajib">Wajib memilih level</p>
            <div class="level-list">
              <label class="level-option"><span>LEVEL 0</span><input type="radio" name="level-pedas" class="level-check"></label>
              <label class="level-option"><span>LEVEL 1</span><input type="radio" name="level-pedas" class="level-check"></label>
              <label class="level-option"><span>LEVEL 2</span><input type="radio" name="level-pedas" class="level-check"></label>
              <label class="level-option"><span>LEVEL 3</span><input type="radio" name="level-pedas" class="level-check"></label>
              <label class="level-option"><span>LEVEL 4</span><input type="radio" name="level-pedas" class="level-check"></label>
              <label class="level-option"><span>LEVEL 5</span><input type="radio" name="level-pedas" class="level-check"></label>
            </div>
          </div>
        </div>
      </div>
      <div class="menu-detail-catatan">
        <p class="menu-detail-label">Catatan Khusus</p>
        <input type="text" class="catatan-input" placeholder="Contoh: Sayur banyakin, telornya di acak">
      </div>
      <div class="menu-detail-footer">
        <button class="popup-btn" data-close>Tambahkan ke Keranjang</button>
      </div>
    </div>

    <div class="popup popup-antrian-full" id="popup-antrian-full">
      <div class="antrian-full-header">
        <h2 class="antrian-full-title">Antrian</h2>
        <button class="antrian-close" data-close>
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M18 6L6 18M6 6L18 18" stroke="#333" stroke-width="2.2" stroke-linecap="round"/></svg>
        </button>
      </div>

      <div class="antrian-tabs">
        <button class="antrian-tab antrian-tab-active" data-filter="all">All <span class="tab-badge tab-badge-yellow">15</span></button>
        <button class="antrian-tab" data-filter="dine">Dine in <span class="tab-badge tab-badge-outline">10</span></button>
        <button class="antrian-tab" data-filter="takeaway">Take away <span class="tab-badge tab-badge-outline">5</span></button>
        <button class="antrian-tab" data-filter="served">Served <span class="tab-badge tab-badge-outline">0</span></button>
      </div>

      <div class="antrian-full-grid" id="antrian-grid">
        <div class="antrian-card antrian-green" data-type="dine" data-open="popup-detail-antrian">
          <div class="antrian-card-top"><span class="antrian-table">Table 10</span><span class="antrian-time">Just now</span></div>
          <span class="antrian-order-id">Order #T0123</span>
          <span class="badge badge-dine">Dine in</span>
        </div>
        <div class="antrian-card antrian-green" data-type="takeaway" data-open="popup-detail-antrian">
          <div class="antrian-card-top"><span class="antrian-table">Table 02</span><span class="antrian-time">2 min ago</span></div>
          <span class="antrian-order-id">Order #T0122</span>
          <span class="badge badge-takeaway">Take away</span>
        </div>
        <div class="antrian-card antrian-green" data-type="dine" data-open="popup-detail-antrian">
          <div class="antrian-card-top"><span class="antrian-table">Table 07</span><span class="antrian-time">4 min ago</span></div>
          <span class="antrian-order-id">Order #T0124</span>
          <span class="badge badge-dine">Dine in</span>
        </div>
        <div class="antrian-card antrian-green" data-type="dine" data-open="popup-detail-antrian">
          <div class="antrian-card-top"><span class="antrian-table">Table 03</span><span class="antrian-time">6 min ago</span></div>
          <span class="antrian-order-id">Order #T0125</span>
          <span class="badge badge-dine">Dine in</span>
        </div>
        <div class="antrian-card antrian-green" data-type="takeaway" data-open="popup-detail-antrian">
          <div class="antrian-card-top"><span class="antrian-table">Table 11</span><span class="antrian-time">8 min ago</span></div>
          <span class="antrian-order-id">Order #T0126</span>
          <span class="badge badge-takeaway">Take away</span>
        </div>
        <div class="antrian-card antrian-green" data-type="dine" data-open="popup-detail-antrian">
          <div class="antrian-card-top"><span class="antrian-table">Table 05</span><span class="antrian-time">10 min ago</span></div>
          <span class="antrian-order-id">Order #T0127</span>
          <span class="badge badge-dine">Dine in</span>
        </div>
        <div class="antrian-card antrian-yellow" data-type="dine" data-open="popup-detail-antrian">
          <div class="antrian-card-top"><span class="antrian-table">Table 15</span><span class="antrian-time">12 min ago</span></div>
          <span class="antrian-order-id">Order #T0121</span>
          <span class="badge badge-dine">Dine in</span>
        </div>
        <div class="antrian-card antrian-yellow" data-type="takeaway" data-open="popup-detail-antrian">
          <div class="antrian-card-top"><span class="antrian-table">Table 09</span><span class="antrian-time">14 min ago</span></div>
          <span class="antrian-order-id">Order #T0128</span>
          <span class="badge badge-takeaway">Take away</span>
        </div>
        <div class="antrian-card antrian-yellow" data-type="dine" data-open="popup-detail-antrian">
          <div class="antrian-card-top"><span class="antrian-table">Table 04</span><span class="antrian-time">16 min ago</span></div>
          <span class="antrian-order-id">Order #T0129</span>
          <span class="badge badge-dine">Dine in</span>
        </div>
        <div class="antrian-card antrian-yellow" data-type="dine" data-open="popup-detail-antrian">
          <div class="antrian-card-top"><span class="antrian-table">Table 08</span><span class="antrian-time">18 min ago</span></div>
          <span class="antrian-order-id">Order #T0130</span>
          <span class="badge badge-dine">Dine in</span>
        </div>
        <div class="antrian-card antrian-yellow" data-type="takeaway" data-open="popup-detail-antrian">
          <div class="antrian-card-top"><span class="antrian-table">Table 13</span><span class="antrian-time">20 min ago</span></div>
          <span class="antrian-order-id">Order #T0131</span>
          <span class="badge badge-takeaway">Take away</span>
        </div>
        <div class="antrian-card antrian-red" data-type="dine" data-open="popup-detail-antrian">
          <div class="antrian-card-top"><span class="antrian-table">Table 06</span><span class="antrian-time">22 min ago</span></div>
          <span class="antrian-order-id">Order #T0132</span>
          <span class="badge badge-dine">Dine in</span>
        </div>
        <div class="antrian-card antrian-red" data-type="dine" data-open="popup-detail-antrian">
          <div class="antrian-card-top"><span class="antrian-table">Table 14</span><span class="antrian-time">25 min ago</span></div>
          <span class="antrian-order-id">Order #T0133</span>
          <span class="badge badge-dine">Dine in</span>
        </div>
        <div class="antrian-card antrian-red" data-type="takeaway" data-open="popup-detail-antrian">
          <div class="antrian-card-top"><span class="antrian-table">Table 01</span><span class="antrian-time">28 min ago</span></div>
          <span class="antrian-order-id">Order #T0134</span>
          <span class="badge badge-takeaway">Take away</span>
        </div>
        <div class="antrian-card antrian-red" data-type="dine" data-open="popup-detail-antrian">
          <div class="antrian-card-top"><span class="antrian-table">Table 12</span><span class="antrian-time">30 min ago</span></div>
          <span class="antrian-order-id">Order #T0135</span>
          <span class="badge badge-dine">Dine in</span>
        </div>
      </div>
    </div>
@endsection

@section('scripts')
<script src="{{ asset('js/pos.js') }}"></script>
@endsection