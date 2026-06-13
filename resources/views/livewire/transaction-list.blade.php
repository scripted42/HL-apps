<div>
  @section('title', 'Transaksi (Bon)')

  <!-- Page header -->
  <div class="page-header d-print-none mb-4">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle">Manajemen</div>
          <h2 class="page-title font-weight-black text-dark tracking-tight">Daftar Transaksi (Bon)</h2>
        </div>
        <div class="col-auto ms-auto">
          <a href="{{ route('transactions.create') }}" class="btn btn-primary font-weight-bold">
            <i class="ti ti-plus me-1"></i> Buat Transaksi Baru
          </a>
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

      <div class="card shadow-xs">
        <!-- Filters Header -->
        <div class="card-body border-bottom py-3">
          <div class="row g-3 align-items-center">
            <div class="col-md-3">
              <label class="form-label font-weight-semibold" style="font-size: 0.75rem;">Cari Nomor Bon:</label>
              <input wire:model.live="search" type="text" class="form-control form-control-sm" placeholder="Ketik nomor bon...">
            </div>
            <div class="col-md-3">
              <label class="form-label font-weight-semibold" style="font-size: 0.75rem;">Filter Pelanggan:</label>
              <select wire:model.live="customerFilter" class="form-select form-select-sm">
                <option value="">Semua Pelanggan</option>
                @foreach($customers as $c)
                  <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label font-weight-semibold" style="font-size: 0.75rem;">Filter Status:</label>
              <select wire:model.live="statusFilter" class="form-select form-select-sm">
                <option value="">Semua Status</option>
                <option value="Piutang">Piutang</option>
                <option value="Lunas">Lunas</option>
              </select>
            </div>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table card-table table-vcenter text-nowrap">
            <thead>
              <tr class="text-muted uppercase font-weight-bold" style="font-size: 0.675rem; background-color: #fcfdfe;">
                <th>Nomor Bon</th>
                <th>Pelanggan</th>
                <th>Tanggal</th>
                <th>Tanggal Pelunasan</th>
                <th class="text-center">Status</th>
                <th class="text-center">Jenis</th>
                <th class="text-end">Total Tagihan</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($transactions as $t)
                <tr>
                  <td class="font-monospace font-weight-bold text-dark" style="font-size: 0.9rem;">{{ $t->nomor_bon }}</td>
                  <td class="font-weight-black text-dark" style="font-size: 0.95rem;">
                    <a href="{{ route('customers.show', $t->customer_id) }}" class="text-dark">{{ $t->customer->name }}</a>
                  </td>
                  <td class="font-weight-semibold text-secondary">{{ $t->tanggal->format('d M Y') }}</td>
                  <td class="font-weight-medium text-secondary">
                    {{ $t->tanggal_pelunasan ? $t->tanggal_pelunasan->format('d M Y') : '-' }}
                  </td>
                  <td class="text-center">
                    @if($t->status === 'Lunas')
                      <span class="badge bg-green-lt text-green font-weight-bold">LUNAS</span>
                    @else
                      <span class="badge bg-red-lt text-red font-weight-bold">PIUTANG</span>
                    @endif
                  </td>
                  <td class="text-center">
                    @if($t->is_bonus)
                      <span class="badge bg-warning-lt text-warning font-weight-bold">CLAIM BONUS</span>
                    @else
                      <span class="badge bg-blue-lt text-blue font-weight-bold">NORMAL</span>
                    @endif
                  </td>
                  <td class="text-end font-weight-bold text-dark">
                    Rp {{ number_format($t->total_owed, 0, ',', '.') }}
                  </td>
                  <td class="text-center">
                    <div class="btn-list justify-content-center">
                      @if($t->status === 'Piutang')
                        <button onclick="confirm('Konfirmasi pelunasan untuk Bon #{{ $t->nomor_bon }}?') || event.stopImmediatePropagation()" wire:click="settle({{ $t->id }})" class="btn btn-icon btn-light btn-sm rounded-2 shadow-xs border text-success" title="Lunasi Transaksi">
                          <i class="ti ti-check"></i>
                        </button>
                      @endif
                      <a href="{{ route('transactions.pdf', $t->id) }}" class="btn btn-icon btn-light btn-sm rounded-2 shadow-xs border" title="Unduh Bon PDF">
                        <i class="ti ti-download"></i>
                      </a>
                      <a href="{{ route('transactions.edit', $t->id) }}" class="btn btn-icon btn-light btn-sm rounded-2 shadow-xs border text-indigo" title="Edit Transaksi">
                        <i class="ti ti-edit"></i>
                      </a>
                      <button onclick="confirm('Apakah Anda yakin ingin menghapus transaksi ini? Tindakan ini tidak dapat dibatalkan.') || event.stopImmediatePropagation()" wire:click="delete({{ $t->id }})" class="btn btn-icon btn-light btn-sm rounded-2 shadow-xs border text-danger" title="Hapus Transaksi">
                        <i class="ti ti-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="py-5 text-center text-muted">Belum ada transaksi terdaftar yang sesuai filter.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="card-footer d-flex align-items-center justify-content-between">
          {{ $transactions->links() }}
        </div>
      </div>

    </div>
  </div>
</div>
