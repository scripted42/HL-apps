<div>
  @section('title', 'Pelanggan')

  <!-- Page header -->
  <div class="page-header d-print-none mb-4">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle text-secondary">Manajemen</div>
          <h2 class="page-title font-weight-black text-dark tracking-tight">Daftar Pelanggan</h2>
        </div>
        <div class="col-auto ms-auto">
          <button wire:click="openCreateModal" class="btn btn-primary font-weight-bold">
            <i class="ti ti-plus me-1"></i> Tambah Pelanggan
          </button>
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
        <div class="card-body border-bottom py-3">
          <div class="d-flex">
            <div class="text-secondary">
              Cari Pelanggan:
              <div class="ms-2 d-inline-block">
                <input wire:model.live="search" type="search" class="form-control form-control-sm" placeholder="Ketik nama pelanggan..." style="width: 240px;">
              </div>
            </div>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table card-table table-vcenter text-nowrap">
            <thead>
              <tr class="text-muted uppercase font-weight-bold" style="font-size: 0.675rem; background-color: #fcfdfe;">
                <th>Nama Pelanggan</th>
                <th>Diskon LM (%)</th>
                <th>Diskon BR (%)</th>
                <th class="text-end">Threshold Bonus</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($customers as $c)
                <tr>
                  <td class="font-weight-black text-dark" style="font-size: 0.95rem;">
                    <a href="{{ route('customers.show', $c->id) }}" class="text-dark">{{ $c->name }}</a>
                  </td>
                  <td>
                    @if(!empty($c->discount_lm))
                      @foreach($c->discount_lm as $d)
                        <span class="badge bg-indigo-lt text-indigo font-weight-bold">{{ $d }}%</span>
                      @endforeach
                    @else
                      <span class="text-muted font-weight-medium">-</span>
                    @endif
                  </td>
                  <td>
                    @if(!empty($c->discount_br))
                      @foreach($c->discount_br as $d)
                        <span class="badge bg-purple-lt text-purple font-weight-bold">{{ $d }}%</span>
                      @endforeach
                    @else
                      <span class="text-muted font-weight-medium">-</span>
                    @endif
                  </td>
                  <td class="text-end font-weight-bold text-dark">
                    Rp {{ number_format($c->bonus_threshold, 0, ',', '.') }}
                  </td>
                  <td class="text-center">
                    <div class="btn-list justify-content-center">
                      <a href="{{ route('customers.show', $c->id) }}" class="btn btn-icon btn-light btn-sm rounded-2 shadow-xs border" title="Detail Monthly & Settle">
                        <i class="ti ti-eye"></i>
                      </a>
                      <button wire:click="openEditModal({{ $c->id }})" class="btn btn-icon btn-light btn-sm rounded-2 shadow-xs border text-indigo" title="Edit Pelanggan">
                        <i class="ti ti-edit"></i>
                      </button>
                      <button onclick="confirm('Apakah Anda yakin ingin menonaktifkan pelanggan ini?') || event.stopImmediatePropagation()" wire:click="delete({{ $c->id }})" class="btn btn-icon btn-light btn-sm rounded-2 shadow-xs border text-danger" title="Nonaktifkan Pelanggan">
                        <i class="ti ti-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="py-5 text-center text-muted">Belum ada data pelanggan yang sesuai.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="card-footer d-flex align-items-center justify-content-between">
          {{ $customers->links() }}
        </div>
      </div>

    </div>
  </div>

  <!-- Customer Form Modal -->
  <div class="modal modal-blur fade" id="customer-modal" tabindex="-1" role="dialog" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content shadow-lg border-0">
        <div class="modal-header border-bottom">
          <h5 class="modal-title font-weight-black text-dark h3">{{ $isEditMode ? 'Edit Pelanggan' : 'Tambah Pelanggan Baru' }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form wire:submit.prevent="save">
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label font-weight-semibold">Nama Pelanggan <span class="text-danger">*</span></label>
              <input wire:model="name" type="text" class="form-control @error('name') is-invalid @enderror" placeholder="Ketik nama pelanggan..." required>
              @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            
            <div class="mb-3">
              <label class="form-label font-weight-semibold">Diskon Tangga LM (%)</label>
              <input wire:model="discount_lm_input" type="text" class="form-control @error('discount_lm_input') is-invalid @enderror" placeholder="Contoh: 20, 20, 10">
              <small class="form-hint text-muted">Gunakan tanda koma (,) untuk memisahkan tingkatan diskon bertingkat.</small>
              @error('discount_lm_input') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
              <label class="form-label font-weight-semibold">Diskon Tangga BR (%)</label>
              <input wire:model="discount_br_input" type="text" class="form-control @error('discount_br_input') is-invalid @enderror" placeholder="Contoh: 20, 10">
              <small class="form-hint text-muted">Gunakan tanda koma (,) untuk memisahkan tingkatan diskon bertingkat.</small>
              @error('discount_br_input') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
              <label class="form-label font-weight-semibold">Threshold Batas Bonus (Rupiah) <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text font-weight-bold">Rp</span>
                <input wire:model="bonus_threshold" type="number" class="form-control @error('bonus_threshold') is-invalid @enderror" placeholder="Contoh: 10000000" required>
              </div>
              @error('bonus_threshold') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
          </div>
          <div class="modal-footer border-top">
            <button type="button" class="btn btn-link link-secondary font-weight-semibold" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary font-weight-bold px-4">
              <i class="ti ti-device-floppy me-1"></i> Simpan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  @push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const modalElement = document.getElementById('customer-modal');
      const bsModal = new bootstrap.Modal(modalElement);

      window.addEventListener('show-customer-modal', () => {
        bsModal.show();
      });

      window.addEventListener('hide-customer-modal', () => {
        bsModal.hide();
      });
    });
  </script>
  @endpush
</div>
