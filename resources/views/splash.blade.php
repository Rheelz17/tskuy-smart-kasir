<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang di Warkop Tskuy</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body, html {
            height: 100%;
            font-family: 'Poppins', sans-serif;
            background-color: #ffffff;
            overflow: hidden;
            color: #1a1a1a;
        }

        /* Background Kuning Melengkung (Muncul di Slide 2 & 3) */
        .bg-shape {
            position: absolute;
            top: 0; left: 0; width: 100%;
            height: 55vh;
            background: #FFF9D2; /* Kuning muda sesuai desain */
            border-radius: 0 0 50% 50% / 0 0 15% 15%;
            z-index: 0;
            opacity: 0;
            transform: translateY(-100%);
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .bg-shape.active {
            opacity: 1;
            transform: translateY(0);
        }

        .slider-container {
            position: relative;
            width: 100%; height: 100vh;
            display: flex; align-items: center; justify-content: center;
            z-index: 1;
        }

        .slide {
            position: absolute;
            width: 100%; max-width: 1000px;
            padding: 0 24px;
            display: flex; flex-direction: column; align-items: center; text-align: center;
            opacity: 0;
            pointer-events: none;
            transform: translateX(50px);
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .slide.active {
            opacity: 1;
            pointer-events: auto;
            transform: translateX(0);
        }
        .slide.exit-left {
            transform: translateX(-50px);
            opacity: 0;
        }

        /* --- STYLING SLIDE 1 (LOGO) --- */
        .brand-wrap { display: flex; align-items: center; justify-content: center; gap: 16px; }
        .logo-main { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; }
        .brand-text { text-align: left; line-height: 1.1; }
        .brand-sub { font-size: 20px; font-weight: 600; color: #D97706; display: block; }
        .brand-name { font-size: 48px; font-weight: 800; color: #D97706; display: block; letter-spacing: -1px; }

        /* --- STYLING SLIDE 2 & 3 --- */
        .slide-img { height: 320px; object-fit: contain; margin-bottom: 40px; }
        /* Efek nyala-nyala logo di slide 3 */
        .glowing-logo {
            border-radius: 50%;
            box-shadow: 0 0 0 20px rgba(239, 177, 0, 0.4), 0 0 0 45px rgba(239, 177, 0, 0.2);
            margin: 45px 0 65px;
        }
        
        .slide-title { font-size: 32px; font-weight: 800; color: #111; margin-bottom: 12px; }
        .slide-desc { font-size: 16px; color: #666; max-width: 500px; line-height: 1.6; margin: 0 auto; }

        /* --- KONTROL NAVIGASI BAWAH --- */
        .controls {
            position: absolute; bottom: 0; left: 50%; transform: translateX(-50%);
            width: 100%; max-width: 1000px; padding: 40px 24px;
            display: flex; align-items: center; justify-content: space-between;
            z-index: 10;
            opacity: 0; pointer-events: none;
            transition: opacity 0.5s;
        }
        .controls.active { opacity: 1; pointer-events: auto; }

        .btn-skip {
            font-size: 15px; font-weight: 600; color: #888;
            background: none; border: none; cursor: pointer;
            transition: color 0.2s; padding: 10px;
        }
        .btn-skip:hover { color: #111; }

        .dots { display: flex; gap: 8px; position: absolute; left: 50%; transform: translateX(-50%); }
        .dot { width: 10px; height: 10px; border-radius: 50%; background: #E5E7EB; transition: all 0.3s; }
        .dot.active { background: #D97706; width: 24px; border-radius: 10px; }

        .btn-next, .btn-start {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            height: 48px; border: none; border-radius: 24px;
            font-size: 15px; font-weight: 700; cursor: pointer; transition: all 0.2s;
        }
        .btn-next { width: 48px; background: #fff; color: #D97706; border: 2px solid #FFF1C5; }
        .btn-next:hover { background: #FFF9D2; }
        
        .btn-start { padding: 0 24px; background: #fff; color: #D97706; border: 2px solid #FFF1C5; display: none; }
        .btn-start:hover { background: #FFF9D2; }

        /* --- PILIHAN LOGIN/REGISTER (Muncul pas klik Get Started) --- */
        .auth-choices {
            display: none; flex-direction: row; gap: 16px; margin-top: 30px;
        }
        .btn-auth {
            padding: 12px 32px; border-radius: 12px; font-size: 15px; font-weight: 700;
            text-decoration: none; transition: transform 0.2s;
        }
        .btn-login { background: #EFB100; color: #fff; }
        .btn-login:hover { background: #d9a000; transform: translateY(-2px); }
        .btn-reg { background: #fff; color: #EFB100; border: 2px solid #EFB100; }
        .btn-reg:hover { background: #fffbeb; transform: translateY(-2px); }

        /* --- RESPONSIVE UNTUK HP (MOBILE) --- */
        @media (max-width: 768px) {
            .bg-shape { height: 60vh; border-radius: 0 0 50% 50% / 0 0 10% 10%; }
            .logo-main { width: 80px; height: 80px; }
            .brand-sub { font-size: 16px; }
            .brand-name { font-size: 36px; }
            .slide-img { height: 260px; margin-bottom: 30px; }
            .glowing-logo { height: 180px; width: 180px; box-shadow: 0 0 0 15px rgba(239, 177, 0, 0.4), 0 0 0 35px rgba(239, 177, 0, 0.2); }
            .slide-title { font-size: 24px; }
            .slide-desc { font-size: 14px; }
            .controls { padding: 24px 20px; }
            .auth-choices { flex-direction: column; width: 100%; max-width: 300px; }
            .btn-auth { width: 100%; text-align: center; }
        }
    </style>
</head>
<body>

    <!-- Background Melengkung (Slide 2 & 3) -->
    <div class="bg-shape" id="bg-shape"></div>

    <div class="slider-container">
        
        <!-- SLIDE 1 (Putih Polos) -->
        <div class="slide active" id="slide-0">
            <div class="brand-wrap">
                <img src="{{ asset('image/logo_warkop.png') }}" alt="Logo" class="logo-main">
                <div class="brand-text">
                    <span class="brand-sub">Warkop</span>
                    <span class="brand-name">Tskuy</span>
                </div>
            </div>
        </div>

        <!-- SLIDE 2 (Transaksi Lebih Cepat) -->
        <div class="slide" id="slide-1">
            <!-- TODO: Ganti src gambar ini dengan gambar ilustrasi kasir & pelanggan lu -->
            <img src="https://illustrations.popsy.co/amber/student-going-to-school.svg" alt="Transaksi Lebih Cepat" class="slide-img">
            <h2 class="slide-title">Transaksi Lebih Cepat</h2>
            <p class="slide-desc">Kelola penjualan dengan mudah dan akurat menggunakan sistem POS terintegrasi kami.</p>
        </div>

        <!-- SLIDE 3 (Tumbuhkan Bisnismu) -->
        <div class="slide" id="slide-2">
            <img src="{{ asset('image/logo_warkop.png') }}" alt="Logo Glowing" class="slide-img glowing-logo">
            <h2 class="slide-title">Tumbuhkan Bisnismu</h2>
            <p class="slide-desc" id="desc-akhir">Gunakan sistem kasir yang lebih canggih untuk mengelola Warkop Tskuy.</p>
            
            <!-- Tombol Login/Register yang muncul saat 'Get Started' diklik -->
            <div class="auth-choices" id="auth-choices">
                <a href="{{ route('login') }}" class="btn-auth btn-login">Masuk ke Akun</a>
                <a href="{{ route('register') }}" class="btn-auth btn-reg">Daftar</a>
            </div>
        </div>

    </div>

    <!-- KONTROL BAWAH -->
    <div class="controls" id="controls">
        <button class="btn-skip" id="btn-skip">Skip</button>
        <div class="dots">
            <div class="dot active" id="dot-1"></div>
            <div class="dot" id="dot-2"></div>
        </div>
        <button class="btn-next" id="btn-next">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
        <button class="btn-start" id="btn-start">
            Get Start! 
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
    </div>

    <script>
        let currentSlide = 0;
        const totalSlides = 3;
        
        // Element Selector
        const bgShape = document.getElementById('bg-shape');
        const controls = document.getElementById('controls');
        const btnSkip = document.getElementById('btn-skip');
        const btnNext = document.getElementById('btn-next');
        const btnStart = document.getElementById('btn-start');
        const authChoices = document.getElementById('auth-choices');
        const descAkhir = document.getElementById('desc-akhir');

        // Fungsi Ganti Slide
        function goToSlide(index) {
            // Hapus class active & tambahkan animasi exit
            document.getElementById(`slide-${currentSlide}`).classList.remove('active');
            document.getElementById(`slide-${currentSlide}`).classList.add('exit-left');
            
            currentSlide = index;
            
            // Hapus exit-left kalau mundur (biar reset), trus tambahin active ke slide baru
            const nextSlideEl = document.getElementById(`slide-${currentSlide}`);
            nextSlideEl.classList.remove('exit-left');
            nextSlideEl.classList.add('active');

            // Logika UI berdasarkan halaman
            if (currentSlide === 0) {
                // Halaman Awal
                bgShape.classList.remove('active');
                controls.classList.remove('active');
            } else {
                // Halaman 2 & 3
                bgShape.classList.add('active');
                controls.classList.add('active');
                
                // Update Dots (karena dot cuma ada 2 buat halaman 2 dan 3)
                document.getElementById('dot-1').classList.toggle('active', currentSlide === 1);
                document.getElementById('dot-2').classList.toggle('active', currentSlide === 2);

                // Update Tombol Kanan
                if (currentSlide === 2) {
                    btnNext.style.display = 'none';
                    btnStart.style.display = 'flex';
                } else {
                    btnNext.style.display = 'flex';
                    btnStart.style.display = 'none';
                }
            }
        }

        // Jalankan transisi dari Slide 1 ke 2 otomatis setelah 2 detik
        setTimeout(() => {
            if(currentSlide === 0) goToSlide(1);
        }, 2000);

        // Event Listeners
        btnNext.addEventListener('click', () => {
            if (currentSlide < totalSlides - 1) goToSlide(currentSlide + 1);
        });

        // Aksi pas klik Skip atau Get Started
        function showAuthChoices() {
            goToSlide(2); // Pastikan ada di slide 3
            // Sembunyikan kontrol bawah & teks
            controls.style.opacity = '0';
            setTimeout(() => controls.style.display = 'none', 500);
            descAkhir.style.display = 'none';
            // Tampilkan tombol Login/Register
            authChoices.style.display = 'flex';
        }

        btnSkip.addEventListener('click', showAuthChoices);
        btnStart.addEventListener('click', showAuthChoices);

    </script>
</body>
</html>