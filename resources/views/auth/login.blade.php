<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Masuk - HL Sales & Receivables</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <!-- Google fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Tabler Core CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css">
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <style>
      body {
        font-family: 'Outfit', 'Inter', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif !important;
      }
      .bg-illustration {
        background-color: #ffffff;
      }
    </style>
  </head>
  <body class="d-flex flex-column bg-white">
    <div class="row g-0 flex-fill">
      <!-- Left Column: Form -->
      <div class="col-12 col-lg-6 col-xl-5 d-flex flex-column justify-content-center align-items-center p-5">
        <div style="max-width: 440px; width: 100%;">
          <!-- Branding -->
          <div class="d-flex align-items-center gap-3 mb-5">
            <span class="avatar avatar-md bg-primary-lt text-primary rounded-3">
              <i class="ti ti-briefcase" style="font-size: 1.5rem;"></i>
            </span>
            <div>
              <span class="d-block font-weight-black tracking-tight text-dark" style="font-size: 1.35rem; font-weight: 800; line-height: 1.2;">HL SALES</span>
              <span class="d-block text-muted uppercase font-weight-bold" style="font-size: 0.75rem; letter-spacing: 0.05em; font-weight: 700;">Receivables Mgmt</span>
            </div>
          </div>
          
          <h2 class="h1 mb-2 font-weight-black text-dark tracking-tight">Masuk ke Akun Anda</h2>
          <p class="text-secondary mb-4">Silakan masukkan email dan kata sandi Anda untuk mengakses dashboard manajemen.</p>

          @if ($errors->any())
            <div class="alert alert-danger alert-dismissible" role="alert">
              <div class="d-flex">
                <div>
                  <i class="ti ti-alert-triangle me-2" style="font-size: 1.25rem;"></i>
                </div>
                <div>
                  <h4 class="alert-title font-weight-bold mb-1">Gagal Masuk!</h4>
                  <div class="text-secondary" style="font-size: 0.825rem;">
                    @foreach ($errors->all() as $error)
                      <div>{{ $error }}</div>
                    @endforeach
                  </div>
                </div>
              </div>
              <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
          @endif

          <form action="{{ route('login') }}" method="POST" autocomplete="off" novalidate>
            @csrf
            <div class="mb-3">
              <label class="form-label font-weight-semibold">Alamat Email <span class="text-danger">*</span></label>
              <input type="email" name="email" class="form-control" placeholder="nama@email.com" value="{{ old('email') }}" required autofocus autocomplete="username">
            </div>
            <div class="mb-3">
              <label class="form-label font-weight-semibold">
                Kata Sandi <span class="text-danger">*</span>
              </label>
              <div class="input-group input-group-flat">
                <input type="password" name="password" id="password-input" class="form-control" placeholder="Masukkan kata sandi" required autocomplete="current-password">
                <span class="input-group-text">
                  <a href="#" class="link-secondary" id="toggle-password" title="Tampilkan kata sandi" data-bs-toggle="tooltip">
                    <i class="ti ti-eye" id="eye-icon"></i>
                  </a>
                </span>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-check">
                <input type="checkbox" name="remember" class="form-check-input"/>
                <span class="form-check-label text-secondary font-weight-medium">Ingat saya di perangkat ini</span>
              </label>
            </div>
            <div class="form-footer mt-4">
              <button type="submit" class="btn btn-primary w-100 py-2.5 font-weight-bold" style="font-size: 0.95rem;">
                <i class="ti ti-login me-1"></i> Masuk Sekarang
              </button>
            </div>
          </form>
          
          <div class="text-center text-secondary mt-5" style="font-size: 0.75rem;">
            &copy; {{ date('Y') }} HL Sales & Receivables. Hak Cipta Dilindungi.
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-6 col-xl-7 d-none d-lg-flex flex-column justify-content-center align-items-center bg-illustration p-5 border-0">
        <div style="max-width: 540px; width: 100%;" class="text-center">
          <img src="{{ asset('images/login-illustration.png') }}" alt="HL Sales Illustration" class="img-fluid mb-4" style="max-height: 400px; object-fit: contain;">
          <h2 class="h2 text-dark font-weight-bold tracking-tight mb-2">HL Sales & Receivables Management</h2>
          <p class="text-secondary max-w-sm mx-auto" style="font-size: 0.9rem;">Kelola transaksi penjualan, piutang bon pelanggan, laba bersih, serta status pencapaian bonus secara instan dan tepat.</p>
        </div>
      </div>
    </div>

    <!-- Bootstrap & Tabler Core JS -->
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/js/tabler.min.js" defer></script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.getElementById('toggle-password');
        const passwordInput = document.getElementById('password-input');
        const eyeIcon = document.getElementById('eye-icon');

        togglePassword.addEventListener('click', function(e) {
          e.preventDefault();
          const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
          passwordInput.setAttribute('type', type);
          if (type === 'text') {
            eyeIcon.classList.remove('ti-eye');
            eyeIcon.classList.add('ti-eye-off');
          } else {
            eyeIcon.classList.remove('ti-eye-off');
            eyeIcon.classList.add('ti-eye');
          }
        });
      });
    </script>
  </body>
</html>
