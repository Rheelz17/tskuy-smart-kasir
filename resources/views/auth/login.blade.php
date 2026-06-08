<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Warkop Tskuy — Login</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <!-- Panggil CSS pakai fungsi asset() Laravel -->
  <link rel="stylesheet" href="{{ asset('css/style-login-regist.css') }}">
</head>
<body>
  <section class="page" id="page-login">
    <div class="auth-layout">
      <div class="auth-form-col">
        <div class="auth-card">
          <div class="brand">
            <div class="brand-logo">
              <!-- Panggil gambar pakai fungsi asset() -->
              <img src="{{ asset('image/logo_warkop.png') }}" alt="Tskuy Kasir Logo" class="logo-login-img">
            </div>
            <div class="brand-text">
              <span class="brand-sub">Warkop</span>
              <span class="brand-name">Tskuy</span>
            </div>
          </div>

          <p class="auth-greeting">Selamat Datang Kembali!</p>
          <h1 class="auth-title">Masuk</h1>

          <!-- Tampilkan Pesan Error Jika Gagal Login -->
          @if ($errors->any())
              <div style="color: #ef4444; font-size: 12px; margin-bottom: 10px;">
                  Email atau password salah!
              </div>
          @endif

          <!-- FORM LOGIN DIMULAI DI SINI -->
          <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
              <label class="form-label">Email</label>
              <!-- Tambahkan name="email" dan value old -->
              <input type="email" name="email" class="form-input" placeholder="Masukkan Email" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="form-group" style="margin-top: 14px; margin-bottom: 20px;">
              <label class="form-label">Kata Sandi</label>
              <!-- Tambahkan name="password" -->
              <input type="password" name="password" class="form-input" placeholder="Masukkan Password" required>
              <a href="#popup-lupa" class="lupa-sandi-link">Lupa Kata Sandi?</a>
            </div>

            <!-- Ubah tag <a> jadi <button> -->
            <button type="submit" class="btn-primary">Masuk</button>
          </form>

          <p class="auth-footer-text" style="margin-top: 16px;">
            Belum punya akun?
            <a href="{{ route('register') }}" class="auth-link">Buat</a>
          </p>

        </div>
      </div>

      <div class="auth-illus-col">
        <div class="illus-bg"></div>
      </div>
    </div>
  </section>

  <div class="popup-target" id="popup-lupa">
    <div class="popup-box">

      <a href="#" class="popup-close-circle" aria-label="Tutup">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
          <line x1="18" y1="6" x2="6" y2="18"/>
          <line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </a>

      <h2 class="popup-title">Lupa Kata Sandi?</h2>
      <p class="popup-desc">Masukkan alamat email yang terdaftar pada akun Anda. Kami akan mengirimkan tautan untuk mereset kata sandi.</p>

      <div class="form-group" style="margin-top: 8px;">
        <label class="form-label">Alamat Email</label>
        <div class="input-icon-wrap">
          <span class="input-icon input-icon-left">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#aaa" stroke-width="2" stroke-linecap="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          </span>
          <input type="email" class="form-input form-input-icon" placeholder="Masukkan alamat email">
        </div>
      </div>
      <a href="#popup-buat-sandi" class="btn-primary btn-block">Kirim Tautan</a>

    </div>
  </div>

  <div class="popup-target" id="popup-buat-sandi">
    <div class="popup-box">

      <a href="#" class="popup-close-circle" aria-label="Tutup">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
          <line x1="18" y1="6" x2="6" y2="18"/>
          <line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </a>

      <h2 class="popup-title">Buat Kata Sandi Baru</h2>
      <p class="popup-desc">Silakan buat kata sandi baru untuk akun Anda.</p>

      <div class="form-group" style="margin-top: 8px;">
        <label class="form-label">Kata Sandi Baru <span class="required">*</span></label>
        <div class="input-icon-wrap">
          <input type="password" class="form-input" placeholder="Buat sandi baru">
          <span class="input-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#aaa" stroke-width="2" stroke-linecap="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </span>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Konfirmasi Kata Sandi <span class="required">*</span></label>
        <div class="input-icon-wrap">
          <input type="password" class="form-input" placeholder="Ulangi sandi baru">
          <span class="input-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#aaa" stroke-width="2" stroke-linecap="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </span>
        </div>
      </div>

      <a href="#popup-berhasil" class="btn-primary btn-block">Simpan Kata Sandi Baru</a>

    </div>
  </div>

  <div class="popup-target" id="popup-berhasil">
    <div class="popup-box popup-box-center">

      <a href="#" class="popup-close-circle" aria-label="Tutup">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
          <line x1="18" y1="6" x2="6" y2="18"/>
          <line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </a>

      <h2 class="popup-title">Berhasil</h2>
      <p class="popup-desc">Selamat! Kata sandi akun Anda berhasil diubah. Klik Lanjut untuk kembali login.</p>

      <a href="#page-login" class="btn-primary btn-block" style="margin-top: 16px;">Selesai</a>

    </div>
  </div>
  
  <a href="#" class="target-overlay" aria-label="Tutup popup"></a>

</body>
</html>