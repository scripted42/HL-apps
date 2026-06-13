<div>
  @section('title', $customer->name)

  <!-- Page header -->
  <div class="page-header d-print-none mb-4">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle text-secondary">
            <a href="{{ route('customers.index') }}" class="link-secondary"><i class="ti ti-arrow-left"></i> Kembali ke Daftar</a>
          </div>
          <h2 class="page-title font-weight-black text-dark tracking-tight">Detail Pelanggan: {{ $customer->name }}</h2>
        </div>
      </div>
    </div>
  </div>

  <!-- Page body -->
  <div class="page-body">
    <div class="container-xl">
      
      @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible mb-4 shadow-xs" role="alert">
          <div class="d-flex">
            <div><i class="ti ti-circle-check me-2" style="font-size: 1.25rem;"></i></div>
            <div class="font-weight-medium">{{ session('message') }}</div>
          </div>
          <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
      @endif

      <!-- Customer Profile & Bonus Indicator -->
      <div class="row row-cards mb-4">
        <div class="col-12 col-md-4">
          <div class="card shadow-xs h-100">
            <div class="card-body">
              <div class="subheader text-secondary">Informasi Profil</div>
              <h3 class="h2 text-dark font-weight-bold tracking-tight my-2">{{ $customer->name }}</h3>
              
              <div class="space-y-3 mt-4">
                <div>
                  <div class="text-muted font-weight-semibold" style="font-size: 0.75rem;">Diskon Tangga LM:</div>
                  <div class="mt-1">
                    @if(!empty($customer->discount_lm))
                      @foreach($customer->discount_lm as $d)
                        <span class="badge bg-indigo-lt text-indigo font-weight-bold">{{ $d }}%</span>
                      @endforeach
                    @else
                      <span class="text-muted font-weight-medium">-</span>
                    @endif
                  </div>
                </div>
                <div>
                  <div class="text-muted font-weight-semibold" style="font-size: 0.75rem;">Diskon Tangga BR:</div>
                  <div class="mt-1">
                    @if(!empty($customer->discount_br))
                      @foreach($customer->discount_br as $d)
                        <span class="badge bg-purple-lt text-purple font-weight-bold">{{ $d }}%</span>
                      @endforeach
                    @else
                      <span class="text-muted font-weight-medium">-</span>
                    @endif
                  </div>
                </div>
                <div>
                  <div class="text-muted font-weight-semibold" style="font-size: 0.75rem;">Batas Threshold Bonus:</div>
                  <div class="font-weight-bold text-dark mt-0.5">
                    Rp {{ number_format($customer->bonus_threshold, 0, ',', '.') }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Bonus Progress Indicator (PRD requirement: visual feedback on threshold) -->
        <div class="col-12 col-md-5">
          <div class="card shadow-xs h-100">
            <div class="card-status-top bg-warning"></div>
            <div class="card-body">
              <div class="subheader text-secondary">Kartu Progress Bonus Pelanggan</div>
              
              <div class="d-flex align-items-center justify-content-between my-3">
                <div>
                  <span class="d-block text-muted font-weight-semibold" style="font-size: 0.75rem;">Akumulasi Omzet Lunas (Siklus Berjalan):</span>
                  <span class="h1 font-weight-black text-dark tracking-tight">
                    Rp {{ number_format($bonusStats['accumulated_omzet'], 0, ',', '.') }}
                  </span>
                </div>
                <div class="text-end">
                  <span class="badge bg-amber-lt text-amber font-weight-bold px-2.5 py-1" style="font-size: 0.85rem;">
                    {{ $bonusStats['bonuses_available'] }} Bonus Siap Klaim
                  </span>
                </div>
              </div>

              <!-- Progress Bar -->
              <div class="mb-3">
                <div class="d-flex justify-content-between text-muted font-weight-semibold mb-1" style="font-size: 0.725rem;">
                  <span>Progres ke Bonus Berikutnya</span>
                  <span>{{ round($bonusStats['progress_percentage'], 1) }}%</span>
                </div>
                <div class="progress progress-lg" style="height: 10px;">
                  <div class="progress-bar bg-warning" style="width: {{ $bonusStats['progress_percentage'] }}%" role="progressbar" aria-valuenow="{{ $bonusStats['progress_percentage'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>

              <div class="text-muted font-weight-medium" style="font-size: 0.725rem;">
                <i class="ti ti-info-circle me-1 text-secondary"></i> Kurang <strong>Rp {{ number_format($bonusStats['remaining_for_next_bonus'], 0, ',', '.') }}</strong> untuk mendapatkan bonus berikutnya.
              </div>
            </div>
          </div>
        </div>

        <!-- Totals Card -->
        <div class="col-12 col-md-3">
          <div class="card shadow-xs h-100">
            <div class="card-body d-flex flex-column justify-content-between">
              <div>
                <div class="subheader text-secondary">Outstanding Piutang</div>
                <div class="h2 font-weight-black text-danger tracking-tight my-2">
                  Rp {{ number_format($totalPiutang, 0, ',', '.') }}
                </div>
              </div>
              <div class="border-top pt-3 mt-3">
                <div class="subheader text-secondary">Total Omzet Lunas</div>
                <div class="h3 font-weight-bold text-success tracking-tight my-1">
                  Rp {{ number_format($totalPaid, 0, ',', '.') }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Monthly Breakdown List -->
      <h3 class="h3 font-weight-black text-dark mb-3">Breakdown Transaksi Bulanan</h3>
      <div class="space-y-4">
        @forelse($groupedTransactions as $monthKey => $monthData)
          <div class="card shadow-xs border-top-0">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
              <div>
                <h4 class="card-title font-weight-black text-dark h3 mb-0">{{ $monthData['month_name'] }}</h4>
              </div>
              <div class="btn-list">
                @if($monthData['stats']['piutang'] > 0)
                  <button wire:click="openSettleModal('{{ $monthKey }}')" class="btn btn-emerald btn-sm font-weight-bold">
                    <i class="ti ti-check me-1"></i> Lunasi Satu Bulan
                  </button>
                @endif
                <a href="{{ route('customers.pdf-recap', [$customer->id, 'month' => $monthKey]) }}" class="btn btn-light btn-sm font-weight-bold shadow-xs border">
                  <i class="ti ti-download me-1"></i> Unduh Rekap PDF
                </a>
              </div>
            </div>

            <!-- Monthly Aggregates Summary -->
            <div class="card-body bg-light-lt py-3 border-bottom">
              <div class="row row-cards text-center">
                <div class="col-6 col-sm-3 border-end">
                  <div class="subheader text-muted" style="font-size: 0.65rem;">Piutang Bulan Ini</div>
                  <div class="h4 font-weight-bold text-danger mb-0">Rp {{ number_format($monthData['stats']['piutang'], 0, ',', '.') }}</div>
                </div>
                <div class="col-6 col-sm-3 border-end">
                  <div class="subheader text-muted" style="font-size: 0.65rem;">Terbayar Bulan Ini</div>
                  <div class="h4 font-weight-bold text-success mb-0">Rp {{ number_format($monthData['stats']['paid'], 0, ',', '.') }}</div>
                </div>
                <div class="col-6 col-sm-3 border-end">
                  <div class="subheader text-muted" style="font-size: 0.65rem;">Omzet Lunas (LM | BR)</div>
                  <div class="h4 font-weight-bold text-dark mb-0">
                    Rp {{ number_format($monthData['stats']['omzet_lm'] + $monthData['stats']['omzet_br'], 0, ',', '.') }}
                    <span class="d-block text-muted" style="font-size: 0.65rem; font-weight: 500;">
                      LM: {{ number_format($monthData['stats']['omzet_lm']/1000000, 1, ',', '.') }}jt | BR: {{ number_format($monthData['stats']['omzet_br']/1000000, 1, ',', '.') }}jt
                    </span>
                  </div>
                </div>
                <div class="col-6 col-sm-3">
                  <div class="subheader text-muted" style="font-size: 0.65rem;">Laba HL Lunas (LM | BR)</div>
                  <div class="h4 font-weight-bold text-emerald mb-0">
                    Rp {{ number_format($monthData['stats']['laba_lm'] + $monthData['stats']['laba_br'], 0, ',', '.') }}
                    <span class="d-block text-muted" style="font-size: 0.65rem; font-weight: 500;">
                      LM: {{ number_format($monthData['stats']['laba_lm']/1000000, 1, ',', '.') }}jt | BR: {{ number_format($monthData['stats']['laba_br']/1000000, 1, ',', '.') }}jt
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <div class="table-responsive">
              <table class="table card-table table-vcenter text-nowrap">
                <thead>
                  <tr class="text-muted uppercase font-weight-bold" style="font-size: 0.675rem; background-color: #fcfdfe;">
                    <th>Nomor Bon</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Tipe Transaksi</th>
                    <th class="text-end">Total Tagihan</th>
                    <th class="text-center">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($monthData['transactions'] as $t)
                    <tr>
                      <td class="font-monospace font-weight-bold text-dark">{{ $t->nomor_bon }}</td>
                      <td class="font-weight-semibold text-secondary">{{ $t->tanggal->format('d M Y') }}</td>
                      <td>
                        @if($t->status === 'Lunas')
                          <span class="badge bg-green-lt text-green font-weight-bold">LUNAS</span>
                        @else
                          <span class="badge bg-red-lt text-red font-weight-bold">PIUTANG</span>
                        @endif
                      </td>
                      <td>
                        @if($t->is_bonus)
                          <span class="badge bg-warning-lt text-warning font-weight-bold">KLAIM BONUS</span>
                        @else
                          <span class="badge bg-blue-lt text-blue font-weight-bold">TRANSAKSI NORMAL</span>
                        @endif
                      </td>
                      <td class="text-end font-weight-bold text-dark">Rp {{ number_format($t->total_owed, 0, ',', '.') }}</td>
                      <td class="text-center">
                        <div class="btn-list justify-content-center">
                          @if($t->status === 'Piutang')
                            <button onclick="confirm('Konfirmasi pelunasan untuk Bon #{{ $t->nomor_bon }}?') || event.stopImmediatePropagation()" wire:click="settleSingleTransaction({{ $t->id }})" class="btn btn-icon btn-light btn-sm rounded-2 shadow-xs border text-success" title="Lunasi Bon Ini">
                              <i class="ti ti-check"></i>
                            </button>
                          @endif
                          <a href="{{ route('transactions.pdf', $t->id) }}" class="btn btn-icon btn-light btn-sm rounded-2 shadow-xs border" title="Unduh Bon PDF">
                            <i class="ti ti-download"></i>
                          </a>
                          <a href="{{ route('transactions.edit', $t->id) }}" class="btn btn-icon btn-light btn-sm rounded-2 shadow-xs border text-indigo" title="Edit Transaksi">
                            <i class="ti ti-edit"></i>
                          </a>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        @empty
          <div class="card shadow-xs p-5 text-center text-muted">
            Belum ada transaksi terdaftar untuk pelanggan ini.
          </div>
        @endforelse
      </div>

    </div>
  </div>

  <!-- Settle Month Confirmation Modal -->
  <div class="modal modal-blur fade" id="settle-modal" tabindex="-1" role="dialog" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content shadow-lg border-0">
        <div class="modal-header border-bottom">
          <h5 class="modal-title font-weight-black text-dark h3">Lunasi Tagihan Satu Bulan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center py-4">
          <i class="ti ti-alert-triangle text-warning mb-3" style="font-size: 3rem;"></i>
          <h3 class="font-weight-black text-dark tracking-tight">Konfirmasi Pelunasan Bulanan</h3>
          <p class="text-secondary mt-2">
            Apakah Anda yakin ingin menandai <strong>semua Bon Piutang</strong> pada bulan <strong>{{ $selectedMonthName }}</strong> untuk pelanggan <strong>{{ $customer->name }}</strong> sebagai <strong>LUNAS</strong>?
          </p>
          <div class="text-muted font-weight-semibold" style="font-size: 0.825rem;">
            Tindakan ini akan mengupdate tanggal pelunasan semua transaksi tersebut menjadi hari ini.
          </div>
        </div>
        <div class="modal-footer border-top justify-content-between">
          <button type="button" class="btn btn-link link-secondary font-weight-semibold" data-bs-dismiss="modal">Batal</button>
          <button wire:click="settleMonth" type="button" class="btn btn-emerald font-weight-bold px-4">
            <i class="ti ti-circle-check me-1"></i> Ya, Lunasi Semua
          </button>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const modalElement = document.getElementById('settle-modal');
      const bsModal = new bootstrap.Modal(modalElement);

      window.addEventListener('show-settle-modal', () => {
        bsModal.show();
      });

      window.addEventListener('hide-settle-modal', () => {
        bsModal.hide();
      });
    });
  </script>
  @endpush
</div>
