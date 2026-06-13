<div>
  @section('title', $isEditMode ? 'Edit Transaksi' : 'Buat Transaksi')

  <!-- Page header -->
  <div class="page-header d-print-none mb-4">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle">Transaksi (Bon)</div>
          <h2 class="page-title font-weight-black text-dark tracking-tight">
            {{ $isEditMode ? 'Edit Transaksi' : 'Buat Transaksi Baru' }}
          </h2>
        </div>
        <div class="col-auto ms-auto">
          <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary font-weight-bold">
            <i class="ti ti-arrow-left me-1"></i> Kembali ke Daftar
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Page body -->
  <div class="page-body">
    <div class="container-xl">
      
      @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible mb-4 shadow-xs" role="alert">
          <div class="d-flex">
            <div><i class="ti ti-alert-triangle me-2" style="font-size: 1.25rem;"></i></div>
            <div class="font-weight-medium">{{ session('error') }}</div>
          </div>
          <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
      @endif

      <form wire:submit.prevent="save">
        <div class="row g-4">
          <!-- Left Column: Details -->
          <div class="col-lg-8">
            <!-- Header Card -->
            <div class="card mb-4 shadow-xs">
              <div class="card-header bg-white py-3">
                <h3 class="card-title font-weight-bold text-dark">Informasi Utama Transaksi</h3>
              </div>
              <div class="card-body">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label font-weight-semibold">Nomor Bon</label>
                    <input type="text" wire:model="nomor_bon" class="form-control @error('nomor_bon') is-invalid @enderror" placeholder="BON-YYYYMMDD-XXXX">
                    @error('nomor_bon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>
                  <div class="col-md-6">
                    <label class="form-label font-weight-semibold">Pelanggan</label>
                    <select wire:model.live="customer_id" class="form-select @error('customer_id') is-invalid @enderror">
                      <option value="">-- Pilih Pelanggan --</option>
                      @foreach($customersList as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                      @endforeach
                    </select>
                    @error('customer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>

                  <div class="col-md-6">
                    <label class="form-label font-weight-semibold">Tanggal Transaksi</label>
                    <input type="date" wire:model="tanggal" class="form-control @error('tanggal') is-invalid @enderror">
                    @error('tanggal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>
                  <div class="col-md-6">
                    <label class="form-label font-weight-semibold">Status Pembayaran</label>
                    <select wire:model.live="status" class="form-select @error('status') is-invalid @enderror">
                      <option value="Piutang">Piutang</option>
                      <option value="Lunas">Lunas</option>
                    </select>
                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>

                  @if($status === 'Lunas')
                    <div class="col-md-6">
                      <label class="form-label font-weight-semibold">Tanggal Pelunasan</label>
                      <input type="date" wire:model="tanggal_pelunasan" class="form-control @error('tanggal_pelunasan') is-invalid @enderror">
                      @error('tanggal_pelunasan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                  @endif

                  <div class="col-12">
                    <label class="form-label font-weight-semibold">Deskripsi / Catatan Proyek</label>
                    <textarea wire:model="deskripsi" class="form-control" rows="2" placeholder="Tuliskan keterangan detail transaksi..."></textarea>
                  </div>
                </div>

                <hr class="my-4">

                <!-- Claim Bonus Switch -->
                <div class="row g-3">
                  <div class="col-md-6">
                    <div class="form-check form-switch pt-2">
                      <input class="form-check-input" type="checkbox" id="isBonusSwitch" wire:model.live="is_bonus">
                      <label class="form-check-label font-weight-bold text-dark" for="isBonusSwitch">Klaim Transaksi Bonus</label>
                      <small class="form-hint">Mencentang ini akan menolkan (Rp 0) semua harga jual item barang.</small>
                    </div>
                  </div>
                  @if($is_bonus)
                    <div class="col-md-6">
                      <label class="form-label font-weight-semibold">Jumlah Kupon Bonus yang Diklaim</label>
                      <input type="number" wire:model.blur="bonuses_claimed" class="form-control @error('bonuses_claimed') is-invalid @enderror" min="1">
                      @error('bonuses_claimed') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                  @endif
                </div>
              </div>
            </div>

            <!-- Items Repeater -->
            <div class="card shadow-xs">
              <div class="card-header bg-white d-flex align-items-center py-3">
                <h3 class="card-title font-weight-bold text-dark">Daftar Item Barang</h3>
                <button type="button" wire:click="addItem" class="btn btn-sm btn-outline-primary ms-auto font-weight-bold">
                  <i class="ti ti-plus me-1"></i> Tambah Item
                </button>
              </div>
              
              <div class="table-responsive">
                <table class="table card-table table-vcenter">
                  <thead>
                    <tr class="text-muted uppercase font-weight-bold" style="font-size: 0.65rem; background-color: #fcfdfe;">
                      <th style="min-width: 200px;">Nama Produk</th>
                      <th class="text-center" style="width: 80px;">Tipe</th>
                      <th style="width: 130px;">Harga Dasar</th>
                      <th style="width: 140px;">Diskon Bertingkat (%)</th>
                      <th style="width: 120px;">Harga Satuan</th>
                      <th class="text-center" style="width: 90px;">Qty</th>
                      <th class="text-end" style="width: 140px;">Total Omzet</th>
                      <th class="text-center" style="width: 50px;">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($items as $index => $item)
                      <tr wire:key="item-{{ $item['key'] ?? $index }}">
                        <td>
                          <select wire:model.live="items.{{ $index }}.product_id" class="form-select form-select-sm @error('items.'.$index.'.product_id') is-invalid @enderror">
                            <option value="">-- Pilih Produk --</option>
                            @foreach($productsList as $p)
                              <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                          </select>
                        </td>
                        <td class="text-center">
                          @if($item['product_type'] === 'LM')
                            <span class="badge bg-purple-lt text-purple font-weight-bold">LM</span>
                          @elseif($item['product_type'] === 'BR')
                            <span class="badge bg-azure-lt text-azure font-weight-bold">BR</span>
                          @else
                            -
                          @endif
                        </td>
                        <td>
                          <input type="number" wire:model.live="items.{{ $index }}.harga_base" class="form-control form-control-sm text-end font-weight-semibold" readonly>
                        </td>
                        <td>
                          <input type="text" wire:model.live="items.{{ $index }}.discount_steps_input" class="form-control form-control-sm bg-light" placeholder="0" readonly>
                        </td>
                        <td>
                          <span class="font-weight-bold text-dark" style="font-size: 0.85rem;">
                            Rp {{ number_format($item['discounted_unit_price'], 0, ',', '.') }}
                          </span>
                        </td>
                        <td>
                          <input type="number" wire:model.blur="items.{{ $index }}.quantity" class="form-control form-control-sm text-center font-weight-semibold" min="1">
                        </td>
                        <td class="text-end font-weight-black text-dark">
                          Rp {{ number_format($item['line_omzet'], 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                          <button type="button" wire:click="removeItem({{ $index }})" class="btn btn-icon btn-sm btn-light text-danger rounded-2 shadow-xs border" {{ count($items) <= 1 ? 'disabled' : '' }}>
                            <i class="ti ti-trash"></i>
                          </button>
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="8" class="text-center py-4 text-muted">Belum ada item barang ditambahkan.</td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Right Column: Stats & Totals -->
          <div class="col-lg-4">
            <!-- Customer Stats Panel -->
            @if($customerStats)
              <div class="card mb-4 border-top border-top-width-3 border-indigo shadow-xs">
                <div class="card-body">
                  <div class="d-flex align-items-center mb-3">
                    <span class="avatar avatar-sm bg-indigo-lt text-indigo rounded-3 me-2">
                      <i class="ti ti-user-check"></i>
                    </span>
                    <div>
                      <span class="text-muted d-block uppercase font-weight-bold" style="font-size: 0.65rem; letter-spacing: 0.05em;">Progress Bonus Pelanggan</span>
                      <span class="font-weight-black text-dark" style="font-size: 1rem;">{{ $customerStats['threshold'] > 0 ? number_format($customerStats['threshold'], 0, ',', '.') : '-' }} Threshold</span>
                    </div>
                  </div>

                  <div class="mb-3">
                    <div class="d-flex justify-content-between font-weight-semibold mb-1" style="font-size: 0.8rem;">
                      <span class="text-secondary">Progress Bonus Berikutnya</span>
                      <span class="text-indigo font-weight-bold">{{ $customerStats['progress_percentage'] }}%</span>
                    </div>
                    <div class="progress progress-sm">
                      <div class="progress-bar bg-indigo progress-bar-striped progress-bar-animated" style="width: {{ $customerStats['progress_percentage'] }}%"></div>
                    </div>
                  </div>

                  <div class="table-responsive">
                    <table class="table table-sm table-borderless mb-0">
                      <tbody>
                        <tr class="align-middle">
                          <td class="px-0 py-1 text-muted" style="font-size: 0.775rem;">Total Omzet Lunas</td>
                          <td class="px-0 py-1 text-end font-weight-bold text-dark" style="font-size: 0.825rem;">
                            Rp {{ number_format($customerStats['total_paid_omzet'], 0, ',', '.') }}
                          </td>
                        </tr>
                        <tr class="align-middle">
                          <td class="px-0 py-1 text-muted" style="font-size: 0.775rem;">Omzet Terakumulasi</td>
                          <td class="px-0 py-1 text-end font-weight-bold text-dark" style="font-size: 0.825rem;">
                            Rp {{ number_format($customerStats['carry_over_omzet'], 0, ',', '.') }}
                          </td>
                        </tr>
                        <tr class="align-middle">
                          <td class="px-0 py-1 text-muted" style="font-size: 0.775rem;">Sisa Untuk Bonus</td>
                          <td class="px-0 py-1 text-end font-weight-bold text-indigo" style="font-size: 0.825rem;">
                            Rp {{ number_format($customerStats['remaining_for_next_bonus'], 0, ',', '.') }}
                          </td>
                        </tr>
                        <tr class="align-middle">
                          <td class="px-0 py-1 text-muted" style="font-size: 0.775rem;">Klaim Bonus Tersedia</td>
                          <td class="px-0 py-1 text-end">
                            <span class="badge bg-green-lt text-green font-weight-bold px-2" style="font-size: 0.775rem;">
                              {{ $customerStats['bonuses_available'] }} KLAIM
                            </span>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>

                  @if($is_bonus && $customerStats['bonuses_available'] <= 0)
                    <div class="alert alert-warning mb-0 mt-3 py-2 px-3 shadow-xs" role="alert" style="font-size: 0.75rem;">
                      <div class="d-flex align-items-center">
                        <i class="ti ti-alert-circle me-1" style="font-size: 1rem;"></i>
                        <div>Pelanggan tidak memiliki jatah bonus tersedia. Klaim tetap diperbolehkan namun status akan menjadi negatif.</div>
                      </div>
                    </div>
                  @endif
                </div>
              </div>
            @endif

            <!-- Billing Totals Card -->
            <div class="card shadow-xs">
              <div class="card-header bg-white py-3">
                <h3 class="card-title font-weight-bold text-dark">Ringkasan Tagihan</h3>
              </div>
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <span class="text-secondary font-weight-medium">Subtotal Omzet</span>
                  <span class="font-weight-bold text-dark">
                    Rp {{ number_format($this->subtotal, 0, ',', '.') }}
                  </span>
                </div>
                
                <div class="mb-4">
                  <label class="form-label font-weight-semibold">Ongkos Kirim (Ongkir)</label>
                  <div class="input-group">
                    <span class="input-group-text font-weight-semibold">Rp</span>
                    <input type="number" wire:model.blur="ongkir" class="form-control text-end font-weight-bold" min="0" placeholder="0">
                  </div>
                </div>

                <hr class="my-3">

                <div class="d-flex justify-content-between align-items-center mb-4">
                  <span class="h3 mb-0 font-weight-bold text-dark">Total Tagihan</span>
                  <span class="h2 mb-0 font-weight-black text-primary tracking-tight">
                    Rp {{ number_format($this->totalOwed, 0, ',', '.') }}
                  </span>
                </div>

                <button type="submit" class="btn btn-primary w-100 font-weight-bold py-2 shadow-xs">
                  <i class="ti ti-device-floppy me-1"></i> Simpan Transaksi
                </button>
              </div>
            </div>
          </div>
        </div>
      </form>

    </div>
  </div>
</div>
