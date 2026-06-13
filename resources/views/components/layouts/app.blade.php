<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>@yield('title', 'Dashboard') - HL Sales & Receivables</title>
  <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
  <!-- Google fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap"
    rel="stylesheet">
  <!-- Tabler Core CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css">
  <!-- Tabler Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
  <style>
      body, .page, .page-wrapper {
        background-color: #ffffff !important;
      }
      body {
        font-family: 'Outfit', 'Inter', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif !important;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
      }

    h1,
    .h1,
    .page-title {
      font-size: 1.6rem !important;
      font-weight: 800 !important;
      letter-spacing: -0.02em !important;
      line-height: 1.25 !important;
    }

    h2,
    .h2 {
      font-size: 1.35rem !important;
      font-weight: 700 !important;
      letter-spacing: -0.015em !important;
      line-height: 1.3 !important;
    }

    h3,
    .h3,
    .card-title {
      font-size: 1.15rem !important;
      font-weight: 700 !important;
      letter-spacing: -0.01em !important;
      line-height: 1.35 !important;
    }

    h4,
    .h4 {
      font-size: 0.975rem !important;
      font-weight: 600 !important;
      line-height: 1.4 !important;
    }

    h5,
    .h5 {
      font-size: 0.85rem !important;
      font-weight: 600 !important;
      line-height: 1.4 !important;
    }

    /* Hide input number spinner arrows */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
      -webkit-appearance: none;
      margin: 0;
    }
    input[type=number] {
      -moz-appearance: textfield;
    }
  </style>
  @livewireStyles
  @stack('styles')
</head>

<body>
  <div class="page">
    <!-- Navbar -->
    <header class="navbar navbar-expand-md navbar-light d-print-none">
      <div class="container-xl">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu"
          aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
          <a href="{{ url('/dashboard') }}" class="d-flex align-items-center gap-2" style="text-decoration: none;">
            <span class="avatar avatar-sm bg-primary-lt text-primary rounded-3">
              <i class="ti ti-briefcase" style="font-size: 1.25rem;"></i>
            </span>
            <div>
              <span class="font-weight-black tracking-tight text-dark" style="font-size: 1.15rem; font-weight: 800;">HL
                SALES</span>
              <span class="d-block text-muted uppercase font-weight-bold"
                style="font-size: 0.65rem; letter-spacing: 0.05em; font-weight: 700; margin-top: -2px;">Receivables
                Mgmt</span>
            </div>
          </a>
        </h1>
        <div class="navbar-nav flex-row order-md-last">
          <div class="nav-item dropdown">
            <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown"
              aria-label="Open user menu">
              <span class="avatar avatar-sm rounded-circle bg-indigo text-white font-weight-bold">
                {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
              </span>
              <div class="d-none d-xl-block ps-2">
                <div>{{ auth()->user()->name ?? 'Administrator' }}</div>
                <div class="mt-1 small text-muted">Owner</div>
              </div>
            </a>
            <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
              <form action="{{ route('logout') }}" method="POST" id="logout-form">
                @csrf
                <button type="submit" class="dropdown-item text-danger font-weight-bold">
                  <i class="ti ti-logout me-2"></i> Keluar
                </button>
              </form>
            </div>
          </div>
        </div>
        <div class="collapse navbar-collapse" id="navbar-menu">
          <div class="d-flex flex-column flex-md-row flex-fill align-items-stretch align-items-md-center">
            <ul class="navbar-nav">
              <li class="nav-item {{ Request::is('dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('/dashboard') }}">
                  <span class="nav-link-icon d-md-none d-lg-inline-block">
                    <i class="ti ti-dashboard"></i>
                  </span>
                  <span class="nav-link-title font-weight-semibold">Dasbor</span>
                </a>
              </li>
              <li class="nav-item {{ Request::is('transactions*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('/transactions') }}">
                  <span class="nav-link-icon d-md-none d-lg-inline-block">
                    <i class="ti ti-file-invoice"></i>
                  </span>
                  <span class="nav-link-title font-weight-semibold">Transaksi (Bon)</span>
                </a>
              </li>
              <li class="nav-item {{ Request::is('customers*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('/customers') }}">
                  <span class="nav-link-icon d-md-none d-lg-inline-block">
                    <i class="ti ti-users"></i>
                  </span>
                  <span class="nav-link-title font-weight-semibold">Pelanggan</span>
                </a>
              </li>
              <li class="nav-item {{ Request::is('products*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('/products') }}">
                  <span class="nav-link-icon d-md-none d-lg-inline-block">
                    <i class="ti ti-box"></i>
                  </span>
                  <span class="nav-link-title font-weight-semibold">Produk</span>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </header>
    <div class="page-wrapper">
      {{ $slot }}
      <footer class="footer footer-transparent d-print-none mt-auto py-3">
        <div class="container-xl">
          <div class="row text-center align-items-center flex-row-reverse">
            <div class="col-12 col-lg-auto mt-3 mt-lg-0">
              <ul class="list-inline list-inline-dots mb-0">
                <li class="list-inline-item">
                  &copy; {{ date('Y') }} <a href="{{ url('/') }}" class="link-secondary">HL Sales & Receivables</a>. Hak
                  Cipta Dilindungi.
                </li>
              </ul>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </div>
  <!-- Tabler Core JS -->
  <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/js/tabler.min.js" defer></script>
  @livewireScripts
  @stack('scripts')
</body>

</html>