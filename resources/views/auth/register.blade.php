<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Warkop Tskuy — Register</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <!-- Pastikan nama file CSS lu udah bener -->
    <link rel="stylesheet" href="{{ asset('css/style-login-regist.css') }}" />
  </head>
  <body>
    <section class="page page-register" id="page-register">
      <div class="auth-layout auth-layout-register">
        <div class="auth-illus-col">
          <div class="illus-bg">
            <div class="illus-pattern"></div>
          </div>
        </div>

        <div class="auth-form-col auth-form-col-register">
          <div class="auth-card auth-card-register">
            <div class="brand">
              <div class="brand-logo">
                <img src="{{ asset('image/logo_warkop.png') }}" alt="Tskuy Kasir Logo" class="logo-login-img" />
              </div>
              <div class="brand-text">
                <span class="brand-sub">Warkop</span>
                <span class="brand-name">Tskuy</span>
              </div>
            </div>

            <p class="auth-greeting">Selamat Bergabung</p>
            <h1 class="auth-title">Buat akun</h1>

            <!-- Tampilkan Error Validasi -->
            @if ($errors->any())
                <div style="color: #ef4444; font-size: 12px; margin-bottom: 10px;">
                    Pastikan semua data diisi dengan benar dan username/email belum terdaftar.
                </div>
            @endif

            <!-- FORM REGISTRASI DIMULAI DI SINI -->
            <form method="POST" action="{{ route('register') }}">
              @csrf
              <div class="register-grid">
                
                <div class="form-group">
                  <label class="form-label">Nama lengkap <span class="required">*</span></label>
                  <input type="text" name="name" class="form-input" placeholder="Masukkan Nama Lengkap" value="{{ old('name') }}" required />
                </div>

                <div class="form-group">
                  <label class="form-label">Nama pengguna <span class="required">*</span></label>
                  <input type="text" name="username" class="form-input" placeholder="Masukkan nama pengguna" value="{{ old('username') }}" required />
                </div>

                <div class="form-group">
                  <label class="form-label">Alamat Email <span class="required">*</span></label>
                  <input type="email" name="email" class="form-input" placeholder="Masukkan email" value="{{ old('email') }}" required />
                  <span class="form-hint">Gunakan email aktif anda</span>
                </div>

                <!-- TAMBAHAN: Kolom Nomor HP/WhatsApp -->
                <div class="form-group">
                  <label class="form-label">Nomor WhatsApp / HP</label>
                  <input type="text" name="phone" class="form-input" placeholder="Opsional (08xxx)" value="{{ old('phone') }}" />
                  <span class="form-hint">Untuk info pesanan</span>
                </div>

                <div class="form-group">
                  <label class="form-label">Kata Sandi <span class="required">*</span></label>
                  <div class="input-icon-wrap">
                    <input type="password" name="password" class="form-input" placeholder="Buat kata sandi" required />
                  </div>
                </div>

                <div class="form-group">
                  <label class="form-label">Masukkan Ulang Kata Sandi <span class="required">*</span></label>
                  <div class="input-icon-wrap">
                    <input type="password" name="password_confirmation" class="form-input" placeholder="Konfirmasi kata sandi" required />
                  </div>
                  <span class="form-hint">Ulangi kata sandi yang telah anda masukkan</span>
                </div>
              </div>

              <button type="submit" class="btn-primary" style="margin-top: 24px;">Registrasi</button>
            </form>

            <p class="auth-footer-text" style="margin-top: 16px;">
              Sudah punya akun?
              <a href="{{ route('login') }}" class="auth-link">Masuk</a>
            </p>
          </div>
        </div>
      </div>
    </section>
  </body>
</html>