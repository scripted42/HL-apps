<div>
  <!-- Page header -->
  <div class="page-header d-print-none mb-4">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <!-- Page pre-title -->
          <div class="page-pretitle text-secondary">
            Ringkasan
          </div>
          <h2 class="page-title font-weight-black text-dark tracking-tight">
            Dashboard Utama
          </h2>
        </div>
      </div>
    </div>
  </div>

  <!-- Page body -->
  <div class="page-body">
    <div class="container-xl">
      <div class="row row-cards">
        
        <!-- Welcome Card -->
        <div class="col-12">
          <div class="card card-md welcome-card-tabler overflow-hidden border-0 shadow-sm relative" style="background: linear-gradient(135deg, #f1f6fe 0%, #e5effe 100%) !important; border-radius: 12px;">
            <div class="card-body py-4 py-md-3">
              <div class="row align-items-center">
                <div class="col-12 col-md-7 py-3">
                  <h2 class="h1 font-weight-black tracking-tight mb-2" style="color: #0f172a !important;">Selamat Datang Kembali, {{ $welcomeName }}!</h2>
                  <p class="text-secondary" style="color: #475569 !important; font-size: 0.925rem; line-height: 1.5; margin-bottom: 1.5rem;">
                    Aplikasi HL Sales & Receivables siap membantu mengelola piutang, memantau omzet lunas, laba bersih, serta status pencapaian bonus pelanggan secara akurat.
                  </p>
                  <a href="{{ route('transactions.create') }}" class="btn btn-primary btn-pill shadow-xs px-4 py-2 font-weight-bold" style="background-color: #206bc4 !important; border-color: #206bc4 !important;">
                    <i class="ti ti-plus me-1"></i> Buat Bon Baru
                  </a>
                </div>
                <div class="col-12 col-md-5 d-none d-md-block text-end position-relative" style="height: 200px; overflow: visible;">
                  <img src="{{ asset('images/dashboard-welcome.png') }}" alt="Dashboard Welcome Illustration" class="img-fluid" style="max-height: 240px; width: auto; position: absolute; bottom: -24px; right: 0; object-fit: contain; mix-blend-mode: multiply;">
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 4 Metrics Cards -->
        <!-- Card 1: Total Piutang -->
        <div class="col-sm-6 col-lg-3">
          <div class="card card-sm premium-card-tabler">
            <div class="card-status-top bg-danger"></div>
            <div class="card-body">
              <div class="d-flex align-items-center justify-content-between">
                <div class="subheader text-secondary">Total Piutang</div>
                <span class="badge {{ $piutangDiff <= 0 ? 'bg-green-lt text-green' : 'bg-red-lt text-red' }} font-weight-bold">
                  @if($piutangDiff <= 0)
                    <i class="ti ti-arrow-down-right"></i> {{ abs(round($piutangDiff, 1)) }}%
                  @else
                    <i class="ti ti-arrow-up-right"></i> {{ round($piutangDiff, 1) }}%
                  @endif
                </span>
              </div>
              <div class="h1 font-weight-black text-dark tracking-tight my-2">
                Rp {{ number_format($totalPiutang, 0, ',', '.') }}
              </div>
              <div class="text-muted font-weight-semibold" style="font-size: 0.725rem;">
                {{ $piutangCount }} Bon belum lunas (outstanding)
              </div>
              <!-- Sparkline -->
              <div class="mt-3" style="height: 32px;">
                <svg class="w-100 h-100" viewBox="0 0 100 30" preserveAspectRatio="none">
                  <polyline points="{{ $piutangPoints }}" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Card 2: Total Omzet Lunas -->
        <div class="col-sm-6 col-lg-3">
          <div class="card card-sm premium-card-tabler">
            <div class="card-status-top bg-primary"></div>
            <div class="card-body">
              <div class="d-flex align-items-center justify-content-between">
                <div class="subheader text-secondary">Total Omzet Lunas</div>
                <span class="badge {{ $omzetDiff >= 0 ? 'bg-green-lt text-green' : 'bg-red-lt text-red' }} font-weight-bold">
                  @if($omzetDiff >= 0)
                    <i class="ti ti-arrow-up-right"></i> {{ round($omzetDiff, 1) }}%
                  @else
                    <i class="ti ti-arrow-down-right"></i> {{ abs(round($omzetDiff, 1)) }}%
                  @endif
                </span>
              </div>
              <div class="h1 font-weight-black text-dark tracking-tight my-2">
                Rp {{ number_format($totalOmzet, 0, ',', '.') }}
              </div>
              <div class="text-muted font-weight-semibold" style="font-size: 0.725rem;">
                LM: {{ number_format($omzetLM/1000000, 1, ',', '.') }}jt | BR: {{ number_format($omzetBR/1000000, 1, ',', '.') }}jt
              </div>
              <!-- Sparkline -->
              <div class="mt-3" style="height: 32px;">
                <svg class="w-100 h-100" viewBox="0 0 100 30" preserveAspectRatio="none">
                  <polyline points="{{ $omzetPoints }}" fill="none" stroke="#206bc4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Card 3: Total Laba HL Lunas -->
        <div class="col-sm-6 col-lg-3">
          <div class="card card-sm premium-card-tabler">
            <div class="card-status-top bg-success"></div>
            <div class="card-body">
              <div class="d-flex align-items-center justify-content-between">
                <div class="subheader text-secondary">Total Laba HL Lunas</div>
                <span class="badge {{ $labaDiff >= 0 ? 'bg-green-lt text-green' : 'bg-red-lt text-red' }} font-weight-bold">
                  @if($labaDiff >= 0)
                    <i class="ti ti-arrow-up-right"></i> {{ round($labaDiff, 1) }}%
                  @else
                    <i class="ti ti-arrow-down-right"></i> {{ abs(round($labaDiff, 1)) }}%
                  @endif
                </span>
              </div>
              <div class="h1 font-weight-black text-dark tracking-tight my-2">
                Rp {{ number_format($totalLaba, 0, ',', '.') }}
              </div>
              <div class="text-muted font-weight-semibold" style="font-size: 0.725rem;">
                LM: {{ number_format($labaLM/1000000, 1, ',', '.') }}jt | BR: {{ number_format($labaBR/1000000, 1, ',', '.') }}jt
              </div>
              <!-- Sparkline -->
              <div class="mt-3" style="height: 32px;">
                <svg class="w-100 h-100" viewBox="0 0 100 30" preserveAspectRatio="none">
                  <polyline points="{{ $labaPoints }}" fill="none" stroke="#2fb344" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Card 4: Bonus Eligibility -->
        <div class="col-sm-6 col-lg-3">
          <div class="card card-sm premium-card-tabler">
            <div class="card-status-top bg-warning"></div>
            <div class="card-body">
              <div class="d-flex align-items-center justify-content-between">
                <div class="subheader text-secondary">Bonus Eligibility</div>
                <span class="badge bg-amber-lt text-amber font-weight-bold">Aktif</span>
              </div>
              <div class="h1 font-weight-black text-dark tracking-tight my-2">
                {{ $customersWithBonusCount }} Pelanggan
              </div>
              <div class="text-muted font-weight-semibold" style="font-size: 0.725rem;">
                Berhak klaim bonus (omzet tercapai)
              </div>
              <!-- Sparkline -->
              <div class="mt-3" style="height: 32px;">
                <svg class="w-100 h-100" viewBox="0 0 100 30" preserveAspectRatio="none">
                  <polyline points="{{ $bonusPoints }}" fill="none" stroke="#f59f00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Chart Row -->
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title font-weight-bold text-dark">Perbandingan Omzet & Laba Bersih (Cash Basis)</h3>
            </div>
            <div class="card-body">
              <div style="height: 300px; position: relative;">
                <canvas id="omzetLabaChart" style="width: 100%; height: 100%;"></canvas>
              </div>
            </div>
          </div>
        </div>

        <!-- Row 3: Split Sections (Activity, Transactions, Invoices) -->
        <!-- Left: Recent Activity Feed (5 columns) -->
        <div class="col-md-5">
          <div class="card" style="min-height: 100%;">
            <div class="card-header">
              <h3 class="card-title font-weight-bold text-dark">Aktivitas Terakhir</h3>
            </div>
            <div class="card-body">
              <ul class="list-unstyled mb-0" style="border-left: 2px solid #f1f3f5; padding-left: 24px; margin-left: 8px;">
                @forelse($recentActivities as $act)
                  <li class="position-relative mb-4 pb-1">
                    <span class="position-absolute d-flex align-items-center justify-content-center rounded-circle {{ $act['color'] }}" 
                          style="left: -38px; top: 0px; width: 28px; height: 28px; border: 4px solid #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,0.06);">
                      <i class="ti {{ $act['icon'] }}" style="font-size: 0.825rem;"></i>
                    </span>
                    <div class="text-muted mb-1" style="font-size: 0.725rem; font-weight: 500;">
                      {{ $act['time']->diffForHumans() }}
                    </div>
                    <div class="text-dark font-weight-medium" style="font-size: 0.85rem; line-height: 1.45;">
                      {!! $act['description'] !!}
                    </div>
                  </li>
                @empty
                  <div class="text-center py-5 text-muted">Belum ada aktivitas terbaru.</div>
                @endforelse
              </ul>
            </div>
          </div>
        </div>

        <!-- Right: Last Transactions & PDF Invoices (7 columns) -->
        <div class="col-md-7">
          <div class="row row-cards">
            <!-- Last Transactions -->
            <div class="col-12">
              <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <h3 class="card-title font-weight-bold text-dark mb-0">Transaksi Terakhir</h3>
                  <a href="{{ route('transactions.index') }}" class="small font-weight-bold link-primary">Lihat Semua</a>
                </div>
                <div class="table-responsive">
                  <table class="table card-table table-vcenter text-nowrap">
                    <thead>
                      <tr class="text-muted uppercase font-weight-bold" style="font-size: 0.675rem; background-color: #fcfdfe;">
                        <th>Nomor Bon</th>
                        <th>Pelanggan</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Total Tagihan</th>
                        <th class="text-center">Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse($recentTransactions as $t)
                        <tr>
                          <td class="font-monospace font-weight-bold text-dark">{{ $t->nomor_bon }}</td>
                          <td class="font-weight-semibold text-secondary">{{ $t->customer->name }}</td>
                          <td class="text-center">
                            @if($t->status === 'Lunas')
                              <span class="badge bg-green-lt text-green font-weight-bold">LUNAS</span>
                            @else
                              <span class="badge bg-red-lt text-red font-weight-bold">PIUTANG</span>
                            @endif
                          </td>
                          <td class="text-end font-weight-bold text-dark">Rp {{ number_format($t->total_owed, 0, ',', '.') }}</td>
                          <td class="text-center">
                            <a href="{{ route('transactions.edit', $t->id) }}" class="btn btn-icon btn-light btn-sm rounded-2 shadow-xs border" title="Edit Transaksi">
                              <i class="ti ti-edit"></i>
                            </a>
                          </td>
                        </tr>
                      @empty
                        <tr>
                          <td colspan="5" class="py-4 text-center text-muted">Belum ada transaksi terdaftar.</td>
                        </tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <!-- PDF Invoice downloads -->
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title font-weight-bold text-dark">Unduh Bon PDF Terbaru</h3>
                </div>
                <div class="card-body p-3">
                  <div class="row g-2">
                    @forelse($recentInvoices as $inv)
                      <div class="col-12">
                        <div class="d-flex align-items-center justify-content-between p-3 rounded-3 border bg-light-lt hover-shadow" style="transition: all 0.2s;">
                          <div class="d-flex align-items-center gap-3">
                            <span class="avatar bg-white border rounded-3 text-primary">
                              <i class="ti ti-file-text" style="font-size: 1.25rem;"></i>
                            </span>
                            <div>
                              <span class="d-block font-monospace font-weight-bold text-dark">{{ $inv->nomor_bon }}</span>
                              <span class="d-block text-muted font-weight-medium" style="font-size: 0.7rem;">{{ $inv->customer->name }} &bull; {{ $inv->tanggal->format('d M Y') }}</span>
                            </div>
                          </div>
                          <a href="{{ route('transactions.pdf', $inv->id) }}" class="btn btn-primary btn-sm px-3 font-weight-bold">
                            <i class="ti ti-download me-1"></i> Unduh PDF
                          </a>
                        </div>
                      </div>
                    @empty
                      <div class="text-center py-4 text-muted w-100">Belum ada invoice untuk diunduh.</div>
                    @endforelse
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('omzetLabaChart').getContext('2d');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: @json($chartLabels),
        datasets: [
          {
            label: 'Total Omzet',
            data: @json($chartOmzet),
            borderColor: '#206bc4',
            backgroundColor: 'rgba(32, 107, 196, 0.05)',
            fill: true,
            tension: 0.3,
            borderWidth: 2.5,
            pointBackgroundColor: '#206bc4',
          },
          {
            label: 'Total Laba HL',
            data: @json($chartLaba),
            borderColor: '#2fb344',
            backgroundColor: 'rgba(47, 179, 68, 0.05)',
            fill: true,
            tension: 0.3,
            borderWidth: 2.5,
            pointBackgroundColor: '#2fb344',
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'top',
            labels: {
              font: {
                family: 'Outfit',
                weight: '600'
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
              },
              font: {
                family: 'Outfit',
                weight: '500'
              }
            },
            grid: {
              color: '#f1f3f5'
            }
          },
          x: {
            ticks: {
              font: {
                family: 'Outfit',
                weight: '500'
              }
            },
            grid: {
              color: '#f1f3f5'
            }
          }
        }
      }
    });
  });
</script>
@endpush
